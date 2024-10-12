/*
 *  Document   : uiWidgets.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Widgets page
 */

var UiWidgets = function() {

    return {
        init: function() {
            /* Mini Line Charts with jquery.sparkline plugin, for more examples you can check out http://omnipotent.net/jquery.sparkline/#s-about */
            var widgetChartLineOptions = {
                type: 'line',
                width: '200px',
                height: '120px',
                tooltipOffsetX: -25,
                tooltipOffsetY: 20,
                lineColor: '#afde5c',
                fillColor: '#afde5c',
                spotColor: '#555555',
                minSpotColor: '#555555',
                maxSpotColor: '#555555',
                highlightSpotColor: '#555555',
                highlightLineColor: '#555555',
                spotRadius: 3,
                tooltipPrefix: '',
                tooltipSuffix: ' Sales',
                tooltipFormat: '{{prefix}}{{y}}{{suffix}}'
            };
            $('#widget-chart-sales').sparkline('html', widgetChartLineOptions);

            widgetChartLineOptions['lineColor'] = '#5cadde';
            widgetChartLineOptions['fillColor'] = '#5cadde';
            widgetChartLineOptions['tooltipPrefix'] = '';
            widgetChartLineOptions['tooltipSuffix'] = ' Tickets';
            $('#widget-chart-tickets').sparkline('html', widgetChartLineOptions);

            var widgetChartBarOptions = {
                type: 'bar',
                barWidth: 10,
                barSpacing: 7,
                height: '120px',
                tooltipOffsetX: -25,
                tooltipOffsetY: 20,
                barColor: '#de815c',
                tooltipPrefix: '$ ',
                tooltipSuffix: '',
                tooltipFormat: '{{prefix}}{{value}}{{suffix}}'
            };
            $('#widget-chart-earnings').sparkline('html', widgetChartBarOptions);

            /*
             * Flot Charts Jquery plugin is used for charts
             *
             * For more examples or getting extra plugins you can check http://www.flotcharts.org/
             * Plugins included in this template: pie, resize, stack, time
             */

            // Get the elements where we will attach the charts
            var widgetChartClassic  = $('#widget-chart-classic');
            var widgetChartPie      = $('#widget-chart-pie');

            // Data for the charts
            var dataEarnings    = [[1, 1900], [2, 2300], [3, 3200], [4, 2500], [5, 4200], [6, 3100], [7, 3600], [8, 2500], [9, 4600], [10, 3700], [11, 4200], [12, 5200]];
            var dataSales       = [[1, 850], [2, 750], [3, 1500], [4, 900], [5, 1500], [6, 1150], [7, 1500], [8, 900], [9, 1800], [10, 1700], [11, 1900], [12, 2550]];
            var dataTickets     = [[1, 130], [2, 330], [3, 220], [4, 350], [5, 150], [6, 275], [7, 280], [8, 380], [9, 120], [10, 330], [11, 190], [12, 410]];

            var dataMonths      = [[1, 'Jan'], [2, 'Feb'], [3, 'Mar'], [4, 'Apr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Aug'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dec']];

            // Classic Chart
            $.plot(widgetChartClassic,
                [
                    {
                        label: 'Earnings',
                        data: dataEarnings,
                        lines: {show: true, fill: true, fillColor: {colors: [{opacity: .6}, {opacity: .6}]}},
                        points: {show: true, radius: 5}
                    },
                    {
                        label: 'Sales',
                        data: dataSales,
                        lines: {show: true, fill: true, fillColor: {colors: [{opacity: .2}, {opacity: .2}]}},
                        points: {show: true, radius: 5}
                    },
                    {
                        label: 'Tickets',
                        data: dataTickets,
                        lines: {show: true, fill: true, fillColor: {colors: [{opacity: .2}, {opacity: .2}]}},
                        points: {show: true, radius: 5}
                    }
                ],
                {
                    colors: ['#5ccdde', '#454e59', '#ffffff'],
                    legend: {show: true, position: 'nw', backgroundOpacity: 0},
                    grid: {borderWidth: 0, hoverable: true, clickable: true},
                    yaxis: {tickColor: '#f5f5f5', ticks: 3},
                    xaxis: {ticks: dataMonths, tickColor: '#f5f5f5'}
                }
            );

            // Creating and attaching a tooltip to the classic chart
            var previousPoint = null, ttlabel = null;
            widgetChartClassic.bind('plothover', function(event, pos, item) {

                if (item) {
                    if (previousPoint !== item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $('#chart-tooltip').remove();
                        var x = item.datapoint[0], y = item.datapoint[1];

                        if (item.seriesIndex === 0) {
                            ttlabel = '$ <strong>' + y + '</strong>';
                        } else if (item.seriesIndex === 1) {
                            ttlabel = '<strong>' + y + '</strong> sales';
                        } else {
                            ttlabel = '<strong>' + y + '</strong> tickets';
                        }

                        $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                            .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                    }
                }
                else {
                    $('#chart-tooltip').remove();
                    previousPoint = null;
                }
            });

            // Pie Chart
            $.plot(widgetChartPie,
                [
                    {label: 'Sales', data: 30},
                    {label: 'Tickets', data: 10},
                    {label: 'Earnings', data: 60}
                ],
                {
                    colors: ['#454e59', '#5cafde', '#5ccdde'],
                    legend: {show: false},
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 2/3,
                                formatter: function(label, pieSeries) {
                                    return '<div class="chart-pie-label">' + label + '<br>' + Math.round(pieSeries.percent) + '%</div>';
                                },
                                background: {opacity: .75, color: '#000000'}
                            }
                        }
                    }
                }
            );
        }
    };
}();