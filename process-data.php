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
        $top_row       = [ 'Asset' ];
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
        
        if ( $show_diff ) {
            $top_row[]  = sprintf( 'Diff in %s', get_option( 'bp_currency' ) );
            $top_row[]  = 'Diff in %';
        }
        
        if ( $show_total ) {
            $top_row[] = '% of total';
        }
        
        $all_rows[] = $top_row;
        
        $total_columns = count( $data );

        foreach( bp_get_asset_types() as $type ) {
            $entry_row = [];

            if ( bp_is_type_hidden( $type->id ) ) {
                continue;
            }
            if ( bp_is_type_closed( $type->id, $data ) ) {
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
                if ( $show_diff && $total_columns == $date_counter ) {
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
        if ( $show_diff ) {
            $diff_percent = ( $total_diff / $start_value ) * 100;
            $total_row[]  = bp_format_value( $total_diff );
            $total_row[]  = bp_format_value( $diff_percent, 'percent' );
        }

        if ( $show_total ) {
            $total_row[] = '100 %';
        }
        $all_rows[]  = $total_row;

        return $all_rows;
    }
    
    
    /**
     * Prepare data for charts
     *
     * combochart: https://developers.google.com/chart/interactive/docs/gallery/combochart
     *
     * @param $data
     * @param $asset_types
     * @param $graph_type
     * @param $graph_groups
     *
     * @return array|false
     */
    function bp_process_data_for_chart( $data, string|array $asset_types, array $asset_groups, $graph_type = false ) {
        if ( ! is_array( $data ) || ! isset( $asset_types ) ) {
            return false;
        }
        
        $all_rows[] = bp_get_chart_toprow( $data, $asset_types, $asset_groups, $graph_type );
        
        if ( 'line' === $graph_type ) {
            // @TODO: check if range spans NYE
            $a_lot = 10 < count( $data ) ? false : true;
            foreach( $data as $date => $date_entries ) {
                $entry_row   = [];
                $date        = $a_lot ? bp_format_value( $date, 'date' ) : gmdate( 'd-m', strtotime( $date ) );
                $entry_row[] = $date;
                
                if ( ! empty( $asset_types ) ) {
                    if ( 'all' == $asset_types ) {
                        $day_value = 0;
                        foreach( $date_entries as $asset_row ) {
                            $type_id = (int) $asset_row->type;
                            if ( bp_is_type_hidden( $type_id ) ) {
                                continue;
                            }
                            
                            if ( bp_is_type_closed( $type_id, $data ) ) {
                                continue;
                            }
                            $day_value = $day_value + $asset_row->value;
                        }
                        $entry_row[] = $day_value;
                        
                    } else {
                        foreach( $asset_types as $asset_type ) {
                            if ( bp_is_type_hidden( (int) $asset_type ) ) {
                                continue;
                            }
    
                            if ( bp_is_type_closed( (int) $asset_type, $data ) ) {
                                continue;
                            }
        
                            $types_colummn = array_column( $date_entries, 'type' );
                            $key           = array_search( (int) $asset_type, $types_colummn );
        
                            if ( is_int( $key ) ) {
                                $entry_row[] = (float) $date_entries[$key]->value;
                            } else {
                                $entry_row[] = (float) '0';
                            }
                        }
                    }
                } elseif ( ! empty( $asset_groups ) ) {
                    if ( 1 == count( $asset_groups ) ) {
                        $day_value = 0;
                        foreach( $date_entries as $asset_row ) {
                            $day_value = $day_value + $asset_row->value;
                        }
                        $entry_row[] = (float) $day_value;
                    } elseif ( 1 < count( $asset_groups ) ) {
                        $day_values = [];
                        foreach( $asset_groups as $asset_group_id ) {
                            if ( ! in_array( (int) $asset_group_id, $day_values ) ) {
                                $day_values[$asset_group_id] = 0;
                            }
                            foreach( $date_entries as $asset_row ) {
                                if ( $asset_group_id == $asset_row->asset_group ) {
                                    $day_values[$asset_group_id] = $day_values[$asset_group_id] + $asset_row->value;
                                }
                            }
                        }
                        $entry_row = array_merge( $entry_row, $day_values );
                    }
                }
                $all_rows[] = $entry_row;
            }
            
        } elseif ( 'total_type' === $graph_type ) {
            foreach( $data as $asset_row ) {
                if ( bp_is_type_hidden( (int) $asset_row->type ) ) {
                    continue;
                }
                
                if ( bp_is_type_closed( (int) $asset_row->type, $data ) ) {
                    continue;
                }
                
                $entry_row  = [ bp_get_type_by_id( $asset_row->type ), (float) $asset_row->value ];
                $all_rows[] = $entry_row;
            }

        } elseif ( 'total_group' === $graph_type ) {
            $groups = [];
            foreach( $data as $asset_row ) {
                if ( bp_is_type_hidden( (int) $asset_row->type ) ) {
                    continue;
                }
                
                $group_id = bp_get_group_by_type_id( $asset_row->type, 'id' );
                $group_name = bp_get_group_by_id( $group_id, 'name' );
                if ( ! array_key_exists( $group_name, $groups ) ) {
                    $groups[ $group_name ] = [];
                }
                $groups[ $group_name ][] = $asset_row->value;
            }
            
            $total = 0;
            foreach( $groups as $group_name => $group_values ) {
                $total_in_group = 0;
                
                foreach( $group_values as $value ) {
                    $total          = $total + $value;
                    $total_in_group = $total_in_group + $value;
                }
                $entry_row   = [ $group_name, (float) $total_in_group ];
                $all_rows[]  = $entry_row;
            }
            
        } elseif ( str_starts_with( $graph_type, 'total_' ) ) {
            if ( 'all' == $asset_types ) {
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
