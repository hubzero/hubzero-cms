/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


var dv_show_chart;
var dv_plot;

jQuery(document).ready(function($) {
	var native_canvas = !$.ui.ie;

	// Hide/Show Charts
	$(document).on('click', '#dv-spreadsheet-charts', function() {
		$('.dv_top_pannel').hide();
		if ($(this).hasClass('btn-inverse')) {
			$(this).removeClass('btn-inverse');
			$('#dv_charts_panel').hide();
		} else {
			$(this).addClass('btn-inverse');
			$('#dv_charts_panel').show();
			$('#dv_chart_name').trigger('change');
		}
	});

	$('#dv_chart_name').change(function() {
		$('#dv_chart_desc').html('');
		if ($(this).val() != -1) {
			var chart = dv_data.charts_list[$(this).val()];
			if (typeof chart.description !== "undefined") {
				$('#dv_chart_desc').html(chart.description);
				$('#dv_pdcharts_draw_btn').trigger('click');
				return;
			}
		}
	});

	$('#dv_pdcharts_draw_btn').click(function() {
		draw_plot();
	});

	function draw_plot() {
		var data_arr = dv_table.fnGetFilteredData();
		var dataseries = [];
		var series = [];
		var chart;
		var i, j;
		var options = {};

		if ($('#dv_chart_name').val() != -1) {
			chart = dv_data.charts_list[$('#dv_chart_name').val()];
		} else {
			alert('Please select a chart');
		}

		if (typeof chart.width != 'undefined') {
			$('#dv_charts_panel').width(+$('#dv_charts_control_panel').width() + +chart.width);
		} else {
			if ($('#dv_charts_panel').width() < ($(window).width() - 100)) {
				$('#dv_charts_panel').width($(window).width() - 100);
			}
		}

		if (typeof chart.height != 'undefined') {
			$('#dv_charts_panel').height(chart.height);
		} else {
			$('#dv_charts_panel').height(380);
		}

		if (typeof data_arr == 'undefined' || data_arr.length < 1) {
			$('#dv_charts_preview_chart').html("<h2>No Data available...</h2>");
			return;
		}

		options.title = chart.title;
		options.series = [];

		var data = [];

		if (typeof chart.data_series_group != 'undefined') {
			group_col = $.inArray(chart.data_series_group, dv_data.vis_cols);
			group_ids = [];

			data_series_obj = {};
			group_label = {};

			for (i=0; i<data_arr.length; i++) {
				if ($.inArray(data_arr[i][group_col], group_ids) === -1) {
					group_ids.push(data_arr[i][group_col]);
					data_series_obj[data_arr[i][group_col]] = [];
				}

				data_point = [];
				for (j=0; j<chart.data_series.length; j++) {
					var dp;
					if (chart.data_series[j][1] === 'text') {
						dp = data_arr[i][$.inArray(chart.data_series[j][0], dv_data.vis_cols)];
						data_point.push(dp.stripTags());
					} else {
						dp = data_arr[i][$.inArray(chart.data_series[j][0], dv_data.vis_cols)];
						if (isNaN(+dp.stripTags())) {
							data_point.push(null);
						} else {
							data_point.push(+dp.stripTags());
						}
					}
				}

				if (typeof chart.data_series_label_2 != 'undefined') {
					group_label[data_arr[i][group_col]] = [];
					label_2 = '';
					for (j=0; j<chart.data_series_label_2.vals.length; j++) {
						label_2 = label_2 + data_arr[i][$.inArray(chart.data_series_label_2.vals[j], dv_data.vis_cols)].stripTags();
						if (typeof chart.data_series_label_2.separator != 'undefined' && (j+1) < chart.data_series_label_2.vals.length) {
							label_2 = label_2 + chart.data_series_label_2.separator;
						}
					}
					group_label[data_arr[i][group_col]].push(label_2);
				}

				data_series_obj[data_arr[i][group_col]].push(data_point);
			}

			for (i=0; i<group_ids.length; i++) {
				data.push(data_series_obj[group_ids[i]]);

				if (typeof chart.data_series_label_2 != 'undefined') {
					if (typeof chart.func_series != 'undefined' && typeof chart.func_series[group_ids[i]] != undefined) {
						options.series.push({
							'label': group_label[group_ids[i]],
							'dataviewfn': chart.func_series[group_ids[i]]
						});
					} else {
						options.series.push({'label': group_label[group_ids[i]]});
					}
				} else {
					if (typeof chart.func_series != 'undefined' && typeof chart.func_series[group_ids[i]] != undefined) {
						options.series.push({
							'label': chart.data_series_label + " - " + group_ids[i].stripTags(),
							'dataviewfn': chart.func_series[group_ids[i]]
						});
					} else {
						options.series.push({'label': chart.data_series_label + " - " + group_ids[i].stripTags()});
					}
				}
			}

			options.seriesDefaults = chart.series;
		} else {
			for (k=0; k<chart.series.length; k++) {
				d = [];
				for (i=0; i<data_arr.length; i++) {
					data_point = [];
					for (j=0; j<chart.series[k].data_series.length; j++) {
						var dp;
						if (chart.series[k].data_series[j][1] === 'text') {
							dp = data_arr[i][$.inArray(chart.series[k].data_series[j][0], dv_data.vis_cols)];
							data_point.push(dp.stripTags());
						} else {
							dp = data_arr[i][$.inArray(chart.series[k].data_series[j][0], dv_data.vis_cols)];
							if (isNaN(+dp.stripTags())) {
								data_point.push(null);
							} else {
								data_point.push(+dp.stripTags());
							}
						}
					}
					d.push(data_point);
				}
				data.push(d);
			}

			if (chart.series.renderer === 'bar') {
				chart.series.renderer = $.jqplot.BarRenderer;
			}
			options.seriesDefaults = {};
			options.series = chart.series;
		}

		$('#dv_charts_preview_chart').empty();
		options.axes = chart.axes;
		options.axes.yaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;
		options.axes.xaxis.labelRenderer = $.jqplot.CanvasAxisLabelRenderer;

		if (typeof chart.legend != 'undefined') {
			options.legend = chart.legend;
			options.legend.renderer = $.jqplot.EnhancedLegendRenderer;
			options.legend.placement = 'inside';
			options.legend.show = true;
		} else {
			options.legend = {
					renderer: $.jqplot.EnhancedLegendRenderer,
					placement:'inside',
					location:'ne',
					rendererOptions:{
						numberColumns: 2
					},
					show: true
				};
		}

		if (options.seriesDefaults.renderer == 'bar') {
			options.seriesDefaults.renderer = $.jqplot.BarRenderer;
			options.seriesDefaults.showHighlight = false;
			options.axes.xaxis.renderer =  $.jqplot.CategoryAxisRenderer;
			options.highlighter = {show: false};
			options.cursor = {show: false};
		} else {
			options.highlighter = {
				show: true,
				showTooltip: true,
				tooltipLocation: 's'
			};

			options.cursor = {
				style: 'crosshair',
				show: true,
				zoom: true,
				showTooltip: true
			};
		}
		
		options.axes.xaxis.rendererOptions = {tickRenderer: $.jqplot.CanvasAxisTickRenderer};
		options.axes.yaxis.rendererOptions = {tickRenderer: $.jqplot.CanvasAxisTickRenderer};

		plot = draw(data, options);

		/* Events issue fixed on IE8/IE7 mode, still need to be tested on IE7 proper
		if (jQuery.browser.msie && $.browser.version < 9) {
			$('#dv_charts_preview_chart').children().each(function() {
				$(this).unbind();
				$(this).die();
				$(this).undelegate();
			});
		}
		*/
		
		$('#dv_charts_panel').bind('resizestop', function(event, ui) {
			plot.replot();
		});

		if (!native_canvas) {
			$(window).bind('load', function() {
				plot.replot();
			});
		}

	}

	function draw(data, options) {
		return $.jqplot('dv_charts_preview_chart', data, {
			animate: true,
			animateReplot: true,
			title: options.title,
			seriesDefaults: options.seriesDefaults,
			series: options.series,
			legend: options.legend,
			axes: options.axes,
			highlighter: options.highlighter,
			cursor: options.cursor
		});
	}

	$("tfoot input").bind('keyup', function(e) {
		var idx = $("tfoot input").index(this);
		if (e.keyCode !== 13 || $('#dv_charts_panel:visible').length < 1) {
			return;
		}

		if (e.keyCode === 13 || $('#dv_charts_panel:visible').length === 1) {
			$('#dv_pdcharts_draw_btn').trigger('click');
		}
	});

	if(typeof dv_settings.show_charts != 'undefined') {
		$('#dv-spreadsheet-charts').addClass('btn-inverse');

		if (!native_canvas) {
			$(window).bind('load', function() {
				$('#dv_charts_panel').show();
				$('#dv_chart_name').val(dv_show_chart);
				$('#dv_chart_name').trigger('change');
			});
		} else {
			$('#dv_charts_panel').show();
			$('#dv_chart_name').val(dv_show_chart);
			$('#dv_chart_name').trigger('change');
		}
	}

	if (!native_canvas) {
		$('#dv_pdcharts_download_btn').hide();
	}

	$("#dv_charts_panel").resizable({
		minHeight: 380,
		minWidth: 800
	});
});
