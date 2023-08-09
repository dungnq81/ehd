<?php

class Polylang_Theme_Translation_Translator
{
    /** @var PLL_MO */
    protected $translator;

    /** @var PLL_Language */
    protected $language;

    /**
     * @param PLL_Language $language
     */
    public function __construct($language)
    {
        if (class_exists('PLL_MO') && $language instanceof PLL_Language) {
            $this->language = $language;
            $this->translator = new PLL_MO();
            $this->translator->import_from_db($language);
        } else {
            $this->translator = new NOOP_Translations();
        }
        add_filter('gettext', [$this, 'gettext'], 99, 3);
        add_filter('ngettext', [$this, 'ngettext'], 99, 5);
        add_filter('gettext_with_context', [$this, 'gettext_with_context'], 99, 4);
        add_filter('plugin_locale', [$this, 'plugin_locale'], 99, 2);
    }

    /**
     * @param string $domain
     * @return bool
     */
    protected function make_translation($domain)
    {
        $settings = Polylang_Theme_Translation_Settings::getInstance();
        return ( in_array($domain, $settings['themes'])
            || in_array($domain, $settings['plugins'])
            || in_array($domain, $settings['domains'])
            || in_array($domain, $settings['additional_domains'])
            || in_array($domain, ['pll_string', 'polylang-tt'])
        );
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $domain
     * @return string
     */
    public function gettext($translation, $text, $domain)
    {
        if ($this->make_translation($domain)) {
            $pllTranslation = $this->translator->translate($text);
            if ($pllTranslation != $text) {
                return $pllTranslation;
            }
        }
        return $translation;
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $domain
     * @return string
     */
    public function ngettext($translation, $single, $plural, $number, $domain)
    {
        if ($this->make_translation($domain)) {
            $pllTranslationSingle = $this->translator->translate($single);
            $pllTranslationPlural = $this->translator->translate($plural);
            if ($pllTranslationSingle != $single || $pllTranslationPlural != $plural) {
                return $this->translator->translate_plural($pllTranslationSingle, $pllTranslationPlural, $number);
            }
        }
        return $translation;
    }

    /**
     * @param string $translated
     * @param string $text
     * @param string $context
     * @param string $domain
     * @return string
     */
    public function gettext_with_context($translated, $text, $context, $domain)
    {
        if ($this->make_translation($domain)) {
            $pllTranslation = $this->translator->translate($text);
            if ($pllTranslation !== $text) {
                return $pllTranslation;
            }
        }
        return $translated;
    }

    /**
     * @param string $locale
     * @param string $domain
     * @return string
     */
    public function plugin_locale($locale, $domain)
    {
        if ($this->language instanceof PLL_Language) {
            return $this->language->locale;
        }
        return $locale;
    }
}