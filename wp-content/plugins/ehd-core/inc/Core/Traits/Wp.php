<?php

namespace EHD\Plugins\Core\Traits;

use EHD\Plugins\Walkers\Horizontal_Nav_Walker;
use EHD\Plugins\Walkers\Vertical_Nav_Walker;
use WP_Error;
use WP_Query;
use WP_Term;

\defined('ABSPATH') || die;

trait Wp
{
    use Arr;
    use Base;
    use Str;
    use Url;
    use Cast;

    // -------------------------------------------------------------

    /**
     * @param array $args
     *
     * @return bool|false|string|void
     */
    public static function verticalNav(array $args = [])
    {
        $args = wp_parse_args(
            (array) $args,
            [
                'container'      => false, // Remove nav container
                'menu_id'        => '',
                'menu_class'     => 'menu vertical',
                'theme_location' => '',
                'depth'          => 4,
                'fallback_cb'    => false,
                'walker'         => new Vertical_Nav_Walker(),
                'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-accordion-menu data-submenu-toggle="true">%3$s</ul>',
                'echo'           => false,
            ]
        );

        if (true === $args['echo']) {
            echo wp_nav_menu($args);
        } else {
            return wp_nav_menu($args);
        }
    }

    // -------------------------------------------------------------

    /**
     * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
     *
     * @param array $args
     *
     * @return bool|false|string|void
     */
    public static function horizontalNav(array $args = [])
    {
        $args = wp_parse_args(
            (array) $args,
            [
                'container'      => false,
                'menu_id'        => '',
                'menu_class'     => 'dropdown menu horizontal horizontal-menu',
                'theme_location' => '',
                'depth'          => 4,
                'fallback_cb'    => false,
                'walker'         => new Horizontal_Nav_Walker(),
                'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
                'echo'           => false,
            ]
        );

        if (true === $args['echo']) {
            echo wp_nav_menu($args);
        } else {
            return wp_nav_menu($args);
        }
    }

    // -------------------------------------------------------------

    /**
     * Call a shortcode function by tag name.
     *
     * @param string     $tag     The shortcode whose function to call.
     * @param array      $atts    The attributes to pass to the shortcode function. Optional.
     * @param array|null $content The shortcode's content. Default is null (none).
     *
     * @return false|mixed False on failure, the result of the shortcode on success.
     */
    public static function doShortcode($tag, array $atts = [], $content = null)
    {
        global $shortcode_tags;
        if (!isset($shortcode_tags[$tag])) {
            return false;
        }

        return call_user_func($shortcode_tags[$tag], $atts, $content, $tag);
    }

    // -------------------------------------------------------------

    /**
     * @param $image_url
     * @return false|mixed
     */
    public static function getImageId($image_url)
    {
        global $wpdb;

        $sql = 'SELECT ID FROM ' . $wpdb->prefix . "posts WHERE post_type LIKE 'attachment' AND guid LIKE '%" . esc_sql($image_url) . "';";
        $attachment = $wpdb->get_col($sql);
        $img_id = reset($attachment);
        if (!$img_id) {
            if (str_contains($image_url, '-scaled.')) {
                $image_url = str_replace('-scaled.', '.', $image_url);
                $img_id = self::getImageId($image_url);
            }
        }

        return $img_id;
    }

    // -------------------------------------------------------------

    /**
     * Using `rawurlencode` on any variable used as part of the query string, either by using
     * `add_query_arg()` or directly by string concatenation, will prevent parameter hijacking.
     *
     * @param $url
     * @param $args
     * @return string
     */
    public static function addQueryArg($url, $args)
    {
        $args = array_map('rawurlencode', $args);
        return add_query_arg($args, $url);
    }

    // -------------------------------------------------------------

    /**
     * @param      $attachment_id
     * @param bool $return_object
     * @return array|object|null
     */
    public static function getAttachment($attachment_id, bool $return_object = true)
    {
        $attachment = get_post($attachment_id);
        if (!$attachment) {
            return null;
        }

        $_return = [
            'alt'         => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
            'caption'     => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'href'        => get_permalink($attachment->ID),
            'src'         => $attachment->guid,
            'title'       => $attachment->post_title,
        ];

        if (true === $return_object) {
            $_return = self::toObject($_return);
        }

        return $_return;
    }

    // -------------------------------------------------------------

