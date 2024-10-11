<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        
        $all_dates   = array_values( bp_get_dates() );
        $data_8dates = bp_get_data();
        $types       = bp_get_asset_types();
        
        if ( ! empty( $data_8dates ) ) {
            $asset_group        = isset( $_POST[ 'asset_group' ] ) ? $_POST[ 'asset_group' ] : [];
            $asset_type         = isset( $_POST[ 'asset_type' ] ) ? $_POST[ 'asset_type' ] : 'all';
            $dates              = array_keys( $data_8dates );
            $date_from          = $dates[ count( $dates ) - 2 ];
            $date_until         = end( $dates );
            $graph_type         = false;
            $grouped_data       = [];
            $is_graph_page      = false;
            $is_dashboard       = true;
            $scroll_class       = false;
            $show_all_option    = true;
            $show_all           = isset( $_POST[ 'show_all' ] ) ? true : false;
            $show_asset_groups  = false;
            $show_asset_types   = false;
            $show_diff          = false;
            $show_graph         = false;
            $show_graph_options = false;
            $show_total         = false;
            
            if ( ! empty( $_POST ) ) {
                if ( isset( $_POST[ 'bp_date' ] ) ) {
                    if ( isset( $_POST[ 'update_data' ] ) ) {
                        // view after update
                        $show_diff = isset( $dates[ 1 ] ) ? true : false;
                    } else {
                        // view after insert
                    }
                } else {
                    // view after use date filter
                    $date_from  = ! empty( $_POST[ 'stats_from' ] ) ? $_POST[ 'stats_from' ] : '';
                    $date_until = ! empty( $_POST[ 'stats_until' ] ) ? $_POST[ 'stats_until' ] : '';
                }
            } else {
                // default view
                $show_diff = isset( $dates[ 1 ] ) ? true : false;
            }
            
            if ( ! empty( $date_from ) && ! empty( $date_until ) ) {
                $grouped_data = bp_get_results_range( $date_from, $date_until, $asset_type, $asset_group, $show_all );
                $show_diff    = true;
                $show_total   = true;

                if ( 1 == count( $grouped_data ) ) {
                    $show_diff = false;
                }
                
            } else {
                // when are there no dates ?
                error_log('Dashboard - No dates');
                $grouped_data = array_reverse( $data_8dates );
            }
            
            $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
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
            ?>

            <?php if ( ! empty( $grouped_data ) ) { ?>
                <div id="data-input">
                    <?php include 'includes/from-till-form.php'; ?>
                </div>
    
                <div id="data-output">
                    <?php if ( $grouped_data ) { ?>
                        <?php include 'includes/data-output.php'; ?>
                        <?php //do_action( 'add_graph', $grouped_data ); ?>
                    <?php } ?>
                </div>
            <?php } elseif ( empty( $types ) ) { ?>
                <div id="data-output">
                    <a href="<?php echo admin_url( 'admin.php?page=bp-assets-types' ); ?>">
                        Add types first
                    </a>
                </div>
            <?php } else { ?>
                <a href="<?php echo admin_url( 'admin.php?page=bp-assets-add-data' ); ?>">
                    Add data now
                </a>
            <?php } ?>
        </div>
    <?php
    }
