<?php

use EHD\Cores\Helper;

$html_custom_css_post = Helper::getCustomPost( 'html_custom_css' );
$css = $html_custom_css_post->post_content ?? '';
$css = wp_unslash($css);

?>
<h2><?php _e('CSS Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-textarea" id="section_html_custom_css">
    <label class="heading" for="html_custom_css">Custom CSS</label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_css" name="html_custom_css" id="html_custom_css" rows="8"><?php echo $css?></textarea>
        </div>
    </div>
</div>