<?php

namespace EHD\Ins;

/**
 * litespeed Plugins
 * @author WEBHD
 */
\defined('\WPINC') || die;

//LSCWP_BASENAME='litespeed-cache/litespeed-cache.php'
if (!defined('LSCWP_BASENAME')) {
    return;
}

if (!class_exists('LiteSpeed_Ins')) {
    class LiteSpeed_Ins
    {
        public function __construct() {}
    }
}