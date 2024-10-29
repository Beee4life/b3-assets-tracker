// src: https://developers.google.com/chart/interactive/docs/gallery/linechart
jQuery(document).ready(function () {
    if (typeof(chart_vars) != "undefined" && chart_vars !== null) {
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var currency = chart_vars.currency;
            var legend_position = chart_vars.legend;
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
                    legend: { position: legend_position },
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
    } else {
        console.log('No results or something else went wrong');
    }
});
