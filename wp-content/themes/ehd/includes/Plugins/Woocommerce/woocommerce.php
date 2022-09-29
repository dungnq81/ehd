<?php

use EHD\Themes\Func;

\defined('\WPINC') || die;

// ------------------------------------------

if (!function_exists('ewc_comments_template')) {

    /**
     * @return void
     */
    function ewc_comments_template()
    {
        wc_get_template('single-product/tabs/comments.php');
    }
}

// ------------------------------------------

/**
 * Add default product tabs to product pages.
 *
 * overwrite `woocommerce_default_product_tabs`
 *
 * @param array $tabs Array of tabs.
 * @return array
 */
function woocommerce_default_product_tabs(array $tabs = [])
{
    global $product, $post;

    // Description tab - shows product content.
    if ($post->post_content) {
        $tabs['description'] = [
            'title' => __('Description', 'ehd'),
            'priority' => 10,
            'callback' => 'woocommerce_product_description_tab',
        ];
    }

    // Additional information tab - shows attributes.
    if ($product && ($product->has_attributes() || apply_filters('wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions()))) {
        $tabs['additional_information'] = [
            'title' => __('Additional information', 'ehd'),
            'priority' => 20,
            'callback' => 'woocommerce_product_additional_information_tab',
        ];
    }

    // After-Sales Policies
    // Components
    // Specifications

    // Reviews tab - shows comments.
    $facebook_comment = false;
    $zalo_comment = false;
    if (class_exists('\ACF')) {
        $facebook_comment = get_field('facebook_comment', $product->get_id());
        $zalo_comment = get_field('zalo_comment', $product->get_id());
    }

    if (comments_open() || true === $facebook_comment || true === $zalo_comment) {
        $tabs['reviews'] = [
            /* translators: %s: reviews count */
            'title' => sprintf(__('Reviews (%d)', 'ehd'), $product->get_review_count()),
            'priority' => 40,
            //'callback' => 'comments_template',
            'callback' => 'ewc_comments_template',
        ];
    }

    return $tabs;
}

// ------------------------------------------

/**
 * Show an archive description on taxonomy archives.
 */
function ewc_taxonomy_archive_description()
{
    // Don't display the description on search results page.
    if (is_search()) {
        return;
    }

    if (is_product_taxonomy() && in_array(absint(get_query_var('paged')), [0, 1], true)) {
        $term = get_queried_object();
        if ($term) {

            $seo_desc = null;
            if (function_exists('get_fields')) {
                $ACF = get_fields($term);
                isset($ACF['seo_desc']) && $seo_desc = $ACF['seo_desc'];
            }

            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

            /**
             * Filters the archive's raw description on taxonomy archives.
             *
             * @param string  $term_description Raw description text.
             * @param WP_Term $term             Term object for this taxonomy archive.
             * @since 6.7.0
             */
            $term_description = apply_filters('woocommerce_taxonomy_archive_description_raw', $term->description, $term);

            if ($thumbnail_id || $seo_desc || $term_description) {
                echo '<div class="wc-description wc-archive-description"><div class="wc-description-inner">';

                if ($thumbnail_id) :
                    echo '<div class="term-thumb">' . wp_get_attachment_image($thumbnail_id, 'post-thumbnail') . '</div>';
                endif;

                if ($term_description) :
                    echo '<div class="term-description">' . wc_format_content(wp_kses_post($term_description)) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                endif;

                if ($seo_desc) :
                    $seo = get_post($seo_desc);
                    echo '<div class="seo-description">' . get_the_content(null, false, $seo) . '</div>';
                endif;
                echo '</div></div>';
            }
        }
    }
}

// ------------------------------------------

/**
 * Show a shop page description on product archives.
 */
function ewc_product_archive_description()
{
    // Don't display the description on search results page.
    if (is_search()) {
        return;
    }

    if (is_post_type_archive('product') && in_array(absint(get_query_var('paged')), [0, 1], true)) {
        $shop_page = get_post(wc_get_page_id('shop'));
        if ($shop_page) {

            $allowed_html = wp_kses_allowed_html('post');

            // This is needed for the search product block to work.
            $allowed_html = array_merge(
                $allowed_html,
                [
                    'form' => [
                        'action' => true,
                        'accept' => true,
                        'accept-charset' => true,
                        'enctype' => true,
                        'method' => true,
                        'name' => true,
                        'target' => true,
                    ],

                    'input' => [
                        'type' => true,
                        'id' => true,
                        'class' => true,
                        'placeholder' => true,
                        'name' => true,
                        'value' => true,
                    ],

                    'button' => [
                        'type' => true,
                        'class' => true,
                        'label' => true,
                    ],

                    'svg' => [
                        'hidden' => true,
                        'role' => true,
                        'focusable' => true,
                        'xmlns' => true,
                        'width' => true,
                        'height' => true,
                        'viewbox' => true,
                    ],
                    'path' => [
                        'd' => true,
                    ],
                ]
            );

            $post_thumbnail = get_the_post_thumbnail($shop_page, 'post-thumbnail');
            $description = wc_format_content(wp_kses($shop_page->post_content, $allowed_html));
            if ($description || $post_thumbnail) {

                echo '<div class="wc-description wc-shop-description"><div class="wc-description-inner">';
                if ($post_thumbnail) :
                    echo '<div class="page-thumb">' . $post_thumbnail . '</div>';
                endif;

                if ($description) :
                    echo '<div class="page-description">' . $description . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                endif;
                echo '</div></div>';
            }
        }
    }
}

// ------------------------------------------

if (!function_exists('ewc_get_gallery_image_html')) {
    /**
     * @param      $attachment_id
     * @param bool $main_image
     * @param bool $cover
     * @param bool $lightbox
     * @return string
     */
    function ewc_get_gallery_image_html($attachment_id, bool $main_image = false, bool $cover = false, bool $lightbox = false)
    {
        $gallery_thumbnail = wc_get_image_size('gallery_thumbnail');
        $thumbnail_size = apply_filters('woocommerce_gallery_thumbnail_size', [$gallery_thumbnail['width'], $gallery_thumbnail['height']]);
        $image_size = apply_filters('woocommerce_gallery_image_size', $main_image ? 'woocommerce_single' : $thumbnail_size);
        $full_size = apply_filters('woocommerce_gallery_full_size', apply_filters('woocommerce_product_thumbnails_large_size', 'widescreen'));
        $thumbnail_src = wp_get_attachment_image_src($attachment_id, $thumbnail_size);
        $full_src = wp_get_attachment_image_src($attachment_id, $full_size);
        $alt_text = trim(wp_strip_all_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
        $image = wp_get_attachment_image(
            $attachment_id,
            $image_size,
            false,
            apply_filters(
                'woocommerce_gallery_image_html_attachment_image_params',
                [
                    'title' => _wp_specialchars(get_post_field('post_title', $attachment_id), ENT_QUOTES, 'UTF-8', true),
                    'data-caption' => _wp_specialchars(get_post_field('post_excerpt', $attachment_id), ENT_QUOTES, 'UTF-8', true),
                    'data-src' => esc_url($full_src[0]),
                    'data-large_image' => esc_url($full_src[0]),
                    'data-large_image_width' => esc_attr($full_src[1]),
                    'data-large_image_height' => esc_attr($full_src[2]),
                    'class' => esc_attr($main_image ? 'wp-post-image' : ''),
                ],
                $attachment_id,
                $image_size,
                $main_image
            )
        );

        $ratio = Func::getThemeMod('product_menu_setting');
        $ratio_class = $ratio;
        if ($ratio == 'default' || empty($ratio)) {
            $ratio_class = '1-1';
        }

        $auto = $cover ? '' : ' auto';

        if ($lightbox) {
            $popup_image = '<span data-rel="lightbox" class="image-popup" data-src="' . esc_url($full_src[0]) . '" data-glyph=""></span>';
            return '<div data-thumb="' . esc_url($thumbnail_src[0]) . '" data-thumb-alt="' . esc_attr($alt_text) . '" class="wpg__image cover"><a class="res' . $auto . ' ar-' . $ratio_class . '" href="' . esc_url($full_src[0]) . '">' . $image . '</a>' . $popup_image . '</div>';
        }

        return '<div data-thumb="' . esc_url($thumbnail_src[0]) . '" data-thumb-alt="' . esc_attr($alt_text) . '" class="woocommerce-product-gallery__image wpg__thumb cover"><a class="res' . $auto . ' ar-' . $ratio_class . '" href="' . esc_url($full_src[0]) . '">' . $image . '</a></div>';
    }
}

// ------------------------------------------

if (!function_exists('ewc_video_product')) {
    /**
     * @param        $product_id
     * @param string $acf_field
     * @return false|mixed
     */
    function ewc_video_product($product_id, string $acf_field = 'video_link')
    {
        if (class_exists('\ACF') && $product_id) {
            $vid_url = get_field($acf_field, $product_id);
            if ($vid_url && filter_var($vid_url, FILTER_VALIDATE_URL)) {
                return $vid_url;
            }
        }

        return false;
    }
}

// ------------------------------------------

if (!function_exists('ewc_sale_flash_percent')) {
    /**
     * @param $product
     * @return float
     */
    function ewc_sale_flash_percent($product)
    {
        global $product;
        $percent_off = '';

        if ($product->is_on_sale()) {

            if ($product->is_type('variable')) {
                $percent_off = ceil(100 - ($product->get_variation_sale_price() / $product->get_variation_regular_price('min')) * 100);
            } elseif ($product->get_regular_price() && !$product->is_type('grouped')) {
                $percent_off = ceil(100 - ($product->get_sale_price() / $product->get_regular_price()) * 100);
            }
        }

        return $percent_off;
    }
}

// ------------------------------------------

if (!function_exists('ewc_template_loop_quick_view')) {

    /**
     * Get the quick view template for the loop.
     *
     * @param array $args Arguments.
     */
    function ewc_template_loop_quick_view(array $args = [])
    {
        global $product;

        if ($product) {
            $defaults = [
                'class' => implode(
                    ' ',
                    array_filter(
                        [
                            'button',
                            'product_type_' . $product->get_type()
                        ]
                    )
                ),
                'attributes' => [
                    'data-product_id' => $product->get_id(),
                    'aria-label' => __('Quick View', 'ehd'),
                    'rel' => 'nofollow',
                ],
            ];

            $args = apply_filters('woocommerce_loop_quick_view_args', wp_parse_args($args, $defaults), $product);

            if (isset($args['attributes']['aria-label'])) {
                $args['attributes']['aria-label'] = wp_strip_all_tags($args['attributes']['aria-label']);
            }

            wc_get_template('loop/quick-view.php', $args);
        }
    }
}

// ------------------------------------------

if (!function_exists('ewc_woocommerce_share')) {
    add_action('woocommerce_share', 'ewc_woocommerce_share', 11);

    /**
     * woocommerce_share action
     * @return void
     */
    function ewc_woocommerce_share()
    {
        get_template_part('template-parts/parts/sharing');
    }
}

// ------------------------------------------

if (!function_exists('ewc_template_single_meta_sku')) {
    add_action('woocommerce_single_product_summary', 'ewc_template_single_meta_sku', 9);

    /**
     * Output the product meta.
     */
    function ewc_template_single_meta_sku()
    {
        wc_get_template('single-product/meta_sku.php');
    }
}

// ------------------------------------------

if (!function_exists('ewc_output_recently_viewed_products')) {
    add_action('woocommerce_after_single_product_summary', 'ewc_output_recently_viewed_products', 25);
    add_action('woocommerce_after_shop', 'ewc_output_recently_viewed_products', 19);

    /**
     * Output the product sale flash.
     */
    function ewc_output_recently_viewed_products()
    {
        wc_get_template('single-product/recently_viewed.php');
    }
}