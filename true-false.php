<?php
    /**
     * Check if a type/position is closed
     *
     * @param $type
     * @param $data
     *
     * @return bool
     */
    function bp_is_type_closed( $type, $data ) {
        if ( $type && $data ) {
            global $wpdb;
            $dates = array_keys( $data );
            $table = $wpdb->prefix . 'asset_types';
            $query = $wpdb->prepare( "SELECT closed FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );
            
            if ( ! empty( $results[ 0 ]->closed ) && '0000-00-00' !== $results[ 0 ]->closed && $results[ 0 ]->closed < $dates[ 0 ] ) {
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
            $dates   = array_keys( $data );
            $table   = $wpdb->prefix . 'asset_types';
            $query   = $wpdb->prepare( "SELECT added FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );

            if ( ! empty( $results[ 0 ]->added ) && '0000-00-00' !== $results[ 0 ]->added && $results[ 0 ]->added < $dates[ 0 ] ) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Check if type is hidden
     *
     * @param $type
     *
     * @return bool
     */
    function bp_is_type_hidden( $type ) {
        if ( $type ) {
            global $wpdb;
            $table   = $wpdb->prefix . 'asset_types';
            $query   = $wpdb->prepare( "SELECT hide FROM $table WHERE id = %d", $type );
            $results = $wpdb->get_results( $query );
            
            if ( isset( $results[ 0 ]->hide ) && '1' == $results[ 0 ]->hide ) {
                return true;
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
    function bp_is_visible( $type, $data = [] ) : bool {
        $added  = bp_is_type_added( (int) $type, $data );
        $hidden = bp_is_type_hidden( (int) $type );
        $closed = bp_is_type_closed( (int) $type, $data );

        if ( $hidden || $closed || ! $added ) {
            return false;
        }
        
        return true;
    }
