<?php
    /**
     * Get results, optional from a specific range
     *
     * @param array $dates
     * @param string|array $asset_type
     * @param array $asset_group
     * @param bool $show_all
     *
     * @return array|object|stdClass[]|null
     */
    function bp_get_results_range( array $dates, string|array $asset_type, string|array $asset_group = [], $show_all = false ) {
        global $wpdb;
        $results      = [];
        $table_assets = $wpdb->prefix . 'asset_data';
        $table_groups = $wpdb->prefix . 'asset_groups';
        $table_types  = $wpdb->prefix . 'asset_types';

        if ( 1 == count( $dates ) ) {
            // pie chart
            // possible bar chart

            if ( ! empty( $asset_type ) ) {
                if ( 'all' == $asset_type ) {
                    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date = %s ORDER BY date, type ASC", $table_assets, $dates[0] ) );
                } else {
                    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE type IN (" . implode( ',', $asset_type ) . ") AND date = %s ORDER BY date, type ASC", $table_assets, $dates[0] ) );
                }

            } elseif ( ! empty( $asset_group ) ) {
                $types = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM %i WHERE asset_group IN (" . implode( ',', $asset_group ) . ")", $table_types ) );
                if ( ! empty( $types ) ) {
                    foreach( $types as $type ) {
                        $asset_types[] = (int) $type->id;
                    }
                }
                if ( ! empty( $asset_types ) ) {
                    if ( 1 == count( $asset_group ) ) {
                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE type IN (" . implode( ',', $asset_types ) . ") AND date = %s ORDER BY type ASC", $table_assets, $dates[0] ) );

                    } elseif ( 1 < count( $asset_group ) ) {
                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i INNER JOIN %i ON %i.type = %i.id WHERE type IN (" . implode( ',', $asset_types ) . ") AND date = %s ORDER BY date, type ASC", $table_assets, $table_types, $table_assets, $table_types, $dates[0] ) );
                    }
                }
                // @TODO: group by asset_group
                // @TODO: format results

            } else {
                // get pie chart for totals on this date
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT * from %i WHERE date = %s", $table_assets, $dates[0] ) );
            }

            return $results;

        } elseif ( 2 == count( $dates ) ) {

            if ( 'all' == $asset_type || 'all' == $asset_group ) {
                // dashboard/shortcode
                if ( $show_all ) {
                    // dashboard
                    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date BETWEEN %s AND %s ORDER BY date ASC", $table_assets, $dates[0], $dates[1] ) );

                } else {
                    foreach( $dates as $date ) {
                        $day_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE date = %s", $table_assets, $date ) );
                        if ( ! empty( $day_results ) ) {
                            $results = array_merge( $results, $day_results );
                        }
                    }
                }

            } elseif ( ! empty( $asset_type ) ) {
                // only for graphs
                foreach( $dates as $date ) {
                    $day_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE type IN (" . implode( ',', $asset_type ) . ") AND date = %s ORDER BY date, type ASC", $table_assets, $date ) );
                    if ( ! empty( $day_results ) ) {
                        $results = array_merge( $results, $day_results );
                    }
                }

                return $results;

            } elseif ( ! empty( $asset_group ) ) {
                // @TODO
            }

            $grouped_data = [];
            if ( ! empty( $results ) ) {
                foreach( $results as $row ) {
                    if ( ! array_key_exists( $row->date, $grouped_data ) ) {
                        $grouped_data[ $row->date ] = [];
                    }
                    $grouped_data[ $row->date ][] = $row;
                }

                return $grouped_data;
            }

        } elseif ( 2 < count( $dates ) ) {
        }

        return [];
    }
