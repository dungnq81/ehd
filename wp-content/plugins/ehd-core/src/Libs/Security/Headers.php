<?php

namespace EHD_Libs\Security;

use EHD_Cores\Abstract_Htaccess;
use EHD_Cores\Helper;
use WP_HTTP_Response;

class Headers extends Abstract_Htaccess {

	/**
	 * The path to the htaccess template.
	 *
	 * @var string
	 */
	public string $template = 'xss-headers.tpl';

	/**
	 * Regular expressions to check if the rules are enabled.
	 *
	 * @var array Regular expressions to check if the rules are enabled.
	 */
	public array $rules = [
		'enabled'     => '/\#\s+eHD\s+XSS\s+Header/si',
		'disabled'    => '/\#\s+eHD\s+XSS\s+Header(.+?)\#\s+eHD\s+XSS\s+Header\s+END(\n)?/ims',
		'disable_all' => '/\#\s+eHD\s+XSS\s+Header(.+?)\#\s+eHD\s+XSS\s+Header\s+END(\n)?/ims',
	];

	/**
	 * The security headers array, containing the specific options and headers.
	 *
	 * @var array
	 */
	public array $headers = [
		'xss_protection' => [
			'X-Content-Type-Options' => 'nosniff',
			'X-XSS-Protection'       => '1; mode=block',
		],
	];

	/**
	 * The security headers that need to be added.
	 *
	 * @var array
	 */
	public array $security_headers;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->security_headers = $this->prepare_headers();
		parent::__construct();
	}

	/**
	 * Set the necessary security headers.
	 *
	 * @param array $headers Associative array of headers to be sent.
	 */
	public function set_security_headers( array $headers ): array {
		// Bail if no headers to add.
		if ( empty( $this->security_headers ) ) {
			return $headers;
		}

		// Loop and modify the headers.
		foreach ( $this->security_headers as $header_key => $header_value ) {
			$headers[ $header_key ] = $header_value;
		}

		// Return the headers array.
		return $headers;
	}

	/**
	 * Set the necessary security headers for the rest api.
	 *
	 * @param WP_HTTP_Response $result Result to send to the client. Usually a WP_REST_Response.
	 */
	public function set_rest_security_headers( WP_HTTP_Response $result ): WP_HTTP_Response {
		// Return result if no headers to add.
		if ( empty( $this->security_headers ) ) {
			return $result;
		}

		// Add the specified headers.
		foreach ( $this->security_headers as $header_key => $header_value ) {
			$result->header( $header_key, $header_value );
		}

		// Return the result to the user.
		return $result;
	}

	/**
	 * Prepare the headers.
	 *
	 * @return array $prepared_headers The security headers we need to add.
	 */
	public function prepare_headers(): array {
		$headers = [];

		// Loop trough all headers.
		foreach ( $this->headers as $header_option => $security_headers ) {

			// Check if the security optimization is enabled.
			$security_options = Helper::getOption( 'security__options', false, false );
			$advanced_xss_protection = $security_options['advanced_xss_protection'] ?? '';
			if ( 1 !== (int) $advanced_xss_protection ) {
				continue;
			}

			// Add the header to the array if optimization enabled.
			foreach ( $security_headers as $header_key => $header_value ) {
				$headers[ $header_key ] = $header_value;
			}
		}

		return $headers;
	}
}
