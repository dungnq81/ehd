<?php

namespace EHD\Cores\Traits;

use EHD\Cores\Helper;

\defined('ABSPATH') || die;

trait Cast
{
    use Arr;

    // --------------------------------------------------

    /**
     * @param mixed $value
     * @return int
     */
    public static function toInt(mixed $value)
    {
        return (int) round(self::toFloat($value));
    }

    /**
     * @param mixed $value
     * @return float
     */
    public static function toFloat(mixed $value)
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    /**
     * @param mixed $value
     * @param bool  $explode
     * @return array
     */
    public static function toArray(mixed $value, bool $explode = true)
    {
        if (is_object($value)) {
            $reflection = new \ReflectionObject($value);
            $properties = $reflection->hasMethod('toArray')
                ? $value->toArray()
                : get_object_vars($value);

            return json_decode(json_encode($properties), true);
        }

        if (is_scalar($value) && $explode) {
            return self::convertFromString($value);
        }

        return (array) $value;
    }

    /**
     * @param mixed $value
     * @param bool  $strict
     *
     * @return string
     */
    public static function toString(mixed $value, bool $strict = true)
    {
        if (is_object($value) && in_array('__toString', get_class_methods($value))) {
            return (string) $value->__toString();
        }
        if (Helper::isEmpty($value)) {
            return '';
        }
        if (self::isIndexedAndFlat($value)) {
            return implode(', ', $value);
        }
        if (!is_scalar($value)) {
            return $strict ? '' : serialize($value);
        }
        return (string) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function toBool(mixed $value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $value
     * @return object
     */
    public static function toObject(mixed $value)
    {
        if (!is_object($value)) {
            return (object) self::toArray($value);
        }
        return $value;
    }
}
