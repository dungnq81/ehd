<?php

namespace EHD\Helpers;

\defined('\WPINC') || die;

class Cast
{
    /**
     * @param mixed $value
     * @return int
     */
    public static function toInt($value)
    {
        return (int)round(static::toFloat($value));
    }

    /**
     * @param mixed $value
     * @return float
     */
    public static function toFloat($value)
    {
        return (float)filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    /**
     * @param mixed $value
     * @return array
     */
    public static function toArray($value, bool $explode = true)
    {
        if (is_object($value)) {
            $reflection = new \ReflectionObject($value);
            $properties = $reflection->hasMethod('toArray')
                ? $value->toArray()
                : get_object_vars($value);
            return json_decode(json_encode($properties), true);
        }
        if (is_scalar($value) && $explode) {
            return Arr::convertFromString($value);
        }
        return (array)$value;
    }

    /**
     * @param mixed $value
     * @param bool $strict
     *
     * @return string
     */
    public static function toString($value, bool $strict = true)
    {
        if (is_object($value) && in_array('__toString', get_class_methods($value))) {
            return (string)$value->__toString();
        }
        if (is_empty($value)) {
            return '';
        }
        if (Arr::isIndexedAndFlat($value)) {
            return implode(', ', $value);
        }
        if (!is_scalar($value)) {
            return $strict ? '' : serialize($value);
        }
        return (string)$value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function toBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $value
     * @return object
     */
    public static function toObject($value)
    {
        if (!is_object($value)) {
            return (object)static::toArray($value);
        }
        return $value;
    }
}