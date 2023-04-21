<?php

namespace EHD\Themes;

use EHD\Cores\Helper;
use EHD\Libs\Browser;
use PHPMailer\PHPMailer\Exception;

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
		    'dashicons-admin-settings',
		    80
	    );

	    // submenu page
	    add_submenu_page( 'ehd-settings', __( 'Advanced', EHD_PLUGIN_TEXT_DOMAIN ), __( 'Advanced', EHD_PLUGIN_TEXT_DOMAIN ), 'manage_options', 'customize.php' );
	    add_submenu_page( 'ehd-settings', __( 'Server Info', EHD_PLUGIN_TEXT_DOMAIN ), __( 'Server Info', EHD_PLUGIN_TEXT_DOMAIN ), 'manage_options', 'server-info', [ &$this, 'server_info' ] );
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
    public function options_page(): void
    {
	    if ( isset( $_POST['ehd_update_settings'] ) ) {

		    $nonce = $_REQUEST['_wpnonce'];
		    if ( ! wp_verify_nonce( $nonce, 'ehd_settings' ) ) {
			    wp_die( __( 'Error! Nonce Security Check Failed! please save the settings again.', EHD_PLUGIN_TEXT_DOMAIN ) );
		    }

		    /** Global */
		    $html_header      = $_POST['html_header'] ?? '';
		    $html_footer      = $_POST['html_footer'] ?? '';
		    $html_body_top    = $_POST['html_body_top'] ?? '';
		    $html_body_bottom = $_POST['html_body_bottom'] ?? '';

		    Helper::updateCustomPost( $html_header, 'html_header', 'text/html', true );
		    Helper::updateCustomPost( $html_footer, 'html_footer', 'text/html', true );
		    Helper::updateCustomPost( $html_body_top, 'html_body_top', 'text/html', true );
		    Helper::updateCustomPost( $html_body_bottom, 'html_body_bottom', 'text/html', true );

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

		    Helper::updateOption( 'smtp__options', $smtp_options, true );

		    /** Aspect Ratio */
		    $aspect_ratio_options = [];
		    $ar_post_type_list    = apply_filters( 'ehd_aspect_ratio_post_type', [] );
		    foreach ( $ar_post_type_list as $i => $ar ) {
			    $aspect_ratio_options[ 'ar-' . $ar . '-width' ]  = ! empty( $_POST[ $ar . '-width' ] ) ? sanitize_text_field( $_POST[ $ar . '-width' ] ) : 4;
			    $aspect_ratio_options[ 'ar-' . $ar . '-height' ] = ! empty( $_POST[ $ar . '-height' ] ) ? sanitize_text_field( $_POST[ $ar . '-height' ] ) : 3;
		    }

		    Helper::updateOption( 'aspect_ratio__options', $aspect_ratio_options, false );

		    /** Contact info */
		    $contact_info_options = [
			    'hotline' => ! empty( $_POST['contact_info_hotline'] ) ? sanitize_text_field( $_POST['contact_info_hotline'] ) : '',
			    'address' => ! empty( $_POST['contact_info_address'] ) ? sanitize_text_field( $_POST['contact_info_address'] ) : '',
			    'phones'  => ! empty( $_POST['contact_info_phones'] ) ? sanitize_text_field( $_POST['contact_info_phones'] ) : '',
			    'emails'  => ! empty( $_POST['contact_info_emails'] ) ? sanitize_text_field( $_POST['contact_info_emails'] ) : '',
		    ];

		    Helper::updateOption( 'contact_info__options', $contact_info_options, true );

		    $html_contact_info_others = $_POST['contact_info_others'] ?? '';
		    Helper::updateCustomPost( $html_contact_info_others, 'html_others', 'text/html', false );

		    /** Contact Button */
		    $contact_btn_options = [
			    'contact_title'        => ! empty( $_POST['contact_title'] ) ? sanitize_text_field( $_POST['contact_title'] ) : '',
			    'contact_url'          => ! empty( $_POST['contact_url'] ) ? sanitize_text_field( $_POST['contact_url'] ) : '',
			    'contact_window'       => ! empty( $_POST['contact_window'] ) ? sanitize_text_field( $_POST['contact_window'] ) : '',
			    'contact_waiting_time' => ! empty( $_POST['contact_waiting_time'] ) ? sanitize_text_field( $_POST['contact_waiting_time'] ) : '',
			    'contact_show_repeat'  => ! empty( $_POST['contact_show_repeat'] ) ? sanitize_text_field( $_POST['contact_show_repeat'] ) : '',
		    ];

		    Helper::updateOption( 'contact_btn__options', $contact_btn_options, true );

		    $html_contact_popup_content = $_POST['contact_popup_content'] ?? '';
		    Helper::updateCustomPost( $html_contact_popup_content, 'html_contact', 'text/html', false );

		    /** block editor */
		    $block_editor_options = [
			    'use_widgets_block_editor_off'           => ! empty( $_POST['use_widgets_block_editor_off'] ) ? sanitize_text_field( $_POST['use_widgets_block_editor_off'] ) : '',
			    'gutenberg_use_widgets_block_editor_off' => ! empty( $_POST['gutenberg_use_widgets_block_editor_off'] ) ? sanitize_text_field( $_POST['gutenberg_use_widgets_block_editor_off'] ) : '',
			    'use_block_editor_for_post_type_off'     => ! empty( $_POST['use_block_editor_for_post_type_off'] ) ? sanitize_text_field( $_POST['use_block_editor_for_post_type_off'] ) : '',
			    'block_style_off'                        => ! empty( $_POST['block_style_off'] ) ? sanitize_text_field( $_POST['block_style_off'] ) : '',
		    ];

		    Helper::updateOption( 'block_editor__options', $block_editor_options, true );

		    /** Custom CSS */
		    $html_custom_css = $_POST['html_custom_css'] ?? '';
		    Helper::updateCustomCssPost( $html_custom_css, 'ehd_css', false );

		    /** echo message success */
		    Helper::messageSuccess( 'Settings saved' );
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
                            <button type="submit" name="ehd_update_settings" class="button button-primary"><?php _e('Save Changes', EHD_PLUGIN_TEXT_DOMAIN); ?></button>
                        </div>
                        <ul class="ul-menu-list">
                            <li class="global-settings">
                                <a class="current" title="Global Settings" href="#global_settings"><?php _e('Global Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="smtp smtp-settings">
                                <a title="SMTP" href="#smtp_settings"><?php _e('SMTP', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="aspect-ratio aspect-ratio-settings">
                                <a title="Aspect ratio" href="#aspect_ratio_settings"><?php _e('Aspect Ratio', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="contact-info contact-info-settings">
                                <a title="Contact Info" href="#contact_info_settings"><?php _e('Contact Info', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="contact-button contact-button-settings">
                                <a title="Contact Button" href="#contact_button_settings"><?php _e('Contact Button', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="gutenberg gutenberg-settings">
                                <a title="Block Editor" href="#block_editor_settings"><?php _e('Block Editor', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
                            </li>
                            <li class="custom-css custom-css-settings">
                                <a title="Custom CSS" href="#custom_css_settings"><?php _e('Custom CSS', EHD_PLUGIN_TEXT_DOMAIN); ?></a>
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
                        <div id="contact_info_settings" class="group tabs-panel">
		                    <?php require __DIR__ . '/options/contact-info.php'; ?>
                        </div>
                        <div id="contact_button_settings" class="group tabs-panel">
		                    <?php require __DIR__ . '/options/contact-button.php'; ?>
                        </div>
                        <div id="block_editor_settings" class="group tabs-panel">
		                    <?php require __DIR__ . '/options/block-editor.php'; ?>
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
    public function server_info() : void {

	?>
    <div class="wrap">
        <div id="main">
            <h2 class="hide-text"></h2>
            <div class="server-info-body">
                <h2>Server info</h2>
                <p class="desc">System configuration information</p>
                <div class="server-info-inner code">
                    <ul>
	                    <li><?php echo sprintf( '<span>Platform:</span> %s', php_uname() ); ?></li>
	                    <?php if ( $server_software = $_SERVER['SERVER_SOFTWARE'] ?? null ) : ?>
	                    <li><?php echo sprintf( '<span>SERVER:</span> %s', $server_software ); ?></li>
	                    <?php endif; ?>
	                    <li><?php echo sprintf( '<span>PHP version:</span> %s', PHP_VERSION ); ?></li>
	                    <li><?php echo sprintf( '<span>WordPress version:</span> %s', get_bloginfo( 'version' ) ); ?></li>
	                    <li><?php echo sprintf( '<span>WordPress multisite:</span> %s', ( is_multisite() ? 'Yes' : 'No' ) ); ?></li>
	                    <?php
	                    $openssl_status = 'Available';
	                    $openssl_text   = '';
	                    if ( ! extension_loaded( 'openssl' ) && ! defined( 'OPENSSL_ALGO_SHA1' ) ) {
		                    $openssl_status = 'Not available';
		                    $openssl_text   = ' (openssl extension is required in order to use any kind of encryption like TLS or SSL)';
	                    }
	                    ?>
                        <li><?php echo sprintf('<span>openssl:</span> %s%s', $openssl_status, $openssl_text); ?></li>
                        <li><?php echo sprintf('<span>allow_url_fopen:</span> %s', (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled')); ?></li>
                        <?php
                        $stream_socket_client_status = 'Not Available';
                        $fsockopen_status            = 'Not Available';
                        $socket_enabled              = false;

                        if ( function_exists( 'stream_socket_client' ) ) {
	                        $stream_socket_client_status = 'Available';
	                        $socket_enabled              = true;
                        }
                        if ( function_exists( 'fsockopen' ) ) {
	                        $fsockopen_status = 'Available';
	                        $socket_enabled   = true;
                        }

                        $socket_text = '';
                        if ( ! $socket_enabled ) {
	                        $socket_text = ' (In order to make a SMTP connection your server needs to have either stream_socket_client or fsockopen)';
                        }
                        ?>
                        <li><?php echo sprintf('<span>stream_socket_client:</span> %s', $stream_socket_client_status); ?></li>
                        <li><?php echo sprintf('<span>fsockopen:</span> %s%s', $fsockopen_status, $socket_text); ?></li>
	                    <?php if ( $agent = $_SERVER['HTTP_USER_AGENT'] ?? null ) : ?>
	                    <li><?php echo sprintf('<span>User agent:</span> %s', $agent); ?></li>
	                    <?php endif; ?>
                        <li><?php echo sprintf('<span>IP:</span> %s', Helper::getClientIp() ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php }

	/** ---------------------------------------- */

	/**
	 * @param $phpmailer
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setup_phpmailer_init( $phpmailer ): void
    {
        Helper::PHPMailerInit( $phpmailer, 'smtp__options' );
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
     * @return bool
     */
    private function _smtp__is_configured() : bool
    {
	    $smtp_options    = Helper::getOption( 'smtp__options' );
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
}
