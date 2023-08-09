<?php

/**
 * Get list of plugins
 *
 * @return array
 */
function pll_get_plugins() {
    $pluginsNames = [];
    $plugins = wp_get_active_and_valid_plugins();

    foreach ($plugins as $plugin) {
        $pluginDir = dirname($plugin);
        $pluginName = pathinfo($plugin, PATHINFO_FILENAME);
        if (!in_array($pluginName, Polylang_Theme_Translation::EXCLUDE_PLUGINS) && $pluginDir !== WP_PLUGIN_DIR) {
            $pluginsNames[] = $pluginName;
        }
    }

    return $pluginsNames;
}

/**
 * Get list of themes
 *
 * @return array
 */
function pll_get_themes() {
    $themesNames = [];
    $themes = wp_get_themes();

    /**
     * @var string $name
     * @var \WP_Theme $theme
     */
    foreach ($themes as $name => $theme) {
        $themesNames[] = $name;
    }

    return $themesNames;
}

function pll_get_domains() {
    $domains = ["default"];
    $domainsFiltered = apply_filters('ttfp_domains', $domains);
    if (is_array($domainsFiltered)) {
        return $domainsFiltered;
    }
    return $domains;
}

function pll_get_theme_textdomain($name) {
    $theme = wp_get_theme($name);
    $textdomain = $theme->get('TextDomain');
    if (empty($textdomain)) {
        return $name;
    }
    return $textdomain;
}

function pll_get_theme_fullname($name) {
    $theme = wp_get_theme($name);
    $fullname = $theme->get('Name');
    if (empty($fullname)) {
        return $name;
    }
    return $fullname;
}

function pll_get_plugin_textdomain($plugin_slug) {
    $info = pll_get_plugin_info($plugin_slug);
    if (!empty($info) && is_array($info)) {
        $textdomain = $info['TextDomain'] ?? "";
        if (!empty($textdomain)) {
            return $textdomain;
        }
    }
    return $plugin_slug;
}

function pll_get_plugin_fullname($plugin_slug) {
    $info = pll_get_plugin_info($plugin_slug);
    if (!empty($info) && is_array($info)) {
        $fullname = $info['Name'] ?? "";
        if (!empty($fullname)) {
            return $fullname;
        }
    }
    return $plugin_slug;
}

function pll_get_plugin_info($plugin_slug) {
    $plugins = NULL;
    $all = get_plugins();
    foreach ($all as $key => $item) {
        $pluginName = pathinfo($key, PATHINFO_FILENAME);
        if ($pluginName == $plugin_slug) {
            $plugins = get_plugins(DIRECTORY_SEPARATOR . dirname($key));
            break;
        }
    }

    if (!empty($plugins)) {
        return array_pop($plugins);
    }
    return [];
}