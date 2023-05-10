<?php

namespace EHD_Settings;

use EHD_Cores\Helper;
use PHPMailer\PHPMailer\Exception;

/**
 * SMTP Class
 *
 * @author EHD
 */

\defined('ABSPATH') || die;

final class SMTP {
	public function __construct() {

		/** SMTP Settings */
		if ( Helper::smtpConfigured() ) {
			add_action( 'phpmailer_init', [ &$this, 'setup_phpmailer_init' ], 11 );
		}
	}

	/**
	 * @param $phpmailer
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setup_phpmailer_init( $phpmailer ): void {
		Helper::PHPMailerInit( $phpmailer, 'smtp__options' );
	}
}