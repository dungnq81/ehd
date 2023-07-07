<?php

namespace EHD_Cores\Traits;

use Vectorface\Whip\Whip;

\defined( 'ABSPATH' ) || die;

trait Base {
	public static function htAccess(): bool {
		if ( ! isset( $_SERVER['HTACCESS'] ) ) {
			return false;
		}

		return true;
	}

	// --------------------------------------------------

	/**
	 * @param string $version
	 *
	 * @return  bool
	 */
	public static function isPhp( string $version = '5.0.0' ): bool {
		static $phpVer;
		if ( ! isset( $phpVer[ $version ] ) ) {
			$phpVer[ $version ] = ! ( ( version_compare( PHP_VERSION, $version ) < 0 ) );
		}

		return $phpVer[ $version ];
	}

	// --------------------------------------------------

	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public static function isInteger( $input ): bool {
		return ( ctype_digit( strval( $input ) ) );
	}

	// --------------------------------------------------

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function runClosure( $value ) {
		if ( $value instanceof \Closure || ( is_array( $value ) && is_callable( $value ) ) ) {
			return call_user_func( $value );
		}

		return $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 * @param mixed $fallback
	 * @param bool $strict
	 *
	 * @return mixed
	 */
	public static function ifEmpty( $value, $fallback, bool $strict = false ) {
		$isEmpty = $strict ? empty( $value ) : self::isEmpty( $value );

		return $isEmpty ? $fallback : $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $condition
	 * @param mixed $ifTrue
	 * @param mixed $ifFalse
	 *
	 * @return mixed
	 */
	public static function ifTrue( $condition, $ifTrue, $ifFalse = null ) {
		return $condition ? self::runClosure( $ifTrue ) : self::runClosure( $ifFalse );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function isEmpty( $value ): bool {
		if ( is_string( $value ) ) {
			return trim( $value ) === '';
		}

		return ! is_numeric( $value ) && ! is_bool( $value ) && empty( $value );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function notEmpty( $value ): bool {
		return ! empty( $value );
	}

	// --------------------------------------------------

	/**
	 * @param array|string $array
	 *
	 * @return array
	 */
	public static function removeEmptyValues( $array = [] ): array {

		if ( ! is_array( $array ) && $array ) {
			return [ $array ];
		}

		if ( empty( $array ) ) {
			return __return_empty_array();
		}

		$result = [];
		foreach ( $array as $key => $value ) {
			if ( self::isEmpty( $value ) ) {
				continue;
			}

			$result[ $key ] = self::ifTrue( ! is_array( $value ), $value, function () use ( $value ) {
				return self::removeEmptyValues( $value );
			} );
		}

		return $result;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 * @param string|int $min
	 * @param string|int $max
	 *
	 * @return bool
	 */
	public static function inRange( $value, $min, $max ): bool {
		$inRange = filter_var( $value, FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => intval( $min ),
				'max_range' => intval( $max ),
			],
		] );

		return false !== $inRange;
	}

	// --------------------------------------------------

	/**
	 * @return string
	 */
	public static function getIpAddress(): string {
		$whip          = new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR | Whip::PROXY_HEADERS | Whip::INCAPSULA_HEADERS );
		$clientAddress = $whip->getValidIpAddress();

		if ( false !== $clientAddress ) {
			return preg_replace( '/^::1$/', '127.0.0.1', $clientAddress );
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	// --------------------------------------------------

	/**
	 * Search an IP range for a given IP.
	 *
	 * @param string $ip
	 * @param string $range
	 * @param string $separator
	 *
	 * @return bool
	 */
	public static function ipInRange( string $ip, string $range, string $separator = '/' ): bool {
		$range = explode( $separator, $range );

		// Get the netmask from the range.
		$netmask = $range[1];

		// Get the base range ip and convert to long.
		$start_ip = ip2long( $range[0] );

		// Get the count of the possible IPs.
		$ip_count = 1 << ( 32 - $netmask );

		// Iterate through all possible IPs and return true on match, false if not found.
		for ( $i = 0; $i < $ip_count - 1; $i ++ ) {
			if ( long2ip( ( $start_ip + $i ) ) === $ip ) {
				return true;
			}
		}

		return false;
	}

	// --------------------------------------------------

	/**
	 * @param       $url
	 * @param array $resolution
	 *
	 * @return string
	 */
	public static function youtubeImage( $url, array $resolution = [] ): string {
		if ( ! $url ) {
			return '';
		}

		if ( ! is_array( $resolution ) || empty( $resolution ) ) {
			$resolution = [
				'sddefault',
				'hqdefault',
				'mqdefault',
				'default',
				'maxresdefault',
			];
		}

		$url_img = self::pixelImg();
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$id      = $vars['v'];
			$url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[0] . '.jpg';
		}

		return $url_img;
	}

	// --------------------------------------------------

	/**
	 * @param string $img
	 *
	 * @return string
	 */
	public static function pixelImg( string $img = '' ): string {
		if ( file_exists( $img ) ) {
			return $img;
		}

		return "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
	}

	// --------------------------------------------------

	/**
	 * @param      $url
	 * @param int $autoplay
	 * @param bool $lazyload
	 * @param bool $control
	 *
	 * @return string|null
	 */
	public static function youtubeIframe( $url, int $autoplay = 0, bool $lazyload = true, bool $control = true ): ?string {
		$autoplay = (int) $autoplay;
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		$home = trailingslashit( network_home_url() );

		if ( isset( $vars['v'] ) ) {
			$idurl     = $vars['v'];
			$_size     = ' width="800px" height="450px"';
			$_autoplay = 'autoplay=' . $autoplay;
			$_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
			if ( $autoplay ) {
				$_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
			}
			$_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . $home . '&' . $_autoplay;
			$_control = '';
			if ( ! $control ) {
				$_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ng_video_iframe_' . $idurl;
			}
			$_src  .= $_control . '&html5=1';
			$_src  = ' src="' . $_src . '"';
			$_lazy = '';
			if ( $lazyload ) {
				$_lazy = ' loading="lazy"';
			}

			return '<iframe id="ytb_iframe_' . $idurl . '" title="YouTube Video Player"' . $_lazy . $_auto . $_size . $_src . ' style="border:0"></iframe>';
		}

		return null;
	}

	// --------------------------------------------------

	/**
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param string $email the email address
	 * @param string $title the link title
	 * @param string|null|array $attributes any attributes
	 *
	 * @return string
	 */
	public static function safeMailTo( string $email, string $title = '', $attributes = '' ): ?string {
		if ( ! $email || ! is_email( $email ) ) {
			return null;
		}

		if ( trim( $title ) === '' ) {
			$title = $email;
		}

		$x = str_split( '<a href="mailto:', 1 );

		for ( $i = 0, $l = strlen( $email ); $i < $l; $i ++ ) {
			$x[] = '|' . ord( $email[ $i ] );
		}

		$x[] = '"';

		if ( $attributes !== '' ) {
			if ( is_array( $attributes ) ) {
				foreach ( $attributes as $key => $val ) {
					$x[] = ' ' . $key . '="';
					for ( $i = 0, $l = strlen( $val ); $i < $l; $i ++ ) {
						$x[] = '|' . ord( $val[ $i ] );
					}
					$x[] = '"';
				}
			} else {
				for ( $i = 0, $l = mb_strlen( $attributes ); $i < $l; $i ++ ) {
					$x[] = mb_substr( $attributes, $i, 1 );
				}
			}
		}

		$x[] = '>';

		$temp = [];
		for ( $i = 0, $l = strlen( $title ); $i < $l; $i ++ ) {
			$ordinal = ord( $title[ $i ] );

			if ( $ordinal < 128 ) {
				$x[] = '|' . $ordinal;
			} else {
				if ( empty( $temp ) ) {
					$count = ( $ordinal < 224 ) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if ( count( $temp ) === $count ) // @phpstan-ignore-line
				{
					$number = ( $count === 3 ) ? ( ( $temp[0] % 16 ) * 4096 ) + ( ( $temp[1] % 64 ) * 64 ) + ( $temp[2] % 64 ) : ( ( $temp[0] % 32 ) * 64 ) + ( $temp[1] % 64 );
					$x[]    = '|' . $number;
					$count  = 1;
					$temp   = [];
				}
			}
		}

		$x[] = '<';
		$x[] = '/';
		$x[] = 'a';
		$x[] = '>';

		$x = array_reverse( $x );

		// improve obfuscation by eliminating newlines & whitespace
		$output = '<script type="text/javascript">'
		          . 'var l=new Array();';

		foreach ( $x as $i => $value ) {
			$output .= 'l[' . $i . "] = '" . $value . "';";
		}

		return $output . ( 'for (var i = l.length-1; i >= 0; i=i-1) {'
		                   . "if (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");"
		                   . 'else document.write(unescape(l[i]));'
		                   . '}'
		                   . '</script>' );
	}

	// --------------------------------------------------

	/**
	 * @return string[]
	 */
	public static function getSqlOperators(): array {
		$compare                = self::getMetaCompare();
		$compare['IS NULL']     = 'IS NULL';
		$compare['IS NOT NULL'] = 'IS NOT NULL';

		return $compare;
	}

	/**
	 * @return string[]
	 */
	public static function getMetaCompare(): array {
		// meta_compare (string) - Operator to test the 'meta_value'. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP' or 'RLIKE'. Default value is '='.
		return [
			'='           => '=',
			'>'           => '&gt;',
			'>='          => '&gt;=',
			'<'           => '&lt;',
			'<='          => '&lt;=',
			'!='          => '!=',
			'LIKE'        => 'LIKE',
			'RLIKE'       => 'RLIKE',
			'NOT LIKE'    => 'NOT LIKE',
			'IN'          => 'IN (...)',
			'NOT IN'      => 'NOT IN (...)',
			'BETWEEN'     => 'BETWEEN',
			'NOT BETWEEN' => 'NOT BETWEEN',
			'EXISTS'      => 'EXISTS',
			'NOT EXISTS'  => 'NOT EXISTS',
			'REGEXP'      => 'REGEXP',
			'NOT REGEXP'  => 'NOT REGEXP'
		];
	}
}
