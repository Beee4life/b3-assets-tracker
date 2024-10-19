<?php

    function yoft_delete_data_form() {
        global $wpdb;
        $table_data = $wpdb->prefix . 'asset_data';

        // remove data form
        if ( isset( $_POST[ 'bp_remove_date' ] ) ) {
            global $wpdb;
            $date = sanitize_text_field( $_POST[ 'bp_remove_date' ] );

            if ( 'all' === $date ) {
                $wpdb->query( "TRUNCATE TABLE $table_data" );

            } else {
                $query = $wpdb->prepare( "DELETE FROM $table_data WHERE date = '%s'", $date );
                $deleted = $wpdb->query($query);

                if ( $deleted && is_int( $deleted ) && function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'success_date_removed', esc_html( __( 'Date removed.', 'b3-assets-tracker' ) ) );
                }
            }
        }
    }
    add_action( 'init', 'yoft_delete_data_form' );
