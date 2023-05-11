<?php

namespace EHD_Libs;

use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Htaccess Class
 *
 * @author WEBHD
 */

class Htaccess {
	/**
	 * WordPress filesystem.
	 *
	 * @var object WP Filesystem.
	 */
	protected $wp_filesystem = null;

	/**
	 * Path to htaccess file.
	 *
	 * @var ?string The path to htaccess file.
	 */
	public ?string $path = null;

	/**
	 * The constructor.
	 */
	public function __construct() {
		if ( null === $this->wp_filesystem ) {
			$this->wp_filesystem = Helper::wpFileSystem();
		}
	}

	/**
	 * Get the filepath to the htaccess.
	 *
	 * @return string Path to the htaccess.
	 */
	public function filePath(): string {
		return $this->wp_filesystem->abspath() . '.htaccess';
	}

	/**
	 * Set the htaccess path.
	 *
	 * @return false|string
	 */
	public function setPath() {
		$filepath = $this->filePath();

		// Create the htaccess if it doesn't exists.
		if ( ! $this->wp_filesystem->exists( $filepath ) ) {
			$this->wp_filesystem->touch( $filepath );
		}

		// Bail if it isn't writable.
		if ( ! $this->wp_filesystem->is_writable( $filepath ) ) {
			return false;
		}

		return $this->path = $filepath;
	}

	/**
	 * Disable the rule and remove it from the htaccess.
	 *
	 * @param string $rules
	 * @return bool True on success, false otherwise.
	 */
	public function disable( string $rules = '' ): bool {
		// Bail if htaccess doesn't exists.
		if ( empty( $this->path ) ) {
			return false;
		}

		// Bail if the rule is already disabled.
		if ( ! $this->isEnabled() ) {
			return true;
		}

		// Get the content of htaccess.
		$content = $this->wp_filesystem->get_contents( $this->path );

		// Remove the rule.
		$new_content = preg_replace( $rules, '', $content );

		return Helper::doLockWrite( $this->path, $new_content );
	}

	/**
	 * Add rule to htaccess and enable it.
	 *
	 * @param string $old_rule
	 * @param string $new_rule
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function enable( string $old_rule = '', string $new_rule = ''): bool {
		// Bail if htaccess doesn't exists.
		if ( empty( $this->path ) ) {
			return false;
		}

		// Bail if the rule is already enabled.
		if ( $this->isEnabled() ) {
			return true;
		}

		// Disable old rules first.
		$content = preg_replace(
			$old_rule,
			'',
			$this->wp_filesystem->get_contents( $this->path )
		);

		// Add the rule and write the new htaccess.
		$content = $new_rule . PHP_EOL . $content;

		// Return the result.
		return Helper::doLockWrite( $this->path, $content );
	}

	/**
	 * Toggle specific rule.
	 *
	 * @param  boolean $type Whether to enable or disable the rules.
	 */
	public function toggleRules( $type = 1 ): bool {
		$this->setPath();
		return ( 1 === $type ) ? $this->enable() : $this->disable();
	}

	/**
	 * @param string $rules
	 *
	 * @return false|int
	 */
	public function isEnabled( string $rules = '' ) {
		// Get the content of htaccess.
		$content = $this->wp_filesystem->get_contents( $this->path );

		// Return the result.
		return preg_match( $rules, $content );
	}
}
