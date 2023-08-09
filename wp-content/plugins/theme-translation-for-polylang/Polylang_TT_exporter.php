<?php
defined('ABSPATH') or die('No script kiddies please!');

class Polylang_TT_exporter
{
    /** @var Polylang_Theme_Translation */
    protected $translationModule;

    /**
     * Polylang_TT_theme constructor.
     * @param Polylang_Theme_Translation $translationModule
     */
    public function __construct(Polylang_Theme_Translation $translationModule)
    {
        $this->translationModule = $translationModule;
    }


    public function export()
    {
        if (PLL() instanceof PLL_Settings) {
            $themesStrings = $this->translationModule->run_theme_scanner();
            $pluginsStrings = $this->translationModule->run_plugin_scanner();

            $header = [
                __("String", 'polylang-tt'),
                __("Source (plugin or theme)", 'polylang-tt'),
            ];

            $languages = PLL()->model->get_languages_list();
            /** @var PLL_Language $language */
            foreach ($languages as $language) {
                $header[] = sprintf("%s (%s)", $language->name, $language->locale);
            }

            $csvData = $this->export_data($themesStrings, $languages);
            $csvData = array_merge($csvData, $this->export_data($pluginsStrings, $languages));

            $filename = "polylang-translations.csv";
            $fp = fopen('php://output', 'w');

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            fputcsv($fp, $header);

            foreach ($csvData as $item) {
                fputcsv($fp, $item);
            }
            exit;
        }
    }

    /**
     * @param array $modulesStrings
     * @param array $languages
     * @return array
     */
    protected function export_data(array $modulesStrings, array $languages)
    {
        $csvData = [];
        $counter = 0;
        foreach ($modulesStrings as $name => $strings) {
            foreach ($strings as $string) {
                $csvData[$counter] = [
                    $string,
                    $name,
                ];
                foreach ($languages as $language) {
                    $csvData[$counter][] = pll_translate_string($string, $language->slug);
                }
                $counter++;
            }
        }
        return $csvData;
    }
}
