<?php
    
    defined( 'ABSPATH' ) || exit;
    
    $pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=woo_variation_swatches_pro_run_migration&status=pending' );
    
    $cron_disabled = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
    $cron_cta      = $cron_disabled ? esc_html__( 'You should manually run queued updates here.', 'woo-variation-swatches-pro' ) : esc_html__( 'View progress &rarr;', 'woo-variation-swatches-pro' );

?>

    <p>
        <strong><?php esc_html_e( 'Variation Swatches for WooCommerce - Pro', 'woo-variation-swatches-pro' ); ?></strong><br>
        <?php esc_html_e( 'Variation Swatches for WooCommerce - Pro is migrating in the background. The migration process may take a little while, so please be patient.', 'woo-variation-swatches-pro' ); ?>
        <?php
            if ( $cron_disabled ) {
                echo '<br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this migration from completing.', 'woo-variation-swatches-pro' );
            }
        ?>
        &nbsp;<a href="<?php echo esc_url( $pending_actions_url ); ?>"><?php echo esc_html( $cron_cta ); ?></a>
    </p>

