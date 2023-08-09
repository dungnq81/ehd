<?php

namespace EHD_Cores\Traits;

use IntlBreakIterator;

\defined( 'ABSPATH' ) || die;

trait Str {
	/**
	 * https://github.com/cofirazak/phpMissingFunctions
	 *
	 * Replicates php's ucfirst() function with multibyte support.
	 *
	 * @param string $str The string being converted.
	 * @param null|string $encoding Optional encoding parameter is the character encoding.
	 *                              If it is omitted, the internal character encoding value will be used.
	 *
	 * @return string The input string with first character uppercase.
	 */
	public static function mbUcFirst( string $str, string $encoding = null ): string {
		if ( is_null( $encoding ) ) {
			$encoding = mb_internal_encoding();
		}

		return mb_strtoupper( mb_substr( $str, 0, 1, $encoding ), $encoding ) . mb_substr( $str, 1, null, $encoding );
	}

	/**
	 * @param $content
	 *
	 * @return array|string|string[]
	 */
	public static function removeEmptyP( $content ) {
		return \str_replace( '<p></p>', '', $content );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function camelCase( string $string ): string {
		$string = ucwords( str_replace( [ '-', '_' ], ' ', trim( $string ) ) );

		return str_replace( ' ', '', $string );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function dashCase( string $string ): string {
		return str_replace( '_', '-', self::snakeCase( $string ) );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function snakeCase( string $string ): string {
		if ( ! ctype_lower( $string ) ) {
			$string = preg_replace( '/\s+/u', '', $string );
			$string = preg_replace( '/(.)(?=[A-Z])/u', '$1_', $string );
			$string = mb_strtolower( $string, 'UTF-8' );
		}

		return str_replace( '-', '_', $string );
	}

	/**
	 * @param int $length
	 *
	 * @return string
	 */
	public static function random( int $length = 8 ): string {
		$text = base64_encode( wp_generate_password() );

		return substr( str_replace( [ '/', '+', '=' ], '', $text ), 0, $length );
	}

	/**
	 * @param string $string
	 * @param string $prefix
	 * @param string|null $trim
	 *
	 * @return string
	 */
	public static function prefix( $string, string $prefix, $trim = null ): string {
		if ( '' === $string ) {
			return $string;
		}
		if ( null === $trim ) {
			$trim = $prefix;
		}

		return $prefix . trim( self::removePrefix( $string, $trim ) );
	}

	/**
	 * @param string $prefix
	 * @param string $string
	 *
	 * @return string
	 */
	public static function removePrefix( string $string, string $prefix ): string {
		return self::startsWith( $prefix, $string )
			? substr( $string, strlen( $prefix ) )
			: $string;
	}

	/**
	 * @param string|string[] $needles
	 * @param string $haystack
	 *
	 * @return bool
	 */
	public static function startsWith( $needles, $haystack ): bool {
		$needles = (array) $needles;
		foreach ( $needles as $needle ) {
			if ( str_starts_with( $haystack, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string|string[] $needles
	 * @param string $haystack
	 *
	 * @return bool
	 */
	public static function endsWith( $needles, $haystack ): bool {
		$needles = (array) $needles;
		foreach ( $needles as $needle ) {
			if ( str_ends_with( $haystack, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $string
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function suffix( $string, $suffix ): string {
		if ( ! self::endsWith( $suffix, $string ) ) {
			return $string . $suffix;
		}

		return $string;
	}

	/**
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 *
	 * @return string
	 */
	public static function replaceFirst( $search, $replace, $subject ): string {
		if ( $search == '' ) {
			return $subject;
		}
		$position = strpos( $subject, $search );
		if ( $position !== false ) {
			return substr_replace( $subject, $replace, $position, strlen( $search ) );
		}

		return $subject;
	}

	/**
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 *
	 * @return string
	 */
	public static function replaceLast( string $search, string $replace, string $subject ): string {
		$position = strrpos( $subject, $search );
		if ( '' !== $search && false !== $position ) {
			return substr_replace( $subject, $replace, $position, strlen( $search ) );
		}

		return $subject;
	}

	/**
	 * Strpos over an array.
	 *
	 * @param     $haystack
	 * @param     $needles
	 * @param int $offset
	 *
	 * @return bool
	 */
	public static function strposOffset( $haystack, $needles, int $offset = 0 ): bool {
		if ( ! is_array( $needles ) ) {
			$needles = [ $needles ];
		}
		foreach ( $needles as $query ) {
			if ( strpos( $haystack, $query, $offset ) !== false ) {
				// stop on first true result.
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function titleCase( string $string ): string {
		$value = str_replace( [ '-', '_' ], ' ', $string );

		return mb_convert_case( $value, MB_CASE_TITLE, 'UTF-8' );
	}

	/**
	 * Keywords
	 *
	 * Takes multiple words separated by spaces and changes them to keywords
	 * Makes sure the keywords are separated by a comma followed by a space.
	 *
	 * @param string $str The keywords as a string, separated by whitespace.
	 *
	 * @return string The list of keywords in a comma separated string form.
	 */
	public static function keyWords( string $str ): string {
		$str = preg_replace( '/(\v|\s){1,}/u', ' ', $str );

		return preg_replace( '/[\s]+/', ', ', trim( $str ) );
	}

	/**
	 * @param string $value
	 * @param int $length
	 * @param string $end
	 *
	 * @return string
	 */
	public static function truncate( $value, $length, string $end = '' ): string {
		return mb_strwidth( $value, 'UTF-8' ) > $length
			? mb_substr( $value, 0, $length, 'UTF-8' ) . $end
			: $value;
	}

	/**
	 * @param $string
	 * @param string $replace
	 * @param bool $remove_js
	 * @param bool $flatten
	 * @param null $allowed_tags
	 *
	 * @return string
	 */
	public static function stripAllTags( $string, string $replace = ' ', bool $remove_js = true, bool $flatten = false, $allowed_tags = null ): string {

		if ( ! is_scalar( $string ) ) {
			return '';
		}

		if ( true === $remove_js ) {
			$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', $replace, $string );
		}

		$string = strip_tags( $string, $allowed_tags );

		if ( true === $flatten ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}

		return trim( $string );
	}

	/**
	 * @param $string
	 * @param bool $strip_tags
	 * @param string $replace
	 *
	 * @return array|string|string[]|null
	 */
	public static function stripSpace( $string, bool $strip_tags = true, string $replace = '' ) {
		if ( true === $strip_tags ) {
			$string = strip_tags( $string );
		}

		$string = preg_replace(
			'/(\v|\s){1,}/u',
			$replace,
			$string
		);

		$string = preg_replace( '~\x{00a0}~', $replace, $string );

		return preg_replace( '/\s+/', $replace, $string );
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function normalize( string $text ): string {
		$allowedHtml         = wp_kses_allowed_html();
		$allowedHtml['mark'] = []; // allow using the <mark> tag to highlight text

		$text = wp_kses( $text, $allowedHtml );
		$text = strip_shortcodes( $text );
		$text = excerpt_remove_blocks( $text ); // just in case...
		$text = convert_smilies( $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		return preg_replace( '/(\v){2,}/u', '$1', $text );
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function text( string $text ): string {
		$text = static::normalize( $text );
		$text = nl2br( $text );
		$text = wptexturize( $text );

		// replace all multiple-space and carriage return characters with a space
		return preg_replace( '/(\v|\s){1,}/u', ' ', $text );
	}

	/**
	 * @param string $text
	 * @param int $limit
	 *
	 * @return int
	 */
	public static function excerptIntlSplit( string $text, int $limit ): int {
		$words = \IntlRuleBasedBreakIterator::createWordInstance( '' );
		$words->setText( $text );
		$count = 0;
		foreach ( $words as $offset ) {
			if ( IntlBreakIterator::WORD_NONE === $words->getRuleStatus() ) {
				continue;
			}
			++ $count;
			if ( $count != $limit ) {
				continue;
			}

			return $offset;
		}

		return strlen( $text );
	}

	/**
	 * @param string $text
	 * @param int $limit
	 *
	 * @return int
	 */
	protected static function excerptSplit( string $text, int $limit ): int {
		if ( str_word_count( $text, 0 ) > $limit ) {
			$words = array_keys( str_word_count( $text, 2 ) );

			return $words[ $limit ];
		}

		return strlen( $text );
	}

	/**
	 * @param string $text
	 * @param int $limit
	 * @param bool $splitWords
	 * @param string $showMore
	 *
	 * @return string
	 */
	public static function excerpt( string $text, int $limit = 55, bool $splitWords = true, string $showMore = '...' ): string {

		$text = strip_tags( $text );

		$text        = static::normalize( $text );
		$splitLength = $limit;

		if ( $splitWords ) {
			$splitLength = extension_loaded( 'intl' )
				? static::excerptIntlSplit( $text, $limit )
				: static::excerptSplit( $text, $limit );
		}

		$hiddenText = mb_substr( $text, $splitLength );
		if ( ! empty( $hiddenText ) ) {
			$text = ltrim( mb_substr( $text, 0, $splitLength ) ) . $showMore;
		}

		$text = nl2br( $text );
		$text = wptexturize( $text );

		// replace all multiple-space and carriage return characters with a space
		return preg_replace( '/(\v|\s){1,}/u', ' ', $text );
	}
}
