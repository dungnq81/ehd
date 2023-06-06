<?php

namespace EHD_Cores;

/**
 * Htaccess Class
 *
 * @author WEBHD
 */

\defined( 'ABSPATH' ) || die;

abstract class Htaccess {
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
	 * Regular expressions to check.
	 *
	 * @var ?string
	 */
	public ?string $rule_enabled = null;
	public ?string $rule_disabled = null;
	public ?string $rule_disabled_all = null;

	/**
	 * @var ?string
	 */
	public ?string $tpl = null;

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
	 * @param $enabled
	 *
	 * @return Htaccess
	 */
	public function setRuleEnabled( $enabled ) {
		$this->rule_enabled = $enabled;
		return $this;
	}

	/**
	 * @param $disabled
	 *
	 * @return Htaccess
	 */
	public function setRuleDisabled( $disabled ) {
		$this->rule_disabled = $disabled;
		return $this;
	}

	/**
	 * @param $disabled_all
	 *
	 * @return Htaccess
	 */
	public function setRuleDisabledAll( $disabled_all ) {
		$this->rule_disabled_all = $disabled_all;
		return $this;
	}

	/**
	 * @param $tpl
	 *
	 * @return Htaccess
	 */
	public function setTemplate( $tpl ) {
		$this->tpl = $tpl;
		return $this;
	}

	/**
	 * Set the htaccess path.
	 *
	 * @return Htaccess
	 */
	public function setPath() {
		$filepath = $this->filePath();

		// Create the htaccess if it doesn't exists.
		if ( ! $this->wp_filesystem->exists( $filepath ) ) {
			$this->wp_filesystem->touch( $filepath );
		}

		// Bail if it isn't writable.
		if ( $this->wp_filesystem->is_writable( $filepath ) ) {
			$this->path = $filepath;
		}

		return $this;
	}

	/**
	 * Disable the rule and remove it from the htaccess.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function disable(): bool {
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
		$new_content = preg_replace( $this->rule_disabled, '', $content );

		return Helper::doLockWrite( $this->path, $new_content );
	}

	/**
	 * Add rule to htaccess and enable it.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function enable(): bool {
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
			$this->rule_disabled_all,
			'',
			$this->wp_filesystem->get_contents( $this->path )
		);

		// Add the rule and write the new htaccess.
		$new_rule = $this->wp_filesystem->get_contents( $this->tpl );
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
	 * @return false|int
	 */
	public function isEnabled() {
		// Get the content of htaccess.
		$content = $this->wp_filesystem->get_contents( $this->path );

		// Return the result.
		return preg_match( $this->rule_enabled, $content );
	}
}
