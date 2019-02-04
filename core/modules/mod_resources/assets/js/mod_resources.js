jQuery(document).ready(function ($) {
	var mod_resources_charts = [];

	$('.mod_resources-chart').each(function(i, el){
		var data = $('#' + $(el).attr('data-datasets')).html();
		var datasets = jQuery.parseJSON(data);
		var mod_resources_chart = $.plot(
			$(el),
			datasets.datasets,
			{
				legend: {
					show: false
				},
				series: {
					pie: {
						innerRadius: 0.5,
						show: true,
						label: {
							show: false
						},
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

		mod_resources_charts.push(mod_resources_chart);
	});
});