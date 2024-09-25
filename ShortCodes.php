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
         * Shortcode aff link
         *
         * @param $attr
         * @param $content
         *
         * @return string|void
         */
        function shortcode_results( $attr, $content = null ) {
            $attributes = shortcode_atts( [
                'from'    => '',
                'till'    => '',
                'showall' => '',
                'footer'  => 'true',
            ], $attr );

            if ( empty( $attributes[ 'from' ] ) || empty( $attributes[ 'till' ] ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    return '[shortcode misses attributes]';
                } else {
                    return '';
                }
            }
            
            // used by include
            $show_diff    = 'front';
            $date_from    = gmdate( 'Y-m-d', strtotime( $attributes[ 'from' ] ) );
            $date_until   = gmdate( 'Y-m-d', strtotime( $attributes[ 'till' ] ) );
            $show_all     = empty( $attributes[ 'showall' ] ) ? false : true;
            $grouped_data = array_reverse( bp_get_results_range( $date_from, $date_until, false, $show_all ) );
            $show_all     = false;
            $show_total   = true;
            $grouped_data = bp_process_data_for_table( $grouped_data, $show_diff, $show_total );
            $types        = bp_get_types();
            
            if ( ! is_admin() && is_array( $grouped_data ) ) {
                ob_start();
                $amount_columns = $grouped_data[ 0 ];
                $post           = get_post( get_the_ID() );
                $scroll_class   = 6 == count( $amount_columns ) ? 'tablescroll' : '';

                echo sprintf( '<div class="shortcode-notice %s">%s</div>', $scroll_class, 'Table scrolls horizontally.' );

                include 'includes/data-output.php';
                
                if ( 'true' == $attributes[ 'footer' ] ) {
                    $page_id = get_page_by_path( 'assets-tracker' );
                    if ( $page_id ) {
                        $message = sprintf( 'This data comes from a WordPress %s I created to track my assets and easily share it within the site.', sprintf( '<a href="%s">%s</a>', '/assets-tracker/', 'plugin' ) );
                    } else {
                        $post_ts = gmdate( 'U', strtotime( $post->post_date ) );
                        if ( 1726701314 <= (int) $post_ts ) {
                            $message = 'Deze data komt uit een WordPress plugin die ik zelf geschreven heb om mijn assets te kunnen tracken en sharen.';
                        } else {
                            $message = 'This data comes from a WordPress plugin I created to track my assets and easily share it within the site.';
                        }
                    }
                    echo sprintf( '<div class="shortcode-notice">%s</div>', $message );
                }
                $result = ob_get_clean();
                
                return $result;
            }
        }
    }
