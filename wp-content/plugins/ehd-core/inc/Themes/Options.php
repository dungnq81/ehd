<?php

namespace EHD\Themes;

use EHD\Cores\Helper;

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
    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_admin_notice() : void {}

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_admin_menu() : void
    {
        add_menu_page(
            __('eHD Settings', EHD_PLUGIN_TEXT_DOMAIN),
            __('eHD Settings', EHD_PLUGIN_TEXT_DOMAIN),
            'manage_options',
            'ehd-settings',
            [&$this, 'options_page'],
            'dashicons-admin-generic',
            80
        );

        //...
        add_submenu_page('ehd-settings', 'Advanced', 'Advanced', 'manage_options', 'customize.php');
        add_submenu_page('ehd-settings', 'Server Info', 'Server Info', 'manage_options', 'server-info', [&$this, 'server_info']);
        add_submenu_page('ehd-settings', 'Help & Guides', 'Help & Guides', 'manage_options', 'panel-support', [&$this, 'panel_support']);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_page() : void
    {
        ?>
        <div class="wrap" id="ehd_container">
            <div id="message_success" class="notice notice-success is-dismissible">
                <p><strong>Settings saved.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
            <div id="message_error" class="notice notice-error is-dismissible">
                <p><strong>Settings error.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
            <form id="ehd_form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('ehd-settings'); ?>
                <div id="main" class="filter-tabs clearfix">
                    <div id="ehd_nav" class="tabs-nav">
                        <div class="logo-title">
                            <h3>
                                eHD Options
                                <span>Version: <?php echo EHD_PLUGIN_VERSION?></span>
                            </h3>
                        </div>
                        <div class="save-bar">
                            <button type="submit" name="ehd_update_settings" class="button button-primary">Save Changes</button>
                        </div>
                        <ul class="ul-menu-list">
                            <li class="global-settings">
                                <a class="current" title="Global Settings" href="#global_settings">Global Settings</a>
                            </li>
                            <li class="smtp smtp-settings">
                                <a title="SMTP" href="#smtp_settings">SMTP</a>
                            </li>
                            <li class="socials social-settings">
                                <a title="Socials" href="#social_settings">Socials</a>
                            </li>
                            <li class="news news-settings">
                                <a title="News" href="#news_settings">News</a>
                            </li>
                            <li class="products product-settings">
                                <a title="Products" href="#product_settings">Products</a>
                            </li>
                            <li class="blocks block-settings">
                                <a title="Blocks" href="#block_settings">Blocks Editor</a>
                            </li>
                        </ul>
                    </div>
                    <div id="ehd_content" class="tabs-content">
                        <h2 class="hidden-text">eHD Options</h2>
                        <div id="global_settings" class="group tabs-panel show">
                            <h2>Global Settings</h2>
                        </div>
                        <div id="smtp_settings" class="group tabs-panel">
                            <h2>SMTP Settings</h2>
                            <div class="section section-text" id="section_smtp_host">
                                <label class="heading" for="smtp_host">SMTP Host</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="text" id="smtp_host" name="smtp-host">
                                    </div>
                                    <div class="explain">The SMTP server which will be used to send email. For example: smtp.gmail.com</div>
                                </div>
                            </div>
                            <div class="section section-select" id="section_smtp_auth">
                                <label class="heading" for="smtp_auth">SMTP Authentication</label>
                                <div class="option">
                                    <div class="controls">
                                        <div class="select_wrapper">
                                            <select class="ehd-control ehd-select" name="smtp-auth" id="smtp_auth">
                                                <option value="true">True</option>
                                                <option value="false">False</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="explain">Whether to use SMTP Authentication when sending an email (recommended: True).</div>
                                </div>
                            </div>
                            <div class="section section-text" id="section_smtp_username">
                                <label class="heading" for="smtp_username">SMTP Username</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="text" id="smtp_username" name="smtp-username">
                                    </div>
                                    <div class="explain">Your SMTP Username. For example: abc@gmail.com</div>
                                </div>
                            </div>
                            <div class="section section-password" id="section_smtp_password">
                                <label class="heading" for="smtp_password">SMTP Password</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="password" id="smtp_password" name="smtp-password">
                                    </div>
                                    <div class="explain">Your SMTP Password (The saved password is not shown for security reasons. If you do not want to update the saved password, you can leave this field empty when updating other options).</div>
                                </div>
                            </div>
                            <div class="section section-select" id="section_smtp_encryption">
                                <label class="heading" for="smtp_encryption">Type of Encryption</label>
                                <div class="option">
                                    <div class="controls">
                                        <div class="select_wrapper">
                                            <select class="ehd-control ehd-select" name="smtp-encryption" id="smtp_encryption">
                                                <option value="tls">TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">No Encryption</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="explain">The encryption which will be used when sending an email (recommended: TLS).</div>
                                </div>
                            </div>
                            <div class="section section-text" id="section_smtp_port">
                                <label class="heading" for="smtp_port">SMTP Port</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="text" id="smtp_port" name="smtp-port">
                                    </div>
                                    <div class="explain">The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.</div>
                                </div>
                            </div>
                            <div class="section section-text" id="section_smtp_from_email">
                                <label class="heading" for="smtp_from_email">From Email Address</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="text" id="smtp_from_email" name="smtp-from-email">
                                    </div>
                                    <div class="explain">The email address which will be used as the From Address if it is not supplied to the mail function.</div>
                                </div>
                            </div>
                            <div class="section section-text" id="section_smtp_from_name">
                                <label class="heading" for="smtp_from_name">From Name</label>
                                <div class="option">
                                    <div class="controls">
                                        <input class="ehd-input ehd-control" type="text" id="smtp_from_name" name="smtp-from-name">
                                    </div>
                                    <div class="explain">The name which will be used as the From Name if it is not supplied to the mail function.</div>
                                </div>
                            </div>
                            <div class="section section-checkbox" id="section_smtp_disable_ssl_verification">
                                <label class="heading" for="smtp_disable_ssl_verification">Disable SSL Certificate Verification</label>
                                <div class="option">
                                    <div class="controls">
                                        <input type="hidden" name="smtp-disable-ssl-verification" value="0">
                                        <input type="checkbox" class="ehd-checkbox ehd-control" name="smtp-disable-ssl-verification" id="smtp_disable_ssl_verification" value="1">
                                    </div>
                                    <div class="explain">You should get your host to fix the SSL configurations instead of bypassing it.</div>
                                </div>
                            </div>
                        </div>
                        <div id="social_settings" class="group tabs-panel">
                            <h2>Socials Settings</h2>
                        </div>
                        <div id="news_settings" class="group tabs-panel">
                            <h2>News Settings</h2>
                        </div>
                        <div id="product_settings" class="group tabs-panel">
                            <h2>Products Settings</h2>
                        </div>
                        <div id="block_settings" class="group tabs-panel">
                            <h2>Blocks Editor Settings</h2>
                        </div>
                        <div class="save-bar">
                            <button type="submit" name="ehd_update_settings" class="button button-primary">Save Changes</button>
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
}
