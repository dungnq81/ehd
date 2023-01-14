<?php

namespace EHD\Cores\Traits;

\defined('ABSPATH') || die;

trait Str
{
    /**
     * @param $content
     * @return array|string|string[]
     */
    public static function removeEmptyP($content)
    {
        return \str_replace('<p></p>', '', $content);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function camelCase(string $string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', trim($string)));
        return str_replace(' ', '', $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function dashCase(string $string)
    {
        return str_replace('_', '-', self::snakeCase($string));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function snakeCase(string $string)
    {
        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', $string);
            $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
            $string = mb_strtolower($string, 'UTF-8');
        }
        return str_replace('-', '_', $string);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function random(int $length = 8)
    {
        $text = base64_encode(wp_generate_password());
        return substr(str_replace(['/', '+', '='], '', $text), 0, $length);
    }

    /**
     * @param string      $string
     * @param string      $prefix
     * @param string|null $trim
     *
     * @return string
     */
    public static function prefix($string, string $prefix, $trim = null)
    {
        if ('' === $string) {
            return $string;
        }
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix . trim(self::removePrefix($string, $trim));
    }

    /**
     * @param string $prefix
     * @param string $string
     *
     * @return string
     */
    public static function removePrefix(string $string, string $prefix)
    {
        return self::startsWith($prefix, $string)
            ? substr($string, strlen($prefix))
            : $string;
    }

    /**
     * @param string|string[] $needles
     * @param string          $haystack
     *
     * @return bool
     */
    public static function startsWith($needles, $haystack)
    {
        $needles = (array) $needles;
        foreach ($needles as $needle) {
            if (str_starts_with($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string|string[] $needles
     * @param string          $haystack
     *
     * @return bool
     */
    public static function endsWith($needles, $haystack)
    {
        $needles = (array) $needles;
        foreach ($needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
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
    public static function suffix($string, $suffix)
    {
        if (!self::endsWith($suffix, $string)) {
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
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }
        $position = strpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
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
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);
        if ('' !== $search && false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
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
    public static function strposOffset($haystack, $needles, int $offset = 0)
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }
        foreach ($needles as $query) {
            if (strpos($haystack, $query, $offset) !== false) {
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
    public static function titleCase($string)
    {
        $value = str_replace(['-', '_'], ' ', $string);
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
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
    public static function keyWords(string $str)
    {
        $str = preg_replace('/(\v|\s){1,}/u', ' ', $str);
        return preg_replace('/[\s]+/', ', ', trim($str));
    }

    /**
     * @param string $value
     * @param int    $length
     * @param string $end
     *
     * @return string
     */
    public static function truncate($value, $length, $end = '')
    {
        return mb_strwidth($value, 'UTF-8') > $length
            ? mb_substr($value, 0, $length, 'UTF-8') . $end
            : $value;
    }

    /**
     * @param      $string
     * @param bool $strip_tags
     * @return array|string|string[]|null
     */
    public static function stripSpace($string, bool $strip_tags = true)
    {
        $string = preg_replace(
            '/(\v|\s){1,}/u',
            '',
            $string
        );

        $string = preg_replace('/\s+/', '', $string);
        $string = preg_replace('~\x{00a0}~', '', $string);

        if (true === $strip_tags) {
            $string = strip_tags($string);
        }

        return $string;
    }
}
