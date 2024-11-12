<?php

    function yoft_add_type_form() {
        global $wpdb;
        $table_types = $wpdb->prefix . 'asset_types';

        // add types form
        if ( isset( $_POST[ 'add_type_nonce' ] ) ) {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'add_type_nonce' ] ) ), 'add-type-nonce' ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_nonce_no_match', esc_html( __( 'Something went wrong. Please try again.', 'b3-assets-tracker' ) ) );
                }

            } else {
                if ( isset( $_POST[ 'bp_type' ] ) ) {
                    if ( empty( $_POST[ 'bp_type' ] ) ) {
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'error_no_type', esc_html( __( 'No type selected.', 'b3-assets-tracker' ) ) );
                        }
                    } else {
                        if ( isset( $_POST[ 'update_type' ] ) ) {
                            $type_id       = (int) $_POST[ 'update_type' ];
                            $stored_added  = bp_get_type_by_id( $type_id, 'added' );
                            $stored_closed = bp_get_type_by_id( $type_id, 'closed' );
                            $change_added  = ! empty( $_POST[ 'bp_added' ] ) ? wp_unslash( $_POST[ 'bp_added' ] ) : false;
                            $change_closed = ! empty( $_POST[ 'bp_closed' ] ) ? wp_unslash( $_POST[ 'bp_closed' ] ) : false;
                            $add_date      = $stored_added;
                            $close_date    = $stored_closed;

                            if ( $stored_added !== $change_added ) {
                                $add_date = $change_added;
                            }

                            if ( $stored_closed !== $change_closed ) {
                                $close_date = $change_closed;
                            }

                            $data = [
                                'name'        => sanitize_text_field( wp_unslash( $_POST[ 'bp_type' ] ) ),
                                'ordering'    => ! empty( $_POST[ 'bp_order' ] ) ? (int) $_POST[ 'bp_order' ] : 1,
                                'asset_group' => ! empty( $_POST[ 'bp_asset_group' ] ) ? (int) $_POST[ 'bp_asset_group' ] : false,
                                'hide'        => ! empty( $_POST[ 'bp_hide' ] ) ? (int) $_POST[ 'bp_hide' ] : '',
                                'added'       => $add_date,
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
                                '%s',
                            ];
                            $updated = $wpdb->update( $table_types, $data, $where, $format );
                            if ( $updated && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_updated', esc_html( __( 'Type updated.', 'b3-assets-tracker' ) ) );
                            }

                        } else {
                            // insert
                            $type   = sanitize_text_field( wp_unslash( $_POST[ 'bp_type' ] ) );
                            $closed = isset( $_POST[ 'bp_closed' ] ) ? gmdate( 'Y-m-d', time() ) : '0000-00-00';
                            $group  = isset( $_POST[ 'bp_asset_group' ] ) ? (int) $_POST[ 'bp_asset_group' ] : false;
                            $hide   = isset( $_POST[ 'bp_hide' ] ) ? (int) $_POST[ 'bp_hide' ] : '';
                            $order  = ! empty( $_POST[ 'bp_order' ] ) ? (int) $_POST[ 'bp_order' ] : 1;

                            $data  = [
                                'name' => $type,
                                'added' => gmdate( 'Y-m-d', time() ),
                            ];
                            $format = [
                                '%s',
                                '%s',
                            ];
                            if ( $closed ) {
                                $data[ 'closed' ] = $closed;
                                $format[] = '%s';
                            }
                            if ( $group ) {
                                $data[ 'asset_group' ] = $group;
                                $format[] = '%d';
                            }
                            if ( $order ) {
                                $data[ 'ordering' ] = $order;
                                $format[] = '%d';
                            }
                            if ( $hide ) {
                                $data[ 'hide' ] = $hide;
                                $format[] = '%d';
                            }

                            $return = $wpdb->insert( $table_types, $data, $format );
                            if ( $return && function_exists( 'bp_errors' ) ) {
                                bp_errors()->add( 'success_type_inserted', esc_html( __( 'Type inserted.', 'b3-assets-tracker' ) ) );
                            }
                        }
                    }
                }
            }
        }
    }
    add_action( 'init', 'yoft_add_type_form' );
