<?php
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
            $dates = array_keys( $data );
            global $wpdb;
            $table = $wpdb->prefix . 'asset_types';
            $query = $wpdb->prepare( "SELECT closed FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );

            if ( ! empty( $results[0]->closed ) && '0000-00-00' !== $results[0]->closed ) {
                if ( $results[0]->closed < $dates[0] ) {
                    return true;
                }
            }
        }
        
        return false;
    }

    function bp_is_type_hidden( $type ) {
        if ( $type ) {
            global $wpdb;
            $table = $wpdb->prefix . 'asset_types';
            $query = $wpdb->prepare( "SELECT hide FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );
            if ( isset( $results[0]->hide ) ) {
                if ( '1' == $results[0]->hide ) {
                    return true;
                }
            }
        }
        
        return false;
    }
