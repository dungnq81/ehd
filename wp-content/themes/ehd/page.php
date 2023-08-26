<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package eHD
 * @since 1.0.0
 */

\defined( 'ABSPATH' ) || die;

get_header();

the_content();

// homepage widget
if ( is_active_sidebar( 'ehd-home-sidebar' ) ) :
	dynamic_sidebar( 'ehd-home-sidebar' );
endif;

?>
    <div class="layout-demo grid-container">
        <div class="grid-y grid-gap is-grid m-up-4 t-up-3 d-up-2">
            <div class="cell cell-1" style="background-color: #0a4b78;padding: 20px;">cell-1</div>
            <div class="cell cell-2" style="background-color: #0c6ca0;padding: 20px;">cell-2</div>
            <div class="cell cell-3" style="background-color: #00a32a;padding: 20px;">cell-3</div>
            <div class="cell cell-4" style="background-color: #0c88b4;padding: 20px;">cell-4</div>
            <div class="cell cell-5" style="background-color: #6f42c1;padding: 20px;">cell-5</div>
            <div class="cell cell-6" style="background-color: #8a6d3b;padding: 20px;">cell-6</div>
            <div class="cell cell-7" style="background-color: #f4a224;padding: 20px;">cell-7</div>
            <div class="cell cell-8" style="background-color: darkred;padding: 20px;">cell-8</div>
            <div class="cell cell-9" style="background-color: #ff2222;padding: 20px;">cell-9</div>
            <div class="cell cell-10" style="background-color: mediumblue;padding: 20px;">cell-10</div>
            <div class="cell cell-11" style="background-color: purple;padding: 20px;">cell-11</div>
            <div class="cell cell-12" style="background-color: yellowgreen;padding: 20px;">cell-12</div>
        </div>
    </div>
<?php

get_footer();
