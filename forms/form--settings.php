<?php
    function yoft_settings_form() {
        if ( isset( $_POST[ 'assets_settings_nonce' ] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'assets_settings_nonce' ] ) ), 'assets-settings-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'b3-assets-tracker' ) ) );
                }

            } else {
                if ( ! empty( $_POST[ 'bp_currency' ] ) ) {
                    update_option( 'bp_currency', sanitize_text_field( wp_unslash( $_POST[ 'bp_currency' ] ) ) );
                } else {
                    update_option( 'bp_currency', '&euro;' );
                }
                if ( ! empty( $_POST[ 'bp_date_format' ] ) ) {
                    update_option( 'bp_date_format', sanitize_text_field( wp_unslash( $_POST[ 'bp_date_format' ] ) ) );
                } else {
                    update_option( 'bp_date_format', 'd-m-y' );
                }
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'success_settings_saved', esc_html( __( 'Settings saved.', 'b3-assets-tracker' ) ) );
                }
            }
        }
    }
    add_action( 'init', 'yoft_settings_form' );
