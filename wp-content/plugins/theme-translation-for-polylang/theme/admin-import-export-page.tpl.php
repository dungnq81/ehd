<?php
switch ($data['msg']) {
    case "translations-imported":
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                print sprintf('%s. %s: %d.', __('Translations updated', 'polylang-tt'), __('Items', 'polylang-tt'), $data['items']);
                ?>
            </p>
        </div>
        <?php
        break;
    case "translations-import-error":
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                print __('Translations import error.', 'polylang-tt');
                ?>
            </p>
        </div>
        <?php
        break;
    case "settings-saved":
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                print __('Settings saved.', 'polylang-tt');
                ?>
            </p>
        </div>
        <?php
        break;
}
?>
<code><?php _e('Powered by Theme and plugin translation for Polylang (TTfP)', 'polylang-tt'); ?></code>
<h3>
    <?php _e('Settings', 'polylang-tt'); ?>
</h3>

<h4>
    <?php _e('Select area to be scanned in strings translations tab', 'polylang-tt'); ?>
    :
</h4>

<div class="form-wrap" style="width: 100%;">
    <form id="settings" method="post" enctype="multipart/form-data"
          style="display: inline-block;"
          action="<?php echo esc_url(add_query_arg('pll_action', 'settings')); ?>">
        <?php wp_nonce_field('settings', '_wpnonce_settings'); ?>
        <input type="hidden" name="action_settings" value="1">

        <p><?php _e('Force translate admin dashboard:', 'polylang-tt'); ?></p>
        <label>
            <input type="radio" name="force_translate_admin"
                   value="<?php print "0"; ?>"
                   <?php if ($data['force_translate_admin'] == 0): ?>checked<?php endif; ?>>
            <?php _e('None', 'polylang-tt'); ?>
        </label>
        <label>
            <input type="radio" name="force_translate_admin"
                   value="<?php print "1"; ?>"
                   <?php if ($data['force_translate_admin'] == Polylang_Theme_Translation::VALUE_DEFAULT_POLYLANG_LANG): ?>checked<?php endif; ?>>
            <?php _e('Translate admin dashboard to default polylang language', 'polylang-tt'); ?>
            [<?php print pll_default_language(); ?>]
        </label>
        <label>
            <input type="radio" name="force_translate_admin"
                   value="<?php print "2"; ?>"
                   <?php if ($data['force_translate_admin'] == Polylang_Theme_Translation::VALUE_DEFAULT_USER_LANG): ?>checked<?php endif; ?>>
            <?php _e('Translate admin dashboard to current user language (from slug)', 'polylang-tt'); ?>
        </label>

        <br/>
        <p><?php _e('Wordpress core and admin domains:', 'polylang-tt'); ?></p>
        <?php foreach ($data['domains'] as $domain): ?>
            <label>
                <input type="checkbox" name="domains[]"
                       value="<?php print $domain; ?>"
                       <?php if (in_array($domain, $data['settings']['domains'])): ?>checked<?php endif; ?>>
                <?php print $domain; ?>
            </label>
        <?php endforeach; ?>
        <br/>
        <p><?php _e('Themes:', 'polylang-tt'); ?></p>
        <?php foreach ($data['themes'] as $theme): ?>
            <label>
                <input type="checkbox" name="themes[]"
                       value="<?php print $theme; ?>"
                       <?php if (in_array($theme, $data['settings']['themes'])): ?>checked<?php endif; ?>>
                <?php print $theme; ?>
                <small>
                    (
                    <?php _e('Theme Name:', 'polylang-tt'); ?> <?php print pll_get_theme_fullname($theme); ?>
                    ,
                    <?php _e('Text Domain:', 'polylang-tt'); ?> <?php print pll_get_theme_textdomain($theme); ?>
                    )
                </small>
            </label>
        <?php endforeach; ?>
        <br/>
        <p><?php _e('Plugins:', 'polylang-tt'); ?></p>
        <?php foreach ($data['plugins'] as $plugin): ?>
            <label>
                <input type="checkbox" name="plugins[]"
                       value="<?php print $plugin; ?>"
                       <?php if (in_array($plugin, $data['settings']['plugins'])): ?>checked<?php endif; ?>>
                <?php print $plugin; ?>
                <small>
                    (
                    <?php _e('Plugin Name:', 'polylang-tt'); ?> <?php print pll_get_plugin_fullname($plugin); ?>
                    ,
                    <?php _e('Text Domain:', 'polylang-tt'); ?> <?php print pll_get_plugin_textdomain($plugin); ?>
                    )
                </small>
            </label>
        <?php endforeach; ?>

        <?php
        submit_button(__('Save', 'polylang-tt')); // Since WP 3.1
        ?>
    </form>
