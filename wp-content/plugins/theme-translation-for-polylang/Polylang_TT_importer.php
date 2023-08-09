<?php
defined('ABSPATH') or die('No script kiddies please!');

class Polylang_TT_importer
{
    /**
     * @param $fileName
     * @return int
     */
    public function import($fileName)
    {
        $counter = 0;
        $rows = 0;
        if (PLL() instanceof PLL_Settings) {
            $file = fopen($fileName, "r");
            $languages = PLL()->model->get_languages_list();
            $header = [];
            while (($row = fgetcsv($file)) !== FALSE) {
                if ($rows === 0) { // header
                    $header = $row;
                } else {
                    /** @var PLL_Language $language */
                    foreach ($languages as $key => $language) {
                        if (isset($header[$key + 2]) && strpos($header[$key + 2], $language->locale) !== false) {
                            $original = $row[0];
                            $mo = new PLL_MO();
                            $mo->import_from_db($language);
                            $translation = $row[$key + 2] ?? '';
                            if (!empty($translation)) {
                                $translation = apply_filters('tt_pll_sanitize_string_translation', $translation, $original, $language->slug);
                                $mo->add_entry($mo->make_entry($original, $translation));
                            }
                            $mo->export_to_db($language);
                            $counter++;
                        }
                    }
                }
                $rows++;
            }
        }
        return $counter;
    }
}
