<?php
    function bp_format_value( $value, $type = 'price' ) {
        if ( $type ) {
            switch ( $type ) {
                case 'date':
                    $value = gmdate( get_option( 'bp_date_format' ), strtotime( $value ) );
                    break;
                case 'percent':
                    $value = sprintf( '%s %%', number_format( $value, 2, ',', '.' ) );
                    break;
                case 'price':
                    $value = sprintf( '%s %s', get_option( 'bp_currency' ), number_format( $value, 2, ',', '.' ) );
                    break;
            }
        }
        
        return $value;
    }

    
    function b3_validate_graph_fields( $post_data = [] ) {
        if ( empty( $post_data ) ) {
            return false;
        }
        
        if ( isset( $post_data[ 'show_graph' ] ) ) {
            if ( ! isset( $post_data[ 'asset_type' ] ) && ! isset( $post_data[ 'asset_group' ] )  ) {
                if ( strpos( $post_data[ 'graph_type' ], 'total_' ) === false ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_no_type', esc_html( __( 'You did not select an asset type or group.', 'b3-assets-tracker' ) ) );
                        return;
                    }
                }
            }
            
            if ( isset( $post_data[ 'asset_type' ] ) ) {
                if ( in_array( 'all', $post_data[ 'asset_type' ] ) ) {
                    if ( 1 < count( $post_data[ 'asset_type' ] ) ) {
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'error_only_all', esc_html( __( 'If you select "All", you can\'t select any other types.', 'b3-assets-tracker' ) ) );
                            return;
                        }
                    }
                }
                if ( isset( $post_data[ 'asset_group' ] )  ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_type_group', esc_html( __( 'You need to select an an asset type OR group, not both.', 'b3-assets-tracker' ) ) );
                        return;
                    }
                }
                
            }
            
            if ( 'line' === $post_data[ 'graph_type' ] ) {
                if ( empty( $post_data[ 'stats_from' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_no_start_date', esc_html( __( 'You didn\'t select a start date.', 'b3-assets-tracker' ) ) );
                        
                        return false;
                    }
                }
            } elseif ( 'total_type' === $post_data[ 'graph_type' ] ) {
                if ( ! empty( $post_data[ 'stats_from' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'warning_no_start_date_needed', esc_html( __( 'You don\'t need a start date for a total. The until date is used for that.', 'b3-assets-tracker' ) ) );
                    }
                }
            }
        } else {
            // @TODO: non-graph validation
        }
        
        return true;
    }
