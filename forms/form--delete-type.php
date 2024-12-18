<?php

    function yoft_delete_type_form() {
        global $wpdb;
        $table_data  = $wpdb->prefix . 'asset_data';
        $table_types = $wpdb->prefix . 'asset_types';

        // delete types form
        if ( isset( $_POST[ 'delete_types_nonce' ] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'delete_types_nonce' ] ) ), 'delete-types-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'b3-assets-tracker' ) ) );
                }
            } else {
                if ( isset( $_POST[ 'bp_delete_type' ] ) && is_array( $_POST[ 'bp_delete_type' ] ) && ! empty( $_POST[ 'bp_delete_type' ] ) ) {
                    foreach( sanitize_text_field( wp_unslash( $_POST[ 'bp_delete_type' ] ) ) as $type ) {
                        // delete type
                        $wpdb->delete( $table_types, [ 'id' => (int) $type ], [ '%d' ] );
                        // delete entries with type
                        $wpdb->delete( $table_data, [ 'id' => (int) $type ], [ '%d' ] );
                    }
                }
            }
        }
    }
    add_action( 'init', 'yoft_delete_type_form' );
