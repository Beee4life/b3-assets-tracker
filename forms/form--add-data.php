<?php

    function yoft_add_data_form() {
        global $wpdb;
        $table_data = $wpdb->prefix . 'asset_data';

        // add/update data
        if ( isset( $_POST[ 'add_data_nonce' ] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'add_data_nonce' ] ) ), 'add-data-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'b3-assets-tracker' ) ) );
                }

            } else {
                $validated_fields = bp_validate_form_input( $_POST );

                if ( true === $validated_fields ) {
                    unset( $_POST[ 'add_data_nonce' ] );
                    $input  = $_POST;
                    $values = is_array( $input[ 'bp_value' ] ) ? $input[ 'bp_value' ] : [];

                    // private feature for Beee
                    if ( getenv( 'ASSETS' ) && ! empty( getenv( 'ASSETS' ) ) && 7 == get_current_blog_id() ) {
                        $assets = explode( ',', getenv( 'ASSETS' ) );

                        if ( ! empty( $values[ $assets[ 0 ] ] ) && ! empty( $values[ $assets[ 1 ] ] ) ) {
                            $total_degiro = $values[ $assets[ 0 ] ];
                            $total_etf    = $values[ $assets[ 1 ] ];
                            $total_stocks = $total_degiro - $total_etf;
                            $values[ 4 ]  = (string) $total_stocks;
                        }
                    }

                    if ( isset( $input[ 'update_data' ] ) ) {
                        // update row
                        foreach( $values as $type => $value ) {
                            $data = [
                                'value' => ! empty( $value ) ? sanitize_text_field( $value ) : '0.00',
                            ];
                            $where = [
                                'date' => sanitize_text_field( $input[ 'update_data' ] ),
                                'type' => (int) $type,
                            ];
                            $format = [
                                '%f'
                            ];

                            // check if exists
                            $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE type = %d and date = %s", $table_data, (int) $type, $input[ 'update_data' ] ) );

                            if ( null == $row ) {
                                $data = [
                                    'value'   => ! empty( $value ) ? sanitize_text_field( $value ) : '0.00',
                                    'date'    => sanitize_text_field( $input[ 'update_data' ] ),
                                    'type'    => $type,
                                    'updated' => time(),
                                ];
                                $wpdb->insert( $table_data, $data );

                            } else {
                                $data[ 'updated' ] = time();
                                $format[]          = '%d';
                                $wpdb->update( $table_data, $data, $where, $format );
                            }

                        }

                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'success_values_updated', esc_html__( 'Values updated.', 'b3-assets-tracker' ) );
                        }

                    } else {
                        // insert row
                        $date        = sanitize_text_field( $input[ 'bp_date' ] );
                        $date_exists = bp_date_exists( $date );

                        if ( true === $date_exists ) {
                            if ( function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'error_date_exists', esc_html__( 'This date already exists, please edit the existing date.', 'b3-assets-tracker' ) );
                            }
                        } else {
                            foreach( $values as $type => $value ) {
                                $data = [
                                    'date'    => $date,
                                    'type'    => (int) $type,
                                    'value'   => ! empty( $value ) ? sanitize_text_field( $value ) : '0.00',
                                    'updated' => time(),
                                ];
                                $format = [
                                    '%s',
                                    '%d',
                                    '%f',
                                    '%d',
                                ];
                                $wpdb->insert( $table_data, $data );
                            }
                            if ( function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_values_inserted', esc_html__( 'Values inserted.', 'b3-assets-tracker' ) );
                            }
                        }
                    }

                } elseif ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( $validated_fields[ 'code' ], esc_html( $validated_fields[ 'message' ] ) );
                }


                if ( ! is_admin() ) {
                    $redirect_url = get_home_url();

                    if ( ! empty( bp_errors()->get_error_codes() ) ) {
                        $message = bp_errors()->get_error_codes()[0];

                        switch( $message ) {
                            case 'error_date_exists':
                                $redirect_url = add_query_arg( 'oops', 'date-exists', $redirect_url );
                                break;
                            case 'success_values_inserted':
                                $redirect_url = add_query_arg( 'data', 'inserted', $redirect_url );
                                break;
                            case 'success_values_inserted':
                                $redirect_url = add_query_arg( 'data', 'updated', $redirect_url );
                                break;
                        }

                    } else {
                        // fallback
                        $redirect_url = add_query_arg( 'data', 'updated', $redirect_url );
                    }

                    wp_redirect( $redirect_url );
                    exit;
                }
            }
        }
    }
    add_action( 'init', 'yoft_add_data_form' );
