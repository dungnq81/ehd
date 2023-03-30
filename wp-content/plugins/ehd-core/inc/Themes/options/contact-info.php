<?php

use EHD\Cores\Helper;

$contact_info_options = Helper::getOption( 'contact_info__options' );

$hotline = $contact_info_options['hotline'] ?? '';
$address = ! empty( $contact_info_options['address'] ) ? wp_unslash($contact_info_options['address']) : '';
$phones = $contact_info_options['phones'] ?? '';
$emails = $contact_info_options['emails'] ?? '';

$contact_info_others = Helper::getCustomPostContent( 'html_others', false );

?>
<h2><?php _e('Contact Info Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-text" id="section_hotline">
	<label class="heading" for="contact_info_hotline"><?php _e('Hotline', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
	<div class="option">
		<div class="controls">
			<input value="<?php echo esc_attr($hotline); ?>" class="ehd-input ehd-control" type="text" id="contact_info_hotline" name="contact_info_hotline">
		</div>
		<div class="explain">Hotline number, support easier interaction on the phone.</div>
	</div>
</div>
<div class="section section-textarea" id="section_address">
	<label class="heading" for="contact_info_address"><?php _e('Address', EHD_PLUGIN_TEXT_DOMAIN) ?></label>
	<div class="option">
		<div class="controls">
			<textarea class="ehd-textarea ehd-control" name="contact_info_address" id="contact_info_address" rows="4"><?php echo $address; ?></textarea>
		</div>
	</div>
</div>
<div class="section section-text" id="section_phones">
	<label class="heading" for="contact_info_phones"><?php _e('Phones', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
	<div class="option">
		<div class="controls">
			<input value="<?php echo esc_attr($phones); ?>" class="ehd-input ehd-control" type="text" id="contact_info_phones" name="contact_info_phones">
		</div>
		<div class="explain">The contact phone number, you may input multiple numbers, separated by "commas."</div>
	</div>
</div>
<div class="section section-text" id="section_emails">
	<label class="heading" for="contact_info_emails"><?php _e('Emails', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
	<div class="option">
		<div class="controls">
			<input value="<?php echo esc_attr($emails); ?>" class="ehd-input ehd-control" type="text" id="contact_info_emails" name="contact_info_emails">
		</div>
		<div class="explain">Email contact address, with the ability to enter multiple addresses, with each address separated by a "comma".</div>
	</div>
</div>
<div class="section section-text" id="section_others">
	<label class="heading" for="contact_info_others"><?php _e('Others', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
	<div class="option">
		<div class="controls">
			<textarea class="ehd-textarea ehd-control codemirror_html" name="contact_info_others" id="contact_info_others" rows="4"><?php echo $contact_info_others; ?></textarea>
		</div>
		<div class="explain">Other supplementary materials</div>
	</div>
</div>
