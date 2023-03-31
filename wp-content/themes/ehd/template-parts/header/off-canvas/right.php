<?php

use EHD\Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Displays off-canvas navigation
 *
 * @package ehd
 */

$txt_logo = Helper::getOption( 'blogname' );
$img_logo = Helper::getThemeMod( 'alternative_logo' );

if ( ! $img_logo ) :
	$html = sprintf( '<a href="%1$s" class="mobile-logo-link" rel="home">%2$s</a>', Helper::home(), $txt_logo );
else :
	$image = '<img src="' . $img_logo . '" alt="mobile logo">';
	$html  = sprintf( '<a href="%1$s" class="mobile-logo-link" rel="home">%2$s</a>', Helper::home(), $image );
endif;

?>
<div class="off-canvas position-right" id="off-canvas-menu" data-off-canvas data-content-scroll="false">
    <div class="menu-heading-outer">
        <button class="menu-lines" aria-label="Close" type="button" data-close>
            <span class="line line-1"></span>
            <span class="line line-2"></span>
        </button>
        <div class="title-bar-title"><?php echo $html; ?></div>
	    <?php
	    if (is_active_sidebar('w-language-sidebar'))
		    dynamic_sidebar('w-language-sidebar');
	    ?>
    </div>
</div>
