<?php
defined('ABSPATH') or die('No script kiddies please!');

class Polylang_TT_theme
{
    /**
     * @param $name
     * @param $data
     * @return string
     */
    public static function includeTemplates($name, $data)
    {
        $name = str_replace('/', '_', $name);
        $result = '';

        if ($theme_file = locate_template(array($name . '.tpl.php'))) {
            $template_path = $theme_file;
        } else {
            $template_path = __DIR__ . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $name . '.tpl.php';
        }

        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            $result = ob_get_clean();
        }
        return (string)$result;
    }
}
