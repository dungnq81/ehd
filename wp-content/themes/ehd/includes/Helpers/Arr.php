<?php

namespace EHD\Helpers;

\defined('\WPINC') || die;

class Arr
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
    public static function convertFromString($value, $callback = null)
    {
        if (is_scalar($value)) {
            $value = array_map('trim', explode(',', Cast::toString($value)));
        }
        $callback = if_empty(Cast::toString($callback), 'is_not_empty');
        return static::reindex(array_filter((array)$value, $callback));
    }

    /**
     * @param mixed $array
     * @return array
     */
    public static function reindex($array)
    {
        return static::isIndexedAndFlat($array) ? array_values($array) : $array;
    }

    /**
     * @param mixed $array
     * @return bool
     */
    public static function isIndexedAndFlat($array)
    {
        if (!is_array($array) || array_filter($array, 'is_array')) {
            return false;
        }
        return wp_is_numeric_array($array);
    }

    /**
     * @param string $key
     * @param array $array
     * @param array $insert_array
     *
     * @return array
     */
    public static function insertAfter($key, array $array, array $insert_array)
    {
        return static::insert($array, $insert_array, $key, 'after');
    }

    /**
     * @param string $key
     * @param array $array
     * @param array $insert_array
     *
     * @return array
     */
    public static function insertBefore($key, array $array, array $insert_array)
    {
        return static::insert($array, $insert_array, $key, 'before');
    }

    /**
     * @param array $array
     * @param array $insert_array
     * @param string $key
     * @param $position
     *
     * @return array
     */
    public static function insert(array $array, array $insert_array, $key, $position = 'before')
    {
        $keyPosition = array_search($key, array_keys($array));
        if ($keyPosition === false) {
            return array_merge($array, $insert_array);
        }
        $keyPosition = Cast::toInt($keyPosition);
        if ('after' == $position) {
            ++$keyPosition;
        }
        $result = array_slice($array, 0, $keyPosition);
        $result = array_merge($result, $insert_array);
        return array_merge($result, array_slice($array, $keyPosition));
    }

    /**
     * @param array $values
     * @param string $prefix
     * @param mixed $prefixed
     *
     * @return array
     */
    public static function prefixKeys($values, string $prefix = '_', $prefixed = true)
    {
        $trim = (true === $prefixed) ? $prefix : '';
        $prefixed = [];
        foreach ($values as $key => $value) {
            $key = trim($key);
            if (str_starts_with($key, $prefix)) {
                $key = substr($key, strlen($prefix));
            }
            $prefixed[$trim . $key] = $value;
        }
        return $prefixed;
    }

    /**
     * @param array $values
     * @param string $prefix
     *
     * @return array
     */
    public static function unprefixKeys(array $values, string $prefix = '_')
    {
        return static::prefixKeys($values, $prefix, false);
    }

    /**
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     *
     * @return array
     */
    public static function prepend(&$array, $value, $key = null)
    {
        if (!is_null($key)) {
            return $array = [$key => $value] + $array;
        }
        array_unshift($array, $value);
        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function removeEmptyValues(array $array = [])
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_empty($value)) {
                continue;
            }
            $result[$key] = if_true(!is_array($value), $value, function () use ($value) {
                return static::removeEmptyValues($value);
            });
        }
        return $result;
    }
}