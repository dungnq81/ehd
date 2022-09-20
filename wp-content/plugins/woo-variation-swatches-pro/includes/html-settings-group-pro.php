<?php
    defined( 'ABSPATH' ) or die( 'Keep Quit' );
    
    $action_link = woo_variation_swatches()->get_backend()->get_admin_menu()->get_settings_link( 'woo_variation_swatches', array( 'section' => 'group', 'action' => 'new-group' ) );
    $delete_link = woo_variation_swatches()->get_backend()->get_admin_menu()->get_settings_link( 'woo_variation_swatches', array( 'section' => 'group', 'action' => 'delete-group' ) );
    $edit_link   = woo_variation_swatches()->get_backend()->get_admin_menu()->get_settings_link( 'woo_variation_swatches', array( 'section' => 'group', 'action' => 'edit-group' ) );
    
    
    $is_edit = isset( $_GET[ 'action' ] ) && ( $_GET[ 'action' ] === 'edit-group' ) && isset( $_GET[ 'slug' ] );
    
    $get_all = (array) woo_variation_swatches()->get_backend()->get_group()->get_all();
    
    $data = false;
    if ( $is_edit ) {
        $slug        = sanitize_text_field( $_GET[ 'slug' ] );
        $action_link = woo_variation_swatches()->get_backend()->get_admin_menu()->get_settings_link( 'woo_variation_swatches', array( 'section' => 'group', 'action' => 'update-group', 'slug' => $_GET[ 'slug' ] ) );
        $data        = woo_variation_swatches()->get_backend()->get_group()->get( $slug );
    }
?>

<h2><?php esc_html_e( 'Swatches Group', 'woo-variation-swatches-pro' ); ?></h2>

<div class="woo-variation-swatches-group-section-wrapper">

    <div id="col-container">

        <div id="col-left">
            <div class="col-wrap">

                <div class="form-wrap">
                    
                    <?php if ( $is_edit ): ?>
                        <h3><?php esc_html_e( 'Edit Group', 'woo-variation-swatches-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Edit existing group name.', 'woo-variation-swatches-pro' ); ?></p>
                    <?php else: ?>
                        <h3><?php esc_html_e( 'Add New Group', 'woo-variation-swatches-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Create group based attributes terms.', 'woo-variation-swatches-pro' ); ?></p>
                    
                    <?php endif; ?>

                    <form action="<?php echo esc_url( $action_link ) ?>" method="post">
                        <div class="form-field">
                            <label for="group_name"><?php esc_html_e( 'Name', 'woo-variation-swatches-pro' ); ?></label>
                            <input required name="woo_variation_swatches_group[name]" value="<?php echo ( $is_edit ) ? esc_attr( $data ) : '' ?>" id="woo_variation_swatches_group_name" type="text"/>
                            <p class="description"><?php esc_html_e( 'Name for the group (shown on the front-end).', 'woo-variation-swatches-pro' ); ?></p>
                        </div>
                        
                        <?php if ( ! $is_edit ): ?>
                            <div class="form-field">
                                <label for="group_slug"><?php esc_html_e( 'Slug', 'woo-variation-swatches-pro' ); ?></label>
                                <input name="woo_variation_swatches_group[slug]" id="woo_variation_swatches_group_slug" type="text" maxlength="28"/>
                                <p class="description"><?php esc_html_e( 'Unique slug/reference for the group; must be no more than 28 characters. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'woo-variation-swatches-pro' ); ?></p>
                            </div>
                        <?php endif; ?>

                        <p class="submit">
                            <?php wp_nonce_field( 'woo_variation_swatches_group' ); ?>
                            <button type="submit" id="submit" class="button button-primary"><?php echo( ( $is_edit ) ? esc_html__( 'Update Group', 'woo-variation-swatches-pro' ) : esc_html__( 'Add New Group', 'woo-variation-swatches-pro' ) ); ?></button>
                        </p>

                    </form>
                </div>

            </div>
        </div>
        <div id="col-right">
            <div class="col-wrap">

                <table class="widefat woo-variation-swatches-group-table-list wp-list-table ui-sortable" style="width:100%">
                    <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e( 'Name', 'woo-variation-swatches-pro' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Slug', 'woo-variation-swatches-pro' ); ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    
                    <?php
                        
                        if ( empty( $get_all ) ):
                            ?>
                            <tr>
                                <td colspan="2"><?php esc_html_e( 'No groups currently exist.', 'woo-variation-swatches-pro' ); ?></td>
                            </tr>
                        
                        <?php else: ?>
                            
                            <?php foreach ( $get_all as $slug => $group_name ): ?>

                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( $group_name ) ?></strong>
                                        <div class="row-actions">
                                            <span class="edit"><a href="<?php echo wp_nonce_url( $edit_link . '&amp;slug=' . $slug, 'woo_variation_swatches_group' ) ?>"><?php esc_html_e( 'Edit', 'woo-variation-swatches-pro' ); ?></a> | </span>
                                            <span class="delete"><a class="delete" href="<?php echo wp_nonce_url( $delete_link . '&amp;slug=' . $slug, 'woo_variation_swatches_group' ) ?>"><?php esc_html_e( 'Delete', 'woo-variation-swatches-pro' ); ?></a></span></div>

                                    </td>
                                    <td><?php echo esc_html( $slug ) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        
                        <?php endif; ?>


                    </tbody>

                </table>

            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    /* <![CDATA[ */

    jQuery('a.delete').on('click', function () {
        if (window.confirm('<?php esc_html_e( 'Are you sure you want to delete this group?', 'woo-variation-swatches-pro' ); ?>')) {
            return true;
        }
        return false;
    });

    /* ]]> */
</script>