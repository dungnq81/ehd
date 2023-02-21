<?php
/**
 * hooks functions
 * @author WEBHD
 */

use EHD\Cores\Helper;
use EHD\Themes\Css;

\defined('ABSPATH') || die;

// Admin footer text
add_filter('admin_footer_text', function () {
    printf('<span id="footer-thankyou">%1$s <a href="https://webhd.vn" target="_blank">%2$s</a>.&nbsp;</span>', __('Powered by', EHD_TEXT_DOMAIN), EHD_AUTHOR);
});
