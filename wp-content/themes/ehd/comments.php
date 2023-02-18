<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to generate_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @package eHD
 * @since 1.0.0
 */

\defined( 'ABSPATH' ) || die;

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}

?>
<section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Post Comments', EHD_TEXT_DOMAIN ); ?>">

</section><!-- #comments -->
