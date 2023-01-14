<?php

namespace EHD\Cores\Traits;

\defined('ABSPATH') || die;

trait Arr
{
    /**
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    public static function compare(array $arr1, array $arr2)
    {
        sort($arr1);
        sort($arr2);
        return $arr1 == $arr2;
    }

    /**
     * @param mixed $value
     * @param mixed $callback
     *
     * @return array
     */
    public static function convertFromString(mixed $value, mixed $callback = null)
    {
        if (is_scalar($value)) {
            $value = array_map('trim', explode(',', (string) $value));
        }

        return self::reIndex(array_filter((array) $value, $callback));
    }

    /**
     * @param mixed $array
     * @return array
     */
    public static function reIndex(mixed $array)
    {
        return self::isIndexedAndFlat($array) ? array_values($array) : $array;
    }

    /**
     * @param mixed $array
     * @return bool
     */
    public static function isIndexedAndFlat(mixed $array)
    {
        if (!is_array($array) || array_filter($array, 'is_array')) {
            return false;
        }
        return wp_is_numeric_array($array);
    }

    /**
     * @param string $key
     * @param array  $array
     * @param array  $insert_array
     *
     * @return array
     */
    public static function insertAfter($key, array $array, array $insert_array)
    {
        return self::insert($array, $insert_array, $key, 'after');
    }

    /**
     * @param mixed $key
     * @param array $array
     * @param array $insert_array
     *
     * @return array
     */
    public static function insertBefore($key, array $array, array $insert_array)
    {
        return self::insert($array, $insert_array, $key, 'before');
    }

    /**
     * @param array  $array
     * @param array  $insert_array
     * @param string $key
     * @param string $position
     *
     * @return array
     */
    public static function insert(array $array, array $insert_array, $key, string $position = 'before')
    {
        $keyPosition = array_search($key, array_keys($array));
        if ($keyPosition === false) {
            return array_merge($array, $insert_array);
        }

        $keyPosition = (int) $keyPosition;
        if ('after' == $position) {
            ++$keyPosition;
        }
        $result = array_slice($array, 0, $keyPosition);
        $result = array_merge($result, $insert_array);
        return array_merge($result, array_slice($array, $keyPosition));
    }

    /**
     * @param array      $array
     * @param mixed      $value
     * @param mixed|null $key
     *
     * @return array
     */
    public static function prepend(array &$array, $value, $key = null)
    {
        if (!is_null($key)) {
            return $array = [$key => $value] + $array;
        }

        array_unshift($array, $value);
        return $array;
    }
}
