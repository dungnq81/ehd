<?php

use EHD\Cores\Helper;

$ar_post_type_list    = apply_filters('ar_post_type_list', [ 'blogs' ]);
$aspect_ratio_options = get_option('aspect_ratio__options');

?>
<h2><?php _e( 'Aspect Ratio Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<?php
foreach ( $ar_post_type_list as $ar ) :
	$title = Helper::mbUcFirst( $ar );

	$width  = $aspect_ratio_options[ 'ar-' . $ar . '-width' ] ?? '';
	$height = $aspect_ratio_options[ 'ar-' . $ar . '-height' ] ?? '';
?>
<div class="section section-text" id="section_aspect_ratio">
    <span class="heading"><?php _e( $title, EHD_PLUGIN_TEXT_DOMAIN ); ?></span>
    <div class="option inline-option">
        <div class="controls">
            <div class="inline-group">
                <label>
                    Width:
                    <input name="<?=$ar?>-width" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr($width); ?>">
                </label>
                <span>x</span>
                <label>
                    Height:
                    <input name="<?=$ar?>-height" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr($height); ?>">
                </label>
            </div>
        </div>
        <div class="explain"><?php echo $title?> images will be viewed at a custom aspect ratio.</div>
    </div>
</div>
<?php endforeach; ?>
