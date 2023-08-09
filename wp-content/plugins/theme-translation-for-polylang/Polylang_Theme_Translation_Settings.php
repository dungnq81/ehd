<?php

class Polylang_Theme_Translation_Settings {

    protected static $settings;

    private function __construct() {
    }

    /**
     * @return array
     */
    public static function getInstance() {
        if (empty(self::$settings)) {
            self::$settings = get_option(Polylang_Theme_Translation::SETTINGS_OPTION);
        }
        if (!is_array(self::$settings)) {
            self::$settings = [];
        }
        if (!isset(self::$settings['themes']) || !is_array(self::$settings['themes'])) {
            self::$settings['themes'] = [];
        }
        if (!isset(self::$settings['plugins']) || !is_array(self::$settings['plugins'])) {
            self::$settings['plugins'] = [];
        }
        if (!isset(self::$settings['domains']) || !is_array(self::$settings['domains'])) {
            self::$settings['domains'] = ['default'];
        }
        if (!isset(self::$settings['additional_domains']) || !is_array(self::$settings['additional_domains'])) {
            self::$settings['additional_domains'] = [];
            foreach (self::$settings['themes'] as $theme) {
                $textdomain = pll_get_theme_textdomain($theme);
                if ($textdomain !== $theme) {
                    self::$settings['additional_domains'][] = $textdomain;
                }
            }
            foreach (self::$settings['plugins'] as $plugin) {
                $textdomain = pll_get_plugin_textdomain($plugin);
                if ($textdomain !== $plugin) {
                    self::$settings['additional_domains'][] = $textdomain;
                }
            }

            if (!add_option(Polylang_Theme_Translation::SETTINGS_OPTION, self::$settings)) {
                update_option(Polylang_Theme_Translation::SETTINGS_OPTION, self::$settings);
            }
        }
        return self::$settings;
    }

}
