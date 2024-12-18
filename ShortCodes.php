<?php
    /**
     * Assets ShortCodes
     */

    class AssetsShortCodes {
        function __construct() {
        }

        function register_all() {
            add_shortcode( 'results',   [ $this, 'shortcode_results' ] );
            add_shortcode( 'graph',     [ $this, 'shortcode_graph' ] );
        }


        /**
         * Shortcode to output results on front-end
         *
         * @param $attr
         * @param $content
         *
         * @return string|void
         */
        function shortcode_results( $attr, $content = null ) {
            $attributes = shortcode_atts( [
                'from'   => '',
                'till'   => '',
                'until'  => '',
                'footer' => 'false',
            ], $attr );

            if ( empty( $attributes[ 'from' ] ) || empty( $attributes[ 'until' ] ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    if ( empty( ! $attributes[ 'till' ] ) ) {
                        return sprintf( '<p>[%s]</p>', esc_html__( 'Shortcode is using old till value', 'b3-assets-tracker' ) ) ;
                    } else {
                        return sprintf( '<p>[%s]</p>', esc_html__( 'Shortcode is missing 1 or more attributes', 'b3-assets-tracker' ) ) ;
                    }
                } else {
                    return '';
                }
            }

            $date_from    = gmdate( 'Y-m-d', strtotime( $attributes[ 'from' ] ) );
            $date_until   = gmdate( 'Y-m-d', strtotime( $attributes[ 'until' ] ) );
            $dates        = [ $date_from, $date_until ];
            $grouped_data = bp_get_results_range( $dates, 'all' );
            $show_diff    = 1 < count( $grouped_data ) ? true : false;

            if ( ! empty( count( $grouped_data ) ) ) {
                $show_total   = true;
                $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
                $types        = bp_get_asset_types();

                if ( ! is_admin() && is_array( $grouped_data ) ) {
                    ob_start();
                    $amount_columns   = $grouped_data[ 0 ];
                    $scroll_class     = 15 < $amount_columns ? ' tablescroll' : '';
                    $shortcode_notice = sprintf( '<div class="shortcode-notice tablescroll">%s</div>', esc_html__( 'Table scrolls horizontally.' ) );

                    if ( ! is_admin() && 6 < count( $amount_columns ) || is_admin() && 15 < count( $amount_columns ) ) {
                        printf( '<div class="shortcode-notice tablescroll">%s</div>', esc_html__( 'Table scrolls horizontally.' ) );
                    }

                    echo '<div id="data-output"><div id="data">';
                    include 'admin/includes/data-output.php';
                    echo '</div></div>';

                    if ( 'true' == $attributes[ 'footer' ] ) {
                        $page_id = get_page_by_path( 'assets-tracker' );
                        if ( $page_id ) {
                            $message = sprintf( esc_html__( 'This data comes from a WordPress %s I created to track my assets and easily share it within the site.', 'b3-assets-tracker' ), sprintf( '<a href="%s">%s</a>', '/assets-tracker/', 'plugin' ) );
                        } else {
                            $post    = get_post( get_the_ID() );
                            $post_ts = gmdate( 'U', strtotime( $post->post_date ) );
                            if ( 1726701314 <= (int) $post_ts ) {
                                $message = 'Deze data komt uit een WordPress plugin die ik zelf geschreven heb om mijn assets te kunnen tracken en sharen.';
                            } else {
                                $message = esc_html__( 'This data comes from a WordPress plugin I created to track my assets and easily share it within the site.', 'b3-assets-tracker' );
                            }
                        }
                        echo sprintf( '<div class="shortcode-footer">%s</div>', esc_html( $message ) );
                    }
                    $result = ob_get_clean();

                    return $result;
                }
            } else {
                if ( current_user_can( apply_filters( 'b3_assets_role','manage_options' ) ) ) {
                    return sprintf( '<p>[%s]</p>', esc_html__( 'Not enough data for results.', 'b3-assets-tracker' ) );
                }
            }
        }


        /**
         * Shortcode to output results on front-end
         *
         * @param $attr
         * @param $content
         *
         * @return string|void
         */
        function shortcode_graph( $attr, $content = null ) {
            if ( ! is_admin() ) {
                $shortcode_attributes = shortcode_atts( [
                    'from'        => '',
                    'until'       => '',
                    'dates'       => '',
                    'asset_type'  => 'all',
                    'asset_group' => '',
                    'title'       => '',
                    'type'        => 'line',
                    'legend'      => 'right',
                    'footer'      => 'false',
                ], $attr );

                $validated_shortcode_field = b3_validate_shortcode_fields( $shortcode_attributes );
                if ( ! $validated_shortcode_field ) {
                    if ( current_user_can( 'manage_options' ) ) {
                        if ( ! empty( $shortcode_attributes[ 'till' ] ) ) {
                            return sprintf( '<p>[%s]</p>', esc_html__( 'Not enough data for results.', 'b3-assets-tracker' ) );
                        } else {
                            return sprintf( '<p>[%s]</p>', esc_html__( 'Shortcode is missing 1 or more attributes', 'b3-assets-tracker' ) );
                        }
                    } else {
                        return '';
                    }
                }

                $graph_type   = $shortcode_attributes[ 'type' ];
                $asset_groups = [];
                $asset_types  = 'all' != $shortcode_attributes[ 'asset_type' ] ? explode( ',', $shortcode_attributes[ 'asset_type' ] ) : $shortcode_attributes[ 'asset_type' ];
                $grouped_data = [];
                $show_all     = 'all' == $asset_types ? true : false;

                if ( 'line' === $graph_type ) {
                    if ( ! empty( $shortcode_attributes[ 'dates' ] ) ) {
                        $dates = explode( ',', $shortcode_attributes[ 'dates' ] );
                        foreach( $dates as $date ) {
                            $date = gmdate( 'Y-m-d', strtotime( $date ) );
                            if ( ! array_key_exists( $date, $grouped_data ) ) {
                                $grouped_data[ $date ] = [];
                            }

                            $date_rows = bp_get_data( $date );
                            if ( ! empty( $date_rows ) ) {
                                $grouped_data[ $date ] = $date_rows;
                            }
                        }

                    } elseif ( ! empty( $shortcode_attributes[ 'from' ] ) && ! empty( $shortcode_attributes[ 'until' ] ) ) {
                        $date_from    = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'from' ] ) );
                        $date_until   = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'until' ] ) );
                        $dates        = [ $date_from, $date_until ];
                        $grouped_data = bp_get_results_range( $dates, $asset_types, [], $show_all );
                    }

                } elseif ( in_array( $graph_type, [ 'total_type', 'total_group' ] ) ) {
                    $date_until   = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'until' ] ) );
                    $dates        = [ $date_until ];
                    $grouped_data = bp_get_results_range( $dates, 'all', [] );
                }

                $graph_title = bp_get_graph_title( $shortcode_attributes );

                if ( 1 < count( $grouped_data ) ) {
                    $processed_data = bp_process_data_for_chart( $grouped_data, $asset_types, $asset_groups, $graph_type );
                    $margin_top     = apply_filters( 'b3_chart_top_margin', 'auto' );

                    $chart_args = [
                        'asset_group'  => $asset_groups,
                        'asset_type'   => $asset_types,
                        'currency'     => get_option( 'bp_currency' ),
                        'graph_title'  => $graph_title,
                        'graph_type'   => $graph_type,
                        'h_axis_title' => esc_html__( 'Date', 'b3-assets-tracker' ),
                        'v_axis_title' => esc_html__( 'Value', 'b3-assets-tracker' ),
                        'legend'       => $shortcode_attributes[ 'legend' ],
                        'margin_top'   => $margin_top,
                        'margin_left'  => 'auto',
                        'margin_right' => 'auto',
                        'data'         => $processed_data,
                    ];
                    wp_localize_script( 'graphs', 'chart_vars', $chart_args );

                    return bp_get_chart_element();

                } else {
                    if ( current_user_can( 'manage_options' ) ) {
                        return sprintf( '<p>[%s]</p>', esc_html__( 'Not enough data for to display. Check the shortcode in the content.', 'b3-assets-tracker' ) );
                    } else {
                        return sprintf( '<p>%s</p>', esc_html__( 'Something went wrong with the results.', 'b3-assets-tracker' ) );
                    }
                }
            }
        }
    }
