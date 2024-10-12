<?php
    function process_input_forms() {
        global $wpdb;
        $table_data   = $wpdb->prefix . 'asset_data';
        $table_groups = $wpdb->prefix . 'asset_groups';
        $table_types  = $wpdb->prefix . 'asset_types';

        // add types form
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
                        if ( isset( $_POST[ 'update_type' ] ) ) {
                            $type_id    = $_POST[ 'update_type' ];
                            $close_date = bp_get_type_by_id( $type_id, 'closed' );
                            $closed     = ! empty( $_POST[ 'bp_closed' ] ) ? true : false;

                            if ( $closed && ( ! $close_date || '0000-00-00' == $closed ) ) {
                                $close_date = gmdate( 'Y-m-d', time() );

                            } elseif ( ! $closed && '0000-00-00' !== $close_date ) {
                                $close_date = '';
                            } elseif ( '0000-00-00' !== $close_date ) {
                                error_log('HIT else close date');
                            }
                            
                            $data = [
                                'name'        => sanitize_text_field( $_POST[ 'bp_type' ] ),
                                'ordering'    => ! empty( $_POST[ 'bp_order' ] ) ? (int) $_POST[ 'bp_order' ] : 1,
                                'asset_group' => (int) $_POST[ 'bp_asset_group' ],
                                'hide'        => ! empty( $_POST[ 'bp_hide' ] ) ? $_POST[ 'bp_hide' ] : '',
                                'closed'      => $close_date,
                            ];

                            $where = [
                                'id' => $type_id,
                            ];
                            $format = [
                                '%s',
                                '%d',
                                '%d',
                                '%d',
                                '%s',
                            ];
                            $updated = $wpdb->update( $table_types, $data, $where, $format );
                            if ( $updated && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_updated', esc_html( __( 'Type updated.', 'assets' ) ) );
                            }

                        } else {
                            // insert
                            $type   = sanitize_text_field( $_POST[ 'bp_type' ] );
                            $closed = isset( $_POST[ 'bp_closed' ] ) ? gmdate( 'Y-m-d', time() ) : '0000-00-00';
                            $group  = isset( $_POST[ 'bp_asset_group' ] ) ? (int) $_POST[ 'bp_asset_group' ] : false;
                            $hide   = isset( $_POST[ 'bp_hide' ] ) ? $_POST[ 'bp_hide' ] : '';
                            $order  = isset( $_POST[ 'bp_order' ] ) ? (int) $_POST[ 'bp_order' ] : 1;
                            
                            $data  = [
                                'name' => $type,
                            ];
                            if ( $closed ) {
                                $data[ 'closed' ] = $closed;
                            }
                            if ( $group ) {
                                $data[ 'asset_group' ] = $group;
                            }
                            if ( $order ) {
                                $data[ 'ordering' ] = $order;
                            }
                            if ( $hide ) {
                                $data[ 'hide' ] = $hide;
                            }
                            
                            $return = $wpdb->insert( $table_types, $data );
                            if ( $return && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_inserted', esc_html( __( 'Type inserted.', 'assets' ) ) );
                            }
                        }
                    }
                }
            }
        }
        
        // delete types form
        if ( isset( $_POST[ 'delete_types_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'delete_types_nonce' ], 'delete-types-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'assets' ) ) );
                }
            } else {
                if ( is_array( $_POST[ 'delete_types' ] ) && ! empty( $_POST[ 'delete_types' ] ) ) {
                    foreach( $_POST[ 'delete_types' ] as $type ) {
                        // delete type
                        $wpdb->delete( $table_types, [ 'type' => $type ], [ '%d' ] );
                        // delete entries with type
                        $wpdb->delete( $table_data, [ 'type' => $type ], [ '%d' ] );
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
                                    'value' => ! empty( $value ) ? $value : '0.00',
                                    'date'  => $input[ 'update_data' ],
                                    'type'  => $type,
                                ];
                                $wpdb->insert( $table_data, $data );
                                
                            } else {
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
                                'date'  => $input[ 'bp_date' ],
                                'type'  => $type,
                                'value' => ! empty( $value ) ? $value : '0.00',
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
            }
        }
        
        // settings page
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
    add_action( 'init', 'process_input_forms' );

    
    function bp_validate_form_input( $post_data = [] ) {
        if ( ! isset( $post_data[ 'bp_date' ] ) || empty( $_POST[ 'bp_date' ] ) ) {
            return [
                'code'    => 'error_no_date',
                'message' => esc_html( __( 'No date selected.', 'assets' ) ),
            ];
        }
        
        return true;
    }
