<?php

namespace EHD\Themes;

use EHD\Plugins\Core\Helper;

\defined('\WPINC') || die;

class Shortcode
{
    // ------------------------------------------------------

    public function __construct()
    {
        add_shortcode('mobile_menu', [&$this, 'mobile_menu_shortcode'], 11);
        add_shortcode('main_menu', [&$this, 'main_menu_shortcode'], 11);
        add_shortcode('term_menu', [&$this, 'term_menu_shortcode'], 11);
    }

    // ------------------------------------------------------

    /**
     * @param array $atts
     *
     * @return bool|string
     */
    public function term_menu_shortcode(array $atts = [])
    {
        // override default attributes
        $a = shortcode_atts(
            [
                'location'   => 'policy-nav',
                'menu_class' => 'terms-menu',
            ],
            array_change_key_case((array) $atts, CASE_LOWER)
        );

        return Func::termNav($a['location'], $a['menu_class']);
    }

    // ------------------------------------------------------

    /**
     * @param array $atts
     *
     * @return bool|string
     */
    public function main_menu_shortcode(array $atts = [])
    {
        // override default attributes
        $a = shortcode_atts(
            [
                'location'   => 'main-nav',
                'menu_class' => 'desktop-menu',
                'menu_id'    => 'main-menu',
            ],
            array_change_key_case((array) $atts, CASE_LOWER)
        );

        return Func::mainNav($a['location'], $a['menu_class'], $a['menu_id']);
    }

    // ------------------------------------------------------

    /**
     * @param array $atts
     *
     * @return bool|string
     */
    public function mobile_menu_shortcode(array $atts = [])
    {
        // override default attributes
        $a = shortcode_atts(
            [
                'location'   => 'mobile-nav',
                'menu_class' => 'mobile-menu',
                'menu_id'    => 'mobile-menu',
            ],
            array_change_key_case((array) $atts, CASE_LOWER)
        );

        return Func::mobileNav($a['location'], $a['menu_class'], $a['menu_id']);
    }
}
