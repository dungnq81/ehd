<?php

/**
 * Configure responsive images sizes
 * @package WordPress
 */

\defined( 'ABSPATH' ) || die;

/**
 * thumbnail (480x0)
 * medium (768x0)
 * large (1024x0)
 *
 * widescreen (1920x9999)
 * post-thumbnail (1200x9999)
 */

// custom thumb
add_image_size('widescreen', 1920, 9999, false);
add_image_size('post-thumbnail', 1200, 9999, false);

// -----------------------------------------------------------------------

/**
 * Disable unwanted image sizes
 */
add_filter('intermediate_image_sizes_advanced', function ($sizes) {

    unset($sizes['medium_large']);

    unset($sizes['1536x1536']); // disable 2x medium-large size
    unset($sizes['2048x2048']); // disable 2x large size

    return $sizes;
});

// -----------------------------------------------------------------------

// Disable Scaled
add_filter('big_image_size_threshold', '__return_false');

// -----------------------------------------------------------------------

/**
 * Disable Other Sizes
 */
add_action('init', function () {
    remove_image_size('1536x1536'); // disable 2x medium-large size
    remove_image_size('2048x2048'); // disable 2x large size
});

// -------------------------------------------------------------

add_filter('post_thumbnail_html', 'ehd_remove_thumbnail_dimensions', 10, 1);
add_filter('image_send_to_editor', 'ehd_remove_thumbnail_dimensions', 10, 1);
add_filter('the_content', 'ehd_remove_thumbnail_dimensions', 10, 1);

if (!function_exists('ehd_remove_thumbnail_dimensions')) {
    /**
     * @param $html
     * @return string|string[]|null
     */
    function ehd_remove_thumbnail_dimensions($html)
    {
        return preg_replace('/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html);
    }
}
