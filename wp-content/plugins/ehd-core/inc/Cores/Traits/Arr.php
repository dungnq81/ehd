<?php

namespace EHD\Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait Arr {
	/**
	 * https://wordpress.stackexchange.com/questions/252865/tax-query-terms-ids-using-variable
	 *
	 * @param $string
	 * @param string $separator
	 *
	 * @return string[]
	 */
	public static function separatedToArray( $string, string $separator = ',' ): array {
		// Explode on comma
		$vals = explode( $separator, $string );

		// Trim whitespace
		foreach ( $vals as $key => $val ) {
			$vals[ $key ] = trim( $val );
		}

		// Return empty array if no items found
		// http://php.net/manual/en/function.explode.php#114273
		return array_diff( $vals, [ "" ] );
	}

	/**
	 * @param array $arr1
	 * @param array $arr2
	 *
	 * @return bool
	 */
	public static function compare( array $arr1, array $arr2 ): bool {
		sort( $arr1 );
		sort( $arr2 );

		return $arr1 == $arr2;
	}

	/**
	 * @param mixed $value
	 * @param mixed $callback
	 *
	 * @return array
	 */
	public static function convertFromString( $value, $callback = null ): array {
		if ( is_scalar( $value ) ) {
			$value = array_map( 'trim', explode( ',', (string) $value ) );
		}

		return self::reIndex( array_filter( (array) $value, $callback ) );
	}

	/**
	 * @param mixed $array
	 *
	 * @return array
	 */
	public static function reIndex( $array ): array {
		return self::isIndexedAndFlat( $array ) ? array_values( $array ) : $array;
	}

	/**
	 * @param mixed $array
	 *
	 * @return bool
	 */
	public static function isIndexedAndFlat( $array ): bool {
		if ( ! is_array( $array ) || array_filter( $array, 'is_array' ) ) {
			return false;
		}

		return wp_is_numeric_array( $array );
	}

	/**
	 * @param string $key
	 * @param array $array
	 * @param array $insert_array
	 *
	 * @return array
	 */
	public static function insertAfter( string $key, array $array, array $insert_array ): array {
		return self::insert( $array, $insert_array, $key, 'after' );
	}

	/**
	 * @param mixed $key
	 * @param array $array
	 * @param array $insert_array
	 *
	 * @return array
	 */
	public static function insertBefore( $key, array $array, array $insert_array ): array {
		return self::insert( $array, $insert_array, $key, 'before' );
	}

	/**
	 * @param array $array
	 * @param array $insert_array
	 * @param string $key
	 * @param string $position
	 *
	 * @return array
	 */
	public static function insert( array $array, array $insert_array, string $key, string $position = 'before' ): array {
		$keyPosition = array_search( $key, array_keys( $array ) );
		if ( $keyPosition === false ) {
			return array_merge( $array, $insert_array );
		}

		$keyPosition = (int) $keyPosition;
		if ( 'after' == $position ) {
			++ $keyPosition;
		}
		$result = array_slice( $array, 0, $keyPosition );
		$result = array_merge( $result, $insert_array );

		return array_merge( $result, array_slice( $array, $keyPosition ) );
	}

	/**
	 * @param array $array
	 * @param mixed $value
	 * @param mixed|null $key
	 *
	 * @return array
	 */
	public static function prepend( array &$array, $value, $key = null ): array {
		if ( ! is_null( $key ) ) {
			return $array = [ $key => $value ] + $array;
		}

		array_unshift( $array, $value );

		return $array;
	}
}
