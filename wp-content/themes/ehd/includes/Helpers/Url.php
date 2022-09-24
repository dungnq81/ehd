<?php

namespace EHD\Helpers;

\defined( '\WPINC' ) || die;

class Url
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function home(string $path = '')
    {
        return trailingslashit(network_home_url($path));
    }

    /**
     * @param boolean $query_vars
     *
     * @return string
     */
    public static function current($query_vars = false)
    {
        global $wp;
        if (true === $query_vars) {
            return add_query_arg($wp->query_vars, network_home_url($wp->request));
        }
        return static::home($wp->request);
    }

    /**
     * Convert an assets URL to a path.
     *
     * Makes a best guess as to the path of an asset.
     *
     * @param string $url The URL to the asset.
     *
     * @return string|boolean The path to the asset. False of failure.
     */
    public static function path($url)
    {
        $url = remove_query_arg('ver', $url);
        $path = str_replace(
            [trailingslashit(content_url()), trailingslashit(includes_url())],
            [trailingslashit(WP_CONTENT_DIR), trailingslashit(ABSPATH . WPINC)],
            $url
        );
        if (!file_exists($path)) {
            return false;
        }
        return $path;
    }

    /**
     * Normalize the given path. On Windows servers backslash will be replaced
     * with slash. Removes unnecessary double slashes and double dots. Removes
     * last slash if it exists.
     *
     * Examples:
     * path::normalize("C:\\any\\path\\") returns "C:/any/path"
     * path::normalize("/your/path/..//home/") returns "/your/home"
     *
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath(string $path)
    {
        // Backslash to slash convert
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
            $path = preg_replace('/([^\\\])\\\+([^\\\])/s', "$1/$2", $path);
            if (str_ends_with($path, "\\")) {
                $path = substr($path, 0, -1);
            }
            if (str_starts_with($path, "\\")) {
                $path = "/" . substr($path, 1);
            }
        }
        $path = preg_replace('/\/+/s', "/", $path);
        $path = "/$path";
        if (!str_ends_with($path, "/")) {
            $path .= "/";
        }
        $expr = '/\/([^\/]{1}|[^\.\/]{2}|[^\/]{3,})\/\.\.\//s';
        while (preg_match($expr, $path)) {
            $path = preg_replace($expr, "/", $path);
        }
        $path = substr($path, 0, -1);
        $path = substr($path, 1);
        return $path;
    }

    /**
     * @param $url
     * @return bool
     */
    public static function urlExists($url)
    {
        $url = preg_replace('/\s+/', '', $url);
        $headers = @get_headers($url);

        return (bool)stripos($headers[0], "200 OK");
    }

    /**
     * Check for the is image
     *
     * @param string $url The Image url.
     */
    public static function isImage($url, $exists = false)
    {
        if (true === $exists) {
            return static::urlExists($url) && preg_match('/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $url);
        }
        return preg_match('/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $url);
    }

    /**
     * @param string $img
     *
     * @return string
     */
    public static function pixelImg(string $img = '')
    {
        if (file_exists($img)) {
            return $img;
        }
        return "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function queries($url)
    {
        $queries = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $queries);
        return $queries;
    }

    /**
     * @param string $url
     * @param string $param
     * @param string|int $fallback
     *
     * @return string
     */
    public static function query($url, $param, $fallback = null)
    {
        $queries = static::queries($url);
        if (!isset($queries[$param])) {
            return $fallback;
        }
        return $queries[$param];
    }

    /**
     * @param string $url
     * @return int|false
     */
    public static function remoteStatusCheck($url)
    {
        $response = wp_safe_remote_head($url, [
            'timeout' => 5,
            'sslverify' => false,
        ]);
        if (!is_wp_error($response)) {
            return $response['response']['code'];
        }
        return false;
    }
}