<?php

namespace EHD_Libs\Optimizer;

use EHD_Cores\Abstract_Htaccess;

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
}
