/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

jQuery(document).ready(function($) {
	// Hide/Show Charts
	$('#dv_charts').click(function() {
		$('.dv_top_pannel').hide();
		if ($(this).attr('checked') === true) {
			$('.dv_panel_btn').not(this).attr('checked', false);
			$('#dv_charts_panel').show('fast');
		} else {
			$('#dv_charts_panel').hide('fast');
		}
	});

	$('#dv_charts_draw_btn').click(function() {
		if ($('#dv_charts_x').val() === null || $('#dv_charts_y').val() === null) {
			alert('Please select values for x,y');
			return;
		}
		var data_arr = dv_table.fnGetFilteredData();
		var x_data = [];
		var y_data = [];
		var xy_data = [];
		var x_label = '';
		var series = [];

		for (var i=0; i<data_arr.length; i++) {
			if ($(data_arr[i][$('#dv_charts_x').val()]).text() !== '') {
				x_data.push($(data_arr[i][$('#dv_charts_x').val()]).text());
			} else {
				x_data.push(data_arr[i][$('#dv_charts_x').val()]);
			}
		}
		var ds = $('#dv_charts_y').val();

		for (var j=0; j<ds.length; j++) {
			var yd = [];
			var xyd = [];
			for (var i=0; i<data_arr.length; i++) {
				var x,y;
				if ($(data_arr[i][$('#dv_charts_y').val()]).text() !== '') {
					y = +$(data_arr[i][ds[j]]).text();
				} else {
					y = +data_arr[i][ds[j]];
				}

				if ($(data_arr[i][$('#dv_charts_x').val()]).text() !== '') {
					x = +$(data_arr[i][$('#dv_charts_x').val()]).text();
				} else {
					x = +data_arr[i][$('#dv_charts_x').val()];
				}

				yd.push(+y);
				xyd.push(new Array(+x,+y));
			}
			series.push({label: $('#dv_charts_y option[value=' + ds[j] + ']').text()});
			y_data.push(yd);
			xy_data.push(xyd);
		}

		$('#dv_charts_preview_chart').empty();

		var data = {};
		var conf = {};

		data.x_data = x_data;
		data.y_data = y_data;
		data.xy_data = xy_data;

		conf.series = series;
		conf.title = "title";

		switch($('#dv_charts_type').val()) {
			case '0':
				plot = draw_line(data, conf);
				break;
			case '1':
				plot = draw_line2(data, conf);
				break;
			case '2':
				plot = draw_bar(data, conf);
				break;
		}

		$('#dv_charts_panel').bind('resizestop', function(event, ui) {
			plot.replot();
		});
	});

	function draw_line(data, conf) {
		return $.jqplot('dv_charts_preview_chart', data.xy_data, {
			title: conf.title,
			series: conf.series,
			legend: {
				renderer: $.jqplot.EnhancedLegendRenderer,
				placement:'inside',
				location:'ne',
				rendererOptions:{
					numberColumns: 3
				},
				show: true
			},
			axes: {
				xaxis: {
//					min: chart.x_min,
//					ticks: x_data,
//					tickInterval: chart.interval,
//					label: chart.x_label,
					autoscale: true,
					syncTicks: true,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					rendererOptions: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer
						},
					tickOptions: {
						showLabel: true,
						angle:-60
//						formatString:'%d'
					}
				},

				yaxis: {
//					min: chart.y_min,
//					label: chart.y_label,
					autoscale: true,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
			},

			highlighter: {
				show: true
			}
		});
	}

	function draw_line2(data, conf)	{
		return $.jqplot('dv_charts_preview_chart', data.y_data, {
			title: conf.title,
			series: conf.series,
			legend: {
				renderer: $.jqplot.EnhancedLegendRenderer,
				placement:'inside',
				location:'ne',
				rendererOptions:{
					numberColumns: 3
				},
				show: true
			},
			axes: {
				xaxis: {
					autoscale: true,
					syncTicks: true,
//					min: chart.x_min,
//					ticks: x_data,
//					tickInterval: chart.interval,
//					label: chart.x_label,
					renderer:$.jqplot.CategoryAxisRenderer,
					ticks: data.x_data,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					rendererOptions: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer
						},
					tickOptions: {
						showLabel: true,
						angle:-60
//						formatString:'%d'
					}
				},

				yaxis: {
					autoscale: true,
//					min: chart.y_min,
//					label: chart.y_label,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
			},

			highlighter: {
				show: true
			}
		});
	}

	function draw_bar(data, conf) {
		return $.jqplot('dv_charts_preview_chart', data.y_data, {
			title: conf.title,
			series: conf.series,
			seriesDefaults: {
				renderer: $.jqplot.BarRenderer
//				rendererOptions:{barMargin: 25}
			},
			legend: {
				renderer: $.jqplot.EnhancedLegendRenderer,
				placement:'inside',
				location:'ne',
				rendererOptions:{
					numberColumns: 3
				},
				show: true
			},
			axes: {
				xaxis: {
					autoscale: true,
//					min: chart.x_min,
//					ticks: x_data,
//					tickInterval: chart.interval,
//					label: chart.x_label,
					renderer:$.jqplot.CategoryAxisRenderer,
					ticks: data.x_data,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
					rendererOptions: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer
						},
					tickOptions: {
						showLabel: true,
						angle:-60
//						formatString:'%d'
					}
				},

				yaxis: {
					autoscale: true,
//					min: chart.y_min,
//					label: chart.y_label,
					labelRenderer: $.jqplot.CanvasAxisLabelRenderer
				}
			},

			highlighter: {
				show: true
			}
		});
	}
});
