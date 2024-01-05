<?php

namespace EHD_Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait Base {

	// --------------------------------------------------

	/**
	 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
	 *
	 * @return boolean
	 */
	public static function is_mobile(): bool {
		if ( function_exists( 'wp_is_mobile' ) ) {
			return wp_is_mobile();
		}

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif ( @strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices (all iPhone, iPad, etc.)
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
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
	public static function runClosure( $value ): mixed {
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
	public static function ifEmpty( $value, $fallback, bool $strict = false ): mixed {
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
	public static function ifTrue( $condition, $ifTrue, $ifFalse = null ): mixed {
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
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param string $email the email address
	 * @param string $title the link title
	 * @param string $attributes any attributes
	 *
	 * @return string|null
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
