<?php

/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 * @package hd
 */

\defined( '\WPINC' ) || die;// Exit if accessed directly.

    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :

        ?>
        <div class="site-footer">
            <?php do_action( 'before_footer' ); ?>
            <?php do_action( 'w_footer' ); ?>
            <?php do_action( 'footer' ); ?>
        </div>
    <?php
        do_action( 'after_footer' );
    endif;
    wp_footer();
    ?>
    </div>
</body>
</html>
