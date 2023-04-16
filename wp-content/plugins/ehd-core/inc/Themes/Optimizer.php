<?php

namespace EHD\Themes;

use EHD\Cores\Helper;

\defined('ABSPATH') || die;

/**
 * Optimizer Class
 *
 * @author eHD
 */
final class Optimizer
{
    public function __construct()
    {
	    $this->_cleanup();

	    //...
	    if ( ! is_admin() ) {
		    add_filter( 'script_loader_tag', [ &$this, 'script_loader_tag' ], 12, 3 );
		    add_filter( 'style_loader_tag', [ &$this, 'style_loader_tag' ], 12, 2 );
	    }

	    //...
	    if ( ! WP_DEBUG ) {

		    // Remove WP version from RSS.
		    add_filter( 'the_generator', '__return_empty_string' );

		    add_filter( 'style_loader_src', [ &$this, 'remove_version_scripts_styles' ], 11, 1 );
		    add_filter( 'script_loader_src', [ &$this, 'remove_version_scripts_styles' ], 11, 1 );
	    }

		// wp_enqueue_scripts
	    add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 11 );

	    // wp_print_footer_scripts
	    add_action( 'wp_print_footer_scripts', [ &$this, 'print_footer_scripts' ], 99 );

	    // fixed canonical
	    add_action( 'wp_head', [ &$this, 'fixed_archive_canonical' ], 10 );

	    // filter post search only by title
	    add_filter( 'posts_search', [ &$this, 'post_search_by_title' ], 500, 2 );

	    // custom posts where, filter post search only by title
	    //add_filter( 'posts_where', [ &$this, 'posts_title_filter' ], 499, 2 );

	    // remove id li navigation
	    add_filter( 'nav_menu_item_id', '__return_null', 10, 3 );

	    // Adding Shortcode in WordPress Using Custom HTML Widget
	    add_filter( 'widget_text', 'do_shortcode' );
	    add_filter( 'widget_text', 'shortcode_unautop' );

	    // Hooks the wp action to insert some cache control max-age headers.
//	    add_action( 'wp', function ( $wp ) {
//		    if ( is_feed() ) {
//			    if ( ! is_user_logged_in() ) {
//
//				    // Set the max age for feeds to 5 minutes.
//				    header( 'Cache-Control: max-age=' . ( 5 * MINUTE_IN_SECONDS ) );
//			    }
//		    }
//	    } );

	    // normalize upload filename
	    add_filter( 'sanitize_file_name', function ( $filename ) {
		    return remove_accents( $filename );
	    }, 10, 1 );

