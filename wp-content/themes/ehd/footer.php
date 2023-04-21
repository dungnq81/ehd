<?php

/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 * @package hd
 */

\defined( 'ABSPATH' ) || die;

?>
            </div><!-- // .site-content -->
        </div><!-- // .site-page -->
        <?php

        /**
         * ehd_before_footer hook.
         */
        do_action( 'ehd_before_footer' );

        ?>
        <div class="site-footer">
            <?php

            /**
             * ehd_before_footer_content hook.
             */
            do_action( 'ehd_before_footer_content' );

            /**
             * ehd_footer hook.
             */
            do_action( 'ehd_footer' );

            /**
             * ehd_after_footer_content hook.
             */
            do_action( 'ehd_after_footer_content' );

            ?>
        </div>
        <?php

        /**
         * ehd_after_footer hook.
         */
        do_action( 'ehd_after_footer' );

        ?>
    </div><!-- // .site-outer -->

    <?php wp_footer(); ?>

</body>
</html>
