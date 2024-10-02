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

        if ( ! isset( $post_data[ 'asset_type' ] ) ) {
            if ( 'total' !== $post_data[ 'graph_type' ] ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_no_type', esc_html( __( 'You did not select a type.', 'assets' ) ) );
                    return;
                }
            }
        }
        
        if ( isset( $post_data[ 'show_graph' ] ) ) {
            // if ( 'all' !== $post_data[ 'asset_type' ] && 'total' === $post_data[ 'graph_type' ] ) {
            //     if ( function_exists( 'bp_errors' ) ) {
            //         bp_errors()->add( 'warning_not_possible', esc_html( __( 'Pie charts are not for individual assets (yet), so we selected "all".', 'assets' ) ) );
            //     }
            // }
            
            if ( 'line' === $post_data[ 'graph_type' ] ) {
                if ( empty( $post_data[ 'stats_from' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_no_start_date', esc_html( __( 'You didn\'t select a start date.', 'assets' ) ) );
                        
                        return false;
                    }
                }
            } elseif ( 'total' === $post_data[ 'graph_type' ] ) {
                if ( ! empty( $post_data[ 'stats_from' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'warning_no_start_date_needed', esc_html( __( 'You don\'t need a start date for a total. The until date is used for that.', 'assets' ) ) );
                    }
                }
            }
        }
        
        return true;
    }