    /**
     * @param array  $arr_parsed [ $handle: $value ] -- $value[ 'defer', 'delay' ]
     * @param string $tag
     * @param string $handle
     * @param string $src
     *
     * @return array|string|string[]|null
     */
    public static function lazyScriptTag(array $arr_parsed, string $tag, string $handle, string $src)
    {
        foreach ($arr_parsed as $str => $value) {
            if (str_contains($handle, $str)) {
                if ('defer' === $value) {
                    $tag = preg_replace('/\s+defer\s+/', ' ', $tag);
                    return preg_replace('/\s+src=/', ' defer src=', $tag);
                } elseif ('delay' === $value) {
                    $tag = preg_replace('/\s+defer\s+/', ' ', $tag);
                    return preg_replace('/\s+src=/', ' defer data-type=\'lazy\' data-src=', $tag);
                }
            }
        }

        return $tag;
    }

    // -------------------------------------------------------------

    /**
     * @param array  $arr_styles [ $handle ]
     * @param string $html
     * @param string $handle
     *
     * @return array|string|string[]|null
     */
    public static function lazyStyleTag(array $arr_styles, string $html, string $handle)
    {
        foreach ($arr_styles as $style) {
            if (str_contains($handle, $style)) {
                return preg_replace('/media=\'all\'/', 'media=\'print\' onload=\'this.media="all"\'', $html);
            }
        }

        return $html;
    }

    // -------------------------------------------------------------

    /**
     * @param $mod_name
     * @param $default
     *
     * @return mixed|string|string[]
     */
    public static function getThemeMod($mod_name, $default = false)
    {
        static $_is_loaded;
        if (empty($_is_loaded)) {

            // references cannot be directly assigned to static variables, so we use an array
            $_is_loaded[0] = [];
        }

        if ($mod_name) {
            if (!isset($_is_loaded[0][strtolower($mod_name)])) {
                $_mod = get_theme_mod($mod_name, $default);
                if (is_ssl()) {
                    $_is_loaded[0][strtolower($mod_name)] = str_replace(['http://'], 'https://', $_mod);
                } else {
                    $_is_loaded[0][strtolower($mod_name)] = str_replace(['https://'], 'http://', $_mod);
                }
            }

            return $_is_loaded[0][strtolower($mod_name)];
        }

        return $default;
    }

    // -------------------------------------------------------------

    /**
     * @param        $term_id
     * @param string $taxonomy
     * @return array|false|WP_Error|WP_Term|null
     */
    public static function getTerm($term_id, string $taxonomy = 'category')
    {
        $term = false;
        if (is_numeric($term_id)) {
            $term_id = intval($term_id);
            $term = get_term($term_id);
        } else {
            $term = get_term_by('slug', $term_id, $taxonomy);
            if (!$term) {
                $term = get_term_by('name', $term_id, $taxonomy);
            }
        }
        return $term;
    }

    // -------------------------------------------------------------

