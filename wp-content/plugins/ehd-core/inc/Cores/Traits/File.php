<?php

namespace EHD\Cores\Traits;

\defined('ABSPATH') || die;

trait File
{
    /**
     * @param      $filename
     * @param bool $include_dot
     *
     * @return string
     */
    public static function fileExtension($filename, bool $include_dot = false): string {
        $dot = '';
        if ($include_dot === true) {
            $dot = '.';
        }
        return $dot . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * @param      $filename
     * @param bool $include_ext
     *
     * @return string
     */
    public static function fileName($filename, bool $include_ext = false): string {
        return $include_ext ? pathinfo(
                $filename,
                PATHINFO_FILENAME
            ) . self::fileExtension($filename) : pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * @param      $file
     * @param bool $convert_to_array
     *
     * @return false|mixed|string
     */
    public static function Read($file, bool $convert_to_array = true) {
        $file = @file_get_contents($file);
        if (!empty($file)) {
            if ($convert_to_array) {
                return json_decode($file, true);
            }
            return $file;
        }
        return false;
    }

    /**
     * @param      $path
     * @param      $data
     * @param bool $json
     *
     * @return bool
     */
    public static function Save($path, $data, bool $json = true): bool {
        try {
            if ($json) {
                $data = self::jsonEncodePrettify($data);
            }
            @file_put_contents($path, $data);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * @param $data
     *
     * @return false|string
     */
    public static function jsonEncodePrettify($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $dir
     * @param $hidden
     * @param $files
     * @return array
     */
    public static function arrayDir($dir, $hidden = false, $files = true): array {
        $result = [];
        $cdir = scandir($dir);

        foreach ($cdir as $key => $value) {
            if (!in_array($value, ['.', '..'])) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::arrayDir($dir . DIRECTORY_SEPARATOR . $value, $hidden, $files);
                } else {
                    if ($files) {
                        if (!str_starts_with($value, '.')) {

                            // hidden file
                            $result[] = $value;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public static function isEmptyDir($dirname): bool {
        if (!is_dir($dirname)) {
            return false;
        }
        foreach (scandir($dirname) as $file) {
            if (!in_array($file, ['.', '..', '.svn', '.git'])) {
                return false;
            }
        }

        return true;
    }
}
