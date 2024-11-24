<?php

    function yoft_delete_data_form() {
        if ( isset( $_POST[ 'b3_remove_date_nonce' ] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'b3_remove_date_nonce' ] ) ), 'b3-remove-date-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'b3-assets-tracker' ) ) );
                    return;
                }

            } else {
                global $wpdb;
                $table_data = $wpdb->prefix . 'asset_data';

                // remove data form
                if ( isset( $_POST[ 'bp_remove_date' ] ) ) {
                    global $wpdb;
                    $date = sanitize_text_field( wp_unslash( $_POST[ 'bp_remove_date' ] ) );

                    if ( 'all' === $date ) {
                        $wpdb->query( $wpdb->prepare( "TRUNCATE TABLE %i", $table_data ) );

                    } else {
                        $deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM %i WHERE date = %s", $table_data, $date ) );

                        if ( $deleted && is_int( $deleted ) && function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'success_date_removed', esc_html( __( 'Date removed.', 'b3-assets-tracker' ) ) );
                        }
                    }
                }
            }
        }
    }
    add_action( 'init', 'yoft_delete_data_form' );
