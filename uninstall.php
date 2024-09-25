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
    
    delete_option( 'bp_assets_date_format' );
    delete_option( 'bp_currency' );
    
    global $wpdb;

    $tables = [
        $wpdb->prefix . 'asset_data',
        $wpdb->prefix . 'asset_types',
    ];
    
    foreach( $tables as $table ) {
        $query = "DROP TABLE IF EXISTS $table";
        $wpdb->query( $query );
    }
