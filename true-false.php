<?php
    use function Env\env;

    /**
     * Check if a type/position is closed before start date
     *
     * @param $type
     * @param $data
     *
     * @return bool
     */
    function bp_is_type_closed( $type, $data ) {
        if ( $type && $data ) {
            if ( is_array( $data ) ) {
                $dates        = array_keys( $data );
                $compare_date = $dates[ 0 ];
            } elseif ( is_string( $data ) ) {
                $compare_date = $data;
            }

            global $wpdb;
            $table   = $wpdb->prefix . 'asset_types';
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT closed FROM %i WHERE id = %d", $table, $type ) );

            if ( ! empty( $results[ 0 ]->closed ) && '0000-00-00' !== $results[ 0 ]->closed && $results[ 0 ]->closed < $compare_date ) {
                return true;
            }
        }

        return false;
    }

    function bp_is_type_hidden( $type ) {
        if ( $type ) {
            global $wpdb;
            $table   = $wpdb->prefix . 'asset_types';
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT hide FROM %i WHERE id = %d", $table, $type ) );

            if ( isset( $results[0]->hide ) && '1' == $results[0]->hide ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Check if a type/position has been added by date
     *
     * @param $type
     * @param $data
     *
     * @return bool
     */
    function bp_is_type_added( $type, $data ) {
        if ( $type && $data ) {
            global $wpdb;

            $added = bp_get_type_by_id( $type, 'added' );

            if ( is_array( $data ) ) {
                $dates      = array_keys( $data );
                $start_date = $dates[ 0 ];

                if ( $added && 1 < count( $dates ) ) {
                    $end_date = end( $dates );
                }
            } elseif ( is_string( $data ) ) {
                $start_date = $data;
                $end_date   = $data;
            }

            if ( ! empty( $added ) && isset( $end_date ) ) {
                if ( $added <= $end_date ) {
                    return true;
                }
            }
        }

        return false;
    }


    function b3_validate_shortcode_fields( $attributes = [] ) {
        if ( ! $attributes ) {
            return false;
        }

        if ( isset( $attributes[ 'type' ] ) ) {
            if ( 'line' === $attributes[ 'type' ] ) {
                if ( empty( $attributes[ 'from' ] ) || empty( $attributes[ 'until' ] ) ) {
                    if ( ! empty( $attributes[ 'from' ] ) ) {
                        return true;
                    }
                }

            } elseif ( in_array( $attributes[ 'type' ], [ 'total_type', 'total_group' ] ) ) {
                if ( empty( $attributes[ 'until' ] ) ) {
                    return false;
                }
            }
        }

        return true;
    }


    function bp_show_admin_links( $type = '' ) {
        if ( is_admin() ) {
            return true;
        } else {
            return apply_filters( 'b3_show_admin_links', false );
        }
    }


    function bp_show_edit_links() {
        if ( current_user_can( 'manage_options' ) || true === apply_filters( 'bp_show_edit_links', false ) ) {
            return true;
        };

        return false;
    }


    function bp_use_group_icons() {
        return ! is_admin() && '1' == env( 'USE_GROUP_ICONS' ) ? true : false;
    }
