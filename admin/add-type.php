<?php
    /**
     * Content for the 'add type page'
     */
    function bp_assets_add_type() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }
        $current_type = isset( $_GET[ 'type_id' ] ) ? (int) $_GET[ 'type_id' ] : '';
        $asset_groups = bp_get_asset_groups();
        $asset_types  = bp_get_asset_types();
        $button_label = isset( $_GET[ 'type_id' ] ) ? esc_attr__( 'Update', 'b3-assets-tracker' ) : esc_attr__( 'Add', 'b3-assets-tracker' );
        $preset_types = bp_get_preset_types();
        $closed_value = '';
        $group_value  = '';
        $hide_value   = '';
        $order_value  = '';
        $type_value   = '';

        if ( $current_type ) {
            $columns   = array_column( $asset_types, 'id' );
            $types_key = array_search( $current_type, $columns );

            if ( false !== $types_key ) {
                $type         = $asset_types[ $types_key ];
                $closed_value = ! empty( $type->closed ) && '0000-00-00' !== $type->closed ? true : false;
                $group_value  = $type->asset_group;
                $hide_value   = $type->hide;
                $order_value  = $type->ordering;
                $type_value   = $type->name;
            }
        }
        ?>

        <div id="wrap">

            <h1>
                <?php echo esc_html( get_admin_page_title() ); ?>
            </h1>

            <?php
                if ( function_exists( 'bp_show_error_messages' ) ) {
                    bp_show_error_messages();
                }

                do_action( 'bp_admin_menu' );

                include 'includes/types-input.php';

                if ( $asset_types ) {
                    include 'includes/types-output.php';
                }
            ?>
        </div>
    <?php }
