/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	var mod_courses_charts = [],
		month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	$('.mod_courses-chart').each(function(i, el){
		var data = $('#' + $(el).attr('data-datasets')).html();

		var datasets = JSON.parse(data);

		var mod_courses_chart = $.plot($(el), datasets.datasets, {
			series: {
				lines: {
					show: true,
					fill: true
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
				show: true,
				noColumns: 2,
				position: "ne",
				backgroundColor: 'transparent',
				margin: [0, -50]
			},
			xaxis: {
				mode: "time",
				tickLength: 0,
				tickDecimals: 0,
				tickFormatter: function (val, axis) {
					var d = new Date(val);
					return month_short[d.getUTCMonth()];
				}
			},
			yaxis: { min: 0 }
		});

		mod_courses_charts.push(mod_courses_chart);
	});
});