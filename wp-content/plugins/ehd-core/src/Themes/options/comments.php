<?php

use EHD_Cores\Helper;

$comment_options = Helper::getOption( 'comment__options', false, false );

$simple_antispam = $comment_options['simple_antispam'] ?? '';

?>
<h2><?php _e( 'Comments Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_simple_antispam">
    <label class="heading" for="simple_antispam"><?php _e( 'Simple Anti-Spam', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Adds a simple CAPTCHA to the WordPress comment form to prevent spam.', EHD_PLUGIN_TEXT_DOMAIN );?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="simple_antispam" id="simple_antispam" <?php checked( $simple_antispam, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>