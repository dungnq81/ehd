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
}
