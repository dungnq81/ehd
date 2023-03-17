<?php

use EHD\Cores\Helper;

$html_header_post      = Helper::getCustomPost( 'html_custom_header' );
$html_footer_post      = Helper::getCustomPost( 'html_custom_footer' );
$html_body_top_post    = Helper::getCustomPost( 'html_custom_body_top' );
$html_body_bottom_post = Helper::getCustomPost( 'html_custom_body_bottom' );

$html_header      = isset( $html_header_post->post_content ) ? wp_unslash( base64_decode( $html_header_post->post_content ) ) : '';
$html_footer      = isset( $html_footer_post->post_content ) ? wp_unslash( base64_decode( $html_footer_post->post_content ) ) : '';
$html_body_top    = isset( $html_body_top_post->post_content ) ? wp_unslash( base64_decode( $html_body_top_post->post_content ) ) : '';
$html_body_bottom = isset( $html_body_bottom_post->post_content ) ? wp_unslash( base64_decode( $html_body_bottom_post->post_content ) ) : '';

?>
<h2><?php _e('Global Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-textarea" id="section_html_custom_header">
    <label class="heading" for="html_custom_header"><?php _e('Header scripts', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_custom_header" id="html_custom_header" rows="8"><?php echo $html_header; ?></textarea>
        </div>
        <div class="explain">Add custom scripts inside HEAD tag. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_custom_footer">
    <label class="heading" for="html_custom_footer"><?php _e('Footer scripts', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_custom_footer" id="html_custom_footer" rows="8"><?php echo $html_footer; ?></textarea>
        </div>
        <div class="explain">Add custom scripts you might want to be loaded in the footer of your website. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_custom_body_top">
    <label class="heading" for="html_custom_body_top"><?php _e('Body scripts - TOP', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_custom_body_top" id="html_custom_body_top" rows="8"><?php echo $html_body_top; ?></textarea>
        </div>
        <div class="explain">Add custom scripts just after the BODY tag opened. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
<div class="section section-textarea" id="section_html_custom_body_bottom">
    <label class="heading" for="html_custom_body_bottom"><?php _e('Body scripts - BOTTOM', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control codemirror_html" name="html_custom_body_bottom" id="html_custom_body_bottom" rows="8"><?php echo $html_body_bottom; ?></textarea>
        </div>
        <div class="explain">Add custom scripts just before the BODY tag closed. You need to have a SCRIPT tag around scripts.</div>
    </div>
</div>
