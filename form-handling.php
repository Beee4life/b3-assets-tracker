<?php
    function process_input_forms() {
        if ( isset( $_POST[ 'add_type_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'add_type_nonce' ], 'add-type-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }
                
            } else {
                if ( isset( $_POST[ 'bp_type' ] ) ) {
                    if ( empty( $_POST[ 'bp_type' ] ) ) {
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'error_no_type', esc_html( __( 'No type selected.', 'assets' ) ) );
                        }
                    } else {
                        global $wpdb;
                        $table = $wpdb->prefix . 'asset_types';
                        
                        if ( isset( $_POST[ 'update_type' ] ) ) {
                            $data = [
                                'name'        => sanitize_text_field( $_POST[ 'bp_type' ] ),
                                'ordering'    => ! empty( $_POST[ 'bp_order' ] ) ? $_POST[ 'bp_order' ] : 1,
                                'asset_group' => ! empty( $_POST[ 'bp_asset_group' ] ) ? $_POST[ 'bp_asset_group' ] : 0,
                                'hide'        => ! empty( $_POST[ 'bp_hide' ] ) ? $_POST[ 'bp_hide' ] : '',
                            ];
                            $where = [
                                'id' => $_POST[ 'update_type' ],
                            ];
                            $format = [
                                '%s',
                                '%d',
                                '%d',
                                '%d',
                            ];
                            $updated = $wpdb->update( $table, $data, $where, $format );
                            if ( $updated && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_updated', esc_html( __( 'Type updated.', 'assets' ) ) );
                            }

                        } else {
                            // insert
                            $type  = $_POST[ 'bp_type' ];
                            $order = isset( $_POST[ 'bp_order' ] ) ? (int) $_POST[ 'bp_order' ] : false;
                            $group = isset( $_POST[ 'bp_asset_group' ] ) ? (int) $_POST[ 'bp_asset_group' ] : false;
                            
                            $data  = [
                                'name' => $type,
                            ];
                            if ( $group ) {
                                $data[ 'asset_group' ] = $group;
                            }
                            if ( $order ) {
                                $data[ 'ordering' ] = $order;
                            }
                            
                            $return = $wpdb->insert( $table, $data );
                            if ( $return && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_inserted', esc_html( __( 'Type inserted.', 'assets' ) ) );
                            }
                        }
                    }
                }
            }
        }
        
        // add/update data
        if ( isset( $_POST[ 'add_data_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'add_data_nonce' ], 'add-data-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }
                
            } else {
                $validated_fields = bp_validate_form_input( $_POST );
                
                if ( true === $validated_fields ) {
                    global $wpdb;
                    unset( $_POST[ 'add_data_nonce' ] );
                    $input  = $_POST;
                    $table  = $wpdb->prefix . 'asset_data';
                    $values = is_array( $input[ 'bp_value' ] ) ? $input[ 'bp_value' ] : [];
                    
                    if ( isset( $input[ 'update_data' ] ) ) {
                        // update row
                        if ( getenv( 'ASSETS' ) ) {
                            $assets = explode( ',', getenv( 'ASSETS' ) );
                            
                            if ( ! empty( $values[ $assets[ 0 ] ] ) && ! empty( $values[ $assets[ 1 ] ] ) ) {
                                $total_degiro = $values[ $assets[ 0 ] ];
                                $total_etf    = $values[ $assets[ 1 ] ];
                                $total_stocks = $total_degiro - $total_etf;
                                $values[ 4 ]  = (string) $total_stocks;
                            }
                        }

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
                            $query = $wpdb->prepare( "SELECT * FROM $table WHERE type = '%d' and date = '%s'", (int) $type, $input[ 'update_data' ] );
                            $row   = $wpdb->get_row( $query );

                            if ( null == $row ) {
                                $data = [
                                    'value' => ! empty( $value ) ? $value : '0.00',
                                    'date'  => $input[ 'update_data' ],
                                    'type'  => $type,
                                ];
                                $wpdb->insert( $table, $data );
                                
                            } else {
                                $wpdb->update( $table, $data, $where, $format );
                            }

                        }
                        
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'success_type_updated', esc_html( __( 'Values updated.', 'assets' ) ) );
                        }
                        
                    } else {
                        // insert row
                        foreach( $values as $type => $value ) {
                            $data = [
                                'date'  => $input[ 'bp_date' ],
                                'type'  => $type,
                                'value' => ! empty( $value ) ? $value : '0.00',
                            ];
                            $wpdb->insert( $table, $data );
                        }
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'success_type_inserted', esc_html( __( 'Values inserted.', 'assets' ) ) );
                        }
                    }
                } elseif ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( $validated_fields[ 'code' ], __( $validated_fields[ 'message' ], 'assets' ) );
                }
            }
        }
        
        if ( isset( $_POST[ 'assets_settings_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'assets_settings_nonce' ], 'assets-settings-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }
                
            } else {
                if ( ! empty( $_POST[ 'bp_currency' ] ) ) {
                    update_option( 'bp_currency', $_POST[ 'bp_currency' ] );
                } else {
                    update_option( 'bp_currency', '&euro;' );
                }
                if ( ! empty( $_POST[ 'bp_date_format' ] ) ) {
                    update_option( 'bp_date_format', $_POST[ 'bp_date_format' ] );
                } else {
                    update_option( 'bp_date_format', 'd-m-y' );
                }
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'success_settings_saved', esc_html( __( 'Settings saved.', 'assets' ) ) );
                }
            }
        }
        
        if ( isset( $_POST[ 'bp_remove_date' ] ) ) {
            global $wpdb;
            $date  = $_POST[ 'bp_remove_date' ];
            $table = $wpdb->prefix . 'asset_data';
            
            if ( 'all' === $date ) {
                $wpdb->query( "TRUNCATE TABLE $table" );

            } else {
                $query = $wpdb->prepare( "DELETE FROM $table WHERE date = '%s'", $date );
                $deleted = $wpdb->query($query);

                if ( $deleted && is_int( $deleted ) && function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'success_date_removed', esc_html( __( 'Date removed.', 'assets' ) ) );
                }
            }
        }
        
        if ( isset( $_POST[ 'delete_types_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'delete_types_nonce' ], 'delete-types-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }
            } else {
                if ( is_array( $_POST[ 'delete_types' ] ) && ! empty( $_POST[ 'delete_types' ] ) ) {
                    global $wpdb;
                    foreach( $_POST[ 'delete_types' ] as $type ) {
                        // delete type
                        $wpdb->delete( $wpdb->prefix . 'asset_types', [ 'type' => $type ], [ '%d' ] );
                        // delete entries with type
                        $wpdb->delete( $wpdb->prefix . 'asset_data', [ 'type' => $type ], [ '%d' ] );
                    }
                }
            }
        }
    }
    add_action( 'admin_init', 'process_input_forms' );

    
    function bp_validate_form_input( $post_data = [] ) {
        if ( ! isset( $post_data[ 'bp_date' ] ) || empty( $_POST[ 'bp_date' ] ) ) {
            return [
                'code'    => 'error_no_date',
                'message' => esc_html( __( 'No date selected.', 'assets' ) ),
            ];
        }
        
        return true;
    }
