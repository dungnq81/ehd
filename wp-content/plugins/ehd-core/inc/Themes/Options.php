<?php

namespace EHD\Themes;

use EHD\Cores\Helper;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

\defined('ABSPATH') || die;

/**
 * Options Class
 *
 * @author eHD
 */
final class Options
{
    public function __construct()
    {
	    add_action( 'admin_notices', [ &$this, 'options_admin_notice' ] );
	    add_action( 'admin_menu', [ &$this, 'options_admin_menu' ] );
	    add_action( 'admin_enqueue_scripts', [ &$this, 'options_enqueue_assets' ], 32 );

	    /** SMTP Settings */
	    if ( self::_smtp__is_configured() ) {
		    add_action( 'phpmailer_init', [ &$this, 'setup_phpmailer_init' ], 11 );
	    }
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_admin_menu() : void
    {
	    // menu page
	    add_menu_page(
		    __( 'eHD Settings', EHD_PLUGIN_TEXT_DOMAIN ),
		    __( 'eHD Settings', EHD_PLUGIN_TEXT_DOMAIN ),
		    'manage_options',
		    'ehd-settings',
		    [ &$this, 'options_page' ],
		    'dashicons-admin-generic',
		    80
	    );

	    // submenu page
	    add_submenu_page( 'ehd-settings', __( 'Advanced', EHD_PLUGIN_TEXT_DOMAIN ), __( 'Advanced', EHD_PLUGIN_TEXT_DOMAIN ), 'manage_options', 'customize.php' );
	    add_submenu_page( 'ehd-settings', __( 'Server Info', EHD_PLUGIN_TEXT_DOMAIN ), __( 'Server Info', EHD_PLUGIN_TEXT_DOMAIN ), 'manage_options', 'server-info', [ &$this, 'server_info' ] );
	    add_submenu_page( 'ehd-settings', __( 'Help & Guides', EHD_PLUGIN_TEXT_DOMAIN ), __( 'Help & Guides', EHD_PLUGIN_TEXT_DOMAIN ), 'manage_options', 'panel-support', [ &$this, 'panel_support' ] );
    }

    /** ---------------------------------------- */

	/**
	 * @param $hook
	 *
	 * @return void
	 */
	public function options_enqueue_assets( $hook )
    {
		$allowed_pages = array(
			'toplevel_page_ehd-settings',
		);

		if ( in_array( $hook, $allowed_pages ) ) {
			$codemirror_settings = [
				'codemirror_css'  => wp_enqueue_code_editor( [ 'type' => 'text/css' ] ),
				'codemirror_html' => wp_enqueue_code_editor( [ 'type' => 'text/html' ] ),
			];

			wp_enqueue_style( 'wp-codemirror' );
			wp_localize_script( 'admin', 'codemirror_settings', $codemirror_settings );
		}
	}

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_page() : void
    {
        if (isset($_POST['ehd_update_settings'])) {

	        $nonce = $_REQUEST['_wpnonce'];
	        if ( ! wp_verify_nonce( $nonce, 'ehd_settings' ) ) {
		        wp_die( __( 'Error! Nonce Security Check Failed! please save the settings again.', EHD_PLUGIN_TEXT_DOMAIN ) );
	        }

            /** Global */

            $html_custom_header = $_POST['html_custom_header'] ?? '';
            $html_custom_footer = $_POST['html_custom_footer'] ?? '';
            $html_custom_body_top = $_POST['html_custom_body_top'] ?? '';
            $html_custom_body_bottom = $_POST['html_custom_body_bottom'] ?? '';

	        Helper::updateCustomPost( $html_custom_header, 'html_custom_header', 'text/html' );
	        Helper::updateCustomPost( $html_custom_footer, 'html_custom_footer', 'text/html' );
	        Helper::updateCustomPost( $html_custom_body_top, 'html_custom_body_top', 'text/html' );
	        Helper::updateCustomPost( $html_custom_body_bottom, 'html_custom_body_bottom', 'text/html' );

	        /** SMTP */

	        $smtp_host     = ! empty( $_POST['smtp_host'] ) ? sanitize_text_field( $_POST['smtp_host'] ) : '';
	        $smtp_auth     = ! empty( $_POST['smtp_auth'] ) ? sanitize_text_field( $_POST['smtp_auth'] ) : '';
	        $smtp_username = ! empty( $_POST['smtp_username'] ) ? sanitize_text_field( $_POST['smtp_username'] ) : '';

	        if ( ! empty( $_POST['smtp_password'] ) ) {
		        $smtp_password = sanitize_text_field( $_POST['smtp_password'] );
		        $smtp_password = wp_unslash( $smtp_password ); // This removes slash (automatically added by WordPress) from the password when apostrophe is present
		        $smtp_password = base64_encode( $smtp_password );
	        }

	        $smtp_encryption               = ! empty( $_POST['smtp_encryption'] ) ? sanitize_text_field( $_POST['smtp_encryption'] ) : '';
	        $smtp_port                     = ! empty( $_POST['smtp_port'] ) ? sanitize_text_field( $_POST['smtp_port'] ) : '';
	        $smtp_from_email               = ! empty( $_POST['smtp_from_email'] ) ? sanitize_email( $_POST['smtp_from_email'] ) : '';
	        $smtp_from_name                = ! empty( $_POST['smtp_from_name'] ) ? sanitize_text_field( $_POST['smtp_from_name'] ) : '';
	        $smtp_disable_ssl_verification = ! empty( $_POST['smtp_disable_ssl_verification'] ) ? sanitize_text_field( $_POST['smtp_disable_ssl_verification'] ) : '';

	        $smtp_options = [
		        'smtp_host'                     => $smtp_host,
		        'smtp_auth'                     => $smtp_auth,
		        'smtp_username'                 => $smtp_username,
		        'smtp_encryption'               => $smtp_encryption,
		        'smtp_port'                     => $smtp_port,
		        'smtp_from_email'               => $smtp_from_email,
		        'smtp_from_name'                => $smtp_from_name,
		        'smtp_disable_ssl_verification' => $smtp_disable_ssl_verification,
	        ];

	        if ( ! empty( $smtp_password ) ) {
		        $smtp_options['smtp_password'] = $smtp_password;
	        }

	        self::_smtp__update_options( $smtp_options );

	        /** Aspect Ratio */

	        $aspect_ratio_options = [];
	        $ar_post_type_list    = apply_filters( 'ar_post_type_list', [ 'blogs' ] );
	        foreach ( $ar_post_type_list as $i => $ar ) {
		        $aspect_ratio_options[ 'ar-' . $ar . '-width' ]  = ! empty( $_POST[ $ar . '-width' ] ) ? sanitize_text_field( $_POST[ $ar . '-width' ] ) : 3;
		        $aspect_ratio_options[ 'ar-' . $ar . '-height' ] = ! empty( $_POST[ $ar . '-height' ] ) ? sanitize_text_field( $_POST[ $ar . '-height' ] ) : 2;
	        }

	        self::_aspect_ratio__update_options( $aspect_ratio_options );

	        /** Custom CSS */

	        $html_custom_css = $_POST['html_custom_css'] ?? '';
	        Helper::updateCustomCssPost( $html_custom_css, 'html_custom_css' );

	        /** */
	        self::_message_success();
        }

        ?>
        <div class="wrap" id="ehd_container">
            <form id="ehd_form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('ehd_settings'); ?>
                <div id="main" class="filter-tabs clearfix">
                    <div id="ehd_nav" class="tabs-nav">
                        <div class="logo-title">
                            <h3>
                                <?php _e('eHD Settings', EHD_PLUGIN_TEXT_DOMAIN); ?>
                                <span>Version: <?php echo EHD_PLUGIN_VERSION; ?></span>
                            </h3>
                        </div>
                        <div class="save-bar">
                            <button type="submit" name="ehd_update_settings" class="button button-primary"><?php _e('Save Changes', EHD_PLUGIN_TEXT_DOMAIN) ?></button>
                        </div>
                        <ul class="ul-menu-list">
                            <li class="global-settings">
                                <a class="current" title="Global Settings" href="#global_settings"><?php _e('Global Settings', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="smtp smtp-settings">
                                <a title="SMTP" href="#smtp_settings"><?php _e('SMTP', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="aspect-ratio aspect-ratio-settings">
                                <a title="Aspect ratio" href="#aspect_ratio_settings"><?php _e('Aspect Ratio', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="custom-css-settings">
                                <a title="Custom CSS" href="#custom_css_settings"><?php _e('Custom CSS', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                        </ul>
                    </div>
                    <div id="ehd_content" class="tabs-content">
                        <h2 class="hidden-text"></h2>
                        <div id="global_settings" class="group tabs-panel show">
                            <?php require __DIR__ . '/options/global.php'; ?>
                        </div>
                        <div id="smtp_settings" class="group tabs-panel">
                            <?php require __DIR__ . '/options/smtp.php'; ?>
                        </div>
                        <div id="aspect_ratio_settings" class="group tabs-panel">
		                    <?php require __DIR__ . '/options/aspect-ratio.php'; ?>
                        </div>
                        <div id="custom_css_settings" class="group tabs-panel">
		                    <?php require __DIR__ . '/options/custom-css.php'; ?>
                        </div>
                        <div class="save-bar">
                            <button type="submit" name="ehd_update_settings" class="button button-primary"><?php _e('Save Changes', EHD_PLUGIN_TEXT_DOMAIN) ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function panel_support() : void {}

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function server_info() : void {}

	/** ---------------------------------------- */

	/**
	 * @param $phpmailer
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setup_phpmailer_init( $phpmailer ): void {

		// (Re)create it, if it's gone missing.
		if ( ! ( $phpmailer instanceof PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$phpmailer = new PHPMailer( true );

			$phpmailer::$validator = static function ( $email ) {
				return (bool) is_email( $email );
			};
		}

		$smtp_options = get_option( 'smtp__options' );

		$phpmailer->isSMTP(); // Tell PHPMailer to use SMTP
		$phpmailer->Host = $smtp_options['smtp_host']; // Set the hostname of the mail server

		// Whether to use SMTP authentication
		if ( isset( $smtp_options['smtp_auth'] ) && $smtp_options['smtp_auth'] == "true" ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $smtp_options['smtp_username']; // SMTP username
			$phpmailer->Password = base64_decode( $smtp_options['smtp_password'] ); // SMTP password
		}

		// Additional settings

		$type_of_encryption = $smtp_options['smtp_encryption']; // Whether to use encryption
		if ( $type_of_encryption == "none" ) {
			$type_of_encryption = '';
		}
		$phpmailer->SMTPSecure = $type_of_encryption;

		$phpmailer->Port        = $smtp_options['smtp_port'];  // SMTP port
		$phpmailer->SMTPAutoTLS = false; // Whether to enable TLS encryption automatically if a server supports it

		// disable ssl certificate verification if checked
		if ( isset( $smtp_options['smtp_disable_ssl_verification'] ) && ! empty( $smtp_options['smtp_disable_ssl_verification'] ) ) {
			$phpmailer->SMTPOptions = [
				'ssl' => [
					'verify_peer'       => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true,
				]
			];
		}

		$from_email = apply_filters( 'wp_mail_from', $smtp_options['smtp_from_email'] ); // Filters the email address to send from.
		$from_name  = apply_filters( 'wp_mail_from_name', $smtp_options['smtp_from_name'] ); // Filters the name to associate with the "from" email address.

		$phpmailer->setFrom( $from_email, $from_name, false );
		$phpmailer->CharSet = apply_filters( 'wp_mail_charset', get_bloginfo( 'charset' ) );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function options_admin_notice() : void
	{
		// SMTP notices
		if ( ! self::_smtp__is_configured() ) {
			$class   = 'notice notice-error';
			$message = __( 'You need to configure your SMTP credentials in the settings to send emails.', EHD_PLUGIN_TEXT_DOMAIN );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}

		// ...
	}

    /** ---------------------------------------- */

    /**
     * @param $new_smtp_options
     * @return void
     */
    private function _smtp__update_options($new_smtp_options) : void
    {
	    $empty_options = [
		    'smtp_host'                     => '',
		    'smtp_auth'                     => '',
		    'smtp_username'                 => '',
		    'smtp_password'                 => '',
		    'smtp_encryption'               => '',
		    'smtp_port'                     => '',
		    'smtp_from_email'               => '',
		    'smtp_from_name'                => '',
		    'smtp_disable_ssl_verification' => '',
	    ];

	    $options = get_option( 'smtp__options' );

	    if ( is_array( $options ) ) {
		    $current_options = array_merge( $empty_options, $options );
		    $updated_options = array_merge( $current_options, $new_smtp_options );
	    } else {
		    $updated_options = array_merge( $empty_options, $new_smtp_options );
	    }

	    update_option( 'smtp__options', $updated_options );
    }

    /** ---------------------------------------- */

	/**
	 * @param $new_options
	 *
	 * @return void
     * @todo check (ok)
	 */
    private function _aspect_ratio__update_options($new_options): void
    {
//	    $options = get_option( 'aspect_ratio__options' );
//	    if ( is_array( $options ) ) {
//		    $updated_options = array_merge( $options, $new_options );
//	    } else {
//		    $updated_options = $new_options;
//	    }

	    $updated_options = $new_options;
	    update_option( 'aspect_ratio__options', $updated_options );
    }

    /** ---------------------------------------- */

    /**
     * @return bool
     */
    private function _smtp__is_configured() : bool
    {
	    $smtp_options    = get_option( 'smtp__options' );
	    $smtp_configured = true;

	    if ( isset( $smtp_options['smtp_auth'] ) && $smtp_options['smtp_auth'] == "true" ) {
		    if ( empty( $smtp_options['smtp_username'] ) ||
		         empty( $smtp_options['smtp_password'] )
		    ) {
			    $smtp_configured = false;
		    }
	    }

	    if ( empty( $smtp_options['smtp_host'] ) ||
	         empty( $smtp_options['smtp_auth'] ) ||
	         empty( $smtp_options['smtp_encryption'] ) ||
	         empty( $smtp_options['smtp_port'] ) ||
	         empty( $smtp_options['smtp_from_email'] ) ||
	         empty( $smtp_options['smtp_from_name'] )
	    ) {
		    $smtp_configured = false;
	    }

        return $smtp_configured;
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    private function _message_success() : void
    {
	    $class   = 'notice notice-success is-dismissible';
	    $message = __( 'Settings saved.', EHD_PLUGIN_TEXT_DOMAIN );

	    printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr( $class ), $message );
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    private function _message_error() : void
    {
	    $class   = 'notice notice-error is-dismissible';
	    $message = __( 'Settings error.', EHD_PLUGIN_TEXT_DOMAIN );

	    printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr( $class ), $message );
    }
}
