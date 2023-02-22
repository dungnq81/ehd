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
                            <li class="globalsettings current">
                                <a title="Global Settings" href="#option-globalsettings">Global Settings</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
