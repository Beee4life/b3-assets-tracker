<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_graphs() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        
        $add_graph          = false;
        $all_dates          = array_values( bp_get_dates() );
        $all_data           = bp_get_data();
        $asset_type         = ! empty( $_POST[ 'asset_type' ] ) ? $_POST[ 'asset_type' ] : '';
        $dates              = array_keys( $all_data );
        $date_from          = ! empty( $_POST[ 'stats_from' ] ) ? $_POST[ 'stats_from' ] : '';
        $date_until         = ! empty( $_POST[ 'stats_until' ] ) ? $_POST[ 'stats_until' ] : '';
        $last_date          = end( $dates );
        $graph_type         = isset( $_POST[ 'graph_type' ] ) ? $_POST[ 'graph_type' ] : '';
        $grouped_data       = [];
        $show_all           = isset( $_POST[ 'show_all' ] ) ? '1' : '';
        $show_asset_types   = true;
        $show_graph         = true;
        $show_graph_options = true;
        $types              = bp_get_types();
        
        
        $graph_options = [
            // 'bar'   => 'BarChart',
            'line'  => 'LineChart',
            // 'pie'   => 'PieChart',
            // 'total' => 'Total (PieChart)',
        ];
        
        if ( ! empty( $_POST ) ) {
            if ( ! isset( $_POST[ 'graph_type' ] ) || empty( $_POST[ 'graph_type' ] ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_no_type', esc_html( __( 'No graph type selected.', 'assets' ) ) );
                }
            } else {
                $add_graph  = true;
            }
        }
    ?>

        <div id="wrap">
            <h1>
                Graphs
            </h1>

            <?php
                if ( function_exists( 'bp_show_error_messages' ) ) {
                    bp_show_error_messages();
                }
            ?>
            
            <?php echo B3AssetsTracker::bp_admin_menu(); ?>

            <div id="data-input">
                <?php include 'includes/from-till-form.php'; ?>
                <?php do_action( 'add_graph', $add_graph ); ?>
            </div>
        </div>
    <?php }
