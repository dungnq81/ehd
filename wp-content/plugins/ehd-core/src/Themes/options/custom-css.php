<?php

use EHD_Cores\Helper;

$css = Helper::getCustomPostContent( 'ehd_css', false );

?>
<h2><?php _e('CSS Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-textarea" id="section_html_custom_css">
    <label class="heading" for="html_custom_css"><?php _e('Custom CSS', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_css" name="html_custom_css" id="html_custom_css" rows="8"><?php echo $css?></textarea>
        </div>
    </div>
</div>