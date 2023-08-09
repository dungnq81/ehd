=== Theme and plugin translation for Polylang (TTfP) ===
Contributors: marcinkazmierski
Tags: polylang, multilingual, translate, translation, language, languages, twig, multilanguage, international, localization, timber, theme, translator
Requires at least: 5.7
Tested up to: 6.2
Requires PHP: 7.0
Stable tag: 3.4.0
License: GPL2

Theme and plugin translation using Polylang for WordPress.
Extension for Polylang plugin.

== Description ==
= What is "Theme and plugin translation for Polylang"? =

Extension for Polylang plugin (Polylang is an extension to make multilingual WordPress websites.).
Plugin is needed to translate the WordPress themes and plugins by Polylang.

= How to configure it? =

Select themes and plugins to find texts for translation by Polylang.
In admin dashboard:

`Languages -> TTfP Settings`

= How it is work? =

"Theme and plugin translation for Polylang" automatically searches all files of WordPress themes and plugins. It chooses from this file only those files with extensions:

*	php
*	inc
*	twig

In addition, is implemented the integration with Timber library (read more: http://timber.upstatement.com) – which allows to translate twig's skins in simple way.
Plugin in searched skins or plugins chooses texts from Polylang functions, such as:

*    _e(string $text, string $domain = 'default');
*    __(string $text, string $domain = 'default');
*    _x(string $text, string $context, string $domain = 'default');
*    pll_e(string $text);
*    pll__(string $text);
*    esc_html(string $text);
*    esc_html_e(string $text, string $domain = 'default');
*    esc_html__(string $text, string $domain = 'default');
*    _n(string $single, string $plural, int $number, string $domain = 'default');
*    esc_attr_e(string $text, string $domain = 'default');
*    esc_attr__(string $text, string $domain = 'default');

In your function.php, themes or plugins.

For example:

`<p><?php pll_e('My text'); ?></p>`

`<p><?php _e('My another text', 'my_theme'); ?></p>`

On the timber context declare this functions like:

`$context['pll_e'] = TimberHelper::function_wrapper('pll_e');`

`$context['pll_'] = TimberHelper::function_wrapper('pll_');`


See more on: `https://polylang.wordpress.com/documentation/documentation-for-developers/functions-reference/`
These functions are defined by Polylang plugin for printing translations.
Thanks "Theme and plugin translation for Polylang" you can find these strings to translate and add to Polylang register on very simple way.
And then you can translate these texts from the admin dashboard.
The scan result can be seen on the tab with translations:

`Settings -> Languages -> String translation`

or

`Languages -> String translation`


You don't need programs like poedit – you don't change files with extensions like: `.pot`, `.po`, `.mo`.
"Theme and plugin translation for Polylang" is highly efficient because the scanner is worked only on admin dashboard in tab:
In dashboard:

`Settings -> Languages -> String translation`

or

`Languages -> String translation`


= Export and import string translation =

In dashboard:

`Languages -> TTfP Settings`


= Filter reference =

`ttfp_domains`

Allows plugins and themes (in functions.php) to modify list of text domains (unique identifier for retrieving translated strings).
List of text domains is displayed on "TTfP Settings" page to select them for translation by polylang engine.

Example:

`add_filter('ttfp_domains', 'custom_ttfp_domains', 10, 1);
function custom_ttfp_domains(array $domains):array
 {
     $domains[] = "my-custom-domain";
     return $domains;
 }`

= Filter reference =

`ttfp_translation_access`

Returns whether the user has capability to view and edit translations provided by TTfP.

Example:

`add_filter('ttfp_translation_access', 'custom_ttfp_translation_access', 10, 1);
function custom_ttfp_translation_access(bool $hasAccess):bool
 {
     return current_user_can('edit_posts');
 }`

== Installation ==
This plugin requires to be installed and activated the Polylang plugin,
This plugin requires PHP 5.0

1. Upload the "Theme and plugin translation for Polylang" folder to the `/wp-content/plugins/` directory on your web server.
2. Activate the plugin through the Plugins menu in WordPress.
3. In Dashboard go to the `Settings -> Languages -> String translation` or `Languages -> String translation` and find your texts.

= Use =

`<?php
 pll_e('My string'); // similar to _e();
 // or:
 $var = pll_('My string'); // similar to __();
 // or:
  _e('My string', 'my_theme');
 // or:
  $var = __('My string', 'my_theme');`

= How to enable Twig extension with "Theme and plugin translation for Polylang"? [Timber plugin] =

In functions.php add:

`if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });
    return;
}

function timber_context()
{
    $context = Timber::get_context();
    $post = Timber::query_post();
    $context['post'] = $post;
    $context['pll_e'] = TimberHelper::function_wrapper('pll_e');
    $context['pll__'] = TimberHelper::function_wrapper('pll__');
    return $context;
}

Timber::$dirname = array('templates', 'views'); // directory names with twig templates
timber_context();`

Next, for example in index.php add:

`<?php
 $context = timber_context();
 $templates = array('home.twig', 'index.twig'); // twig files for render
 Timber::render($templates, $context);
`

Then you can use in twig templates polylang functions like this (in templates/home.twig):

`{% extends "base.twig" %}
 {% block content %}
     <p>
         {{ pll_e("Test text on TWIG template 1.") }}
     </p>
     <p>
         {{ pll__("Test text on TWIG template 2.") }}
     </p>
 {% endblock %}`


== Screenshots ==

1. Screen show "Polylang" strings translations with "Theme and plugin translation for Polylang".
2. Export/import translations as CSV file with "Theme and plugin translation for Polylang".
3. Settings - Select area to be scanned in Strigs translations polylang tab.

== Changelog ==

= 3.4.0 - 2023/08/03 =

* Added apply_filters: ttfp_translation_access.
* Fixed force translating the administrator's dashboard.
* Switched to PHP 7.0.

= 3.3.5 - 2023/04/24 =

* Fixed text domains of plugins on setting page.
* Fixed pll_admin_current_language filter for translate or no translate admin dashboard.

= 3.3.4 - 2023/04/03 =

* Check if function get_plugin_data exist before use.
* Added pll_admin_current_language filter for translate or no translate admin dashboard.

= 3.3.3 - 2023/01/26 =

* Include text domain of plugins and themes.

= 3.3.2 - 2022/12/27 =

* Fixed notice in Polylang_Theme_Translation_Settings.

= 3.3.1 - 2022/12/15 =

* Added apply_filters: ttfp_domains.

= 3.3.0 - 2022/12/15 =

* Fixed performance in filters in admin.
* Added temporary cache on polylang translations page (for 60s).
* Added WordPress core and admin string scanner: default domain.
* Loading translations on action: pll_language_defined

= 3.2.23 - 2022/12/12 =

* Fixed performance in filters in admin.

= 3.2.22 - 2022/12/12 =

* Fixed translators in filters.

= 3.2.21 - 2022/12/12 =

* Removed esc_html filter.

= 3.2.20 - 2022/12/10 =

* Fixed gettext filter for default domain.

= 3.2.19 - 2022/11/17 =

* Security fix.

= 3.2.18 - 2022/11/17 =

* Security fix.

= 3.2.17 - 2022/11/08 =

* Security fix.

= 3.2.16 - 2022/11/04 =

* Fixed gettext filter.

= 3.2.15 - 2022/11/04 =

* Fixed gettext filter.

= 3.2.14 - 2022/11/03 =

* Fixed gettext filter.

= 3.2.13 - 2022/05/26 =

* Updated plugin description.
* Test with Polylang version 3.2.3 and WordPress 6.0.

= 3.2.12 - 2021/06/15 =

* Added esc_attr_e and esc_attr__ filter.

= 3.2.11 - 2021/05/17 =

* Added esc_html filter.
* Test with WordPress 5.7.2 version and Polylang version 3.0.4.
* Updated version.

= 3.2.10 - 2021/05/10 =

* Fixed gettext_with_context filter.

= 3.2.9 - 2021/05/04 =

* Updated scanner regex.

= 3.2.8 - 2021/05/03 =

* Updated scanner regex.
* Updated readme.

= 3.2.7 - 2021/05/03 =

* Updated scanner regex: added esc_html__.

= 3.2.6 - 2021/04/27 =

* Updated scanner regex.
* Test with Polylang version 3.0.4.
* Updated version.

= 3.2.5 - 2021/02/20 =

* UTF-8 header in csv exporter.
* Test with Polylang version 2.9.2.
* Updated version.

= 3.2.4 - 2020/09/13 =

* Added ngettext polylang filter.
* Added to file skaner function _n(): single + plural.
* Test with Polylang version 2.8.2.
* Updated version.

= 3.2.3 - 2020/03/26 =

* Updated screens.
* Refactoring.
* Test with Polylang version 2.6.10.
* Updated version.

= 3.2.2 - 2020/02/02 =

* Fixed gettext filter.
* Test with WordPress 5.3.2 version and Polylang version 2.6.9.
* Updated version.

= 3.2.1 - 2019/12/07 =

* Updated version.
* Test with WordPress 5.3 version and Polylang version 2.6.7

= 3.2.0 - 2019/09/26 =

* Added setting section in "TTfP Settings" tab.
* Updated version.

= 3.1.1 - 2019/09/20 =

* Fix file scanner.
* Updated version.

= 3.1.0 - 2019/09/13 =

* Translate strings from functions: _e( string $text, string $domain = 'default' ) and __( string $text, string $domain = 'default' ).
* Updated plugin description.
* Test with WordPress 5.2.3 version and Polylang version 2.6.x.
* Updated version.

= 3.0.0 - 2019/05/12 =

* Added import and export feature.
* Updated plugin description.
* Test with WordPress 5.2 version and Polylang version 2.5.x.
* Updated version.

= 2.0.4 - 2018/12/10 =

* Test with WordPress 5.0 version and Polylang version 2.5.
* Updated version.

= 2.0.3 - 2018/07/04 =

* Test with WordPress 4.9.5 version and Polylang version 2.3.4.
* Updated version.

= 2.0.2 - 2018/01/02 =

* Updated plugin description.
* Test with WordPress 4.9 version and Polylang version 2.2.7.
* Updated version.

= 2.0.1 - 2017/10/03 =

* Test with WordPress 4.8 version and Polylang version 2.2.3.
* Updated version.

= 2.0.0 - 2017/03/05 =

* Added plugin scanner.
* Updated version.

= 1.4.0 - 2017/01/29 =

* Polylang version 2.1 - fixed: polylang changed default tab.
* Updated version.

= 1.3.3 - 2017/01/09 =

* Test with WordPress 4.7 version and Polylang version 2.0.12.
* Updated version.

= 1.3.2 - 2016/09/07 =

* Test with 4.6.1 WordPress version.
* Updated version.

= 1.3.1 - 2016/06/07 =

* Added plugin logo.

= 1.3 - 2016/05/15 =

* Fixed warnings.
* Test with 4.5 WordPress version.
* Updated description.
* Updated version.

= 1.2 - 2016/03/27 =

* Updated description.

= 1.1 - 2016/02/03 =

* Fixed readme.txt
