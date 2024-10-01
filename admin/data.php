<?php
    /**
     * Content for the 'data input page'
     */
    function bp_assets_data() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'bpnl' ) ) );
        }
        
        $all_dates    = array_values( bp_get_dates() );
        $data_8dates  = bp_get_data();
        $scroll_class = false;
        $types        = bp_get_types();

        if ( $data_8dates ) {
            $dates      = array_keys( $data_8dates );
            $date_range = false;
            $months     = [];
            $show_diff  = false;
            $show_total = true;
            
            foreach( $all_dates as $date ) {
                $year_month = gmdate( 'Ym', strtotime( $date ) );
                $month      = gmdate( 'F Y', strtotime( $date ) );
                
                if ( ! array_key_exists( $year_month, $months ) ) {
                    $months[ $year_month ] = $month;
                }
            }
            
            if ( ! empty( $_POST[ 'bp_date_range' ] ) ) {
                $date_range      = $_POST[ 'bp_date_range' ];
                $grouped_data    = bp_get_data( $_POST[ 'bp_date_range' ] );
                
                if ( 1 < count( $grouped_data ) ) {
                    $show_diff  = true;
                    $show_total = true;
                }
            } else {
                // default view
                $grouped_data = $data_8dates;
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
            ?>
            
            <?php echo B3AssetsTracker::bp_admin_menu(); ?>
            
            <?php if ( empty( $grouped_data ) && empty( $types ) ) { ?>
                <div id="data-input">
                    <a href="<?php echo admin_url( 'admin.php?page=bp-assets-types' ); ?>">
                        Add types first
                    </a>
                </div>
            <?php } elseif ( empty( $grouped_data ) && empty( ! $types ) ) { ?>
                <div id="data-input">
                    <a href="<?php echo admin_url( 'admin.php?page=bp-assets-add-data' ); ?>">
                        Add data now
                    </a>
                </div>
            <?php } else { ?>
                <div id="data-input">
                    <?php include 'includes/date-range-form.php'; ?>
                    <?php include 'includes/remove-date-form.php'; ?>
                </div>

                <div id="data-output">
                    <?php if ( 15 < count( $grouped_data[ 0 ] ) ) { ?>
                        <?php echo sprintf( '<div class="shortcode-notice tablescroll">%s</div>', 'Table scrolls horizontally.' ); ?>
                    <?php } ?>
                    <?php include 'includes/data-output.php'; ?>
                </div>
            <?php } ?>
        </div>
    <?php }
