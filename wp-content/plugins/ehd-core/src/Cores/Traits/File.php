<?php

namespace EHD_Cores\Traits;

\defined('ABSPATH') || die;

trait File
{
	/**
	 * @return mixed
	 */
	public static function wpFileSystem() {
		global $wp_filesystem;

		// Initialize the WP filesystem, no more using 'file-put-contents' function.
		// Front-end only. In the back-end; its already included
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Checks if the current request is a WP REST API request.
	 *
	 * Case #1: After WP_REST_Request initialisation
	 * Case #2: Support "plain" permalink settings
	 * Case #3: URL Path begins with wp-json/ (your REST prefix). Also supports WP installations in subfolders
	 *
	 * @return bool True if it's rest request, false otherwise.
	 */
	public static function isRest(): bool {
		$prefix = rest_get_url_prefix();
		if (
			defined( 'REST_REQUEST' ) && REST_REQUEST ||
			(
				isset( $_GET['rest_route'] ) &&
				0 === @strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 )
			)
		) {
			return true;
		}

		$rest_url    = wp_parse_url( site_url( $prefix ) );
		$current_url = wp_parse_url( add_query_arg( [] ) );

		return 0 === @strpos( $current_url['path'], $rest_url['path'], 0 );
	}

	/**
	 * @param $path
	 *
	 * @return true
	 */
	public static function fileCreate( $path ): bool {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if the file already exists.
		if ( $wp_filesystem->exists( $path ) ) {
			return true;
		}

		// Create the file.
		return $wp_filesystem->touch( $path );
	}

	/**
	 * Reads entire file into a string
	 *
	 * @param string $file Name of the file to read.
	 * @return string|false Read data on success, false on failure.
	 */
	public static function fileRead( string $file ) {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if we are unable to create the file.
		if ( false === self::fileCreate( $file ) ) {
			return null;
		}

		// Read file
		return $wp_filesystem->get_contents( $file );
	}

	/**
	 * Update a file
	 *
	 * @param string $path    Full path to the file
	 * @param string $content File content
	 */
	public static function fileUpdate( string $path, string $content = '' ) {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if we are unable to create the file.
		if ( false === self::fileCreate( $path ) ) {
			return;
		}

		// Add the new content into the file.
		$wp_filesystem->put_contents( $path, $content );
	}

    /**
     * @param      $filename
     * @param bool $include_dot
     *
     * @return string
     */
	public static function fileExtension( $filename, bool $include_dot = false ): string {
		$dot = '';
		if ( $include_dot === true ) {
			$dot = '.';
		}

		return $dot . strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	}

    /**
     * @param      $filename
     * @param bool $include_ext
     *
     * @return string
     */
	public static function fileName( $filename, bool $include_ext = false ): string {
		return $include_ext ? pathinfo(
			                      $filename,
			                      PATHINFO_FILENAME
		                      ) . self::fileExtension( $filename ) : pathinfo( $filename, PATHINFO_FILENAME );
	}

    /**
     * @param $dir
     * @param bool $hidden
     * @param $files
     *
     * @return array
     */
	public static function arrayDir( $dir, bool $hidden = false, $files = true ): array {
		$result = [];
		$dirs   = scandir( $dir );

		foreach ( $dirs as $key => $value ) {
			if ( ! in_array( $value, [ '.', '..' ] ) ) {
				if ( is_dir( $dir . DIRECTORY_SEPARATOR . $value ) ) {
					$result[ $value ] = self::arrayDir( $dir . DIRECTORY_SEPARATOR . $value, $hidden, $files );
				}
				elseif ( $files ) {
					// hidden file
					if ( ! str_starts_with( $value, '.' ) ) {
						$result[] = $value;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * @param $dirname
	 *
	 * @return bool
	 */
	public static function isEmptyDir( $dirname ): bool {
		if ( ! is_dir( $dirname ) ) {
			return false;
		}

		$dirs = scandir( $dirname );
		foreach ( $dirs as $file ) {
			if ( ! in_array( $file, [ '.', '..', '.svn', '.git' ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Lock file and write something in it.
	 *
	 * @param string $content Content to add.
	 *
	 * @return bool    True on success, false otherwise.
	 */
	public static function doLockWrite( $path, string $content = '' ): bool {
		$fp = fopen( $path, 'w+' );

		if ( flock( $fp, LOCK_EX ) ) {
			fwrite( $fp, $content );
			flock( $fp, LOCK_UN );
			fclose( $fp );
			return true;
		}

		fclose( $fp );
		return false;
	}
}
