// info: https://developers.google.com/chart/interactive/docs/gallery/barchart
// info: https://developers.google.com/chart/interactive/docs/gallery/linechart
// info: https://developers.google.com/chart/interactive/docs/gallery/piechart
jQuery(document).ready(function () {
    if (typeof(chart_vars) != "undefined" && chart_vars !== null) {
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var currency = chart_vars.currency;
            var data = google.visualization.arrayToDataTable(chart_vars.data);
            var graph_title = chart_vars.graph_title;

            if ( chart_vars.graph_type === 'bar' ) {
                var options = {
                    title : graph_title,
                    hAxis: {title: chart_vars.h_axis_title, format: currency + " #.###"},
                    vAxis: {title: chart_vars.v_axis_title },
                    animation: {startup: true, duration: 500 },
                    width: '100%',
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
                };
                var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

            } else if ( chart_vars.graph_type === 'line' && chart_vars.asset_type === 'all' ) {
                var options = {
                    title : graph_title,
                    hAxis: {title: chart_vars.h_axis_title},
                    vAxis: {title: chart_vars.v_axis_title, format: currency + " #.###" },
                    curveType: 'function',
                    legend: 'none',
                    width: '100%',
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
                };
                var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

            } else if ( chart_vars.graph_type === 'line' ) {
                var options = {
                    title : graph_title,
                    hAxis: {title: chart_vars.h_axis_title},
                    vAxis: {title: chart_vars.v_axis_title, format: currency + ' #.###' },
                    curveType: 'function',
                    legend: { position: chart_vars.legend },
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
                };
                var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

            } else if ( chart_vars.graph_type === 'total_type' ) {
                var options = {
                    title : graph_title,
                    is3D : true,
                    legend: { position: chart_vars.legend, maxLines: 3 },
                    pieSliceText: 'label',
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
                };
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

            } else if ( chart_vars.graph_type === 'total_group' ) {
                var options = {
                    title : graph_title,
                    is3D : true,
                    // @TODO: check for legend position
                    pieSliceText: 'label',
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
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
                    height: 500,
                    chartArea: { top: chart_vars.margin_top, left: chart_vars.margin_left, right: chart_vars.margin_right }
                };
                var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
            }

            // google.visualization.events.addListener(chart, 'ready', function () {
            //     chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
            // });

            chart.draw(data, options);
        }
    } else {
        console.log('No results or something else went wrong');
    }
});
