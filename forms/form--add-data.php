<?php
    
    function yoft_add_data_form() {
        global $wpdb;
        $table_data = $wpdb->prefix . 'asset_data';

        // add/update data
        if ( isset( $_POST[ 'add_data_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'add_data_nonce' ], 'add-data-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }

            } else {
                $validated_fields = bp_validate_form_input( $_POST );

                if ( true === $validated_fields ) {
                    unset( $_POST[ 'add_data_nonce' ] );
                    $input  = $_POST;
                    $values = is_array( $input[ 'bp_value' ] ) ? $input[ 'bp_value' ] : [];

                    if ( getenv( 'ASSETS' ) ) {
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
                                'value' => ! empty( $value ) ? $value : '0.00',
                            ];
                            $where = [
                                'date' => $input[ 'update_data' ],
                                'type' => $type,
                            ];
                            $format = [
                                '%f'
                            ];

                            // check if exists
                            $query = $wpdb->prepare( "SELECT * FROM $table_data WHERE type = '%d' and date = '%s'", (int) $type, $input[ 'update_data' ] );
                            $row   = $wpdb->get_row( $query );

                            if ( null == $row ) {
                                $data = [
                                    'value'   => ! empty( $value ) ? $value : '0.00',
                                    'date'    => $input[ 'update_data' ],
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
                            bp_errors()->add( 'success_type_updated', esc_html( __( 'Values updated.', 'assets' ) ) );
                        }

                    } else {
                        // insert row
                        foreach( $values as $type => $value ) {
                            $data = [
                                'date'    => $input[ 'bp_date' ],
                                'type'    => $type,
                                'value'   => ! empty( $value ) ? $value : '0.00',
                                'updated' => time(),
                            ];
                            $wpdb->insert( $table_data, $data );
                        }
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'success_type_inserted', esc_html( __( 'Values inserted.', 'assets' ) ) );
                        }
                    }
                } elseif ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( $validated_fields[ 'code' ], __( $validated_fields[ 'message' ], 'assets' ) );
                }

                if ( ! is_admin() ) {
                    $redirect_url = get_home_url();
                    $redirect_url = add_query_arg( 'data-updated', 'true', $redirect_url );
                    wp_redirect( $redirect_url );
                    exit;
                }
            }
        }

        // remove data form
        if ( isset( $_POST[ 'bp_remove_date' ] ) ) {
            global $wpdb;
            $date = $_POST[ 'bp_remove_date' ];

            if ( 'all' === $date ) {
                $wpdb->query( "TRUNCATE TABLE $table_data" );

            } else {
                $query = $wpdb->prepare( "DELETE FROM $table_data WHERE date = '%s'", $date );
                $deleted = $wpdb->query($query);

                if ( $deleted && is_int( $deleted ) && function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'success_date_removed', esc_html( __( 'Date removed.', 'assets' ) ) );
                }
            }
        }
    }
    add_action( 'init', 'yoft_add_data_form' );
