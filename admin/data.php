<?php
    /**
     * Content for the 'data input page'
     */
    function bp_assets_data() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }

        $all_dates    = array_values( bp_get_dates() );
        $amount       = 8; // default: 8
        $data         = bp_get_data( '', 'reverse', $amount );
        $scroll_class = false;
        $types        = bp_get_asset_types();

        if ( $data ) {
            $dates      = array_keys( $data );
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

            if ( isset( $_POST[ 'b3_date_range_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'b3_date_range_nonce' ] ) ), 'b3-date-range-nonce' ) ) {
                if ( ! empty( $_POST[ 'bp_date_range' ] ) ) {
                    $date_range   = sanitize_text_field( wp_unslash( $_POST[ 'bp_date_range' ] ) );
                    $grouped_data = bp_get_data( $date_range );

                    if ( 1 < count( $grouped_data ) ) {
                        $show_diff  = true;
                        $show_total = true;
                    }
                } else {
                    // default view
                    $grouped_data = $data;
                }
            } else {
                // default view
                $grouped_data = $data;
            }

            $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
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
            ?>

            <?php if ( empty( $grouped_data ) && empty( $types ) ) { ?>
                <div id="data-input">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=bp-assets-types' ) ); ?>">
                        <?php esc_html_e( 'Add types first', 'b3-assets-tracker' ); ?>
                    </a>
                </div>
            <?php } elseif ( empty( $grouped_data ) && empty( ! $types ) ) { ?>
                <div id="data-input">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=bp-assets-add-data' ) ); ?>">
                        <?php esc_html_e( 'Add data now', 'b3-assets-tracker' ); ?>
                    </a>
                </div>
            <?php } else { ?>
                <div id="data-input">
                    <?php include 'includes/date-range-form.php'; ?>
                    <?php include 'includes/remove-date-form.php'; ?>
                    <p>
                        <?php echo sprintf( esc_html__( 'The last %d entries are shown or a specific range, selected above.', 'b3-assets-tracker' ), (int) $amount ); ?>
                    </p>
                </div>

                <div id="data-output">
                    <?php if ( 15 < count( $grouped_data[ 0 ] ) ) { ?>
                        <?php echo sprintf( '<div class="shortcode-notice tablescroll">%s</div>', esc_html__( 'Table scrolls horizontally.', 'b3-assets-tracker' ) ); ?>
                    <?php } ?>
                    <?php include 'includes/data-output.php'; ?>
                </div>
            <?php } ?>
        </div>
    <?php }
