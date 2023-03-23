<?php

namespace EHD\Themes;

use EHD\Cores\Helper;

/**
 * Admin Class
 *
 * @author EHD
 */

\defined('ABSPATH') || die;

final class Admin
{
    public function __construct()
    {
        /** Remove admin wp version */
	    if ( ! WP_DEBUG ) {
		    add_filter( 'update_footer', '__return_empty_string', 11 );
	    }

	    add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 31 );

	    add_action( 'admin_init', [ &$this, 'admin_init' ], 10 );
	    add_action( 'admin_menu', [ &$this, 'dashboard_meta_box' ], 11 );

		/** Custom options */
	    $block_editor_options = get_option( 'block_editor__options' );

	    $use_widgets_block_editor_off           = $block_editor_options['use_widgets_block_editor_off'] ?? '';
	    $gutenberg_use_widgets_block_editor_off = $block_editor_options['gutenberg_use_widgets_block_editor_off'] ?? '';
	    $use_block_editor_for_post_type_off     = $block_editor_options['use_block_editor_for_post_type_off'] ?? '';

	    // Disables the block editor from managing widgets.
	    if ( $use_widgets_block_editor_off ) {
		    add_filter( 'use_widgets_block_editor', '__return_false' );
	    }

	    // Disables the block editor from managing widgets in the Gutenberg plugin.
	    if ( $gutenberg_use_widgets_block_editor_off ) {
		    add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
	    }

	    // Use Classic Editor - Disable Gutenberg Editor
	    if ( $use_block_editor_for_post_type_off ) {
		    add_filter( 'use_block_editor_for_post_type', '__return_false' );
	    }
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    private function _remove_menu(): void
    {
        //echo dump($GLOBALS[ 'menu' ]);

        // Hide menu
	    $hide_menu = Helper::getThemeMod( 'remove_menu_setting' );
	    if ( $hide_menu ) {
		    $array_hide_menu = explode( "\n", $hide_menu );
		    foreach ( $array_hide_menu as $menu ) {
			    if ( $menu ) {
				    remove_menu_page( $menu );
			    }
		    }
	    }
    }

    /** ---------------------------------------- */

    /**
     * Add admin column
     */
    public function admin_init(): void {
        $this->_remove_menu();

        // Add customize column taxonomy
        // https://wordpress.stackexchange.com/questions/77532/how-to-add-the-category-id-to-admin-page
        $taxonomy_arr = [
            'category',
            'post_tag',
            'banner_cat',
            //'service_cat',
            //'service_tag',
        ];
        foreach ($taxonomy_arr as $term) {
            add_filter("{$term}_row_actions", [&$this, 'term_action_links'], 11, 2);
        }

        // customize row_actions
        $post_type_arr = [
            'user',
            'post',
            'page',
        ];
        foreach ($post_type_arr as $post_type) {
            add_filter("{$post_type}_row_actions", [&$this, 'post_type_action_links'], 11, 2);
        }

        // thumb post page
        add_filter('manage_posts_columns', [&$this, 'post_header'], 11, 1);
        add_filter('manage_posts_custom_column', [&$this, 'post_column'], 11, 2);
        add_filter('manage_pages_columns', [&$this, 'post_header'], 5, 1);
        add_filter('manage_pages_custom_column', [&$this, 'post_column'], 5, 2);

        // thumb term
        $thumb_term = [
            'category',
            'banner_cat',
            //'service_cat',
        ];

        foreach ($thumb_term as $term) {
            add_filter("manage_edit-{$term}_columns", [&$this, 'term_header'], 11, 1);
            add_filter("manage_{$term}_custom_column", [&$this, 'term_column'], 11, 3);
        }

        // exclude thumb post column
        $exclude_thumb_posts = [
            'product',
            'site-review',
            'wpcf7_contact_form',
        ];

        foreach ($exclude_thumb_posts as $post) {
            add_filter("manage_{$post}_posts_columns", [$this, 'post_exclude_header'], 12, 1);
        }
    }

    /** ---------------------------------------- */
    /** ---------------------------------------- */

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function post_exclude_header($columns) {
        unset($columns['post_thumb']);
        return $columns;
    }

