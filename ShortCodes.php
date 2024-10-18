<?php
    /**
     * Assets ShortCodes
     */

    class AssetsShortCodes {
        function __construct() {
        }

        function register_all() {
            add_shortcode( 'results',       [ $this, 'shortcode_results' ] );
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
            $range        = 'begin_end';
            $grouped_data = bp_get_results_range( $date_from, $date_until, 'all', [] );
            
            if ( 1 < count( $grouped_data ) ) {
                $show_total   = true;
                $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
                $types        = bp_get_asset_types();
                
                if ( ! is_admin() && is_array( $grouped_data ) ) {
                    ob_start();
                    $amount_columns   = $grouped_data[ 0 ];
                    $scroll_class     = 15 < $amount_columns ? ' tablescroll' : '';
                    $shortcode_notice = sprintf( '<div class="shortcode-notice tablescroll">%s</div>', 'Table scrolls horizontally.' );
                    
                    if ( ! is_admin() && 6 < count( $amount_columns ) ) {
                        echo $shortcode_notice;
                    } elseif ( is_admin() && 15 < count( $amount_columns ) ) {
                        echo $shortcode_notice;
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
                        echo sprintf( '<div class="shortcode-footer">%s</div>', $message );
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
    }
