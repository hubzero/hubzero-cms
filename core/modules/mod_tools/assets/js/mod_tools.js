/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	var mod_tools_charts = [];

	$('.mod_tools-chart').each(function(i, el){
		var data = $('#' + $(el).attr('data-datasets')).html();
		var datasets = JSON.parse(data);
		var mod_tools_chart = $.plot(
			$(el),
			datasets.datasets,
			{
				legend: {
					show: true
				},
				series: {
					pie: {
						innerRadius: 0.5,
						show: true,
						stroke: {
							color: '#efefef'
						}
					}
				},
				grid: {
					hoverable: false
				}
			}
		);

		mod_tools_charts.push(mod_tools_chart);
	});
});