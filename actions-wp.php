<?php
    include 'render-chart.php';
    
    function bp_add_short_code() {
        include 'ShortCodes.php';
        $shortcodes = new AssetsShortCodes();
        $shortcodes->register_all();
    }
    add_action( 'init', 'bp_add_short_code' );
    
    
    function bp_add_assets_toolbar( $wp_admin_bar ) {
        if ( current_user_can( 'manage_options' ) ) {
            $args        = [
                'id'    => 'bp-assets',
                'title' => get_option( 'bp_currency' ),
                'href'  => admin_url( 'admin.php?page=bp-assets-dashboard' ),
                'meta'  => [ 'class' => '' ],
            ];
            $wp_admin_bar->add_node( $args );
        }
    }
    add_action( 'admin_bar_menu', 'bp_add_assets_toolbar', 1999 );
