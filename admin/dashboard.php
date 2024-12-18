<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }

        $all_dates   = array_values( bp_get_dates() );
        $data        = bp_get_data();
        $types       = bp_get_asset_types();
        $asset_group = [];
        $asset_type  = 'all';

        if ( ! empty( $data ) ) {
            $dates = array_keys( $data );

            if ( 1 === count( $dates ) ) {
                $date_from = $dates[ 0 ];
            } elseif ( 1 < count( $dates ) ) {
                $date_from = $dates[ count( $dates ) - 2 ];
            }

            $date_until         = end( $dates );
            $graph_type         = false;
            $grouped_data       = [];
            $is_dashboard       = true;
            $scroll_class       = false;
            $show_all_option    = true;
            $show_all           = false;
            $show_asset_groups  = false;
            $show_asset_types   = false;
            $show_diff          = false;
            $show_graph         = false;
            $show_graph_options = false;
            $show_total         = false;

            if ( isset( $_POST[ 'b3_from_till_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'b3_from_till_nonce' ] ) ), 'b3-from-till-nonce' ) ) {
                $asset_group = isset( $_POST[ 'asset_group' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'asset_group' ] ) ) : $asset_group;
                $asset_type  = isset( $_POST[ 'asset_type' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'asset_type' ] ) ) : $asset_type;
                $date_from   = ! empty( $_POST[ 'stats_from' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_from' ] ) ) : '';
                $date_until  = ! empty( $_POST[ 'stats_until' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_until' ] ) ) : '';
                $show_all    = isset( $_POST[ 'show_all' ] ) ? true : false;

            } else {
                $show_diff = isset( $dates[ 1 ] ) ? true : false;
            }

            if ( isset( $_POST[ 'add_type_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'add_type_nonce' ] ) ), 'add-type-nonce' ) ) {
                if ( isset( $_POST[ 'bp_date' ] ) && isset( $_POST[ 'update_data' ] ) ) {
                    // view after update
                    $show_diff = isset( $dates[ 1 ] ) ? true : false;
                } else {
                    // view after insert
                }
            }

            if ( ! empty( $date_from ) && ! empty( $date_until ) ) {
                $dates        = [ $date_from, $date_until ];
                $grouped_data = bp_get_results_range( $dates, $asset_type, $asset_group, $show_all );
                $show_diff    = true;
                $show_total   = true;

                if ( 1 == count( $grouped_data ) ) {
                    $show_diff = false;
                }

            } else {
                // when are there no dates ?
               $grouped_data = array_reverse( $data );
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

            <?php if ( ! empty( $grouped_data ) ) { ?>
                <div id="data-input">
                    <?php include 'includes/from-till-form.php'; ?>
                </div>

                <div id="data-output">
                    <?php if ( $grouped_data ) { ?>
                        <?php include 'includes/data-output.php'; ?>
                    <?php } ?>
                </div>
            <?php } elseif ( empty( $types ) ) { ?>
                <div id="data-output">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=bp-assets-types' ) ); ?>">
                        <?php esc_html_e( 'Add types first', 'b3-assets-tracker' ); ?>
                    </a>
                </div>
            <?php } else { ?>
                <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=bp-assets-add-data' ) ); ?>">
                    <?php esc_html_e( 'Add data now', 'b3-assets-tracker' ); ?>
                </a>
            <?php } ?>
        </div>
    <?php
    }
