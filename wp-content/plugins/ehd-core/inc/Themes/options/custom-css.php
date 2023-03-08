<?php

use EHD\Cores\Helper;

$html_custom_css_post = Helper::getCustomCssPost();
$css = $html_custom_css_post->post_content ?? '';

?>
<h2><?php _e('Custom CSS Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-textarea" id="section_html_custom_css">
    <label class="heading" for="html_custom_css"></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control" name="html_custom_css" id="html_custom_css" rows="8"><?php echo $css?></textarea>
        </div>
    </div>
</div>