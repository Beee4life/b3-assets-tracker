<?php
    /**
     * Content for the 'dashboard page'
     */
    function bp_assets_graphs() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( __( 'Sorry, you do not have sufficient permissions to access this page.', 'b3-assets-tracker' ) ) );
        }

        // @TODO: prefill first/last dates

        $add_graph             = false;
        $all_dates             = array_values( bp_get_dates() );
        $all_data              = bp_get_data();
        $asset_groups          = bp_get_asset_groups();
        $asset_types           = bp_get_asset_types( 'id_name' );
        $asset_types[ 'all' ]  = 'All';
        $dates                 = array_keys( $all_data );
        $date_from             = '';
        $date_until            = '';
        $is_dashboard          = false;
        $graph_type            = '';
        $graph_options         = bp_get_graph_types();
        $grouped_data          = [];
        $last_date             = end( $dates );
        $selected_asset_types  = [];
        $selected_asset_groups = [];
        $show_asset_groups     = true;
        $show_asset_types      = true;
        $show_all_option       = false;
        $show_graph_options    = true;

        if ( isset( $_POST[ 'b3_from_till_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'b3_from_till_nonce' ] ) ), 'b3-from-till-nonce' ) ) {
            $date_from             = ! empty( $_POST[ 'stats_from' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_from' ] ) ) : $date_from;
            $date_from             = isset( $_POST[ 'graph_type' ] ) && str_starts_with( sanitize_text_field( wp_unslash( $_POST[ 'graph_type' ] ) ), 'total' ) ? '' : $date_from;
            $date_until            = ! empty( $_POST[ 'stats_until' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_until' ] ) ) : $date_until;
            $graph_type            = isset( $_POST[ 'graph_type' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'graph_type' ] ) ) : $graph_type;
            $selected_asset_types  = isset( $_POST[ 'asset_type' ] ) ? wp_unslash( $_POST[ 'asset_type' ] ) : $selected_asset_types;
            $selected_asset_groups = isset( $_POST[ 'asset_group' ] ) ? wp_unslash( $_POST[ 'asset_group' ] ) : $selected_asset_groups;

            if ( is_array( $selected_asset_types ) && 1 < count( $selected_asset_types ) && in_array( 'all', $selected_asset_types ) ) {
                // empty types on error
                $selected_asset_types = [];
            }
        }

        if ( ! empty( $_POST ) ) {
            if ( b3_validate_graph_fields( $_POST ) ) {
                $add_graph = true;
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
            ?>

            <div id="data-input">
                <?php
                    if ( 1 < count( $asset_types ) ) {
                        include 'includes/from-till-form.php';

                        if ( empty( $_POST ) ) {
                            include 'includes/graphs-help.php';
                        } elseif ( isset( $add_graph ) && true == $add_graph ) {
                            echo '<div id="chart_div"></div>';
                        }

                    } else {
                        echo sprintf( '<a href="%s">%s</a>', esc_url_raw( admin_url( 'admin.php?page=bp-assets-types' ) ), 'Add types first' );
                    }
                ?>
            </div>
        </div>
    <?php }
