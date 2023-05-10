<?php

use EHD_Cores\Helper;

$security_options = Helper::getOption( 'security__options', false, false );

$xml_rpc_off   = $security_options['xml_rpc_off'] ?? '';
$remove_readme = $security_options['remove_readme'] ?? '';
$rss_feed_off = $security_options['rss_feed_off'] ?? '';

?>
<h2><?php _e( 'Security Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_xml_rpc_off">
	<label class="heading" for="xml_rpc_off"><?php _e( 'Disable XML-RPC', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
	<div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="xml_rpc_off" id="xml_rpc_off" <?php checked($xml_rpc_off, 1); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'Unless you specifically need to use it, we recommend that XML-RPC is always disabled.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_remove_readme">
	<label class="heading" for="remove_readme"><?php _e( 'Delete the default readme.html', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
	<div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="remove_readme" id="remove_readme" <?php checked($remove_readme, 1); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'Remove the readme.html file.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_rss_feed_off">
	<label class="heading" for="rss_feed_off"><?php _e( 'Disable RSS and ATOM Feeds', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
	<div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="rss_feed_off" id="rss_feed_off" <?php checked($rss_feed_off, 1); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'RSS and ATOM feeds are often used to scrape your content and to perform a number of attacks against your site.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
