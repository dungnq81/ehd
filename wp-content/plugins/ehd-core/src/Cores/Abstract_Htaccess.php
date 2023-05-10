<?php

namespace EHD_Cores;

\defined( 'ABSPATH' ) || die;

/**
 * Htaccess Class
 *
 * @author WEBHD
 */

abstract class Abstract_Htaccess {
	/**
	 * WordPress filesystem.
	 *
	 * @var object WP Filesystem.
	 */
	protected $wp_filesystem = null;

	/**
	 * Path to htaccess file.
	 *
	 * @var string The path to htaccess file.
	 */
	public $path = null;

	/**
	 * The constructor.
	 */
	public function __construct() {

	}
}
