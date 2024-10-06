<?php
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
    
    
    /**
     * Add chart script
     *
     * @https://developers.google.com/chart/interactive/docs/gallery/linechart
     *
     * @return void
     */
    function bp_add_chart_script() {
        ob_start();
        ?>
        <script type="text/javascript">
            if (typeof(chart_vars) != "undefined" && chart_vars !== null) {
                google.charts.load('current', {'packages':['corechart', 'bar']});
                google.charts.setOnLoadCallback(drawChart);
                
                function drawChart() {
                    var data = google.visualization.arrayToDataTable(chart_vars.data);
           
                    if ( chart_vars.graph_type === 'line' && chart_vars.asset_types === 'all' ) {
                        var options = {
                            title : 'Totals',
                            vAxis: {title: 'Value'},
                            hAxis: {title: 'Date'},
                            curveType: 'function',
                            legend: 'none',
                            width: 1200,
                            height: 500
                        };
                        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    } else if ( chart_vars.graph_type === 'line' ) {
                        var options = {
                            title : 'Week diff',
                            vAxis: {title: 'Value'},
                            hAxis: {title: 'Week'},
                            curveType: 'function',
                            legend: { position: 'right' },
                            series: {5: {type: 'line'}},
                            width: 1200,
                            height: 500
                        };
                        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    } else if ( chart_vars.graph_type === 'total' ) {
                        var options = {
                            title : 'Assets per type',
                            is3D : true,
                            // pieHole : 0.1,
                            pieSliceText: 'label',
                            width: 800,
                            height: 500
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                        
                    } else {
                        var options = {
                            title : 'Total value per type',
                            vAxis: {title: 'Value'},
                            hAxis: {title: 'Week'},
                            seriesType: 'bars',
                            series: {5: {type: 'line'}},
                            width: 1200,
                            height: 500
                        };
                        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
                    }
                    chart.draw(data, options);
                }
            }
        </script>
        <?php
        $output = ob_get_clean();
        echo $output;
    }
    add_action( 'admin_head', 'bp_add_chart_script' );
