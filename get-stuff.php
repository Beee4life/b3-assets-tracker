<?php
    include 'get-results-range.php';

    function bp_get_data( $date = '', $order = 'reverse', $limit = 8 ) {
        global $wpdb;
        $grouped_data = [];
        $table        = $wpdb->prefix . 'asset_data';
        $results      = [];

        if ( $date ) {
            if ( 7 < strlen( $date ) ) {
                // single date
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date = %s", $table, $date ) );
            } else {
                // specific month
                $year       = substr( $date, 0, 4 );
                $month      = substr( $date, 4, 2 );
                $year_month = sprintf( '%%%s-%s%%', $year, $month );
                $results    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date LIKE %s", $table, $year_month ) );
            }
        } else {
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i ORDER BY date DESC", $table ) );

        }

        if ( $date && ! isset( $month ) ) {
            return $results;
        }

        if ( ! empty( $results ) ) {
            foreach( $results as $row ) {
                if ( ! array_key_exists( $row->date, $grouped_data ) ) {
                    $grouped_data[ $row->date ] = [];
                }
                $grouped_data[ $row->date ][] = $row;
            }
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


    function bp_get_asset_groups( $return = 'all' ) {
        global $wpdb;
        $table   = $wpdb->prefix . 'asset_groups';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i ORDER BY name", $table ) );

        if ( 'all' === $return ) {
            return $results;
        } elseif ( 'id_name' === $return ) {
            foreach( $results as $row ) {
                $types[$row->id] = $row->name;
            }

            if ( isset( $types ) ) {
                return $types;
            }
        }

        return [];
    }


    function bp_get_asset_types( $return = 'all' ) {
        global $wpdb;
        $table   = $wpdb->prefix . 'asset_types';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i ORDER BY ordering", $table ) );

        if ( 'all' === $return ) {
            return $results;
        } elseif ( 'id_name' === $return ) {
            foreach( $results as $row ) {
                $types[$row->id] = $row->name;
            }

            if ( isset( $types ) ) {
                return $types;
            }
        }

        return [];
    }


    function bp_get_graph_types() {
        $types = [
            'bar'         => esc_attr__( 'Bar chart', 'b3-assets-tracker' ),
            'line'        => esc_attr__( 'Line chart', 'b3-assets-tracker' ),
            'pie'         => esc_attr__( 'Pie chart', 'b3-assets-tracker' ),
            'total_type'  => esc_attr__( 'Total per type', 'b3-assets-tracker' ),
            'total_group' => esc_attr__( 'Total per group', 'b3-assets-tracker' ),
        ];

        return $types;
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
        $table  = $wpdb->prefix . 'asset_groups';
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $table, $group_id ) );

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


    function bp_get_group_by_type_id( $type_id, $return = 'group_id' ) {
        global $wpdb;
        $table  = $wpdb->prefix . 'asset_types';
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $table, $type_id ) );

        if ( 'group_id' === $return && isset( $result[ 0 ]->asset_group ) ) {
            return $result[ 0 ]->asset_group;
        }

        return false;
    }


    function bp_get_type_by_id( $type_id, $return = 'name' ) {
        global $wpdb;
        $table  = $wpdb->prefix . 'asset_types';
        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $table, $type_id ) );

        if ( 'name' === $return ) {
            if ( isset( $result[ 0 ]->name ) ) {
                return $result[ 0 ]->name;
            }
        } elseif ( 'all' === $return ) {
            return $result;
        } elseif ( 'added' === $return ) {
            if ( isset( $result[ 0 ]->added ) ) {
                return $result[ 0 ]->added;
            }
        } elseif ( 'closed' === $return ) {
            if ( isset( $result[ 0 ]->closed ) ) {
                return $result[ 0 ]->closed;
            }
        }

        return false;
    }


    function bp_calculate_diff( $date_from, $date_until, $type ) {
        if ( $date_from && $date_until && $type ) {
            global $wpdb;
            $start_value = bp_get_value_on_date( $date_from, $type );
            $end_value   = bp_get_value_on_date( $date_until, $type );
            $diff        = $end_value - $start_value;

            return $diff;
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


    function bp_get_value_on_date( $data, $type = false ) {
        if ( ! $data ) {
            return '0.00';
        }

        $total = 0;
        if ( is_string( $data ) ) {
            // date only
            global $wpdb;
            $table = $wpdb->prefix . 'asset_data';
            $date  = gmdate( 'Y-m-d', strtotime( $data ) );

            if ( $type ) {
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT value FROM %i WHERE date = %s AND type = %d", $table, $date, $type ) );

                if ( isset( $row->value ) && ! empty( $row->value ) ) {
                    $value = $row->value;
                } else {
                    $value = 0;
                }

                return $value;

            } else {
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date = %s", $table, $date ) );
            }

        } elseif ( is_array( $data ) ) {
            $results = $data;
        }

        if ( isset( $results ) ) {
            foreach( $results as $entry ) {
                if ( isset( $entry->value ) && ! empty( $entry->value ) ) {
                    // @TODO: check if is added/closed
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
        $results = $wpdb->get_col( $wpdb->prepare( "SELECT date FROM %i ORDER BY date", $table ) );
        $uniques = array_unique( $results );

        return $uniques;
    }


    /**
     * Get top row for charts
     *
     * @param array $data
     * @param string|array $asset_type
     * @param array $asset_groups
     * @param string|bool $graph_type
     *
     * @return array|false
     */
    function bp_get_chart_toprow( $data, $asset_types = [], $asset_groups = [], $graph_type = false ) {
        $top_row = false;

        if ( 'bar' === $graph_type ) {
            if ( ! empty( $asset_types ) ) {
                $top_row = [ 'Asset type', 'Value' ];
            } elseif ( ! empty( $asset_groups ) ) {
                $top_row = [ 'Asset group', 'Value' ];
            }

        } elseif ( 'line' === $graph_type ) {
            if ( 'all' == $asset_types || 'all' == $asset_groups ) {
                $top_row = [ 'Date', 'Value' ];

            } elseif ( ! empty( $asset_types ) ) {
                $top_row = [ 'Week' ];
                foreach( $asset_types as $type ) {
                    if ( ! bp_is_type_added( $type, $data ) ) {
                        continue;
                    }
                    if ( bp_is_type_closed( $type, $data ) ) {
                        continue;
                    }
                    if ( bp_is_type_hidden( $type ) ) {
                        continue;
                    }
                    $top_row[] = bp_get_type_by_id( $type );
                }

            } elseif ( ! empty( $asset_groups ) ) {
                $top_row = [ 'Week' ];
                foreach( $asset_groups as $group_id ) {
                    $top_row[] = bp_get_group_by_id( $group_id );
                }

            } else {
                $top_row = [ 'Week', 'Euro' ];
            }

        } elseif ( 'pie' == $graph_type ) {
            if ( ! empty( $asset_types ) ) {
                $top_row = [ 'Asset type', 'Value' ];
            } elseif ( ! empty( $asset_groups ) ) {
                $top_row = [ 'Asset group', 'Value' ];
            }

        } elseif ( in_array( $graph_type, [ 'total_group', 'total_type' ] ) ) {
            $top_row = [ 'Asset', 'Value' ];

        } else {
            // non defined graphs
            $top_row = [ 'Week' ];

            if ( isset( $asset_type ) ) {
                if ( is_string( $asset_type ) ) {

                } elseif( is_array( $asset_type ) ) {
                    foreach( $asset_type as $asset ) {
                        $type      = bp_get_type_by_id( $asset );
                        $top_row[] = $type;
                    }
                }

            } else {
                foreach( bp_get_asset_types() as $type ) {
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


    function bp_get_chart_element() {
        return '<div id="chart_div"></div>';
    }


    function bp_get_asset_icon( $type ) {
        if ( $type && bp_use_group_icons() ) {
            $type_group = bp_get_group_by_type_id( $type );

            if ( $type_group ) {
                $group_name = bp_get_group_by_id( $type_group, 'name' );
                switch ( $group_name ) {
                    case 'Bullion':
                        $fa_code = 'fad fa-coins';
                        break;
                    case 'Cash':
                        $fa_code = bp_get_currency_icon();
                        break;
                    case 'Collectibles':
                        $fa_code = 'fad fa-album-collection';
                        break;
                    case 'Crypto':
                        $fa_code = 'fab fa-bitcoin';
                        break;
                    case 'Securities':
                        $fa_code = 'fad fa-lock';
                        break;
                    default:
                        $fa_code = '';
                };

                if ( ! empty( $group_name ) && ! empty( $fa_code ) ) {
                    return sprintf( '<span class="icon-holder"><i class="%s" title="%s"></i></span>', $fa_code, $group_name );
                }
            }
        }

        return '';
    }


    function bp_get_currency_icon() {
        $currency = get_option( 'bp_currency' );
        switch( $currency ) {
            case '$':
                $icon = 'fas fa-dollar-sign';
                break;
            case '£':
                $icon = 'fas fa-pound-sign';
                break;
            case '¥':
                $icon = 'fas fa-yen-sign';
                break;
            default:
                $icon = 'fas fa-euro-sign';
        }
        return $icon;
    }


    function bp_get_graph_title( $args = [] ) {
        if ( empty( $args ) ) {
            return '';
        }

        if ( ! empty( $args[ 'title' ] ) ) {
            return $args[ 'title' ];
        }

        $title = esc_html__( 'Total', 'b3-assets-tracker' );

        if ( ! empty( $args[ 'type' ] ) ) {
            switch( $args[ 'type' ] ) {
                case 'bar':
                    $title = esc_html__( 'Assets per type', 'b3-assets-tracker' );
                    break;
                case 'line':
                    if ( 'all' == $args[ 'asset_type' ] ) {
                        $title = esc_html__( 'Total assets value', 'b3-assets-tracker' );
                    } else {
                        $title = esc_html__( 'Week diff', 'b3-assets-tracker' );
                    }
                    break;
                case 'total_type':
                    $title = esc_html__( 'Assets per type', 'b3-assets-tracker' );
                    break;
                case 'total_group':
                    $title = esc_html__( 'Assets per group', 'b3-assets-tracker' );
                    break;
                default:
                    $title = esc_html__( 'Total', 'b3-assets-tracker' );
            }
        }

        return $title;
    }
