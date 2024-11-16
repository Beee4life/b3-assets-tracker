<?php
    /*
        Plugin Name:    B3 : Assets Tracker
        Description:    This plugin gives you the option to track and analyze your (financial) assets.
        Version:        1.14.0
        Author:         Beee
        Author URI:     https://berryplasman.com
        License:        GPL2
        License:        GPL v2 (or later)
        License URI:    https://www.gnu.org/licenses/gpl-2.0.html
        Text Domain:    b3-assets-tracker
        Domain Path:    /languages
    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'B3AssetsTracker' ) ) :

        /**
         * Main class
         */
        class B3AssetsTracker {

            /**
             *  A dummy constructor to ensure plugin is only initialized once
             */
            public function __construct() {}

            public function initialize() {

                register_activation_hook( __FILE__,     [ $this, 'bp_plugin_activation' ] );
                register_deactivation_hook( __FILE__,   [ $this, 'bp_plugin_deactivation' ] );

                add_action( 'admin_init',               [ $this, 'bp_check_table' ] );
                add_action( 'admin_menu',               [ $this, 'bp_admin_pages' ] );
                add_action( 'wp_enqueue_scripts',       [ $this, 'bp_add_css_front' ] );
                add_action( 'admin_enqueue_scripts',    [ $this, 'bp_add_css_admin' ] );
                add_action( 'init',                     [ $this, 'bp_load_textdomain' ] );

                include 'actions.php';
                include 'data.php';
                include 'functions.php';
            }


            /**
             * Function which runs upon plugin activation
             */
            public function bp_plugin_activation() {
                $this->bp_check_table();
                update_option( 'bp_currency', '€' );
                update_option( 'bp_date_format', 'd-m-y' );

                global $wpdb;
                $table   = $wpdb->prefix . 'asset_groups';
                $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i", $table ) );

                if ( empty( $results ) ) {
                    foreach( b3_get_default_groups() as $id => $name ) {
                        $data = [ 'id' => $id, 'name' => $name ];
                        $wpdb->insert( $table, $data, [ '%d', '%s' ] );
                    }
                }
            }


            /**
             * Function which runs upon plugin deactivation
             */
            public function bp_plugin_deactivation() {
                delete_option( 'bp_date_format' );
                delete_option( 'bp_currency' );
            }


            /**
             * Add admin page
             */
            public function bp_settings() {
                if ( ! is_admin() ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                return [
                    'db_version' => '1.3',
                    'version'    => get_plugin_data( __FILE__ )['Version'],
                ];
            }


            public function bp_load_textdomain() {
                load_plugin_textdomain( 'b3-assets-tracker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
            }

            /**
             * Add admin page
             */
            public function bp_admin_pages() {
                include 'admin/dashboard.php';
                add_menu_page( 'A$$et$', 'A$$et$', 'manage_options', 'bp-assets-dashboard', 'bp_assets_dashboard', 'dashicons-chart-pie', '3' );
                include 'admin/data.php';
                add_submenu_page( 'options.php', 'Data', 'Data', 'manage_options', 'bp-assets-data', 'bp_assets_data' );
                include 'admin/add-data.php';
                add_submenu_page( 'options.php', 'Add data', 'Add data', 'manage_options', 'bp-assets-add-data', 'bp_assets_add_data' );
                include 'admin/add-type.php';
                add_submenu_page( 'options.php', 'Types', 'Types', 'manage_options', 'bp-assets-types', 'bp_assets_add_type' );
                include 'admin/graphs.php';
                add_submenu_page( 'options.php', 'Graphs', 'Graphs', 'manage_options', 'bp-assets-graphs', 'bp_assets_graphs' );
                include 'admin/settings.php';
                add_submenu_page( 'options.php', 'Settings', 'Settings', 'manage_options', 'bp-assets-settings', 'bp_assets_settings' );
            }


            /**
             * Add css
             */
            public function bp_add_css_admin() {
                wp_register_style( 'bp-assets-admin', plugins_url( 'assets/admin.css', __FILE__ ), [], $this->bp_settings()[ 'version' ] );
                wp_enqueue_style( 'bp-assets-admin' );

                wp_enqueue_script( 'charts', plugins_url( 'assets/js.js', __FILE__ ), [], $this->bp_settings()[ 'version' ], false );
                wp_enqueue_script( 'graphs', plugins_url( 'assets/graphs.js', __FILE__ ), [ 'jquery' ], $this->bp_settings()[ 'version' ], true );

                if ( isset( $_POST[ 'b3_from_till_nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'b3_from_till_nonce' ] ) ), 'b3-from-till-nonce' ) ) {
                    if ( isset( $_POST[ 'show_graph' ] ) ) {
                        $validated = b3_validate_graph_fields( $_POST );

                        if ( $validated ) {
                            $asset_types  = isset( $_POST[ 'asset_type' ] ) ? wp_unslash( $_POST[ 'asset_type' ] ) : [];
                            $asset_types  = in_array( 'all', $asset_types ) ? 'all' : $asset_types;
                            $asset_groups = isset( $_POST[ 'asset_group' ] ) ? wp_unslash( $_POST[ 'asset_group' ] ) : [];
                            $asset_groups = in_array( 'all', $asset_groups ) ? 'all' : $asset_groups;
                            $date_from    = isset( $_POST[ 'stats_from' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_from' ] ) ) : '';
                            $date_until   = isset( $_POST[ 'stats_until' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'stats_until' ] ) ) : '';
                            $dates        = isset( $_POST[ 'bp_dates' ] ) ? wp_unslash( $_POST[ 'bp_dates' ] ) : [];
                            $graph_type   = isset( $_POST[ 'graph_type' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'graph_type' ] ) ) : '';
                            $show_all     = 'all' == $asset_types || 'all' == $asset_groups ? true : false;
                            $grouped_data = bp_get_results_range( $dates, $asset_types, $asset_groups, $show_all );
                            $h_axis_title = esc_html__( 'Date', 'b3-assets-tracker' );
                            $v_axis_title = esc_html__( 'Value', 'b3-assets-tracker' );

                            if ( ! empty( $grouped_data ) ) {
                                $processed_data = bp_process_data_for_chart( $grouped_data, $asset_types, $asset_groups, $graph_type );

                                if ( 'bar' === $graph_type ) {
                                    $h_axis_title = esc_html__( 'Value', 'b3-assets-tracker' );
                                    $v_axis_title = esc_html__( 'Asset', 'b3-assets-tracker' );
                                }

                                $graph_title_args = [
                                    'type'       => $graph_type,
                                    'asset_type' => $asset_types,
                                ];
                                $graph_title      = bp_get_graph_title( $graph_title_args );
                                $margin_top       = 'auto';
                                $margin_left      = 'auto';
                                $margin_right     = 'auto';

                                $chart_args = [
                                    'asset_group'  => $asset_groups,
                                    'asset_type'   => $asset_types,
                                    'currency'     => get_option( 'bp_currency' ),
                                    'graph_title'  => $graph_title,
                                    'graph_type'   => $graph_type,
                                    'h_axis_title' => $h_axis_title,
                                    'v_axis_title' => $v_axis_title,
                                    'legend'       => 'right',
                                    'margin_top'   => $margin_top,
                                    'margin_left'  => $margin_left,
                                    'margin_right' => $margin_right,
                                    'data'         => $processed_data,
                                ];
                                wp_enqueue_script( 'google-chart', 'https://www.gstatic.com/charts/loader.js', [], $this->bp_settings()[ 'version' ], false );
                                wp_localize_script( 'graphs', 'chart_vars', $chart_args );
                            }
                        }
                    }
                }
            }


            public function bp_add_css_front() {
                if ( ! is_admin() ) {
                    wp_register_style( 'bp-assets-front', plugins_url( 'assets/front.css', __FILE__ ), [], $this->bp_settings()[ 'version' ] );
                    wp_enqueue_style( 'bp-assets-front' );

                    // @TODO: add check IF shortcode is used
                    wp_enqueue_script( 'google-chart', 'https://www.gstatic.com/charts/loader.js', [], $this->bp_settings()[ 'version' ], false );
                    wp_enqueue_script( 'graphs', plugins_url( 'assets/graphs.js', __FILE__ ), [ 'jquery' ], $this->bp_settings()[ 'version' ], true );
                }
            }


            public function bp_check_table() {
                $db_version = get_option( 'assets_db_version', false );
                if ( false == $db_version || $db_version != $this->bp_settings()[ 'db_version' ] ) {
                    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                    global $wpdb;
                    ob_start();
                    ?>
                    CREATE TABLE <?php echo esc_sql( $wpdb->prefix ); ?>asset_types (
                    id int(6) unsigned NOT NULL auto_increment,
                    name varchar(50) NOT NULL,
                    ordering int(2) NOT NULL,
                    asset_group int(2) NOT NULL,
                    hide int(1) unsigned NULL,
                    added DATE NULL,
                    closed DATE NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo esc_sql( $wpdb->collate ); ?>;
                    <?php
                    $sql1 = ob_get_clean();
                    dbDelta( $sql1 );

                    ob_start();
                    ?>
                    CREATE TABLE <?php echo esc_sql( $wpdb->prefix ); ?>asset_data (
                    id int(6) unsigned NOT NULL auto_increment,
                    date DATE NOT NULL,
                    type int(2) NOT NULL,
                    value decimal(8,2) NOT NULL,
                    updated int(11) NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo esc_sql( $wpdb->collate ); ?>;
                    <?php
                    $sql2 = ob_get_clean();
                    dbDelta( $sql2 );

                    ob_start();
                    ?>
                    CREATE TABLE <?php echo esc_sql( $wpdb->prefix ); ?>asset_groups (
                    id int(6) unsigned NOT NULL auto_increment,
                    name varchar(50) NOT NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo esc_sql( $wpdb->collate ); ?>;
                    <?php
                    $sql3 = ob_get_clean();
                    dbDelta( $sql3 );
                    update_option( 'assets_db_version', $this->bp_settings()[ 'db_version' ] );
                }
            }
        }

        /**
         * The main function responsible for returning the one true B3AssetsTracker instance to functions everywhere.
         *
         * @return \B3AssetsTracker
         */
        function init_assets_plugin() {
            global $assets_plugin;

            if ( ! isset( $assets_plugin ) ) {
                $assets_plugin = new B3AssetsTracker();
                $assets_plugin->initialize();
            }

            return $assets_plugin;
        }

        // initialize
        init_assets_plugin();

    endif;
