<?php

use EHD_Cores\Helper;

$woocommerce__options = Helper::getOption( 'woocommerce__options', false, false );

$remove_legacy_coupon = $woocommerce__options['remove_legacy_coupon'] ?? '';
$woocommerce_jsonld = $woocommerce__options['woocommerce_jsonld'] ?? '';

?>
<h2><?php _e( 'Woocommerce Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="remove_legacy_coupon">
	<label class="heading" for="remove_legacy_coupon"><?php _e( 'Remove legacy coupon menu', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Fixed WooCommerce Admin notice for removing legacy coupon menu does not disappear.', EHD_PLUGIN_TEXT_DOMAIN )?></div>
	<div class="option">
		<div class="controls">
			<input type="checkbox" class="ehd-checkbox ehd-control" name="remove_legacy_coupon" id="remove_legacy_coupon" <?php checked( $remove_legacy_coupon, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Remove legacy coupon', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="woocommerce_jsonld">
    <label class="heading" for="woocommerce_jsonld"><?php _e( 'WooCommerce 3 JSON/LD', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Remove the default WooCommerce 3 JSON/LD structured data format', EHD_PLUGIN_TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="woocommerce_jsonld" id="woocommerce_jsonld" <?php checked( $woocommerce_jsonld, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Remove WooCommerce 3 JSON/LD', EHD_PLUGIN_TEXT_DOMAIN ); ?></div>
    </div>
</div>