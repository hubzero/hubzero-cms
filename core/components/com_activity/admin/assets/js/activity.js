/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	var com_activity_charts = [],
		month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	$('.com_activity-chart').each(function(i, el){
		var data = $('#' + $(el).attr('data-datasets')).html();

		var datasets = JSON.parse(data);

		var com_activity_chart = $.plot($(el), datasets.datasets, {
			series: {
				lines: {
					show: true,
					fill: false
				},
				points: { show: false },
				shadowSize: 0
			},
			grid: {
				color: 'rgba(0, 0, 0, 0.6)',
				borderWidth: 1,
				borderColor: 'transparent',
				hoverable: true,
				clickable: true
			},
			tooltip: true,
				tooltipOpts: {
				content: "%y %s in %x",
				shifts: {
					x: -60,
					y: 25
				},
				defaultTheme: false
			},
			legend: {
				show: false,
				noColumns: 2,
				position: "ne",
				backgroundColor: 'transparent',
				margin: [0, -50]
			},
			xaxis: {
				mode: "time",
				tickDecimals: 0,
				tickFormatter: function (val, axis) {
					var d = new Date(val);
					return month_short[d.getUTCMonth()] + ' ' + d.getUTCDate();
				}
			},
			yaxis: {
				show: false,
				min: 0
			}
		});

		com_activity_charts.push(com_activity_chart);
	});
});
