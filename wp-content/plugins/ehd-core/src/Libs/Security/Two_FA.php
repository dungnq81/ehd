<?php

namespace EHD_Libs\Security;

use EHD_Cores\Helper;
use Exception;
use PHPGangsta_GoogleAuthenticator;
use PragmaRX\Recovery\Recovery;
use WP_Session_Tokens;

class Two_FA {

	/**
	 * Local variables
	 *
	 * @var mixed
	 */
	public $google_authenticator;
	public ?string $encryption_key_file;
	public Encryption $encryption;
	public Recovery $recovery;

	/**
	 * User meta used by 2FA.
	 *
	 * @var array
	 */
	public array $user_2fa_meta = [
		'configured',
		'secret',
		'qr',
		'backup_codes',
	];

	/**
	 * @return mixed|null
	 */
	public function get_admin_user_roles() {
		$roles = [
			'editor',
			'administrator',
		];

		return apply_filters( 'ehd_security_2fa_roles', $roles );
	}

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->encryption_key_file  = defined( 'EHD_ENCRYPTION_KEY_FILE_PATH' ) ? EHD_ENCRYPTION_KEY_FILE_PATH : EHD_PLUGIN_PATH . 'ehd_encrypt_key.php';
		$this->google_authenticator = new PHPGangsta_GoogleAuthenticator();
		$this->recovery             = new Recovery();
		$this->encryption           = new Encryption( $this->encryption_key_file );
	}

	/**
	 * Generate QR code for specific user.
	 *
	 * @param int $user_id WordPress user ID.
	 *
	 * @return string       The QR code URL.
	 */
	public function generate_qr_code( int $user_id ): string {
		// Get the user by ID.
		$user = get_user_by( 'ID', $user_id );

		// Build the title for the authenticator.
		$title = get_home_url() . ' (' . $user->user_email . ')';

		// Get the user secret code.
		$secret = $this->get_user_secret( $user->ID ); // phpcs:ignore

		// Return the URL.
		return $this->google_authenticator->getQRCodeGoogleUrl( $title, $secret );
	}

	/**
	 * Verify the authentication code.
	 *
	 * @param string $code One time code from the authenticator app.
	 * @param int $user_id The user ID.
	 *
	 * @return bool            True if the code is valid, false otherwise.
	 */
	public function check_authentication_code( string $code, int $user_id ): bool {
		// Get the user secret.
		$secret = $this->get_user_secret( $user_id ); // phpcs:ignore

		// Verify the code.
		return $this->google_authenticator->verifyCode( $secret, $code, 2 );
	}

	/**
	 * Enable 2FA.
	 *
	 * @return bool  True on success, false on failure.
	 */
	public function enable_2fa(): bool {
		// Remove admin notice for file creation.
		delete_option( 'ehd_security_2fa_encryption_file_notice' );

		// Get all users which needs to have 2FA enabled.
		$users = get_users(
			[
				'role__in' => $this->get_admin_user_roles(),
			]
		);

		// Bail if there are no such users found.
		if ( empty( $users ) ) {
			return true;
		}

		foreach ( $users as $user ) {

			// Get the user by the user id.
			$user = get_userdata( $user->data->ID );

			if ( empty( array_intersect( $this->get_admin_user_roles(), $user->roles ) ) ) {
				continue;
			}

			$session_tokens = WP_Session_Tokens::get_instance( $user->data->ID );
			$session_tokens->destroy_all();
		}

		return true;
	}

	/**
	 * Handle 2FA option change.
	 *
	 * @param mixed $new_value New option value.
	 * @param mixed $old_value Old option value.
	 */
	public function handle_option_change( $new_value, $old_value ) {
		if (
			1 === intval( $new_value ) &&
			false === $this->encryption->generate_encryption_file()
		) {
			return $old_value;
		}

		if ( 1 == $new_value ) {
			$this->enable_2fa();
		}

		return $new_value;
	}

	/**
	 * Generate the user secret.
	 *
	 * @param int $user_id WordPress user ID.
	 *
	 * @return bool|int          True on success, false on failure, user ID if the secret exists.
	 * @throws Exception
	 */
	public function generate_user_secret( int $user_id ) {
		// Check if the user has secret code.
		$secret = $this->get_user_secret( $user_id ); // phpcs:ignore

		// Bail if the user already has a secret code.
		if ( ! empty( $secret ) ) {
			return $user_id;
		}

		// Add the user secret meta.
		return update_user_meta( // phpcs:ignore
			$user_id,
			'ehd_security_2fa_secret',
			$this->encryption->encrypt( $this->google_authenticator->createSecret() ) // Generate and encrypt the secret code.
		);
	}

	/**
	 * Generate the user backup codes.
	 *
	 * @param int $user_id WordPress user ID.
	 *
	 * @return array        True on success, false on failure, user ID if the backup codes exists.
	 */
	public function generate_user_backup_codes( int $user_id ): array {
		// Check if the user has backup codes.
		$backup_codes = get_user_meta( $user_id, 'ehd_security_2fa_backup_codes', true ); // phpcs:ignore

		// Bail if the user already has a backup codes.
		if ( ! empty( $backup_codes ) ) {
			return [];
		}

		// Generate the backup codes.
		$generated_backup_codes = $this->recovery->numeric()->setCount( 8 )->setBlocks( 1 )->setChars( 8 )->toArray();

		// Store the backup codes hashed.
		$this->store_hashed_user_meta( $user_id, 'ehd_security_2fa_backup_codes', $generated_backup_codes );

		// Return the codes so we can show them to the user once.
		return $generated_backup_codes;
	}

	/**
	 * Validate the backup codes 2Fa login.
	 *
	 * @param string $code The backup login code.
	 * @param int $user The user id.
	 *
	 * @return bool         True if the code is correct, false on failure.
	 */
	public function validate_backup_login( string $code, int $user ): bool {
		$codes = get_user_meta( $user, 'ehd_security_2fa_backup_codes', true ); // phpcs:ignore

		// Bail if the user doesn't have backup codes.
		if ( empty( $codes ) ) {
			return false;
		}

		// Validate the backup code.
		foreach ( $codes as $index => $hashed_code ) {
			if ( wp_check_password( $code, $hashed_code ) ) {
				// Remove the used key.
				unset( $codes[ $index ] );

				// Update user meta with the removed code data.
				update_user_meta( $user, 'ehd_security_2fa_backup_codes', $codes );

				return true;
			}
		}

		// Bail if the code doesn't exists in the user backup codes.
		return false;
	}

	/**
	 * Display the two factor authentication forms.
	 *
	 * @param array $args Additional args.
	 */
	public function load_form( array $args ) {
		// Bail if template is not provided.
		if ( empty( $args['template'] ) ) {
			return;
		}

		// Path to the form template.
		$path = EHD_PLUGIN_PATH . 'templates/' . $args['template'];

		// Bail if there is no such file.
		if ( ! file_exists( $path ) ) {
			return;
		}

		$args = $this->get_args_for_template( $args );

		// Check if the referer matches wp-login url.
		if ( strtok( wp_get_raw_referer(), '?' ) === wp_login_url() ) {
			$args['is_wp_login'] = true;
		}

		if ( ! empty( $this->get_2fa_nonce_cookie() ) ) {
			$args['is_wp_login'] = true;
		}

		// Include the login header if the function doesn't exists.
		if ( ! function_exists( 'login_header' ) ) {
			include_once ABSPATH . 'wp-login.php';
		}

		// Include the template.php if the function doesn't exists.
		if ( ! function_exists( 'submit_button' ) ) {
			require_once ABSPATH . '/wp-admin/includes/template.php';
		}

		// Jetpack SSO Hiding 2FA form.
		if ( class_exists( 'Jetpack_SSO' ) ) {
			remove_filter( 'login_body_class', [ \Jetpack_SSO::get_instance(), 'login_body_class' ] );
		}

		login_header();

		// Include the template.
		include_once $path;

		login_footer();
		exit;
	}

	/**
	 * Reset the 2FA for specific user ID.
	 *
	 * @param int $user_id WordPress user ID.
	 *
	 * @return array|bool $response Response to react app.
	 */
	public function reset_user_2fa( int $user_id ) {
		// Bail if there is no such user.
		if ( false === get_user_by( 'ID', $user_id ) ) {
			return false;
		}

		// Delete the 2FA user meta and reset the 2FA configuration setting.
		foreach ( $this->user_2fa_meta as $meta ) {
			delete_user_meta( $user_id, 'ehd_security_2fa_' . $meta ); // phpcs:ignore
		}

		return [
			'message' => __( 'User 2FA reset!', EHD_PLUGIN_TEXT_DOMAIN ),
			'result'  => 1,
		];
	}

	/**
	 * Default arguments passed to the form.
	 *
	 * @param array $args Arguments passed.
	 *
	 * @return array           Arguments merged with the default ones.
	 */
	public function get_args_for_template( array $args ): array {
		return array_merge(
			$args,
			[
				'interim_login' => ( isset( $_REQUEST['interim-login'] ) ) ? filter_var( wp_unslash( $_REQUEST['interim-login'] ), FILTER_VALIDATE_BOOLEAN ) : false,
				'redirect_to'   => isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : admin_url(),
				'rememberme'    => ! empty( $_REQUEST['rememberme'] ),
				'is_wp_login'   => false,
			]
		);
	}

	/**
	 * Load the backup codes form.
	 */
	public function load_backup_codes_form() {
		// Get cookie data.
		$cookie_data = $this->get_2fa_nonce_cookie();

		// Bail if cookie data is empty.
		if ( empty( $cookie_data ) ) {
			return;
		}

		// Load the backup code login form.
		$this->load_form(
			[
				'template' => '2fa-login-backup-code.php',
				'action'   => esc_url( add_query_arg( 'action', 'ehd_2fabc', wp_login_url() ) ),
				'error'    => '',
			]
		);
	}

	/**
	 * Set 30 days 2FA auth cookie.
	 *
	 * @param int $user_id WordPress user ID.
	 *
	 * @throws Exception
	 */
	public function set_2fa_dnc_cookie( int $user_id ) {
		// Generate random token.
		$token = bin2hex( random_bytes( 22 ) );

		// Assign the token to the user.
		update_user_meta( $user_id, 'ehd_2fa_dnc_token', $token );

		// Set the 2FA auth cookie.
		setcookie( 'ehd_security_2fa_dnc_cookie', $user_id . '|' . $token, time() + 2592000 ); // phpcs:ignore
	}

	/**
	 * Check if there is a valid 2FA cookie.
	 *
	 * @param string $user_login The username.
	 * @param object $user WP_User object.
	 *
	 * @return bool True if there is a 2FA cookie, false if not.
	 */
	public function check_2fa_cookie( string $user_login, object $user ): bool {
		// 2FA user cookie name.
		$ehd_2fa_user_cookie = 'ehd_security_2fa_dnc_cookie';

		// Bail if the cookie doesn't exists.
		if ( ! isset( $_COOKIE[ $ehd_2fa_user_cookie ] ) ) {
			return false;
		}

		// Parse the cookie.
		$cookie_data = explode( '|', $_COOKIE[ $ehd_2fa_user_cookie ] );

		if (
			// If the 2FA is configured for the user.
			1 == get_user_meta( $cookie_data[0], 'ehd_security_2fa_configured', true ) && // phpcs:ignore
			get_user_meta( $cookie_data[0], 'ehd_2fa_dnc_token', true ) === $cookie_data[1] // If there is already a cookie with that name and the name matches.
		) {
			return true;
		}

		return false;
	}

	/**
	 * Show the backup codes form to the user if this is the initial 2fa setup.
	 *
	 * @param int $user_id WordPress user ID.
	 */
	public function show_backup_codes( int $user_id ) {
		$this->load_form(
			[
				'template'     => 'backup-codes.php',
				'backup_codes' => $this->generate_user_backup_codes( $user_id ),
				'redirect_to'  => ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : get_admin_url(),
				// phpcs:ignore
			]
		);
	}

	/**
	 * Show QR code to the user if backup code is used.
	 *
	 * @return void
	 */
	public function show_qr_backup_code_used() {
		$this->load_form(
			[
				'template'    => 'backup-code-used.php',
				'redirect_to' => ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : get_admin_url(),
				// phpcs:ignore
			]
		);
	}

	/**
	 * Interim WordPress login.
	 */
	public function interim_check() {
		global $interim_login;
		$interim_login = ( isset( $_REQUEST['interim-login'] ) ) ? filter_var( $_REQUEST['interim-login'], FILTER_VALIDATE_BOOLEAN ) : false; // phpcs:ignore

		// Bail if $interim_login is false.
		if ( false === $interim_login ) {
			return;
		}

		$interim_login = 'success'; // WPCS: override ok.
		login_header( '', '<p class="message">' . __( 'You have logged in successfully.', EHD_PLUGIN_TEXT_DOMAIN ) . '</p>' );
		?>
        </div>
		<?php do_action( 'login_footer' ); ?>
        </body></html>
		<?php
		exit;
	}

	/**
	 * Initialize the 2fa
	 *
	 * @param string $user_login
	 * @param object $user
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init_2fa( string $user_login, object $user ) {
		// Bail if the user role does not allow 2FA setup.
		if ( empty( array_intersect( $this->get_admin_user_roles(), $user->roles ) ) ) {
			return;
		}

		// Bail if there is a valid 2FA cookie.
		if ( true === $this->check_2fa_cookie( $user_login, $user ) ) {
			return;
		}

		// Validate the encryption key.
		if ( false === $this->encryption->get_encryption_key() ) {

			// Disable the 2FA and show admin notice.
			$this->disable_2fa_show_notice();
		}

		// Remove the auth cookie.
		wp_clear_auth_cookie();

		$user_cookie_part = bin2hex( random_bytes( 18 ) );

		setcookie( 'ehd_2fa_login_nonce', $user->ID . '|' . $user_cookie_part, time() + DAY_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );

		update_user_meta( $user->ID, 'ehd_2fa_login_nonce', wp_hash( $user_cookie_part ) );

		if ( 1 == get_user_meta( $user->ID, 'ehd_security_2fa_configured', true ) ) { // phpcs:ignore
			// Load the 2fa form.
			$this->load_form(
				[
					'action'   => esc_url( add_query_arg( 'action', 'ehd_2fa', wp_login_url() ) ),
					'template' => '2fa-login.php',
					'error'    => '',
				]
			);
		}

		// Generate user secret code.
		$this->generate_user_secret( $user->ID );

		// Load the 2fa form.
		$this->load_form(
			[
				'action'   => esc_url( add_query_arg( 'action', 'ehd_2fa', wp_login_url() ) ),
				'template' => '2fa-initial-setup-form.php',
				'error'    => '',
				'qr'       => $this->generate_qr_code( $user->ID ),
				'secret'   => $this->get_user_secret( $user->ID ),
			]
		);
	}

	/**
	 * Validate backup codes login.
	 */
	public function validate_2fabc_login() {
		// Get the cookie data.
		$cookie_data = $this->get_2fa_nonce_cookie();

		// Bail if cookie data is empty.
		if ( empty( $cookie_data ) ) {
			return;
		}

		$result = false;

		// Check if the 2fa backup code is set, if not, don't try to apply it's value.
		if ( isset( $_POST['ehd_2fabackupcode'] ) ) {
			// Validate the backup code.
			$result = $this->validate_backup_login(
				wp_unslash( $_POST['ehd_2fabackupcode'] ),
				wp_unslash( $cookie_data[0] )
			); // phpcs:ignore
		}

		// Check the result of the authentication.
		if ( false === $result ) {
			$this->load_form(
				[
					'template' => '2fa-login-backup-code.php',
					'action'   => esc_url( add_query_arg( 'action', 'ehd_2fabc', wp_login_url() ) ),
					'error'    => esc_html__( 'Invalid backup code!', EHD_PLUGIN_TEXT_DOMAIN ),
				]
			);
		}

		// Login the user.
		$this->login_user( $cookie_data[0] );

		// Interim login.
		$this->interim_check();

		// Get the redirect url.
		$redirect_url = ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : get_admin_url(); // phpcs:ignore

		if ( ! isset( $_POST['backup-code-used'] ) ) { // phpcs:ignore

			// Redirect to the reset url.
			wp_safe_redirect( esc_url_raw( wp_unslash( $redirect_url ) ) );
		}

		// Show QR code.
		$this->show_qr_backup_code_used();
	}

	/**
	 * Validate 2FA login
	 *
	 * @return void
	 */
	public function validate_2fa_login() {
		// Get the cookie data.
		$cookie_data = $this->get_2fa_nonce_cookie();

		// Bail if cookie data is empty.
		if ( empty( $cookie_data ) ) {
			return;
		}

		// Validate the encryption key.
		if ( false === $this->encryption->get_encryption_key() ) {

			// Disable the 2FA and show admin notice.
			$this->disable_2fa_show_notice();
		}

		$result = false;

		// Check if the 2fa code is set, if not, don't try to apply it's value.
		if ( isset( $_POST['ehd_2facode'] ) ) {
			$result = $this->check_authentication_code( wp_unslash( $_POST['ehd_2facode'] ), wp_unslash( $cookie_data[0] ) ); // phpcs:ignore
		}

		// Check the result of the authentication.
		if ( false === $result ) {
			// Arguments for 2fa login.
			$args = [
				'template' => '2fa-login.php',
				'error'    => esc_html__( 'Invalid verification code!', EHD_PLUGIN_TEXT_DOMAIN ),
				'action'   => esc_url( add_query_arg( 'action', 'ehd_2fa', wp_login_url() ) ),
			];

			if ( 0 == get_user_meta( $cookie_data[0], 'ehd_security_2fa_configured', true ) ) { // phpcs:ignore
				// Arguments for initial 2fa setup.
				$args = array_merge( $args, [
					'template' => '2fa-initial-setup-form.php',
					'qr'       => $this->generate_qr_code( $cookie_data[0] ),
					'secret'   => $this->get_user_secret( $cookie_data[0] ),
				] );
			}

			$this->load_form( $args ); // phpcs:ignore
		}

		// Login the user.
		$this->login_user( $cookie_data[0] );

		// Interim login.
		$this->interim_check();

		// Get the redirect url.
		$redirect_url = ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : get_admin_url(); // phpcs:ignore

		// Show backup codes to the user in the initial 2FA setup.
		if ( isset( $_POST['ehd-2fa-setup'] ) ) { // phpcs:ignore
			$this->show_backup_codes( $cookie_data[0] );
		}

		// Redirect to the reset url.
		wp_safe_redirect( esc_url_raw( wp_unslash( $redirect_url ) ) );
	}

	/**
	 * Login the user.
	 *
	 * @param int $user_id The user id.
	 */
	private function login_user( int $user_id ) {
		// Set the auth cookie.
		wp_set_auth_cookie( wp_unslash( $user_id ), intval( wp_unslash( $_POST['rememberme'] ) ) ); // phpcs:ignore

		// Delete the nonce meta.
		delete_user_meta( $user_id, 'ehd_2fa_login_nonce' );

		// Delete the nonce cookie.
		setcookie( 'ehd_2fa_login_nonce', '', - 1, SITECOOKIEPATH, COOKIE_DOMAIN ); // phpcs:ignore

		// Set 30 days 2FA auth cookie.
		if ( isset( $_POST['do_not_challenge'] ) ) { // phpcs:ignore
			$this->set_2fa_dnc_cookie( $user_id );
		}

		// Update the user meta if this is the initial 2FA setup.
		if ( ! isset( $_POST['ehd-2fa-setup'] ) ) { // phpcs:ignore
			return;
		}

		// Set a flag, that the user has configured the 2fa.
		update_user_meta( $user_id, 'ehd_security_2fa_configured', 1 ); // phpcs:ignore

		// Invalidate 2FA cookie.
		setcookie( 'ehd_security_2fa_dnc_cookie', '', - 1 ); // phpcs:ignore
	}

	/**
	 * Get the 2fa nonce cookie
	 *
	 * @return false|string[]|void Cookie data if the cookie exists, null otherwise.
	 */
	public function get_2fa_nonce_cookie() {
		// Bail if the cookie doesn't exists.
		if ( empty( $_COOKIE['ehd_2fa_login_nonce'] ) ) {
			return;
		}

		// Parse the cookie.
		$cookie_data = explode( '|', $_COOKIE['ehd_2fa_login_nonce'] );
		// Get the user nonce meta.
		$meta_nonce = get_user_meta( $cookie_data[0], 'ehd_2fa_login_nonce', true );

		if ( empty( $meta_nonce ) || empty( $cookie_data[0] ) ) {
			return;
		}

		// Bail if the nonce is invalid.
		if ( ! hash_equals( $meta_nonce, wp_hash( $cookie_data[1] ) ) ) {
			return;
		}

		// Return the cookie data.
		return $cookie_data;
	}

	/**
	 * Check for all users with 2fa setup.
	 *
	 * @return array The array containing the users using 2FA.
	 */
	public function check_for_users_using_2fa(): array {
		// Get all users with 2FA configured.
		return get_users(
			[
				'role__in'   => $this->get_admin_user_roles(),
				'orderby'    => 'user_login',
				'order'      => 'ASC',
				'fields'     => [
					'ID',
					'user_login',
				],
				'meta_query' => [
					[
						'key'     => 'ehd_security_2fa_configured',
						'value'   => '1',
						'compare' => '=',
					],
				],
			]
		);
	}

	/**
	 * Stores a hashed user meta.
	 *
	 * @param int $user_id The user ID
	 * @param string $meta The user meta
	 * @param array $data The data to be hashed
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public function store_hashed_user_meta( int $user_id, string $meta, array $data = [] ) {
		// Bail if data is not an array.
		if ( ! is_array( $data ) ) {
			return false;
		}

		// Prepare the array.
		$hashed_data = [];

		// Hash the data.
		foreach ( $data as $key => $value ) {
			$hashed_value  = wp_hash_password( $value );
			$hashed_data[] = $hashed_value;
		}

		// Add the user hashed meta.
		return update_user_meta( $user_id, $meta, $hashed_data );
	}

	/**
	 * Gets the user secret.
	 *
	 * @param int $user_id
	 *
	 * @return string|void
	 */
	public function get_user_secret( int $user_id ) {
		// Get the encrypted secret code of the user.
		$user_secret = get_user_meta( $user_id, 'ehd_security_2fa_secret', true );

		// Bail if the user ID or meta value does not exist.
		if ( empty( $user_secret ) ) {
			return;
		}

		// Decrypt and return the secret code.
		return $this->encryption->decrypt( $user_secret );
	}

	/**
	 * Reset 2FA for all users.
	 */
	public function reset_all_users_2fa() {
		// Delete the 2FA user meta and reset the 2FA configuration setting.
		foreach ( $this->user_2fa_meta as $meta ) {
			delete_metadata( 'user', 0, 'ehd_security_2fa_' . $meta, '', true );
		}
	}

	/**
	 * Disables the 2FA and shows admin notice.
	 */
	public function disable_2fa_show_notice() {
		// Disable 2FA.
		Helper::updateOption( 'ehd_security_2fa', 0 ); // phpcs:ignore

		// Reset all users 2FA setup.
		$this->reset_all_users_2fa();

		// Show admin notice for file creation failure.
		Helper::updateOption( 'ehd_security_2fa_encryption_file_notice', 1 ); // phpcs:ignore
	}

	/**
	 * Displays an admin notice that we were not able to create encryption file.
	 */
	public function show_notices() {
		// Bail if there is no need of a notice.
		if ( empty( get_option( 'ehd_security_2fa_encryption_file_notice', false ) ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error ehd-section__content" style="position: relative; margin-top: 1em; display:block!important;"><p>%1$s</p><button type="button" class="notice-dismiss dismiss-ehd-security-notice" data-link="%2$s"><span class="screen-reader-text">Dismiss this notice.</span></button></div>',
			__( 'We were not able to create encryption file used by 2FA, so the Two Factor Authentication service was disabled. Please check your website files and folders permissions or contact your hosting provider for assistance.', EHD_PLUGIN_TEXT_DOMAIN ), // phpcs:ignore
			admin_url( 'admin-ajax.php?action=dismiss_ehd_2fa_notice&notice=2fa_encryption_file_notice' ) // phpcs:ignore
		);
	}

	/**
	 * Hide notices.
	 */
	public function hide_notice() {
		if ( empty( $_GET['notice'] ) ) {
			return;
		}

		Helper::updateOption( 'ehd_security_' . $_GET['notice'], 0 ); // phpcs:ignore

		wp_send_json_success();
	}
}
