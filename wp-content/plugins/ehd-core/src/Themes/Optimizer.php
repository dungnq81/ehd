<?php

namespace EHD_Themes;

use EHD_Cores\Helper;

/**
 * Optimizer Class
 *
 * @author eHD
 */

\defined( 'ABSPATH' ) || die;

final class Optimizer {

	/**
	 * @var array|false|mixed
	 */
	public mixed $optimizer_options = [];

	// ------------------------------------------------------

	public function __construct() {

		$this->optimizer_options = Helper::getOption( 'optimizer__options', false, false );

		$this->_cleanup();
		$this->_optimizer();
	}

	// ------------------------------------------------------

	/**
	 * Launching operation cleanup
	 *
	 * @return void
	 */
	private function _cleanup(): void {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );

		// wp_head
		remove_action( 'wp_head', 'rsd_link' );                        // Remove the EditURI/RSD link
		remove_action( 'wp_head', 'wlwmanifest_link' );                // Remove Windows Live Writer Manifest link
		remove_action( 'wp_head', 'wp_generator' );                    // remove WordPress Generator
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Emoji detection script.

		// All actions related to emojis
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		// Staticize emoji
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		/**
		 * Remove wp-json header from WordPress
		 * Note that the REST API functionality will still be working as it used to;
		 * this only removes the header code that is being inserted.
		 */
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );

		// Remove id li navigation
		add_filter( 'nav_menu_item_id', '__return_null', 10, 3 );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
    private function _optimizer(): void {

        // Filters the rel values that are added to links with `target` attribute.
	    add_filter( 'wp_targeted_link_rel', [ &$this, 'targeted_link_rel' ], 999, 2 );

	    // fixed canonical
	    add_action( 'wp_head', [ &$this, 'fixed_archive_canonical' ] );

	    // only front-end
	    if ( ! is_admin() && ! Helper::is_login() ) {
		    add_filter( 'script_loader_tag', [ &$this, 'script_loader_tag' ], 12, 3 );
		    add_filter( 'style_loader_tag', [ &$this, 'style_loader_tag' ], 12, 2 );

		    add_action( 'wp_print_footer_scripts', [ &$this, 'print_footer_scripts' ], 99 );
	    }

	    add_filter( 'posts_search', [ &$this, 'post_search_by_title' ], 500, 2 ); // filter post search only by title
	    // add_filter( 'posts_where', [ &$this, 'posts_title_filter' ], 499, 2 ); // custom posts where, filter post search only by title

	    add_filter( 'excerpt_more', function () {
		    return ' ' . '&hellip;';
	    } );

	    // Remove admin bar
	    add_action( 'wp_before_admin_bar_render', function () {
		    global $wp_admin_bar;
		    $wp_admin_bar->remove_menu( 'wp-logo' );
	    } );

	    // Adding Shortcode in WordPress Using Custom HTML Widget
	    add_filter( 'widget_text', 'do_shortcode' );
	    add_filter( 'widget_text', 'shortcode_unautop' );

	    // Normalize upload filename
	    add_filter( 'sanitize_file_name', function ( $filename ) {
		    return remove_accents( $filename );
	    }, 10, 1 );

	    // Prevent Specific Plugins from deactivation, delete, v.v...
	    add_filter( 'plugin_action_links', [ &$this, 'plugin_action_links' ], 11, 4 );
    }

	// ------------------------------------------------------

	/**
	 * @param $rel
	 * @param $link_target
	 *
	 * @return string
	 */
    public function targeted_link_rel( $rel, $link_target ): string {
	    $rel .= ' nofollow';
	    return $rel;
    }

	// ------------------------------------------------------

	/**
	 * @param $actions
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $context
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ): mixed {
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
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function print_footer_scripts(): void {
		?>
        <script>document.documentElement.classList.remove("no-js");
            if (-1 !== navigator.userAgent.toLowerCase().indexOf('msie') || -1 !== navigator.userAgent.toLowerCase().indexOf('trident/')) {
                document.documentElement.classList.add('is-IE');
            }</script>
		<?php

//		if ( file_exists( $passive_events = EHD_PLUGIN_PATH . 'assets/js/plugins/passive-events-fix.js' ) ) {
//			echo '<script>';
//			include $passive_events;
//			echo '</script>';
//		}

		if ( file_exists( $skip_link = EHD_PLUGIN_PATH . 'assets/js/plugins/skip-link-focus-fix.js' ) ) {
			echo '<script>';
			include $skip_link;
			echo '</script>';
		}

		if ( file_exists( $flex_gap = EHD_PLUGIN_PATH . 'assets/js/plugins/flex-gap.js' ) ) {
			echo '<script>';
			include $flex_gap;
			echo '</script>';
		}

		if ( file_exists( $load_scripts = EHD_PLUGIN_PATH . 'assets/js/plugins/load-scripts.js' ) ) {
			echo '<script>';
			include $load_scripts;
			echo '</script>';
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function fixed_archive_canonical(): void {
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
	public function script_loader_tag( string $tag, string $handle, string $src ): string {

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

		// Fontawesome kit
		if ( ( 'fontawesome-kit' == $handle ) && ! preg_match( ":\scrossorigin(=|>|\s):", $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " crossorigin='anonymous'", $tag, 1 );
		}

		// Add script handles to the array
		$str_parsed = apply_filters( 'ehd_defer_script', [] );

		return Helper::lazyScriptTag( $str_parsed, $tag, $handle, $src );
	}

	/** ---------------------------------------- */

	/**
	 * Add style handles to the array below
	 *
	 * @param string $html
	 * @param string $handle
	 *
	 * @return string
	 */
	public function style_loader_tag( string $html, string $handle ): string {
		$styles = apply_filters( 'ehd_defer_style', [] );

		return Helper::lazyStyleTag( $styles, $html, $handle );
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
	public function post_search_by_title( $search, $wp_query ): mixed {
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
//	public function posts_title_filter( $where, $wp_query ) {
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
