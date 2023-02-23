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
            __('eHD Options', EHD_PLUGIN_TEXT_DOMAIN),
            __('eHD Options', EHD_PLUGIN_TEXT_DOMAIN),
            'manage_options',
            'ehd-settings',
            [&$this, 'options_page'],
            'dashicons-admin-generic',
            99
        );
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function options_page() : void
    {
        ?>
        <div class="wrap" id="ehd_container">
            <div id="message_updated" class="updated fade"><p><strong>Settings Updated!</strong></p></div>
            <div id="message_error" class="error fade"><p><strong>Settings Error!</strong></p></div>
            <form id="ehd_form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('ehd-settings'); ?>
                <div id="main">
                    <div id="ehd_nav">
                        <div class="logo-title">
                            <h3>
                                eHD Options
                                <span>Version: <?php echo EHD_PLUGIN_VERSION?></span>
                            </h3>
                        </div>
                        <div class="save_bar">
                            <button type="submit" name="ehd_update_settings" class="button button-primary">Save Changes</button>
                        </div>
                        <ul>
                            <li class="global-settings current">
                                <a title="Global Settings" href="#global-settings">Global Settings</a>
                            </li>
                            <li class="smtp smtp-settings">
                                <a title="SMTP" href="#smtp-settings">SMTP</a>
                            </li>
                            <li class="social social-settings">
                                <a title="Socials" href="#social-settings">Socials</a>
                            </li>
                            <li class="news news-settings">
                                <a title="News" href="#news-settings">News</a>
                            </li>
                            <li class="products products-settings">
                                <a title="Products" href="#products-settings">Products</a>
                            </li>
                            <li class="block-editor block-editor-settings">
                                <a title="Blocks Editor" href="#block-editor-settings">Blocks Editor</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
