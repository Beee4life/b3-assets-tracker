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
        
        if ( 'reverse' === $order ) {
            return array_reverse( $grouped_data );
        }

        return $grouped_data;
    }
    
    
    function bp_get_asset_groups() {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_groups';
        $query = "SELECT * FROM $table ORDER BY name";
        
        return $wpdb->get_results( $query );
    }
    
    
    function bp_get_asset_types() {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_types';
        $query = "SELECT * FROM $table ORDER BY ordering";
        
        return $wpdb->get_results( $query );
    }
    
    
    function bp_get_preset_types() {
        $preset_types = [
            '21' => 'Coinbase',
            '22' => 'DeGiro',
        ];
        
        return [];
    }

    
    function bp_get_group_by_id( $group_id, $return = 'name' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_groups';
        $query = "SELECT * FROM $table WHERE id = '$group_id'";
        $result = $wpdb->get_results( $query );
        
        if ( 'name' === $return ) {
            if ( isset( $result[ 0 ]->name ) ) {
                return $result[ 0 ]->name;
            }
        } elseif ( 'id' === $return ) {
            if ( isset( $result[ 0 ]->id ) ) {
                return (int) $result[ 0 ]->id;
            }
        }

        return false;
    }

    
    function bp_get_type_by_id( $type_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'asset_types';
        $query = "SELECT * FROM $table WHERE id = '$type_id'";
        $result = $wpdb->get_results( $query );
        
        if ( isset( $result[0]->name ) ) {
            return $result[0]->name;
        }

        return false;
    }
    
    
    /**
     * Get results, optional from a specific range
     *
     * @param string $from
     * @param string $until
     * @param string|array $asset_type
     * @param array $asset_group
     * @param bool $show_all
     *
     * @return array|object|stdClass[]|null
     */
    function bp_get_results_range( string $from, string $until, string|array $asset_type, array $asset_group = [], $show_all = false ) {
        global $wpdb;
        $table_assets = $wpdb->prefix . 'asset_data';
        $table_groups = $wpdb->prefix . 'asset_groups';
        $table_types  = $wpdb->prefix . 'asset_types';

        if ( $from && $until ) {
            if ( 'all' == $asset_type ) {
                // weekly stats/shortcode
                if ( $show_all ) {
                    // dashboard
                    $query = $wpdb->prepare( "SELECT * FROM $table_assets WHERE date BETWEEN '%s' AND '%s' ORDER BY date ASC", $from, $until );
                } else {
                    $query = $wpdb->prepare( "SELECT * FROM $table_assets WHERE ( date = '%s' OR date = '%s' ) ORDER BY date ASC", $from, $until );
                }
            
            } elseif ( is_array( $asset_type ) ) {
                // only for graphs
                $query = $wpdb->prepare( "SELECT * FROM $table_assets WHERE type IN (" . implode( ',' , $asset_type ) . ") AND date BETWEEN '%s' AND '%s' ORDER BY date, type ASC", $from, $until );

            } elseif ( is_array( $asset_group ) ) {
                // only for graphs
                $types = $wpdb->get_results( "SELECT id FROM $table_types WHERE asset_group IN (" . implode( ',' , $asset_group ) . ")" );
                if ( ! empty( $types ) ) {
                    foreach( $types as $type ) {
                        $asset_types[] = (int) $type->id;
                    }
                }
                if ( ! empty( $asset_types ) ) {
                    if ( 1 == count( $asset_group ) ) {
                        $query = $wpdb->prepare( "SELECT * FROM $table_assets WHERE type IN (" . implode( ',' , $asset_types ) . ") AND date BETWEEN '%s' AND '%s' ORDER BY type ASC", $from, $until );
    
                    } elseif ( 1 < count( $asset_group ) ) {
                        $query = $wpdb->prepare( "SELECT * FROM $table_assets INNER JOIN $table_types ON $table_assets.type = $table_types.id WHERE type IN (" . implode( ',' , $asset_types ) . ") AND date BETWEEN '%s' AND '%s' ORDER BY date, type ASC", $from, $until );
                        if ( 'development' === WP_ENV ) {
                            error_log($query);
                        }
                    }
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

            return $grouped_data;
        
        } elseif ( $until ) {
            // get pie chart for totals on this date
            $query        = $wpdb->prepare( "SELECT * from $table_assets WHERE date = '%s'", $until );
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

    
    function bp_find_id_in_values( $values, $type, $column_key = 'type' ) {
        if ( $values && $type ) {
            $columns = array_column( $values, $column_key );
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
