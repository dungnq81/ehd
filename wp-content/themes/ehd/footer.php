<?php

/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 * @package ehd
 */

\defined( 'ABSPATH' ) || die;

?>
            </div><!-- .site-content -->
        </div><!-- .site-page -->
        <?php

        do_action( 'ehd_before_footer' );

        ?>
        <div class="site-footer">
            <?php

            do_action( 'ehd_before_footer_content' );

            /**
             * @see __construct_footer_widgets - 5
             * @see __construct_footer - 10
             */
            do_action( 'ehd_footer' );

            do_action( 'ehd_after_footer_content' );

            ?>
        </div>
        <?php

        do_action( 'ehd_after_footer' );

        ?>
    </div><!-- .site-outer -->

    <?php wp_footer(); ?>

</body>
</html>
