<?php
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
