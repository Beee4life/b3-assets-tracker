<?php
    function bp_get_data( $date = '', $order = 'reverse', $limit = 8 ) {
        global $wpdb;
        $grouped_data = [];
        $table        = $wpdb->prefix . 'asset_data';
        $query        = "SELECT * FROM $table ORDER BY date DESC";
        
        if ( $date ) {
            if ( 7 < strlen( $date ) ) {
                // single date
                $query = $wpdb->prepare( "SELECT * FROM $table WHERE date = '%s'", $date );
            } else {
                // specific month
                $year       = substr( $date, 0, 4 );
                $month      = substr( $date, 4, 2 );
                $year_month = sprintf( '%s-%s', $year, $month );
                $query      = $wpdb->prepare( "SELECT * FROM $table WHERE date LIKE '%%%s%%'", $year_month );
            }
        }

        $results = $wpdb->get_results( $query );
        if ( $date ) {
            if ( ! isset( $month ) ) {
                return $results;
            }
        }
        
        foreach( $results as $row ) {
            if ( ! array_key_exists( $row->date, $grouped_data ) ) {
                $grouped_data[ $row->date ] = [];
            }
            $grouped_data[ $row->date ][] = $row;
        }
        
        if ( count( $grouped_data ) >= $limit ) {
            $grouped_data = array_slice( $grouped_data, 0, $limit );
        }
        
        if ( $date ) {
            return $grouped_data;
        }
        
        return array_reverse( $grouped_data );
    }
    
    
    function bp_get_types() {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_types';
        $query = "SELECT * FROM $table ORDER BY ordering";
        
        return $wpdb->get_results( $query );
    }

    
    function bp_get_type_by_id( $type ) {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_types';
        $query = "SELECT * FROM $table WHERE id = '$type'";
        $result = $wpdb->get_results( $query );
        
        if ( isset( $result[0]->name ) ) {
            return $result[0]->name;
        }

        return false;
    }

    
    function bp_get_results_range( $from, $until, $asset_type = null, $show_all = false ) {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_data';

        if ( $from && $until ) {
            if ( 'all' === $asset_type ) {
                if ( $show_all ) {
                    $query = $wpdb->prepare( "SELECT * from $table WHERE date BETWEEN '%s' AND '%s' ORDER BY date ASC", $from, $until );
                } else {
                    $query = $wpdb->prepare( "SELECT * from $table WHERE ( date = '%s' OR date = '%s' ) ORDER BY date ASC", $from, $until );
                }
            } else {
                if ( $show_all ) {
                    $query = $wpdb->prepare( "SELECT * from $table WHERE type = '%d' AND date BETWEEN '%s' AND '%s' ORDER BY date ASC", (int) $asset_type, $from, $until );
                } else {
                    $query = $wpdb->prepare( "SELECT * from $table WHERE type = '%d' AND ( date = '%s' OR date = '%s' ) ORDER BY date ASC", (int) $asset_type, $from, $until );
                }
            }
            
            $results = $wpdb->get_results( $query );
            
            $grouped_data = [];
            foreach( $results as $row ) {
                if ( ! array_key_exists( $row->date, $grouped_data ) ) {
                    $grouped_data[ $row->date ] = [];
                }
                $grouped_data[ $row->date ][] = $row;
            }

            if ( ! $show_all ) {
                // extract first and last item
                $first_item = array_slice( $grouped_data, 0, 1 );
                $last_item  = array_slice( $grouped_data, count( $grouped_data ) - 1, 1 );

                if ( 'all' !== $asset_type ) {
                    if ( isset( $first_item[ 0 ]->date ) && isset( $last_item[ 0 ]->date ) ) {
                        $grouped_data = [
                            $first_item[ 0 ]->date => $first_item[ 0 ]->value,
                            $last_item[ 0 ]->date  => $last_item[ 0 ]->value,
                        ];
                    }
                } else {
                    $grouped_data = array_merge( $first_item, $last_item );
                }
            } else {
                error_log('Show all, so probably no edits needed');
            }

            return $grouped_data;
        
        } elseif ( $until ) {
            // get pie chart for totals on this date
            $query        = $wpdb->prepare( "SELECT * from $table WHERE date = '%s'", $until );
            $grouped_data = $wpdb->get_results( $query );

            return $grouped_data;
        }
        
        return [];
    }

    
    function bp_calculate_diff( $date_from, $date_until, $type ) {
        if ( $date_from && $date_until && $type ) {
            global $wpdb;
            $table   = $wpdb->prefix . 'asset_data';
            $query   = $wpdb->prepare( "SELECT value FROM $table WHERE date BETWEEN '%s' AND '%s' AND type = '%d'", $date_from, $date_until, $type );
            $results = $wpdb->get_results( $query );
            
            if ( 1 == count( $results ) ) {
                $start_value = 0;
                $last_item   = end( $results );
                $end_value   = $last_item->value;

            } elseif ( 1 < count( $results ) ) {
                $start_value = $results[ 0 ]->value;
                $last_item   = end( $results );
                $end_value   = $last_item->value;
            }
            if ( isset( $end_value ) && isset( $start_value ) ) {
                $diff = $end_value - $start_value;

                return $diff;
            }
        }
        
        return 0;
    }

    
    function bp_find_id_in_values( $values, $type ) {
        if ( $values && $type ) {
            $columns = array_column( $values, 'type' );
            return array_search( $type, $columns, true );
        }
        
        return false;
    }

    
    function bp_get_value_on_date( $data ) {
        if ( ! $data ) {
            return '0.00';
        }
        
        $total = 0;
        if ( is_string( $data ) ) {
            // date only
            die('TODO: get value on date (string)');
        } elseif ( is_array( $data ) ) {
            foreach( $data as $entry ) {
                if ( isset( $entry->value ) && ! empty( $entry->value ) ) {
                    if ( bp_is_type_hidden( $entry->type ) ) {
                        continue;
                    }
                    $total += $entry->value;
                }
            }
        }
        
        
        return $total;
    }

    
    function bp_get_dates() {
        global $wpdb;
        $table   = $wpdb->prefix . 'asset_data';
        $results = $wpdb->get_col( "SELECT date FROM $table ORDER BY date" );
        $uniques = array_unique( $results );

        return $uniques;
    }
