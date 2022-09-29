<?php

namespace EHD\Plugins;

/**
 * litespeed Plugins
 * @author WEBHD
 */
\defined('\WPINC') || die;

//LSCWP_BASENAME='litespeed-cache/litespeed-cache.php'
if (!defined('LSCWP_BASENAME')) {
    return;
}

if (!class_exists('LiteSpeed_Plugin')) {
    class LiteSpeed_Plugin
    {
        public function __construct() {}
    }
}