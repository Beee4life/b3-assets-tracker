<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_graphs() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        
        $all_dates          = array_values( bp_get_dates() );
        $all_data           = bp_get_data();
        $asset_type         = '';
        $dates              = array_keys( $all_data );
        $last_date          = end( $dates );
        $date_from          = '';
        $date_until         = '';
        $graph_type         = isset( $_POST[ 'graph_type' ] ) ? $_POST[ 'graph_type' ] : '';
        $grouped_data       = [];
        $show_all           = isset( $_POST[ 'show_all' ] ) ? $_POST[ 'show_all' ] : false;
        $show_asset_types   = true;
        $show_graph         = true;
        $show_graph_options = true;
        $types              = bp_get_types();
        
        $graph_options = [
            'bar'   => 'BarChart',
            'line'  => 'LineChart',
            'pie'   => 'PieChart',
            'total' => 'Total (PieChart)',
        ];
        
        if ( ! empty( $_POST ) ) {
            if ( ! isset( $_POST[ 'graph_type' ] ) || empty( $_POST[ 'graph_type' ] ) ) {
                bp_errors()->add( 'error_no_type', esc_html( __( 'No graph type selected.', 'assets' ) ) );
            } else {
                $date_from    = ! empty( $_POST[ 'stats_from' ] ) ? $_POST[ 'stats_from' ] : '';
                $date_until   = ! empty( $_POST[ 'stats_until' ] ) ? $_POST[ 'stats_until' ] : '';
                $asset_type   = ! empty( $_POST[ 'asset_type' ] ) ? $_POST[ 'asset_type' ] : '';
                $grouped_data = bp_get_results_range( $date_from, $date_until, $asset_type, $show_all );
            }

        } else {
            $grouped_data = $all_data;
            
            if ( empty( $grouped_data ) && isset( $_POST[ 'graph_type' ] ) && 'total' === $_POST[ 'graph_type' ] ) {
                $grouped_data = [ 'array' ];
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
            
            <?php echo BpAssets::bp_admin_menu(); ?>

            <div id="data-input">
                <?php include 'includes/from-till-form.php'; ?>
                <?php do_action( 'add_graph', $grouped_data ); ?>
            </div>
        </div>
    <?php }
