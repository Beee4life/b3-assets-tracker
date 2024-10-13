<?php

    function yoft_add_type_form() {
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
    }
    add_action( 'init', 'yoft_add_type_form' );
