<?php

namespace EHD_Cores;

use DirectoryIterator;
use MatthiasMullie\Minify;

use EHD_Cores\Traits\Elementor;
use EHD_Cores\Traits\Plugin;
use EHD_Cores\Traits\WooCommerce;
use EHD_Cores\Traits\Wp;

\defined( 'ABSPATH' ) || die;

/**
 * Helper Class
 *
 * @author WEBHD
 */
final class Helper {

	use Elementor;
	use WooCommerce;
	use Plugin;
	use Wp;

	// --------------------------------------------------

	/**
	 * Find an attribute and add the data as a HTML string.
	 *
	 * @param string $str The HTML string.
	 * @param string $attr The attribute to find.
	 * @param string $content_extra The content that needs to be appended.
	 * @param bool $unique Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public static function appendToAttribute( string $str, string $attr, string $content_extra, bool $unique = false ): string {

		// Check if attribute has single or double quotes.
		// @codingStandardsIgnoreLine
		if ( $start = stripos( $str, $attr . '="' ) ) {
			// Double.
			$quote = '"';

			// @codingStandardsIgnoreLine
		} elseif ( $start = stripos( $str, $attr . "='" ) ) {
			// Single.
			$quote = "'";

		} else {
			// Not found
			return $str;
		}

		// Add quote (for filtering purposes).
		$attr .= '=' . $quote;

		$content_extra = trim( $content_extra );

		if ( $unique ) {

			// Set start pointer to after the quote.
			$start += strlen( $attr );
			// Find first quote after the start pointer.
			$end = strpos( $str, $quote, $start );
			// Get the current content.
			$content = explode( ' ', substr( $str, $start, $end - $start ) );
			// Get our extra content.
			$content_extra = explode( ' ', $content_extra );
			foreach ( $content_extra as $class ) {
				if ( ! empty( $class ) && ! in_array( $class, $content, true ) ) {
					// This one can be added!
					$content[] = $class;
				}
			}
			// Remove duplicates and empty values.
			$content = array_filter( array_unique( $content ) );
			// Convert to space separated string.
			$content = implode( ' ', $content );
			// Get HTML before content.
			$before_content = substr( $str, 0, $start );
			// Get HTML after content.
			$after_content = substr( $str, $end );

			// Combine the string again.
			$str = $before_content . $content . $after_content;

		} else {
			$str = preg_replace(
				'/' . preg_quote( $attr, '/' ) . '/',
				$attr . $content_extra . ' ',
				$str,
				1
			);
		} // End if().

		// Return full HTML string.
		return $str;
	}

	// --------------------------------------------------

	/**
	 * @param $css
	 * @param bool $debug_check
	 *
	 * @return string
	 */
	public static function CSS_Minify( $css, bool $debug_check = true ): string {
		if ( empty( $css ) ) {
			return $css;
		}

		if ( true === $debug_check && WP_DEBUG ) {
			return $css;
		}

		$minifier = new Minify\CSS();
		$minifier->add( $css );

		return $minifier->minify();
	}

	// --------------------------------------------------

	/**
	 * @param ?string $path - full-path dir
	 * @param bool $required_path
	 * @param bool $required_new
	 * @param string $FQN
	 * @param bool $is_widget
	 *
	 * @return void
	 */
	public static function FQN_Load( ?string $path, bool $required_path = false, bool $required_new = false, string $FQN = '\\', bool $is_widget = false ) {
		if ( $path ) {
			$iterator = new DirectoryIterator( $path );
			foreach ( $iterator as $fileInfo ) {
				if ( $fileInfo->isDot() ) {
					continue;
				}

				$filename    = self::fileName( $fileInfo, false );
				$filenameFQN = $FQN . $filename;

				if ( $required_path ) {
					require $path . DIRECTORY_SEPARATOR . $filename . self::fileExtension( $fileInfo, true );
				}

				if ( $required_new ) {
					if ( ! $is_widget ) {
						class_exists( $filenameFQN ) && ( new $filenameFQN() );
					} else {
						class_exists( $filenameFQN ) && register_widget( new $filenameFQN() );
					}
				}
			}
		}
	}

	// -------------------------------------------------------------

	/**
	 * @param       $url
	 * @param array $resolution
	 *
	 * @return string
	 */
	public static function youtubeImage( $url, array $resolution = [] ): string {
		if ( ! $url ) {
			return '';
		}

		if ( ! is_array( $resolution ) || empty( $resolution ) ) {
			$resolution = [
				'sddefault',
				'hqdefault',
				'mqdefault',
				'default',
				'maxresdefault',
			];
		}

		$url_img = self::pixelImg();
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$id      = $vars['v'];
			$url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[0] . '.jpg';
		}

		return $url_img;
	}

	// -------------------------------------------------------------

	/**
	 * @param      $url
	 * @param int $autoplay
	 * @param bool $lazyload
	 * @param bool $control
	 *
	 * @return string|null
	 */
	public static function youtubeIframe( $url, int $autoplay = 0, bool $lazyload = true, bool $control = true ): ?string {
		$autoplay = (int) $autoplay;
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		$home = trailingslashit( network_home_url() );

		if ( isset( $vars['v'] ) ) {
			$idurl     = $vars['v'];
			$_size     = ' width="800px" height="450px"';
			$_autoplay = 'autoplay=' . $autoplay;
			$_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
			if ( $autoplay ) {
				$_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
			}
			$_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . $home . '&' . $_autoplay;
			$_control = '';
			if ( ! $control ) {
				$_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ytb_iframe_' . $idurl;
			}
			$_src  .= $_control . '&html5=1';
			$_src  = ' src="' . $_src . '"';
			$_lazy = '';
			if ( $lazyload ) {
				$_lazy = ' loading="lazy"';
			}

			return '<iframe id="ytb_iframe_' . $idurl . '" title="YouTube Video Player"' . $_lazy . $_auto . $_size . $_src . ' style="border:0"></iframe>';
		}

		return null;
	}

	// -------------------------------------------------------------

	/**
	 * @param bool $img_wrap
	 * @param bool $thumb
	 *
	 * @return string
	 */
	public static function placeholderSrc( bool $img_wrap = true, bool $thumb = true ): string {
		$src = EHD_PLUGIN_URL . 'assets/img/placeholder.png';
		if ( $thumb ) {
			$src = EHD_PLUGIN_URL . 'assets/img/placeholder-320x320.png';
		}
		if ( $img_wrap ) {
			$src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder\">";
		}

		return $src;
	}
}
