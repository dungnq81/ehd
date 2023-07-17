<?php
/**
 * The template for displaying Archive pages.
 *
 * @package eHD
 * @since 1.0.0
 */

\defined( 'ABSPATH' ) || die;

get_header();

$post_page_id = get_option( 'page_for_posts' );
$term = get_queried_object();

get_template_part( 'template-parts/header/archive-title', null, [ 'css_class' => 'archive-title' ] );

?>
<section class="section archives archive-posts">

</section>
<?php

get_footer();
