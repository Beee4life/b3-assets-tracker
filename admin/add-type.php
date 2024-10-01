<?php
    /**
     * Content for the 'add type page'
     */
    function bp_assets_add_type() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        $current_type = isset( $_GET[ 'type_id' ] ) ? $_GET[ 'type_id' ] : '';
        $asset_types  = bp_get_types();
        $preset_types = bp_get_preset_types();
        $order_value  = '';
        $hide_value   = '';
        $type_value   = '';
        
        if ( $current_type ) {
            $columns   = array_column( $asset_types, 'id' );
            $types_key = array_search( $current_type, $columns );

            if ( false !== $types_key ) {
                $type        = $asset_types[ $types_key ];
                $type_value  = $type->name;
                $order_value = $type->ordering;
                $hide_value  = $type->hide;
            }
        }
        ?>

        <div id="wrap">

            <h1>
                <?php echo get_admin_page_title(); ?>
            </h1>

            <?php
                if ( function_exists( 'bp_show_error_messages' ) ) {
                    bp_show_error_messages();
                }
            
                echo B3AssetsTracker::bp_admin_menu();
                
                include 'includes/types-input.php';
    
                if ( $asset_types ) {
                    include 'includes/types-output.php';
                }
            ?>
        </div>
    <?php }