    /** ---------------------------------------- */

	/**
	 * @param $out
	 * @param $column
	 * @param $term_id
	 *
	 * @return int|mixed|string|null
	 */
    public function term_column($out, $column, $term_id) {
        switch ($column) {
            case 'term_thumb':
                $term_thumb = Helper::acfTermThumb($term_id, $column, "thumbnail", true);
                if (!$term_thumb) {
                    $term_thumb = Helper::placeholderSrc();
                }

                return $out = $term_thumb;
                break;

            case 'term_order':
                if (function_exists('get_field')) {
                    $term_order = get_field('term_order', get_term($term_id));
                    return $out = $term_order ?: 0;
                }

                return $out = 0;
                break;

            default:
                return $out;
                break;
        }
    }

    /** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return array|mixed
	 */
    public function term_header($columns) {
        if (class_exists('\ACF')) {

            // thumb
            $thumb = [
                "term_thumb" => sprintf('<span class="wc-image tips">%1$s</span>', __("Thumb", EHD_PLUGIN_TEXT_DOMAIN)),
            ];
            $columns = Helper::insertBefore('name', $columns, $thumb);

            // order
            $menu_order = [
                'term_order' => sprintf('<span class="term-order tips">%1$s</span>', __("Order", EHD_PLUGIN_TEXT_DOMAIN)),
            ];
            $columns = array_merge($columns, $menu_order);
        }

        return $columns;
    }

    /** ---------------------------------------- */

    /**
     * @param $column_name
     * @param $post_id
     */
    public function post_column($column_name, $post_id): void {
        switch ($column_name) {
            case 'post_thumb':
                $post_type = get_post_type($post_id);
                if (!in_array($post_type, ['video', 'product'])) {
                    if (!$thumbnail = get_the_post_thumbnail($post_id, 'thumbnail')) {
                        $thumbnail = Helper::placeholderSrc();
                    }
                    echo $thumbnail;
                } else if ('video' == $post_type) {
                    if (has_post_thumbnail($post_id)) {
                        echo get_the_post_thumbnail($post_id, 'thumbnail');
                    } else if (function_exists('get_field') && $url = get_field('url', $post_id)) {
                        $img_src = Helper::youtubeImage(esc_url($url), ['default']);
                        echo "<img alt src=\"" . $img_src . "\" />";
                    }
                }

                break;

            default:
                break;
        }
    }

    /** ---------------------------------------- */

    /**
     * @param $columns
     *
     * @return array
     */
    public function post_header($columns): array {
        $in = [
            "post_thumb" => sprintf('<span class="wc-image tips">%1$s</span>', __("Thumb", EHD_PLUGIN_TEXT_DOMAIN)),
        ];

        return Helper::insertBefore('title', $columns, $in);
    }

    /** ---------------------------------------- */

    /**
     * @param $actions
     * @param $_object
     *
     * @return mixed
     */
    public function post_type_action_links($actions, $_object) {
        if (!in_array($_object->post_type, ['product', 'site-review'])) {
            Helper::prepend($actions, 'Id:' . $_object->ID, 'action_id');
        }

        return $actions;
    }

    /** ---------------------------------------- */

    /**
     * @param $actions
     * @param $_object
     *
     * @return mixed
     */
    public function term_action_links($actions, $_object) {
        Helper::prepend($actions, 'Id: ' . $_object->term_id, 'action_id');
        return $actions;
    }

    /** ---------------------------------------- */

    /**
     * Remove dashboard widgets
     *
     * @return void
     */
    public function dashboard_meta_box(): void {
        /*Incoming Links Widget*/
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');

        /*Remove WordPress Events and News*/
        remove_meta_box('dashboard_primary', 'dashboard', 'normal');
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function admin_enqueue_scripts(): void {
	    wp_enqueue_style( "admin-style", EHD_PLUGIN_URL . "assets/css/admin.css", [], EHD_PLUGIN_VERSION );
	    wp_enqueue_script( "admin", EHD_PLUGIN_URL . "assets/js/admin.js", [ "jquery" ], EHD_PLUGIN_VERSION, true );
    }
}
