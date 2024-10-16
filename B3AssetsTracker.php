<?php
    /*
        Plugin Name:    B3 : Assets Tracker
        Description:    This plugin gives you the option to track and analyze your (financial) assets.
        Version:        1.4.0
        Author:         Beee
        Author URI:     https://berryplasman.com
        License:        GPL2
        Text-domain:    assets-tracker
        License:        GPL v2 (or later)
        License URI:    https://www.gnu.org/licenses/gpl-2.0.html
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
                add_action( 'init',                     [ $this, 'bp_load_plugin_text_domain' ] );

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
                $results = $wpdb->get_results( "SELECT * FROM $table" );

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
                return [
                    'db_version' => '1.2',
                    'version'    => get_plugin_data( __FILE__ )[ 'Version' ],
                ];
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

                wp_enqueue_script( 'charts', plugins_url( 'assets/js.js', __FILE__ ), [] );

                if ( isset( $_POST[ 'stats_until' ] ) && isset( $_POST[ 'show_graph' ] ) ) {
                    $validated = b3_validate_graph_fields( $_POST );

                    if ( $validated ) {
                        $asset_types  = isset( $_POST[ 'asset_type' ] ) ? $_POST[ 'asset_type' ] : [];
                        $asset_types  = in_array( 'all', $asset_types ) ? 'all' : $asset_types;
                        $asset_groups = isset( $_POST[ 'asset_group' ] ) ? $_POST[ 'asset_group' ] : [];
                        $date_from    = isset( $_POST[ 'stats_from' ] ) ? $_POST[ 'stats_from' ] : '';
                        $date_till    = $_POST[ 'stats_until' ];
                        $graph_type   = isset( $_POST[ 'graph_type' ] ) ? $_POST[ 'graph_type' ] : '';
                        $show_all     = 'all' == $asset_types ? true : false;
                        $grouped_data = bp_get_results_range( $date_from, $date_till, $asset_types, $asset_groups, $show_all );

                        if ( ! empty( $grouped_data ) ) {
                            $processed_data = bp_process_data_for_chart( $grouped_data, $asset_types, $asset_groups, $graph_type );

                            $chart_args = [
                                'asset_group' => $asset_groups,
                                'asset_type'  => $asset_types,
                                'graph_type'  => $graph_type,
                                'currency'    => get_option( 'bp_currency' ),
                                'data'        => $processed_data,
                            ];
                            wp_localize_script( 'charts', 'chart_vars', $chart_args );
                        }

                        $in_footer = false;
                        wp_enqueue_script( 'google-chart', 'https://www.gstatic.com/charts/loader.js', [], '', $in_footer );
                    }
                }
            }


            public function bp_add_css_front() {
                wp_register_style( 'bp-assets-front', plugins_url( 'assets/front.css', __FILE__ ), [], $this->bp_settings()[ 'version' ] );
                wp_enqueue_style( 'bp-assets-front' );
            }


            public function bp_check_table() {
                $db_version = get_option( 'assets_db_version', false );
                if ( false == $db_version || $db_version != $this->bp_settings()[ 'db_version' ] ) {
                    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                    global $wpdb;
                    ob_start();
                    ?>
                    CREATE TABLE <?php echo $wpdb->prefix; ?>asset_types (
                    id int(6) unsigned NOT NULL auto_increment,
                    name varchar(50) NOT NULL,
                    ordering int(2) NOT NULL,
                    asset_group int(2) NOT NULL,
                    hide int(1) unsigned NULL,
                    closed DATE NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo $wpdb->collate; ?>;
                    <?php
                    $sql1 = ob_get_clean();
                    dbDelta( $sql1 );

                    ob_start();
                    ?>
                    CREATE TABLE <?php echo $wpdb->prefix; ?>asset_data (
                    id int(6) unsigned NOT NULL auto_increment,
                    date DATE NOT NULL,
                    type int(2) NOT NULL,
                    value decimal(8,2) NOT NULL,
                    updated int(11) NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo $wpdb->collate; ?>;
                    <?php
                    $sql2 = ob_get_clean();
                    dbDelta( $sql2 );

                    ob_start();
                    ?>
                    CREATE TABLE <?php echo $wpdb->prefix; ?>asset_groups (
                    id int(6) unsigned NOT NULL auto_increment,
                    name varchar(50) NOT NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo $wpdb->collate; ?>;
                    <?php
                    $sql3 = ob_get_clean();
                    dbDelta( $sql3 );
                    update_option( 'assets_db_version', $this->bp_settings()[ 'db_version' ] );
                }
            }
            
            
            public function bp_load_plugin_text_domain() {
                $plugin_folder = dirname( plugin_basename( __FILE__ ) );
                $locale        = apply_filters( 'plugin_locale', get_locale(), $plugin_folder );
                load_textdomain( $plugin_folder, trailingslashit( WP_LANG_DIR ) . $plugin_folder . '/' . $plugin_folder . '-' . $locale . '.mo' );
                load_plugin_textdomain( $plugin_folder, false, $plugin_folder . '/languages/' );
            }


            public static function bp_admin_menu() {
                $admin_url     = admin_url( 'admin.php?page=' );
                $current_class = ' class="current_page"';
                $url_array     = parse_url( esc_url_raw( $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] ) );
                $subpage       = ( isset( $url_array[ 'query' ] ) ) ? substr( $url_array[ 'query' ], 8 ) : false;

                $pages = [
                    'assets-dashboard' => esc_html__( 'Dashboard', 'bp-assets' ),
                    'assets-data'      => esc_html__( 'Data', 'bp-assets' ),
                    'assets-add-data'  => esc_html__( 'Add data', 'bp-assets' ),
                    'assets-graphs'     => esc_html__( 'Graphs', 'bp-assets' ),
                    'assets-types'     => esc_html__( 'Types', 'bp-assets' ),
                    'assets-settings'  => esc_html__( 'Settings', 'bp-assets' ),
                    // 'assets-info'      => esc_html__( 'Info', 'bp-assets' ),
                ];

                ob_start();
                foreach( $pages as $slug => $label ) {
                    $current_page = ( $subpage === $slug ) ? $current_class : false;
                    echo ( 'assets-dashboard' !== $slug ) ? ' | ' : false;
                    echo sprintf( '<a href="%s"%s>%s</a>', sprintf( '%sbp-%s', $admin_url, $slug ), $current_page, $label );
                }
                $menu_items = ob_get_clean();
                $menu       = sprintf( '<p class="bp-admin-menu">%s</p>', $menu_items );

                return $menu;
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
