<?php

namespace EHD_Cores;

/**
 * Htaccess Class
 *
 * @author SiteGround Security
 *
 * Modified by NTH for eHD theme
 */

\defined( 'ABSPATH' ) || die;

abstract class Abstract_Htaccess {
	/**
	 * WordPress filesystem.
	 *
	 * @var ?object
	 */
	protected $wp_filesystem = null;

	/**
	 * Path to htaccess file.
	 *
	 * @var ?string
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
	public function get_filepath(): string {
		return $this->wp_filesystem->abspath() . '.htaccess';
	}

	/**
	 * Set the htaccess path.
	 *
	 * @return $this
	 */
	public function set_filepath(): Abstract_Htaccess {
		$filepath = $this->get_filepath();

		// Create the htaccess if it doesn't exists.
		if ( ! $this->wp_filesystem->exists( $filepath ) ) {
			$this->wp_filesystem->touch( $filepath );
		}

		// If it is writable.
		if ( $this->wp_filesystem->is_writable( $filepath ) ) {
			$this->path = $filepath;
		}

		return $this;
	}

	/**
	 * Disable the rule and remove it from the htaccess.
	 *
	 * @return $this
	 */
	public function disable() {
		// If htaccess exists and rule is already enabled.
		if ( $this->path && $this->is_enabled() ) {

			// Remove the rule.
			$new_content = preg_replace(
				$this->rules['disabled'],
				'',
				$this->wp_filesystem->get_contents( $this->path )
			);

			$this->lock_and_write( $new_content );
		}

		return $this;
	}

	/**
	 *  Add rule to htaccess and enable it.
	 *
	 * @return $this
	 */
	public function enable() {
		// If htaccess exists and rule is already disabled.
		if ( $this->path && ! $this->is_enabled() ) {

			// Disable all other rules first.
			$content = preg_replace(
				$this->rules['disable_all'],
				'',
				$this->wp_filesystem->get_contents( $this->path )
			);

			// Get the new rule.
			$new_rule = $this->wp_filesystem->get_contents( EHD_PLUGIN_PATH . 'templates/' . $this->template );

			// Add the rule and write the new htaccess.
			$content = $content . PHP_EOL . $new_rule;
			$content = $this->do_replacement( $content );

			$this->lock_and_write( $content );
		}

		return $this;
	}

	/**
	 * Lock file and write something in it.
	 *
	 * @param string $content Content to add.
	 *
	 * @return bool            True on success, false otherwise.
	 */
	protected function lock_and_write( string $content ): bool {
		return Helper::doLockWrite( $this->path, $content );
	}

	/**
	 * Check if rule is enabled.
	 *
	 * @return boolean True if the rule is enabled, false otherwise.
	 */
	public function is_enabled(): bool {
		// Get the content of htaccess.
		$content = $this->wp_filesystem->get_contents( $this->path );

		// Return the result.
		return preg_match( $this->rules['enabled'], $content );
	}

	/**
	 * Do a replacement.
	 *
	 * @param string $content The htaccess content.
	 * @return string
	 */
	public function do_replacement( string $content ): string {
		return $content;
	}

	/**
	 * Toggle specific rule.
	 *
	 * @param  boolean $rule Whether to enable or disable the rules.
	 */
	public function toggle_rules( $rule = 1 ) {
		$this->set_filepath();
		( 1 === intval($rule) ) ? $this->enable() : $this->disable();
	}
}
