<?php

namespace EHD\Cores\Traits;

\defined('ABSPATH') || die;

trait Url
{
    public static function urlToPath($url): string {
        return substr(get_home_path(), 0, -1) . wp_make_link_relative($url);
    }

    /**
     * @param $dir
     * @return array|string|string[]
     */
    public static function pathToUrl($dir)
    {
        $dirs = wp_upload_dir();
        $url = str_replace($dirs['basedir'], $dirs['baseurl'], $dir);

        return str_replace(ABSPATH, self::home(), $url);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function home(string $path = ''): string {
        return trailingslashit(network_home_url($path));
    }

    /**
     * @param boolean $query_vars
     *
     * @return string
     */
    public static function current( bool $query_vars = false): string {
        global $wp;
        if (true === $query_vars) {
            return add_query_arg($wp->query_vars, network_home_url($wp->request));
        }
        return self::home($wp->request);
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
    public static function normalizePath(string $path): string {
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

        return substr($path, 1);
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function queries( string $url): array {
        $queries = [];
        parse_str(wp_parse_url($url, PHP_URL_QUERY), $queries);
        return $queries;
    }

    /**
     * @param string $url
     * @param string     $param
     * @param string|int $fallback
     *
     * @return string
     */
    public static function query( string $url, $param, $fallback = null)
    {
        $queries = self::queries($url);
        if (!isset($queries[$param])) {
            return $fallback;
        }
        return $queries[$param];
    }

    /**
     * @param string $url
     *
     * @return int|false
     */
    public static function remoteStatusCheck( string $url)
    {
        $response = wp_safe_remote_head($url, [
            'timeout'   => 5,
            'sslverify' => false,
        ]);
        if (!is_wp_error($response)) {
            return $response['response']['code'];
        }
        return false;
    }
}
