<?php

use EHD\Cores\Helper;

$contact_options = get_option( 'contact_btn__options' );

$contact_title = $contact_options['contact_title'] ?? '';
$contact_url = $contact_options['contact_url'] ?? '';
$contact_window = $contact_options['contact_window'] ?? '';

$contact_popup_content = Helper::getCustomPostContent( 'html_contact', false );

?>
<h2><?php _e('Contact Button Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-text" id="section_contact_button_title">
    <label class="heading" for="contact_title"><?php _e('Contact Button Title', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($contact_title); ?>" class="ehd-input ehd-control" type="text" id="contact_title" name="contact_title">
        </div>
    </div>
</div>
<div class="section section-text" id="section_contact_url">
    <label class="heading" for="contact_url"><?php _e('Contact Button URL', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($contact_url); ?>" class="ehd-input ehd-control" type="url" id="contact_url" name="contact_url" placeholder="https://">
        </div>
    </div>
</div>
<div class="section section-checkbox" id="section_contact_button_window">
    <div class="option" style="padding-top: 15px;">
        <div class="controls">
            <label><input type="checkbox" class="ehd-checkbox ehd-control" name="contact_window" id="contact_window" <?php checked($contact_window, 1); ?> value="1"></label>
        </div>
        <div class="explain"><?php _e( 'Open link in a new window', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-textarea" id="section_contact_popup_content">
    <label class="heading" for="contact_popup_content"><?php _e('Popup Content', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="ehd-textarea ehd-control" name="contact_popup_content" id="contact_popup_content" rows="4"><?php echo $contact_popup_content; ?></textarea>
        </div>
        <div class="explain">The content of the popup, usually the content of a shortcode or image</div>
    </div>
</div>