    /**
     * @param object      $term
     * @param string      $post_type
     * @param bool        $include_children
     *
     * @param int         $posts_per_page
     * @param array       $orderby
     * @param bool|string $strtotime_recent - strtotime( 'last week' );
     * @return bool|WP_Query
     */
    public static function queryByTerm($term, string $post_type = 'any', bool $include_children = false, int $posts_per_page = 0, $orderby = [], bool|string $strtotime_recent = false)
    {
        if (!$term) {
            return false;
        }

        $_args = [
            'post_type' => $post_type ?: 'post',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'post_status'            => 'publish',
            'posts_per_page'         => $posts_per_page ?: 10,
            'tax_query'              => ['relation' => 'AND'],
        ];

        $include_children = (bool) $include_children;
        if ($include_children) {
            $_args['tax_query']['relation'] = 'IN';
        }

        //...
        $term = self::toObject($term);
        if (isset($term->taxonomy) && isset($term->term_id)) {
            $_args['tax_query'][] = [
                'taxonomy'         => $term->taxonomy,
                'terms'            => [$term->term_id],
                'include_children' => $include_children,
            ];
        }

        if (is_array($orderby)) {
            $orderby = self::removeEmptyValues($orderby);
        } else {
            $orderby = ['date' => 'DESC'];
        }

        $_args['orderby'] = $orderby;

        // ...
        if ($strtotime_recent) {

            // constrain to just posts in $strtotime_recent
            $recent = strtotime($strtotime_recent);
            if ($recent) {
                $_args['date_query'] = [
                    'after' => [
                        'year'  => date('Y', $recent),
                        'month' => date('n', $recent),
                        'day'   => date('j', $recent),
                    ],
                ];
            }
        }

        // woocommerce_hide_out_of_stock_items
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items') && class_exists('\WooCommerce')) {

            $product_visibility_term_ids = wc_get_product_visibility_term_ids();

            $_args['tax_query'][] = [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => $product_visibility_term_ids['outofstock'],
                    'operator' => 'NOT IN',
                ],
            ]; // WPCS: slow query ok.
        }

        $_query = new WP_Query($_args);
        if (!$_query->have_posts()) {
            return false;
        }

        return $_query;
    }

    // -------------------------------------------------------------

    /**
     * @param array       $term_ids
     * @param string      $taxonomy
     * @param string      $post_type
     * @param bool        $include_children
     * @param int         $posts_per_page
     * @param bool|string $strtotime_str
     * @return bool|WP_Query
     */
    public static function queryByTerms($term_ids = [], string $taxonomy = 'category', string $post_type = 'any', bool $include_children = false, int $posts_per_page = 10, bool|string $strtotime_str = false)
    {
        if (!$term_ids) {
            return false;
        }

        $_args = [
            'post_type' => $post_type ?: 'post',
            'post_status'         => 'publish',
            'orderby'             => ['date' => 'DESC'],
            'tax_query'           => ['relation' => 'AND'],
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
            'posts_per_page'      => $posts_per_page ?: 10,

            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];

        if (!is_array($term_ids)) {
            $term_ids = self::toArray($term_ids);
        }

        if (!$taxonomy) {
            $taxonomy = 'category';
        }

        $include_children = (bool) $include_children;
        if ($include_children) {
            $_args['tax_query']['relation'] = 'IN';
        }

        $_args['tax_query'][] = [
            'taxonomy'         => $taxonomy,
            'terms'            => $term_ids,
            'include_children' => (bool) $include_children,
        ];

        // ...
        if ($strtotime_str) {

            // constrain to just posts in $strtotime_str
            $recent = strtotime($strtotime_str);
            if ($recent) {
                $_args['date_query'] = [
                    'after' => [
                        'year'  => date('Y', $recent),
                        'month' => date('n', $recent),
                        'day'   => date('j', $recent),
                    ],
                ];
            }
        }

        // woocommerce_hide_out_of_stock_items
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items') && class_exists('\WooCommerce')) {

            $product_visibility_term_ids = wc_get_product_visibility_term_ids();

            $_args['tax_query'][] = [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => $product_visibility_term_ids['outofstock'],
                    'operator' => 'NOT IN',
                ],
            ]; // WPCS: slow query ok.
        }

        // query
        $r = new WP_Query($_args);
        if (!$r->have_posts())
            return false;

        return $r;
    }

    // -------------------------------------------------------------

    /**
     * @param bool $echo
     *
     * @return string|void
     */
    public static function siteTitleOrLogo(bool $echo = true)
    {
        if (function_exists('the_custom_logo') && has_custom_logo()) {
            $logo = get_custom_logo();
            $html = (is_home() || is_front_page()) ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
        } else {
            $tag = is_home() ? 'h1' : 'div';
            $html = '<' . esc_attr($tag) . ' class="site-title"><a title href="' . self::home() . '" rel="home">' . esc_html(get_bloginfo('name')) . '</a></' . esc_attr($tag) . '>';
            if ('' !== get_bloginfo('description')) {
                $html .= '<p class="site-description">' . esc_html(get_bloginfo('description', 'display')) . '</p>';
            }
        }

        if (!$echo) {
            return $html;
        }

        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    // -------------------------------------------------------------

    /**
     * @param string      $theme - default|light|dark
     * @param string|null $class
     * @return string
     */
    public static function siteLogo(string $theme = 'default', ?string $class = '')
    {
        $html = '';
        $custom_logo_id = null;

        if ('default' !== $theme && $theme_logo = self::getThemeMod($theme . '_logo')) {
            $custom_logo_id = attachment_url_to_postid($theme_logo);
        } else if (has_custom_logo()) {
            $custom_logo_id = self::getThemeMod('custom_logo');
        }

        // We have a logo. Logo is go.
        if ($custom_logo_id) {
            $custom_logo_attr = [
                'class'   => $theme . '-logo',
                'loading' => 'lazy',
            ];

            /**
             * If the logo alt attribute is empty, get the site title and explicitly pass it
             * to the attributes used by wp_get_attachment_image().
             */
            $image_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);
            if (empty($image_alt)) {
                $image_alt = get_bloginfo('name', 'display');
            }

            $custom_logo_attr['alt'] = $image_alt;

            /**
             * If the alt attribute is not empty, there's no need to explicitly pass it
             * because wp_get_attachment_image() already adds the alt attribute.
             */
            $logo = wp_get_attachment_image($custom_logo_id, 'full', false, $custom_logo_attr);
            if ($class) {
                $html = '<div class="' . $class . '"><a class="after-overlay" title="' . $image_alt . '" href="' . Url::home() . '">' . $logo . '</a></div>';
            } else {
                $html = '<a class="after-overlay" title="' . $image_alt . '" href="' . self::home() . '">' . $logo . '</a>';
            }
        }

        return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    // -------------------------------------------------------------

    /**
     * @param null        $post
     * @param string|null $class
     *
     * @return string|null
     */
    public static function loopExcerpt($post = null, ?string $class = 'excerpt')
    {
        $excerpt = get_the_excerpt($post);
        if (!self::stripSpace($excerpt)) {
            return null;
        }

        $excerpt = strip_tags($excerpt);
        if (!$class) {
            return $excerpt;
        }

        return "<p class=\"$class\">{$excerpt}</p>";
    }

    // -------------------------------------------------------------

    /**
     * @param             $post
     * @param string|null $class
     * @param bool        $glyph_icon
     * @return string|null
     */
    public static function postExcerpt($post = null, ?string $class = 'excerpt', bool $glyph_icon = false)
    {
        $post = get_post($post);
        if (!self::stripSpace($post->post_excerpt)) {
            return null;
        }

        $open = '';
        $close = '';
        $glyph = '';
        if (true === $glyph_icon) {
            $glyph = ' data-glyph=""';
        }
        if ($class) {
            $open = '<div class="' . $class . '"' . $glyph . '>';
            $close = '</div>';
        }

        return $open . '<div>' . $post->post_excerpt . '</div>' . $close;
    }

    // -------------------------------------------------------------

    /**
     * @param int         $term
     * @param string|null $class
     *
     * @return string|null
     */
    public static function termExcerpt($term = 0, ?string $class = 'excerpt')
    {
        $description = term_description($term);
        if (!self::stripSpace($description)) {
            return null;
        }

        if (!$class) {
            return $description;
        }

        return "<div class=\"$class\">$description</div>";
    }

    // -------------------------------------------------------------

    /**
     * @param             $post
     * @param string|null $taxonomy
     * @return array|false|mixed|WP_Error|WP_Term
     */
    public static function primaryTerm($post = null, ?string $taxonomy = 'category')
    {
        //$post = get_post( $post );
        //$ID   = $post->ID ?? null;

        if (!$taxonomy) {
            $post_type = get_post_type($post);
            $taxonomy = $post_type . '_cat';

            if ('post' == $post_type) {
                $taxonomy = 'category';
            }

//            if ('product' == $post_type) {
//                $taxonomy = 'product_cat';
//            } elseif ('banner' == $post_type) {
//                $taxonomy = 'banner_cat';
//            } elseif ('service' == $post_type) {
//                $taxonomy = 'service_cat';
//            }
        }

        // Rank Math SEO
        // https://vi.wordpress.org/plugins/seo-by-rank-math/
        $primary_term_id = get_post_meta(get_the_ID(), 'rank_math_primary_' . $taxonomy, true);
        if ($primary_term_id) {
            $term = get_term($primary_term_id, $taxonomy);
            if ($term) {
                return $term;
            }
        }

        // Yoast SEO
        // https://vi.wordpress.org/plugins/wordpress-seo/
        if (class_exists('\WPSEO_Primary_Term')) {

            // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
            $wpseo_primary_term = new \WPSEO_Primary_Term($taxonomy, $post);
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $term = get_term($wpseo_primary_term, $taxonomy);
            if ($term) {
                return $term;
            }
        }

        // Default, first category
        $post_terms = get_the_terms($post, $taxonomy);
        if (is_array($post_terms)) {
            return $post_terms[0];
        }

        return false;
    }

    // -------------------------------------------------------------

    /**
     * @param null        $post
     * @param string|null $taxonomy
     * @param string|null $wrapper_open
     * @param string|null $wrapper_close
     *
     * @return string|null
     */
    public static function getPrimaryTerm($post = null, ?string $taxonomy = '', ?string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>')
    {
        $term = self::primaryTerm($post, $taxonomy);
        if (!$term) {
            return null;
        }

        $link = '<a href="' . esc_url(get_term_link($term, $taxonomy)) . '" title="' . esc_attr($term->name) . '">' . $term->name . '</a>';
        if ($wrapper_open && $wrapper_close) {
            $link = $wrapper_open . $link . $wrapper_close;
        }

        return $link;
    }

    // -------------------------------------------------------------

    /**
     * @param             $post
     * @param string|null $taxonomy
     * @param string|null $wrapper_open
     * @param string|null $wrapper_close
     *
     * @return string|null
     */
    public static function postTerms($post, ?string $taxonomy = 'category', ?string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>')
    {
        if (!$taxonomy) {
            $post_type = get_post_type($post);
            $taxonomy = $post_type . '_cat';

            if ('post' == $post_type) {
                $taxonomy = 'category';
            }
        }

        $link = '';
        $post_terms = get_the_terms($post, $taxonomy);
        if (empty($post_terms)) {
            return false;
        }

        foreach ($post_terms as $term) {
            if ($term->slug) {
                $link .= '<a href="' . esc_url(get_term_link($term)) . '" title="' . esc_attr($term->name) . '">' . $term->name . '</a>';
            }
        }

        if ($wrapper_open && $wrapper_close) {
            $link = $wrapper_open . $link . $wrapper_close;
        }

        return $link;
    }

    // -------------------------------------------------------------

    /**
     * @param string|null $taxonomy
     * @param int         $id
     * @param string      $sep
     *
     * @return void
     */
    public static function hashTags(?string $taxonomy = 'post_tag', int $id = 0, string $sep = '')
    {
        if (!$taxonomy) {
            $taxonomy = 'post_tag';
        }

        // Get Tags for posts.
        $hashtag_list = get_the_term_list($id, $taxonomy, '', $sep);

        // We don't want to output .entry-footer if it will be empty, so make sure its not.
        if ($hashtag_list) {
            echo '<div class="hashtags">';
            printf(
            /* translators: 1: SVG icon. 2: posted in label, only visible to screen readers. 3: list of tags. */
                '<div class="hashtag-links links">%1$s<span class="screen-reader-text">%2$s</span>%3$s</div>',
                '<i data-glyph="#"></i>',
                __('Tags', EHD_PLUGIN_TEXT_DOMAIN),
                $hashtag_list
            ); // WPCS: XSS OK.

            echo '</div>';
        }
    }

    // -------------------------------------------------------------

    /**
     * @param null   $post
     * @param string $size
     *
     * @return string|null
     */
    public static function postImageSrc($post = null, string $size = 'thumbnail')
    {
        return get_the_post_thumbnail_url($post, $size);
    }

    // -------------------------------------------------------------

    /**
     *
     * @param        $attachment_id
     * @param string $size
     *
     * @return string|null
     */
    public static function attachmentImageSrc($attachment_id, string $size = 'thumbnail')
    {
        return wp_get_attachment_image_url($attachment_id, $size);
    }

    // -------------------------------------------------------------

    /**
     * @param        $term
     * @param null   $acf_field_name
     * @param string $size
     * @param bool   $img_wrap
     * @return string|null
     */
    public static function acfTermThumb($term, $acf_field_name = null, string $size = "thumbnail", bool $img_wrap = false)
    {
        if (is_numeric($term)) {
            $term = get_term($term);
        }

        if (class_exists('\ACF') && $attach_id = get_field($acf_field_name, $term)) {
            $img_src = wp_get_attachment_image_url($attach_id, $size);
            if ($img_wrap) {
                $img_src = wp_get_attachment_image($attach_id, $size);
            }

            return $img_src;
        }

        return null;
    }

    // -------------------------------------------------------------

    /**
     * @param $post
     * @param $from
     * @param $to
     * @return mixed|void
     */
    public static function humanizeTime($post = null, $from = null, $to = null)
    {
        $_ago = __('ago', EHD_PLUGIN_TEXT_DOMAIN);

        if (empty($to)) {
            $to = current_time('U');
        }
        if (empty($from)) {
            $from = get_the_time('U', $post);
        }

        $diff = (int) abs($to - $from);

        $since = human_time_diff($from, $to);
        $since = $since . ' ' . $_ago;

        return apply_filters('humanize_time', $since, $diff, $from, $to);
    }

    // -------------------------------------------------------------

    /**
     * @return void
     */
    public static function breadcrumbs()
    {
        global $post, $wp_query;

        $before = '<li class="current">';
        $after = '</li>';

        if (!is_home() && !is_front_page()) {
            echo '<ul id="breadcrumbs" class="breadcrumbs" aria-label="Breadcrumbs">';
            echo '<li><a class="home" href="' . self::home() . '">' . __('Home', EHD_PLUGIN_TEXT_DOMAIN) . '</a></li>';

            //...
            if (class_exists('\WooCommerce') && @is_shop()) {
                $shop_page_title = get_the_title(get_option('woocommerce_shop_page_id'));
                echo $before . $shop_page_title . $after;
            } elseif ($wp_query->is_posts_page) {
                $posts_page_title = get_the_title(get_option('page_for_posts', true));
                echo $before . $posts_page_title . $after;
            } elseif ($wp_query->is_post_type_archive) {
                $posts_page_title = post_type_archive_title('', false);
                echo $before . $posts_page_title . $after;
            } /** page, attachment */
            elseif (is_page() || is_attachment()) {

                // parent page
                if ($post->post_parent) {
                    $parent_id = $post->post_parent;
                    $breadcrumbs = [];

                    while ($parent_id) {
                        $page = get_post($parent_id);
                        $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                        $parent_id = $page->post_parent;
                    }

                    $breadcrumbs = array_reverse($breadcrumbs);
                    foreach ($breadcrumbs as $crumb) {
                        echo $crumb;
                    }
                }

                echo $before . get_the_title() . $after;
            } /** single */
            elseif (is_single() && !is_attachment()) {

                if (!in_array(get_post_type(), ['post', 'product', 'service', 'project'])) {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    if (!is_bool($slug)) {
                        echo '<li><a href="' . self::home() . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
                    }
                } else {
                    $term = self::primaryTerm($post);
                    if ($term) {
                        if ($cat_code = get_term_parents_list($term->term_id, $term->taxonomy, ['separator' => ''])) {
                            $cat_code = str_replace('<a', '<li><a', $cat_code);
                            echo str_replace('</a>', '</a></li>', $cat_code);
                        }
                    }
                }

                echo $before . get_the_title() . $after;
            } /** search page */
            elseif (is_search()) {
                echo $before;
                printf(__('Search Results for: %s', EHD_PLUGIN_TEXT_DOMAIN), get_search_query());
                echo $after;
            } /** tag */
            elseif (is_tag()) {
                echo $before;
                printf(__('Tag Archives: %s', EHD_PLUGIN_TEXT_DOMAIN), single_tag_title('', false));
                echo $after;
            } /** author */
            elseif (is_author()) {
                global $author;

                $userdata = get_userdata($author);
                echo $before;
                echo $userdata->display_name;
                echo $after;
            } /** day, month, year */
            elseif (is_day()) {
                echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
                echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a></li>';
                echo $before . get_the_time('d') . $after;
            } elseif (is_month()) {
                echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
                echo $before . get_the_time('F') . $after;
            } elseif (is_year()) {
                echo $before . get_the_time('Y') . $after;
            } /** category, tax */
            elseif (is_category() || is_tax()) {

                $cat_obj = $wp_query->get_queried_object();
                $thisCat = get_term($cat_obj->term_id);

                if (isset($thisCat->parent) && 0 != $thisCat->parent) {
                    $parentCat = get_term($thisCat->parent);
                    if ($cat_code = get_term_parents_list($parentCat->term_id, $parentCat->taxonomy, ['separator' => ''])) {
                        $cat_code = str_replace('<a', '<li><a', $cat_code);
                        echo str_replace('</a>', '</a></li>', $cat_code);
                    }
                }

                echo $before . single_cat_title('', false) . $after;
            } /** 404 */
            elseif (is_404()) {
                echo $before;
                __('Not Found', EHD_PLUGIN_TEXT_DOMAIN);
                echo $after;
            }

            //...
            if (get_query_var('paged')) {
                echo '<li class="paged">';
                echo ' (';
                echo __('page', EHD_PLUGIN_TEXT_DOMAIN) . ' ' . get_query_var('paged');
                echo ')';
                echo $after;
            }

            echo '</ul>';
        }

        // reset
        wp_reset_query();
    }

    // -------------------------------------------------------------

    /**
     * Get lang code
     *
     * @return string
     */
    public static function getLang()
    {
        return strtolower(substr(get_locale(), 0, 2));
    }

    // -------------------------------------------------------------

    /**
     * @param $user_id
     * @return string
     */
    public static function getUserLink($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_the_author_meta('ID');
        }
        return get_author_posts_url($user_id);
    }

    // -------------------------------------------------------------

    /**
     * @param $obj
     * @param $fallback
     * @return array|false|int|mixed|string|WP_Error|WP_Term|null
     */
    public static function getPermalink($obj = null, $fallback = false)
    {
        if (empty($obj) && !empty($fallback)) {
            return $fallback;
        }
        if (is_numeric($obj) || empty($obj)) {
            return get_permalink($obj);
        }
        if (is_string($obj)) {
            return $obj;
        }

        if (is_array($obj)) {
            if (isset($obj['term_id'])) {
                return get_term_link($obj['term_id']);
            }
            if (isset($obj['user_login']) && isset($obj['ID'])) {
                return self::getUserLink($obj['ID']);
            }
            if (isset($obj['ID'])) {
                return get_permalink($obj['ID']);
            }
        }
        if (is_object($obj)) {
            $val_class = get_class($obj);
            if ($val_class == 'WP_Post') {
                return get_permalink($obj->ID);
            }
            if ($val_class == 'WP_Term') {
                return get_term_link($obj->term_id);
            }
            if ($val_class == 'WP_User') {
                return self::getUserLink($obj->ID);
            }
        }

        return $fallback;
    }

    // -------------------------------------------------------------

    /**
     * @param $obj
     * @param $fallback
     * @return false|int|mixed
     */
    public static function getId($obj = null, $fallback = false)
    {
        if (empty($obj) && $fallback) {
            return get_the_ID();
        }
        if (is_numeric($obj)) {
            return intval($obj);
        }
        if (filter_var($obj, FILTER_VALIDATE_URL)) {
            return url_to_postid($obj);
        }
        if (is_string($obj)) {
            return intval($obj);
        }
        if (is_array($obj)) {
            if (isset($obj['term_id'])) {
                return $obj['term_id'];
            }
            if (isset($obj['ID'])) {
                return $obj['ID'];
            }
        }
        if (is_object($obj)) {
            $val_class = get_class($obj);
            if ($val_class == 'WP_Post') {
                return $obj->ID;
            }
            if ($val_class == 'WP_Term') {
                return $obj->term_id;
            }
            if ($val_class == 'WP_User') {
                return $obj->ID;
            }
        }
        return \false;
    }

    // -------------------------------------------------------------

    /**
     * @param string $url
     * @return int
     */
    public static function getPostIdFromUrl(string $url = '')
    {
        if (!$url) {
            global $wp;
            $url = home_url(add_query_arg([], $wp->request));
        }
        return url_to_postid($url);
    }

    // -------------------------------------------------------------

    /**
     * A fallback when no navigation is selected by default.
     *
     * @param string|null $container
     * @return void
     */
    public static function menuFallback(?string $container = '')
    {
        echo '<div class="menu-fallback">';
        if ($container) {
            echo '<div class="' . $container . '">';
        }

        /* translators: %1$s: link to menus, %2$s: link to customize. */
        printf(
            __('Please assign a menu to the primary menu location under %1$s or %2$s the design.', EHD_PLUGIN_TEXT_DOMAIN),
            /* translators: %s: menu url */
            sprintf(
                __('<a class="_blank" href="%s">Menus</a>', EHD_PLUGIN_TEXT_DOMAIN),
                get_admin_url(get_current_blog_id(), 'nav-menus.php')
            ),
            /* translators: %s: customize url */
            sprintf(
                __('<a class="_blank" href="%s">Customize</a>', EHD_PLUGIN_TEXT_DOMAIN),
                get_admin_url(get_current_blog_id(), 'customize.php')
            )
        );
        if ($container) {
            echo '</div>';
        }
        echo '</div>';
    }
}
