<?php
    function bp_process_data_for_table( $data, $show_diff = false, $show_total = false ) {
        if ( ! is_array( $data ) ) {
            return false;
        }

        // get first and last items
        $grouped_data  = $data;
        $sliced_array  = array_slice( $grouped_data, 0, 1 );
        $first_item    = array_shift( $sliced_array );
        $date_from     = $first_item[ 0 ]->date;
        $last_item     = end( $grouped_data );
        $date_until    = $last_item[ 0 ]->date;
        $top_row       = [ 'Week' ];
        $start_value   = bp_get_value_on_date( $first_item );
        $end_value     = bp_get_value_on_date( $last_item );
        $total_counter = 0;
        $total_diff    = 0;
        $totals        = [];
        
        foreach( $data as $date => $entries ) {
            $top_row[] = $date;
            $total_counter++;
            
            if ( ! array_key_exists( $total_counter, $totals ) ) {
                $totals[$total_counter] = 0;
            }
            
            $total_value_on_date      = bp_get_value_on_date( $entries );
            $totals[ $total_counter ] = $totals[ $total_counter ] + $total_value_on_date;
        }
        
        if ( in_array( $show_diff, [ 'dashboard', 'front' ] ) ) {
            $top_row[]  = sprintf( 'Diff in %s', get_option( 'bp_currency' ) );
            $top_row[]  = 'Diff in %';
        }
        
        if ( $show_total ) {
            $top_row[] = '% of total';
        }
        
        $all_rows[] = $top_row;
        
        $total_columns = count( $data );

        foreach( bp_get_types() as $type ) {
            $entry_row = [];

            if ( bp_is_type_hidden( $type->id ) ) {
                continue;
            }
            $entry_row[]  = $type->name;
            $date_counter = 1;

            foreach( $data as $date => $date_entries ) {
                $key = bp_find_id_in_values( $date_entries, $type->id );
                
                if ( false === $key ) {
                    $value       = (float) '0.00';
                    $entry_row[] = sprintf( '%s &mdash;', get_option( 'bp_currency' ) );
                } else {
                    if ( $type->id == $date_entries[ $key ]->type ) {
                        $value       = $date_entries[ $key ]->value;
                        $entry_row[] = bp_format_value( (float) $value );
                    }
                }

                if ( 1 == $date_counter ) {
                    $start_value_row = $value;
                }

                $end_value_row = $value;
                if ( in_array( $show_diff, [ 'dashboard', 'front' ] ) && $total_columns == $date_counter ) {
                    $diff          = bp_calculate_diff( $date_from, $date_until, $type->id );
                    $entry_row[]   = bp_format_value( (float) $diff );
                    $total_diff    = $total_diff + $diff;

                    if ( '0.00' != $start_value_row ) {
                        $diff_percent = ( $diff / $start_value_row ) * 100;
                        $entry_row[]  = bp_format_value( $diff_percent, 'percent' );
                    } else {
                        $entry_row[]  = bp_format_value( 0.00, 'percent' );
                    }
                }
                
                if ( $show_total && $date_counter == $total_counter ) {
                    $percent_total = ( $end_value_row / $end_value ) * 100;
                    $entry_row[]   = bp_format_value( $percent_total, 'percent' );
                }
                $date_counter++;

                if ( count( $top_row ) == count( $entry_row ) ) {
                    break;
                }
            }
            $all_rows[] = $entry_row;
        }
        
        $total_row = [ 'Total' ];

        foreach( $totals as $counter => $total ) {
            $total_row[] = bp_format_value( $total );
        }
        if ( $show_diff || $show_diff && $show_total ) {
            $diff_percent = ( $total_diff / $start_value ) * 100;
            $total_row[]  = bp_format_value( $total_diff );
            $total_row[]  = bp_format_value( $diff_percent, 'percent' );
            $total_row[]  = '100 %';
            $all_rows[]   = $total_row;

        } elseif ( $show_total ) {
            $total_row[] = '100 %';
            $all_rows[]  = $total_row;
        }

        return $all_rows;
    }
    
    
    /**
     * Prepare top row for charts
     *
     * @param array $data
     * @param string|bool $asset_type
     * @param string|bool $graph_type
     * @param string|bool $range
     *
     * @return array|false
     */
    function bp_get_chart_toprow( $data, $asset_type = false, $graph_type = false, $range = false ) {
        $top_row = false;
        
        if ( 'line' === $graph_type ) {
            $top_row = [ 'Week', 'Euro' ];
            
        } elseif ( 'total' === $graph_type ) {
            $top_row = [ 'Asset', '&euro;' ];
            
        } else {
            error_log(sprintf('Catch %s', $graph_type ));
            $top_row = [ 'Week' ];
            
            if ( $asset_type ) {
                $type      = bp_get_type_by_id( $asset_type );
                $top_row[] = $type;
                
            } else {
                foreach( bp_get_types() as $type ) {
                    if ( bp_is_type_hidden( $type->id ) ) {
                        continue;
                    }
                    $name      = bp_get_type_by_id( $type->id );
                    $top_row[] = $name;
                }
            }
        }
        
        return $top_row;
    }
    
    
    /**
     * Prepare data for charts
     *
     * combochart: https://developers.google.com/chart/interactive/docs/gallery/combochart
     *
     * @param $data
     * @param $asset_type
     * @param $graph_type
     * @param $range
     *
     * @return array|false
     */
    function bp_process_data_for_chart( $data, $asset_type = false, $graph_type = false, $range = false ) {
        if ( ! is_array( $data ) ) {
            return false;
        }
        
        $all_rows[] = bp_get_chart_toprow( $data, $asset_type, $graph_type, $range );
        
        if ( 'line' === $graph_type ) {
            foreach( $data as $date_entries ) {
                $date        = bp_format_value( $date_entries[ 0 ]->date, 'date' );
                $total_value = bp_get_value_on_date( $date_entries );
                $all_rows[]  = [ $date, $total_value ];
            }
            
        } elseif ( 'total' === $graph_type ) {
            if ( 'all' == $asset_type ) {
                foreach( $data as $asset_row ) {
                    if ( bp_is_type_hidden( (int) $asset_row->type ) ) {
                        continue;
                    }
                    
                    $entry_row  = [ bp_get_type_by_id( $asset_row->type ), (float) $asset_row->value ];
                    $all_rows[] = $entry_row;
                }
            }
        }
        
        return $all_rows;
    }
