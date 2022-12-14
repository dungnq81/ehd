<?php

namespace EHD\Sites\Plugins;

use EHD\Plugins\Core\Helper;

/**
 * RankMath Plugins
 * @author   WEBHD
 */

\defined('\WPINC') || die;

// If plugin - 'RankMath' not exist then return.
if (!defined('WP_ROCKET_VERSION')) {
    return;
}

// https://github.com/wp-media/wp-rocket-helpers/tree/master/htaccess/wp-rocket-htaccess-remove-all/

if (!class_exists('WpRocket_Plugin')) {
    class WpRocket_Plugin
    {
        public function __construct()
        {
            // server does not support using .htaccess
            if (!Helper::htAccess()) {

                // Remove rewrite rules block of WP Rocket from .htaccess.
                add_filter('rocket_htaccess_charset', '__return_false');
                add_filter('rocket_htaccess_etag', '__return_false');
                add_filter('rocket_htaccess_web_fonts_access', '__return_false');
                add_filter('rocket_htaccess_files_match', '__return_false');
                add_filter('rocket_htaccess_mod_expires', '__return_false');
                add_filter('rocket_htaccess_mod_deflate', '__return_false');
                add_filter('rocket_htaccess_mod_rewrite', '__return_false');
            }
        }
    }
}