	    // Disable XML-RPC authentication
	    add_filter( 'xmlrpc_enabled', '__return_false' );
	    add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
	    add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );
	    add_filter( 'pings_open', '__return_false', 9999 );

	    add_filter( 'wp_headers', function ( $headers ) {
		    unset( $headers['X-Pingback'], $headers['x-pingback'] );
		    return $headers;
	    } );

	    //...
	    add_filter( 'excerpt_more', function () {
		    return ' ' . '&hellip;';
	    } );

	    // Remove admin bar
	    add_action( 'wp_before_admin_bar_render', function () {
		    global $wp_admin_bar;
		    $wp_admin_bar->remove_menu( 'wp-logo' );
	    } );

	    // Prevent Specific Plugins from Deactivation
	    add_filter( 'plugin_action_links', function ( $actions, $plugin_file, $plugin_data, $context ) {

			// action keys
		    $keys = [
				'deactivate',
			    'delete'
		    ];

		    foreach ( $keys as $key ) {
			    if ( array_key_exists( $key, $actions )
			         && in_array(
				         $plugin_file,
				         [
					         'ehd-core/ehd-core.php',
					         //'advanced-custom-fields-pro/acf.php',
				         ] )
			    ) {
				    unset( $actions[ $key ] );
			    }
		    }

		    return $actions;

	    }, 10, 4 );
    }

    /** ---------------------------------------- */

    /**
     * Launching operation cleanup.
     *
     * @return void
     */
    protected function _cleanup() : void
    {
        remove_action('welcome_panel', 'wp_welcome_panel');

	    // wp_head
	    remove_action( 'wp_head', 'rsd_link' );                        // Remove the EditURI/RSD link
	    remove_action( 'wp_head', 'wlwmanifest_link' );                // Remove Windows Live Writer Manifest link
	    remove_action( 'wp_head', 'wp_generator' );                    // remove WordPress Generator
	    remove_action( 'wp_head', 'feed_links_extra', 3 );             //remove comments feed.
	    remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Emoji detection script.

	    // all actions related to emojis
	    remove_action( 'wp_print_styles', 'print_emoji_styles' );
	    remove_action( 'admin_print_styles', 'print_emoji_styles' );
	    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

        /**
         * Remove wp-json header from WordPress
         * Note that the REST API functionality will still be working as it used to;
         * this only removes the header code that is being inserted.
         */
	    remove_action( 'wp_head', 'rest_output_link_wp_head' );
	    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	    remove_action( 'template_redirect', 'rest_output_link_header', 11 );

	    // staticize_emoji
	    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    }

    // ------------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue_scripts()
	{
		$style = '';
		$ar_post_type_list = apply_filters( 'ehd_aspect_ratio_post_type', [] );
		foreach ($ar_post_type_list as $ar_post_type)
		{
			$ratio_obj = Helper::getAspectRatioClass( $ar_post_type, 'aspect_ratio__options' );
			$ratio_style = $ratio_obj->style ?? '';
			if ($ratio_style) {
				$style .= $ratio_style;
			}
		}

		if ($style) {
			wp_add_inline_style( 'ehd-core-style', $style );
		}
	}

    // ------------------------------------------------------

	/**
	 * @return void
	 */
    public function print_footer_scripts(): void
    {
        ?>
        <script>document.documentElement.classList.remove("no-js");
            if (-1 !== navigator.userAgent.toLowerCase().indexOf('msie') || -1 !== navigator.userAgent.toLowerCase().indexOf('trident/')) {
                document.documentElement.classList.add('is-IE');
            }</script>
        <?php

        if (file_exists($passive_events = EHD_PLUGIN_PATH . 'assets/js/plugins/passive-events-fix.js')) {
            echo '<script>';
            include $passive_events;
            echo '</script>' . "\n";
        }

        if (file_exists($skip_link = EHD_PLUGIN_PATH . 'assets/js/plugins/skip-link-focus-fix.js')) {
            echo '<script>';
            include $skip_link;
            echo '</script>' . "\n";
        }

        if (file_exists($flex_gap = EHD_PLUGIN_PATH . 'assets/js/plugins/flex-gap.js')) {
            echo '<script>';
            include $flex_gap;
            echo '</script>' . "\n";
        }

	    if (file_exists($load_scripts = EHD_PLUGIN_PATH . 'assets/js/plugins/load-scripts.js')) {
		    echo '<script>';
		    include $load_scripts;
		    echo '</script>';
	    }
    }

    // ------------------------------------------------------

	/**
	 * @return void
	 */
	public function fixed_archive_canonical(): void
	{
		if ( is_archive() ) {
			echo '<link rel="canonical" href="' . get_pagenum_link() . '" />';
		}
	}

	// ------------------------------------------------------

	/**
	 * @param string $tag
	 * @param string $handle
	 * @param string $src
	 *
	 * @return string
	 */
	public function script_loader_tag( string $tag, string $handle, string $src ): string
	{
		// Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
		foreach ( [ 'async', 'defer' ] as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			// Only allow async or defer, not both.
			break;
		}

		// Custom filter which adds proper attributes

		// fontawesome kit
		if ( ( 'fontawesome-kit' == $handle ) && ! preg_match( ":\scrossorigin(=|>|\s):", $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " crossorigin='anonymous'", $tag, 1 );
		}

		// add script handles to the array
		$str_parsed = apply_filters( 'ehd_defer_script', [] );

		return Helper::lazyScriptTag( $str_parsed, $tag, $handle, $src );
	}

	/** ---------------------------------------- */

	/**
	 * @param string $html
	 * @param string $handle
	 *
	 * @return string
	 */
	public function style_loader_tag( string $html, string $handle ): string
	{
		/** Add style handles to the array below */
		$styles = apply_filters( 'ehd_defer_style', [] );

		return Helper::lazyStyleTag( $styles, $html, $handle );
	}

	// ------------------------------------------------------

	/**
	 * Remove version from scripts and styles
	 *
	 * @param $src
	 *
	 * @return false|mixed|string
	 */
	public function remove_version_scripts_styles( $src )
	{
		if ( $src && str_contains( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	// ------------------------------------------------------

	/**
     * Search only in post title or excerpt
     *
	 * @param $search
	 * @param $wp_query
	 *
	 * @return mixed|string
	 */
	public function post_search_by_title( $search, $wp_query )
	{
		global $wpdb;

		if ( empty( $search ) ) {
			return $search; // skip processing – no search term in query
		}

		$q = $wp_query->query_vars;
		$n = ! empty( $q['exact'] ) ? '' : '%';

		$search = $search_and = '';

		$search_terms = Helper::toArray( $q['search_terms'] );
		foreach ( $search_terms as $term ) {
			$term       = esc_sql( $wpdb->esc_like( $term ) );
			$search     .= "{$search_and}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}' OR $wpdb->posts.post_excerpt LIKE '{$n}{$term}{$n}')";
			$search_and = " AND ";
		}

		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() ) {
				$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}

		return $search;
	}

	// ------------------------------------------------------

	/**
	 * Search only in post title - wp_query
	 *
	 * @param $where
	 * @param $wp_query
	 *
	 * @return mixed|string
	 */
//	public function posts_title_filter( $where, $wp_query )
// {
//		global $wpdb;
//
//		if ( $search_term = $wp_query->get( 'title_filter' ) ) {
//			$term = esc_sql( $wpdb->esc_like( $search_term ) );
//			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $term . '%\'';
//		}
//
//		return $where;
//	}
}
