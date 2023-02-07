<?php
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
*/

use EHD\Cores\Helper;

if ( post_password_required() ) {
    return;
}

$fb_appid = Helper::getThemeMod( 'fb_menu_setting' );
if ( ! $fb_appid ) {
    return;
}

?>
<div class="facebook-comments-area comments-area">
    <h5 class="comments-title"><?php echo __( 'Facebook comments', EHD_TEXT_DOMAIN ) ?></h5>
    <div class="fb-comments" data-href="<?php echo get_the_permalink(); ?>" data-numposts="10" data-colorscheme="light" data-order-by="social" data-mobile="true"></div>
</div>
