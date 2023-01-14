<?php

namespace EHD\Walkers;

use stdClass;
use Walker_Nav_Menu;

\defined('ABSPATH') || die;

if (!class_exists('Horizontal_Nav_Walker')) {
    class Horizontal_Nav_Walker extends Walker_Nav_Menu
    {
        /**
         * @param string   $output
         * @param int      $depth
         * @param stdClass $args An object of wp_nav_menu() arguments.
         */
        function start_lvl(&$output, $depth = 0, $args = null)
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat($t, $depth);
            $output .= "{$n}{$indent}<ul class=\"vertical menu\">{$n}";
        }
    }
}
