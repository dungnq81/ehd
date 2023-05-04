<?php

use EHD_Cores\Helper;

$ar_post_type_list = apply_filters( 'ehd_aspect_ratio_post_type', [] );

?>
<h2><?php _e( 'Aspect Ratio Settings', EHD_PLUGIN_TEXT_DOMAIN ); ?></h2>
<?php
foreach ( $ar_post_type_list as $ar ) :
	$title = Helper::mbUcFirst( $ar );

	if ( ! $title ) {
		break;
	}

	$w_h    = Helper::getAspectRatioOption( $ar, 'aspect_ratio__options' );
	$width  = $w_h[0] ?? '';
	$height = $w_h[1] ?? '';

?>
<div class="section section-text" id="section_aspect_ratio">
    <span class="heading"><?php _e( $title, EHD_PLUGIN_TEXT_DOMAIN ); ?></span>
    <div class="option inline-option">
        <div class="controls">
            <div class="inline-group">
                <label>
                    Width:
                    <input class="ehd-input ehd-control" name="<?=$ar?>-width" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr($width); ?>">
                </label>
                <span>x</span>
                <label>
                    Height:
                    <input class="ehd-input ehd-control" name="<?=$ar?>-height" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr($height); ?>">
                </label>
            </div>
        </div>
        <div class="explain"><?php echo $title?> images will be viewed at a custom aspect ratio.</div>
    </div>
</div>
<?php
endforeach;
