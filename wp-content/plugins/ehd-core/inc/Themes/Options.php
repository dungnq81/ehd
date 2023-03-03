<?php

namespace EHD\Themes;

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
        add_action('admin_notices', [&$this, 'options_admin_notice']);
        add_action('admin_menu', [&$this, 'options_admin_menu']);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_admin_notice() : void
    {
        // SMTP
        if (!self::_smtp__is_configured()) {
            $class = 'notice notice-error';
            $message = __('You need to configure your SMTP credentials in the settings to send emails.', EHD_PLUGIN_TEXT_DOMAIN);

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        }

        // ...
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_admin_menu() : void
    {
        // menu page
        add_menu_page(
            __('eHD Settings', EHD_PLUGIN_TEXT_DOMAIN),
            __('eHD Settings', EHD_PLUGIN_TEXT_DOMAIN),
            'manage_options',
            'ehd-settings',
            [&$this, 'options_page'],
            'dashicons-admin-generic',
            80
        );

        // submenu page
        add_submenu_page('ehd-settings', __('Advanced', EHD_PLUGIN_TEXT_DOMAIN), __('Advanced', EHD_PLUGIN_TEXT_DOMAIN), 'manage_options', 'customize.php');
        add_submenu_page('ehd-settings', __('Server Info', EHD_PLUGIN_TEXT_DOMAIN), __('Server Info', EHD_PLUGIN_TEXT_DOMAIN), 'manage_options', 'server-info', [&$this, 'server_info']);
        add_submenu_page('ehd-settings', __('Help & Guides', EHD_PLUGIN_TEXT_DOMAIN), __('Help & Guides', EHD_PLUGIN_TEXT_DOMAIN), 'manage_options', 'panel-support', [&$this, 'panel_support']);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_page() : void
    {
        if (isset($_POST['ehd_update_settings'])) {

            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'ehd_settings')) {
                wp_die(__('Error! Nonce Security Check Failed! please save the settings again.', EHD_PLUGIN_TEXT_DOMAIN));
            }

            /** SMTP */
            $smtp_host = !empty($_POST['smtp_host']) ? sanitize_text_field($_POST['smtp_host']) : '';
            $smtp_auth = !empty($_POST['smtp_auth']) ? sanitize_text_field($_POST['smtp_auth']) : '';
            $smtp_username = !empty($_POST['smtp_username']) ? sanitize_text_field($_POST['smtp_username']) : '';

            if (!empty($_POST['smtp_password'])) {
                $smtp_password = sanitize_text_field($_POST['smtp_password']);
                $smtp_password = wp_unslash($smtp_password); // This removes slash (automatically added by WordPress) from the password when apostrophe is present
                $smtp_password = base64_encode($smtp_password);
            }

            $smtp_encryption = !empty($_POST['smtp_encryption']) ? sanitize_text_field($_POST['smtp_encryption']) : '';
            $smtp_port = !empty($_POST['smtp_port']) ? sanitize_text_field($_POST['smtp_port']) : '';
            $smtp_from_email = !empty($_POST['smtp_from_email']) ? sanitize_email($_POST['smtp_from_email']) : '';
            $smtp_from_name = !empty($_POST['smtp_from_name']) ? sanitize_text_field($_POST['smtp_from_name']) : '';
            $smtp_disable_ssl_verification = !empty($_POST['smtp_disable_ssl_verification']) ? sanitize_text_field($_POST['smtp_disable_ssl_verification']) : '';

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

            if (!empty($smtp_password)) {
                $smtp_options['smtp_password'] = $smtp_password;
            }

            self::_smtp__update_options($smtp_options);

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
                            <li class="socials social-settings">
                                <a title="Socials" href="#social_settings"><?php _e('Socials', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="news news-settings">
                                <a title="News" href="#news_settings"><?php _e('News', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="products product-settings">
                                <a title="Products" href="#product_settings"><?php _e('Products', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
                            </li>
                            <li class="blocks block-settings">
                                <a title="Blocks" href="#block_settings"><?php _e('Blocks Editor', EHD_PLUGIN_TEXT_DOMAIN) ?></a>
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
                        <div id="social_settings" class="group tabs-panel">
                            <?php require __DIR__ . '/options/social.php'; ?>
                        </div>
                        <div id="news_settings" class="group tabs-panel">
                            <?php require __DIR__ . '/options/news.php'; ?>
                        </div>
                        <div id="product_settings" class="group tabs-panel">
                            <?php require __DIR__ . '/options/product.php'; ?>
                        </div>
                        <div id="block_settings" class="group tabs-panel">
                            <?php require __DIR__ . '/options/block.php'; ?>
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

        $options = get_option('smtp__options');

        if (is_array($options)) {
            $current_options = array_merge($empty_options, $options);
            $updated_options = array_merge($current_options, $new_smtp_options);
        } else {
            $updated_options = array_merge($empty_options, $new_smtp_options);
        }

        update_option('smtp__options', $updated_options);
    }

    /** ---------------------------------------- */

    /**
     * @return bool
     */
    private function _smtp__is_configured() : bool
    {
        $smtp_options = get_option('smtp__options');
        $smtp_configured = true;

        if (isset($smtp_options['smtp_auth']) && $smtp_options['smtp_auth'] == "true") {
            if (empty($smtp_options['smtp_username']) ||
                empty($smtp_options['smtp_password'])
            ) {
                $smtp_configured = false;
            }
        }

        if (empty($smtp_options['smtp_host']) ||
            empty($smtp_options['smtp_auth']) ||
            empty($smtp_options['smtp_encryption']) ||
            empty($smtp_options['smtp_port']) ||
            empty($smtp_options['smtp_from_email']) ||
            empty($smtp_options['smtp_from_name'])
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
        $class = 'notice notice-success is-dismissible';
        $message = __('Settings saved.', EHD_PLUGIN_TEXT_DOMAIN);

        printf('<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr($class), $message);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    private function _message_error() : void
    {
        $class = 'notice notice-error is-dismissible';
        $message = __('Settings error.', EHD_PLUGIN_TEXT_DOMAIN);

        printf('<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr($class), $message);
    }
}
