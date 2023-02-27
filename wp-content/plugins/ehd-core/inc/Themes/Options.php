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
                                <a class="current" title="Global Settings" href="#global-settings">Global Settings</a>
                            </li>
                            <li class="smtp smtp-settings">
                                <a title="SMTP" href="#smtp-settings">SMTP</a>
                            </li>
                            <li class="socials social-settings">
                                <a title="Socials" href="#social-settings">Socials</a>
                            </li>
                            <li class="news news-settings">
                                <a title="News" href="#news-settings">News</a>
                            </li>
                            <li class="products product-settings">
                                <a title="Products" href="#product-settings">Products</a>
                            </li>
                            <li class="blocks block-settings">
                                <a title="Blocks" href="#block-settings">Blocks Editor</a>
                            </li>
                        </ul>
                    </div>
                    <div id="ehd_content" class="tabs-content">
                        <h2 class="hidden-text"></h2>
                        <div id="global-settings" class="group tabs-panel show">
                            <h2>Global Settings</h2>
                        </div>
                        <div id="smtp-settings" class="group tabs-panel">
                            <h2>SMTP Settings</h2>
                        </div>
                        <div id="social-settings" class="group tabs-panel">
                            <h2>Socials Settings</h2>
                        </div>
                        <div id="news-settings" class="group tabs-panel">
                            <h2>News Settings</h2>
                        </div>
                        <div id="product-settings" class="group tabs-panel">
                            <h2>Products Settings</h2>
                        </div>
                        <div id="block-settings" class="group tabs-panel">
                            <h2>Blocks Editor Settings</h2>
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
    public function panel_support() : void
    {

    }
}
