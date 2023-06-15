<?php

use EHD_Cores\Helper;

$security_options = Helper::getOption( 'security__options', false, false );

$xml_rpc_off         = $security_options['xml_rpc_off'] ?? '';
$remove_readme       = $security_options['remove_readme'] ?? '';
$rss_feed_off        = $security_options['rss_feed_off'] ?? '';
$lock_protect_system = $security_options['lock_protect_system'] ?? '';

?>
<h2><?php _e( 'Security Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_xml_rpc_off">
	<label class="heading" for="xml_rpc_off"><?php _e( 'Disable XML-RPC', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'XML-RPC was designed as a protocol enabling WordPress to communicate with third-party systems but recently it has been used in a number of exploits. Unless you specifically need to use it, we recommend that XML-RPC is always disabled.', EHD_PLUGIN_TEXT_DOMAIN )?></div>
	<div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="xml_rpc_off" id="xml_rpc_off" <?php checked( $xml_rpc_off, 1 ); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'Check to activate.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_remove_readme">
	<label class="heading" for="remove_readme"><?php _e( 'Delete the Default Readme.html', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'WordPress comes with a readme.html file containing information about your website. The readme.html is often used by hackers to compile lists of potentially vulnerable sites which can be hacked or attacked.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	<div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="remove_readme" id="remove_readme" <?php checked( $remove_readme, 1 ); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'Remove the readme.html.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_rss_feed_off">
	<label class="heading" for="rss_feed_off"><?php _e( 'Disable RSS and ATOM Feeds', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
	<div class="desc"><?php _e( 'RSS and ATOM feeds are often used to scrape your content and to perform a number of attacks against your site. Only use feeds if you have readers using your site via RSS readers.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    <div class="option">
		<div class="controls">
			<label><input type="checkbox" class="ehd-checkbox ehd-control" name="rss_feed_off" id="rss_feed_off" <?php checked( $rss_feed_off, 1 ); ?> value="1"></label>
		</div>
		<div class="explain"><?php _e( 'Check to activate.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_lock_protect_system">
    <label class="heading" for="lock_protect_system"><?php _e( 'Lock and Protect System Folders', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'By enabling this option you are ensuring that no unauthorised or malicious scripts can be executed in your system folders. This is an often exploited back door you can close with a simple toggle.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    <div class="option">
        <div class="controls">
            <label><input type="checkbox" class="ehd-checkbox ehd-control" name="lock_protect_system" id="lock_protect_system" <?php checked( $lock_protect_system, 1 ); ?> value="1"></label>
        </div>
        <div class="explain"><?php _e( 'Check to activate.', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>
