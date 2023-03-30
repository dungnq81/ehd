<?php

use EHD\Cores\Helper;

$block_editor_options = Helper::getOption( 'block_editor__options' );

$use_widgets_block_editor_off           = $block_editor_options['use_widgets_block_editor_off'] ?? '';
$gutenberg_use_widgets_block_editor_off = $block_editor_options['gutenberg_use_widgets_block_editor_off'] ?? '';
$use_block_editor_for_post_type_off     = $block_editor_options['use_block_editor_for_post_type_off'] ?? '';

$block_style_off = $block_editor_options['block_style_off'] ?? '';

?>
<h2><?php _e('Block Editor Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-checkbox" id="section_use_widgets_block_editor_off">
    <label class="heading" for="use_widgets_block_editor_off"><?php _e('Disable widgets block editor', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="use_widgets_block_editor_off" id="use_widgets_block_editor_off" <?php checked($use_widgets_block_editor_off, 1); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Disables the block editor from managing widgets.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_gutenberg_use_widgets_block_editor_off">
    <label class="heading" for="gutenberg_use_widgets_block_editor_off"><?php _e('Disable Gutenberg widgets', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="gutenberg_use_widgets_block_editor_off" id="gutenberg_use_widgets_block_editor_off" <?php checked($gutenberg_use_widgets_block_editor_off, 1); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Disables the block editor from managing widgets in the Gutenberg.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_use_block_editor_for_post_type_off">
    <label class="heading" for="use_block_editor_for_post_type_off"><?php _e('Disable Block Editor', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="use_block_editor_for_post_type_off" id="use_block_editor_for_post_type_off" <?php checked($use_block_editor_for_post_type_off, 1); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Use Classic Editor - Disable Block Editor.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_block_style_off">
    <label class="heading" for="block_style_off"><?php _e('Remove block CSS', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="block_style_off" id="block_style_off" <?php checked($block_style_off, 1); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Remove block library styles.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>