</div>

<hr>

<h3>
    <?php _e('How it is work?', 'polylang-tt'); ?>
</h3>
<div class="wrap">
    <p>
        <?php _e('"Theme and plugin translation for Polylang (TTfP)" automatically searches all files of WordPress themes and plugins.', 'polylang-tt'); ?>
        <?php _e('It chooses from this file only those files with extensions:', 'polylang-tt'); ?>
    </p>
    <ul>
        <li>php</li>
        <li>inc</li>
        <li>twig</li>
    </ul>
    <p>
        <?php _e('Plugin in searched skins or plugins chooses texts from Polylang functions, such as:', 'polylang-tt'); ?>
    </p>
    <ul>
        <li>_e();</li>
        <li>__();</li>
        <li>pll_e();</li>
        <li>pll__();</li>
    </ul>
    <p>
        <?php _e('This functions are defined by Polylang plugin for printing', 'polylang-tt'); ?>
        <br/>
        <?php _e('Thanks "Theme and plugin translation for Polylang" you can find these strings to translate and add to Polylang register on very simple way.', 'polylang-tt'); ?>
        <br/>
        <?php _e('Then you can translate these texts from the admin dashboard.', 'polylang-tt'); ?>
        <br/>
        <?php _e('The scan result can be seen on the tab with translations:', 'polylang-tt'); ?>
        <br/>
        <?php _e('`Settings -> Languages -> String translation`', 'polylang-tt'); ?>
        <br/>
        <?php _e('or', 'polylang-tt'); ?><br/>
        <?php _e('`Languages -> String translation`', 'polylang-tt'); ?><br/>
    </p>
</div>

<div class="form-wrap">
    <p>
        <?php _e('Export all texts for translate as CSV file', 'polylang-tt'); ?>
        :
    </p>
    <form id="import_export_strings" method="post"
          action="<?php echo esc_url(add_query_arg('pll_action', 'export_strings')); ?>">
        <?php wp_nonce_field('export_strings', '_wpnonce_export_strings'); ?>
        <input name="export_strings" type="hidden" value="1"/>
        <?php
        submit_button(__('Export data', 'polylang-tt')); // Since WP 3.1
        ?>
    </form>
</div>


<hr>

<h3>
    <?php _e('Export/import polylang translations', 'polylang-tt'); ?>
</h3>
<div class="form-wrap">
    <p>
        <?php _e('Import all translated texts', 'polylang-tt'); ?>:
    </p>
    <form id="import_export_strings" method="post" enctype="multipart/form-data"
          action="<?php echo esc_url(add_query_arg('pll_action', 'import_strings')); ?>">
        <?php wp_nonce_field('import_strings', '_wpnonce_import_strings'); ?>
        <input type="hidden" name="action_import_strings" value="1">
        <label for="import_strings">
            <?php _e('Select CSV file:', 'polylang-tt'); ?>:
        </label>
        <input type="file" name="import_strings" id="import_strings"
               accept=".csv" required>
        <?php
        submit_button(__('Run importer', 'polylang-tt')); // Since WP 3.1
        ?>
    </form>
</div>