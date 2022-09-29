<?php

namespace EHD\Plugins\ACF;

/**
 * ACF Plugins
 *
 * @author WEBHD
 */

\defined('\WPINC') || die;

use EHD\Helpers\Cast;

// If plugin - 'ACF' not exist then return.
if (!class_exists('\ACF')) {
    return;
}

if (!class_exists('ACF_Plugin')) {
    class ACF_Plugin
    {
        public function __construct()
        {
            add_filter('acf/fields/wysiwyg/toolbars', [&$this, 'wysiwyg_toolbars'], 11, 1);
            add_filter('wp_nav_menu_objects', [&$this, 'wp_nav_menu_objects'], 11, 2);

            // Hide the ACF Admin UI
            //add_filter( 'acf/settings/show_admin', '__return_false' );

            // ...
            add_filter('acf/format_value/type=textarea', 'wp_kses_post', 10, 1);
        }

        // -------------------------------------------------------------

        /**
         * @param $toolbars
         * @return mixed
         */
        public function wysiwyg_toolbars($toolbars)
        {
            // Add a new toolbar called "Minimal" - this toolbar has only 1 row of buttons
            $toolbars['Minimal'] = [];
            $toolbars['Minimal'][1] = [
                'formatselect',
                'bold',
                'italic',
                'underline',
                'link',
                'unlink',
                'forecolor',
                //'blockquote'
            ];

            // remove the 'Basic' toolbar completely (if you want)
            //unset( $toolbars['Full' ] );
            //unset( $toolbars['Basic' ] );

            // return $toolbars - IMPORTANT!
            return $toolbars;
        }

        // -------------------------------------------------------------

        /**
         * @param $items
         * @param $args
         * @return mixed
         */
        public function wp_nav_menu_objects($items, $args)
        {
            foreach ($items as &$item) {

                $title = $item->title;
                $fields = get_fields($item);

                if ($fields) {
                    $fields = Cast::toObject($fields);

                    //...
                    if ($fields->icon_svg ?? false) {
                        $item->classes[] = 'icon-menu';
                        $title = $fields->icon_svg . '<span>' . $item->title . '</span>';
                    } else if ($fields->icon_image ?? false) {
                        $item->classes[] = 'thumb-menu';
                        $title = '<img width="48px" height="48px" alt="' . esc_attr($item->title) . '" src="' . attachment_image_src($fields->icon_image) . '" loading="lazy" />' . '<span>' . $item->title . '</span>';
                    } else if ($fields->icon_glyph ?? false) {
                        $item->classes[] = 'glyph-menu';
                        $title = '<span data-glyph="' . $fields->icon_glyph . '">' . $item->title . '</span>';
                    }

                    //...
                    if ($fields->label_text ?? false) {

                        $_str = '';
                        if ($fields->label_color ?? false) {
                            $_str .= 'color:' . $fields->label_color . ';';
                        }
                        if ($fields->label_background ?? false) {
                            $_str .= 'background-color:' . $fields->label_background . ';';
                        }

                        $_style = $_str ? ' style="' . $_str . '"' : '';
                        $title .= '<sup' . $_style . '>' . $fields->label_text . '</sup>';
                    }

                    $item->title = $title;
                    unset($fields);
                }
            }

            return $items;
        }
    }
}