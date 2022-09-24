<?php

namespace EHD\Helpers;

\defined('\WPINC') || die;

class File
{
    /**
     * @param $filename
     * @param bool $include_dot
     *
     * @return string
     */
    public static function fileExtension($filename, bool $include_dot = false)
    {
        $dot = '';
        if ($include_dot === true) {
            $dot = '.';
        }
        return $dot . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * @param $filename
     * @param bool $include_ext
     *
     * @return string
     */
    public static function fileName($filename, bool $include_ext = false)
    {
        return $include_ext ? pathinfo(
                $filename,
                PATHINFO_FILENAME
            ) . static::fileExtension($filename) : pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * @param $file
     * @param bool $convert_to_array
     *
     * @return false|mixed|string
     */
    public static function Read($file, bool $convert_to_array = true)
    {
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
     * @param $path
     * @param $data
     * @param bool $json
     *
     * @return bool
     */
    public static function Save($path, $data, bool $json = true)
    {
        try {
            if ($json) {
                $data = json_encode_prettify($data);
            }
            @file_put_contents($path, $data);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}