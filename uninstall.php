<?php
    /**
     * Uninstall functions
     *
     * @since 1.0.0
     */
    
    // If uninstall.php is not called by WordPress, die
    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        die;
    }
    
    delete_option( 'bp_date_format' );
    delete_option( 'bp_currency' );
    
    global $wpdb;

    $tables = [
        $wpdb->prefix . 'asset_data',
        $wpdb->prefix . 'asset_types',
    ];
    
    foreach( $tables as $table ) {
        $wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %i", $table ) );
    }
