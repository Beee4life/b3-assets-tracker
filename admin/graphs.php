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
        $asset_type         = isset( $_POST[ 'asset_type' ] ) ? $_POST[ 'asset_type' ] : '';
        $asset_type         = isset( $_POST[ 'graph_type' ] ) && 'total' === $_POST[ 'graph_type' ] ? 'all' : $asset_type;
        $dates              = array_keys( $all_data );
        $date_from          = ! empty( $_POST[ 'stats_from' ] ) ? $_POST[ 'stats_from' ] : '';
        $date_from          = isset( $_POST[ 'graph_type' ] ) && 'total' === $_POST[ 'graph_type' ] ? '' : $date_from;
        $date_until         = ! empty( $_POST[ 'stats_until' ] ) ? $_POST[ 'stats_until' ] : '';
        $is_dashboard       = false;
        $is_graph_page      = true;
        $last_date          = end( $dates );
        $graph_type         = isset( $_POST[ 'graph_type' ] ) ? $_POST[ 'graph_type' ] : '';
        $grouped_data       = [];
        $show_asset_types   = true;
        $show_graph_options = true;
        $types              = bp_get_types();
        $view_range         = isset( $_POST[ 'view_range' ] ) ? $_POST[ 'view_range' ] : false;

        $range              = [
            '1'         => 'All dates',
            'begin_end' => 'Begin/end',
        ];
        
        $graph_options = [
            // 'bar'   => 'BarChart',
            'line'  => 'LineChart',
            // 'pie'   => 'PieChart',
            'total' => 'Total (PieChart)',
        ];
        
        if ( b3_validate_graph_fields( $_POST ) ) {
            $add_graph = true;
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
                <?php if ( empty( $_POST ) ) { ?>
                    <?php include 'includes/graphs-help.php'; ?>
                <?php } else { ?>
                    <?php do_action( 'add_graph', $add_graph ); ?>
                <?php } ?>
            </div>
        </div>
    <?php }
