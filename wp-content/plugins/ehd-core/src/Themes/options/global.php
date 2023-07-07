<?php

use EHD_Cores\Helper;

$html_header      = Helper::getCustomPostContent( 'html_header', true );
$html_footer      = Helper::getCustomPostContent( 'html_footer', true );
$html_body_top    = Helper::getCustomPostContent( 'html_body_top', true );
$html_body_bottom = Helper::getCustomPostContent( 'html_body_bottom', true );

?>
<h2><?php _e( 'Custom Scripts Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<div class="section section-textarea" id="section_html_header">
    <label class="heading" for="html_header"><?php _e('Header scripts', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_header" id="html_header" rows="4"><?php echo $html_header; ?></textarea>
        </div>
        <div class="explain">Add custom scripts inside HEAD tag. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_footer">
    <label class="heading" for="html_footer"><?php _e('Footer scripts', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_footer" id="html_footer" rows="4"><?php echo $html_footer; ?></textarea>
        </div>
        <div class="explain">Add custom scripts you might want to be loaded in the footer of your website. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_body_top">
    <label class="heading" for="html_body_top"><?php _e('Body scripts - TOP', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_body_top" id="html_body_top" rows="4"><?php echo $html_body_top; ?></textarea>
        </div>
        <div class="explain">Add custom scripts just after the BODY tag opened. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_body_bottom">
    <label class="heading" for="html_body_bottom"><?php _e('Body scripts - BOTTOM', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_body_bottom" id="html_body_bottom" rows="4"><?php echo $html_body_bottom; ?></textarea>
        </div>
        <div class="explain">Add custom scripts just before the BODY tag closed. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
