<?php
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
                    var currency = chart_vars.currency;
                    var data = google.visualization.arrayToDataTable(chart_vars.data);

                    if ( chart_vars.graph_type === 'line' && chart_vars.asset_type === 'all' ) {
                        var options = {
                            title : 'Totals',
                            hAxis: {title: 'Date'},
                            vAxis: {title: 'Value', format: currency + " #.###" },
                            curveType: 'function',
                            legend: 'none',
                            width: '100%',
                            height: 500
                        };
                        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    } else if ( chart_vars.graph_type === 'line' ) {
                        var options = {
                            title : 'Week diff',
                            hAxis: {title: 'Week'},
                            vAxis: {title: 'Value', format: currency + ' #,###' },
                            curveType: 'function',
                            legend: { position: 'right' },
                            series: {5: {type: 'line'}},
                            width: '100%',
                            height: 500
                        };
                        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

                    } else if ( chart_vars.graph_type === 'total_type' ) {
                        var options = {
                            title : 'Assets per type',
                            is3D : true,
                            // pieHole : 0.1,
                            pieSliceText: 'label',
                            width: '100%',
                            height: 500
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

                    } else if ( chart_vars.graph_type === 'total_group' ) {
                        var options = {
                            title : 'Assets per group',
                            is3D : true,
                            // pieHole : 0.1,
                            pieSliceText: 'label',
                            width: '100%',
                            height: 500
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

                    } else {
                        // combo chart
                        var options = {
                            title : 'Total value per type',
                            vAxis: {title: 'Value'},
                            hAxis: {title: 'Week'},
                            seriesType: 'bars',
                            series: {5: {type: 'line'}},
                            width: '100%',
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
        echo esc_js( $output );
    }
    // add_action( 'admin_head', 'bp_add_chart_script' );
