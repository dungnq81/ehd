<?php

namespace EHD_Libs\Optimizer;

use EHD_Cores\Abstract_Htaccess;
use EHD_Cores\Helper;

\defined( 'ABSPATH' ) || die;

class Ssl extends Abstract_Htaccess {

	/**
	 * The path to the htaccess template.
	 *
	 * @var string
	 */
	public string $template = 'ssl.tpl';

	/**
	 * Regular expressions to check if the rules are enabled.
	 *
	 * @var array
	 */
	public array $rules = [
		'enabled'     => '/Https\s+Forced/si',
		'disabled'    => '/\#\s+Https\s+Forced(.+?)\#\s+Https\s+Forced\s+END(\n)?/ims',
		'disable_all' => '/\#\s+Https\s+Forced(.+?)\#\s+Https\s+Forced\s+END(\n)?/ims',
	];

	/**
	 * Disable the rule and remove it from the htaccess.
	 *
	 * @return $this
	 */
	public function disable(): static {
		// Switch the protocol in database.
		$protocol_switched = $this->switch_protocol( false );

		parent::disable();

		return $this;
	}

	/**
	 *  Add rule to htaccess and enable it.
	 *
	 * @return $this
	 */
	public function enable(): static {
		// Switch the protocol in database.
		$protocol_switched = $this->switch_protocol( true );

		// Add rule to htaccess for single sites.
		if ( ! is_multisite() ) {
			parent::enable();
		}

		return $this;
	}

	/**
	 * Change the url protocol.
	 *
	 * @param bool $ssl
	 *
	 * @return bool     The result.
	 */
	private function switch_protocol( bool $ssl = false ): bool {
		$from = ( true === $ssl ) ? 'http' : 'https';
		$to   = ( true === $ssl ) ? 'https' : 'http';

		// Strip the protocol from site url.
		$site_url_without_protocol = preg_replace( '#^https?#', '', Helper::getOption( 'siteurl' ) );

		// Build the command.
		$command = sprintf(
			"wp search-replace '%s' '%s' --all-tables",
			$from . $site_url_without_protocol,
			$to . $site_url_without_protocol
		);

		// Execute the command.
		exec(
			$command,
			$output,
			$status
		);

		// Check for errors during the import.
		if ( ! empty( $status ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Creates an array of insecure links that should be https and an array of secure links to replace with
	 *
	 * @return array
	 */
	public function get_url_list(): array {
		$home = Helper::home();

		// Build the search links.
		$search = [
			str_replace( 'https://', 'http://', $home ),
			"src='http://",
			'src="http://',
		];

		return [
			'search'  => $search, // The search links.
			'replace' => str_replace( 'http://', 'https://', $search ), // The replace links.
		];
	}

	/**
	 * Replace all insecure links before the page is sent to the visitor's browser.
	 *
	 * @param string $content The page content.
	 *
	 * @return string Modified content.
	 */
	public function replace_insecure_links( string $content ): string {
		// Get the url list.
		$urls = $this->get_url_list();

		// now replace these links.
		$content = str_replace( $urls['search'], $urls['replace'], $content );

		// Replace all http links except hyperlinks
		// All tags with src attr are already fixed by str_replace.
		$pattern = [
			'/url\([\'"]?\K(http:\/\/)(?=[^)]+)/i',
			'/<link([^>]*?)href=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
			'/<meta property="og:image" .*?content=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
			'/<form [^>]*?action=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
		];

		// Return modified content.
		return preg_replace( $pattern, 'https://', $content );
	}
}
