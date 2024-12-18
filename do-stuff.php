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
            if ( empty( $post_data[ 'bp_dates' ] ) ) {
                if ( function_exists( 'bp_errors' ) ) {
                    bp_errors()->add( 'error_no_dates', esc_html( __( "You did't select any dates. At least one is needed, depending on your selected graph type.", 'b3-assets-tracker' ) ) );
                }

                return false;
            }

            if ( 'bar' === $post_data[ 'graph_type' ] ) {
                if ( 1 < count( $post_data[ 'bp_dates' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_more_dates', esc_html( __( 'You need only 1 date for a bar chart.', 'b3-assets-tracker' ) ) );
                    }

                    return false;
                }

            } elseif ( 'line' === $post_data[ 'graph_type' ] ) {
                if ( 1 == count( $post_data[ 'bp_dates' ] ) ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_more_dates', esc_html( __( 'You need at least 2 dates for a line chart.', 'b3-assets-tracker' ) ) );
                    }

                    return false;
                }
            }

            if ( ! str_starts_with( $post_data[ 'graph_type' ], 'total_' ) ) {
                if ( ! isset( $post_data[ 'asset_type' ] ) && ! isset( $post_data[ 'asset_group' ] )  ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_no_type_group', esc_html( __( 'You did not select an asset type or group.', 'b3-assets-tracker' ) ) );
                    }

                    return false;

                } elseif ( ! empty( $post_data[ 'asset_type' ] ) && ! empty( $post_data[ 'asset_group' ] )  ) {
                    if ( function_exists( 'bp_errors' ) ) {
                        bp_errors()->add( 'error_type_group', esc_html( __( 'You need to select an an asset type OR group, not both.', 'b3-assets-tracker' ) ) );
                    }

                    return false;
                }
            }

            if ( isset( $post_data[ 'asset_type' ] ) ) {
                if ( in_array( 'all', $post_data[ 'asset_type' ] ) ) {
                    if ( 1 < count( $post_data[ 'asset_type' ] ) ) {
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'error_only_all', esc_html( __( "If you select 'All', you can't select any other types. Please select the types again.", 'b3-assets-tracker' ) ) );
                        }

                        return false;
                    }
                }

            } elseif ( isset( $post_data[ 'asset_group' ] ) ) {
                if ( in_array( 'all', $post_data[ 'asset_group' ] ) ) {
                    if ( 1 < count( $post_data[ 'asset_group' ] ) ) {
                        if ( function_exists( 'bp_errors' ) ) {
                            bp_errors()->add( 'error_only_all', esc_html( __( "If you select 'All', you can't select any other groups. Please select the groups again.", 'b3-assets-tracker' ) ) );
                        }

                        return false;
                    }
                }
            }

        } else {
            // @TODO: non-graph validation
        }

        return true;
    }
