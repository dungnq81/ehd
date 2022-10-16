<?php

namespace EHD\Themes;

\defined('\WPINC') || die;

class Shortcode
{
    /**
     * @return void
     */
    public static function init() : void
    {
        $shortcodes = [

        ];

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }
    }
}
