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
            
            if ( ! empty( $results[ 0 ]->closed ) && '0000-00-00' !== $results[ 0 ]->closed && $results[ 0 ]->closed < $dates[ 0 ] ) {
                return true;
            }
        }
        
        return false;
    }

    function bp_is_type_hidden( $type ) {
        if ( $type ) {
            global $wpdb;
            $table   = $wpdb->prefix . 'asset_types';
            $query   = $wpdb->prepare( "SELECT hide FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );
            
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
    function bp_is_type_added( $type, $data = [] ) {
        if ( $type && $data ) {
            global $wpdb;
            $dates = array_keys( $data );
            
            if ( ! empty( $dates ) ) {
                $table   = $wpdb->prefix . 'asset_types';
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT added FROM $table WHERE id = %d", $type ) );
                $added   = isset( $results[ 0 ]->added ) ? $results[ 0 ]->added : false;
    
                if ( $added && null === $added ) {
                    return true;
                }
                
                if ( $added && 1 < count( $dates ) ) {
                    $start_date = $dates[ 0 ];
                    $end_date   = end( $dates );
                    
                    if ( '0000-00-00' !== $added && $added <= $end_date && $added >= $start_date ) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * Check if asset is hidden, closed or not started yet
     *
     * @param $type
     * @param $data
     *
     * @return bool
     */
    // function bp_is_visible( $type, $data = [] ) : bool {
    //     $added  = bp_is_type_added( (int) $type, $data );
    //     $closed = bp_is_type_closed( (int) $type, $data );
    //     $hidden = bp_is_type_hidden( (int) $type );
    //
    //     if ( ! $added || $closed || $hidden ) {
    //         return false;
    //     }
    //
    //     return true;
    // }
