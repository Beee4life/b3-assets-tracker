<?php
    function bp_admin_menu() {
        $admin_url     = admin_url( 'admin.php?page=' );
        $current_class = ' class="current_page"';

        if ( isset( $_SERVER[ 'HTTP_HOST' ] ) && isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
            $url_array     = wp_parse_url( esc_url_raw( sanitize_text_field( wp_unslash( $_SERVER[ 'HTTP_HOST' ] ) ) . sanitize_text_field( wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) ) );
            $subpage       = ( isset( $url_array[ 'query' ] ) ) ? substr( $url_array[ 'query' ], 8 ) : false;
            
            $pages = [
                'assets-dashboard' => esc_html__( 'Dashboard', 'b3-assets-tracker' ),
                'assets-data'      => esc_html__( 'Data', 'b3-assets-tracker' ),
                'assets-add-data'  => esc_html__( 'Add data', 'b3-assets-tracker' ),
                'assets-graphs'    => esc_html__( 'Graphs', 'b3-assets-tracker' ),
                'assets-types'     => esc_html__( 'Types', 'b3-assets-tracker' ),
                'assets-settings'  => esc_html__( 'Settings', 'b3-assets-tracker' ),
            ];
            
            echo '<p class="bp-admin-menu">';
            foreach( $pages as $slug => $label ) {
                $current_page = ( $subpage === $slug ) ? $current_class : false;
                echo ( 'assets-dashboard' !== $slug ) ? ' | ' : false;
                echo sprintf( '<a href="%s"%s>%s</a>', sprintf( '%sbp-%s', esc_url_raw( $admin_url ), esc_attr( $slug ) ), esc_attr( $current_page ), esc_html( $label ) );
            }
            echo '</p>';
        }
    }
    add_action( 'bp_admin_menu', 'bp_admin_menu' );
