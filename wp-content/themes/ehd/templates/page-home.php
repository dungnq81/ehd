<?php
/**
 * The template for displaying homepage
 * Template Name: Home
 * Template Post Type: page
 */

\defined( 'ABSPATH' ) || die;

use EHD\Cores\Helper;

get_header('home');

if (have_posts()) the_post();

echo '<main role="main">';

if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

if (Helper::stripSpace($post->post_content)) {
    echo '<section class="section homepage-section"><div class="grid-container">';
    echo '<div class="content clearfix">';

    // post content
    the_content();

    echo '</div></div>';
    echo '</section>';
}

// homepage widget
if (is_active_sidebar('w-home-sidebar')) :
    dynamic_sidebar('w-home-sidebar');
endif;

echo '</main>';

get_footer('home');
