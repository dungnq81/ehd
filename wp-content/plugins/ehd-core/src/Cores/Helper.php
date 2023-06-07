<?php

namespace EHD_Cores;

use DirectoryIterator;

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
	 * @param string $path
	 * @param string $FQN
	 * @param bool $required_path
	 * @param bool $required_new
	 *
	 * @return void
	 */
	public static function FQN_Load( string $path, string $FQN = '\\', bool $required_path = false, bool $required_new = true ) {
		if ( $path ) {
			$iterator = new DirectoryIterator( $path );
			foreach ( $iterator as $fileInfo ) {
				if ( $fileInfo->isDot() ) {
					continue;
				}

				$filename    = self::fileName( $fileInfo, false );
				$filenameFQN = $FQN . $filename;

				if ( $required_path ) {
					require $path . DIRECTORY_SEPARATOR . $filename;
				}

				if ( $required_new ) {
					class_exists( $filenameFQN ) && ( new $filenameFQN() );
				}
			}
		}
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
