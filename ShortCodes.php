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
                'footer' => 'false',
            ], $attr );

            if ( empty( $attributes[ 'from' ] ) || empty( $attributes[ 'till' ] ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    return '[shortcode is missing attributes]';
                } else {
                    return '';
                }
            }

            $show_diff    = true;
            $date_from    = gmdate( 'Y-m-d', strtotime( $attributes[ 'from' ] ) );
            $date_until   = gmdate( 'Y-m-d', strtotime( $attributes[ 'till' ] ) );
            $grouped_data = bp_get_results_range( $date_from, $date_until, 'all', [] );

            if ( 1 < count( $grouped_data ) ) {
                $show_total   = true;
                $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
                $types        = bp_get_asset_types();

                if ( ! is_admin() && is_array( $grouped_data ) ) {
                    ob_start();
                    $amount_columns   = $grouped_data[ 0 ];
                    $scroll_class     = 15 < $amount_columns ? ' tablescroll' : '';
                    $shortcode_notice = sprintf( '<div class="shortcode-notice tablescroll">%s</div>', esc_html( 'Table scrolls horizontally.' ) );

                    if ( ! is_admin() && 6 < count( $amount_columns ) || is_admin() && 15 < count( $amount_columns ) ) {
                        printf( '<div class="shortcode-notice tablescroll">%s</div>', esc_html( 'Table scrolls horizontally.' ) );
                    }

                    echo '<div id="data-output"><div id="data">';
                    include 'admin/includes/data-output.php';
                    echo '</div></div>';

                    if ( 'true' == $attributes[ 'footer' ] ) {
                        $page_id = get_page_by_path( 'assets-tracker' );
                        if ( $page_id ) {
                            $message = sprintf( 'This data comes from a WordPress %s I created to track my assets and easily share it within the site.', sprintf( '<a href="%s">%s</a>', '/assets-tracker/', 'plugin' ) );
                        } else {
                            $post    = get_post( get_the_ID() );
                            $post_ts = gmdate( 'U', strtotime( $post->post_date ) );
                            if ( 1726701314 <= (int) $post_ts ) {
                                $message = 'Deze data komt uit een WordPress plugin die ik zelf geschreven heb om mijn assets te kunnen tracken en sharen.';
                            } else {
                                $message = 'This data comes from a WordPress plugin I created to track my assets and easily share it within the site.';
                            }
                        }
                        echo sprintf( '<div class="shortcode-footer">%s</div>', esc_html( $message ) );
                    }
                    $result = ob_get_clean();

                    return $result;
                }
            } else {
                if ( current_user_can( 'manage_options' ) ) {
                    return '<p>Er is te weinig data om weer te geven. Check de shortcode in de content.</p>';
                } else {
                    return '<p>Er is iets verkeerd gegaan met de resultaten.</p>';
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
                    'till'        => '',
                    'until'       => '',
                    'dates'       => '',
                    'asset_type'  => 'all',
                    'asset_group' => '',
                    'type'        => 'line',
                    'footer'      => 'false',
                ], $attr );

                $validated_shortcode_field = b3_validate_shortcode_fields( $shortcode_attributes );
                if ( ! $validated_shortcode_field ) {
                    if ( current_user_can( 'manage_options' ) ) {
                        return '[shortcode is missing attributes]';
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

                    } elseif ( ! empty( $shortcode_attributes[ 'from' ] ) || ! empty( $shortcode_attributes[ 'till' ] ) ) {
                        $date_from    = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'from' ] ) );
                        $date_until   = ! empty( $shortcode_attributes[ 'till' ] ) ? $shortcode_attributes[ 'till' ] : '';
                        $date_until   = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'till' ] ) );
                        $grouped_data = bp_get_results_range( $date_from, $date_until, $asset_types, [], $show_all );
                    }

                } elseif ( in_array( $graph_type, [ 'total_type', 'total_group' ] ) ) {
                    $date_from    = '';
                    $date_till    = gmdate( 'Y-m-d', strtotime( $shortcode_attributes[ 'till' ] ) );
                    $grouped_data = bp_get_results_range( $date_from, $date_till, [], [] );
                }

                if ( 1 < count( $grouped_data ) ) {
                    $processed_data = [];
                    $processed_data = bp_process_data_for_chart( $grouped_data, $asset_types, $asset_groups, $graph_type );

                    $chart_args = [
                        'asset_group' => $asset_groups,
                        'asset_type'  => $asset_types,
                        'graph_type'  => $graph_type,
                        'currency'    => get_option( 'bp_currency' ),
                        'data'        => $processed_data,
                    ];
                    wp_localize_script( 'graphs', 'chart_vars', $chart_args );

                    return bp_get_chart_element();

                } else {
                    if ( current_user_can( 'manage_options' ) ) {
                        return '<p>Er is te weinig data om weer te geven. Check de shortcode in de content.</p>';
                    } else {
                        return '<p>Er is iets verkeerd gegaan met de resultaten.</p>';
                    }
                }
            }
        }
    }
