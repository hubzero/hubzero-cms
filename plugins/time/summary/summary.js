/**
 * @package     hubzero-cms
 * @file        plugins/time/summary/summary.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function( $ ) {
	// Fancy select boxes
	if (!!$.prototype.HUBfancyselect) {
		$('.plg_time_summary select').HUBfancyselect({
			'showSearch'         : true,
			'searchPlaceholder'  : 'seach...'
		});
	}

	// Date picker for date input field
	$(".hadDatepicker").datepicker({
		// Set a unix/MySQL friendly date format
		dateFormat: 'yy-mm-dd'
	});

	// Make clickable items clickable
	$('.clickable').click(function ( e ) {
		$(this).next('.respondable').slideToggle();
	});

	// Flot
	if ($('.charts').length) {
		var plot   = false;
		var data   = [];
		var mapped = [];
		var draw   = function () {
			$('.charts .tasks-bar').css({
				'width'  : $('.charts').width(),
				'height' : (data.length > 1) ? data.length*35 : 50
			});

			if (!plot) {
				var ticks  = [];
				var max    = 0;

				$.each(data, function ( i, val ) {
					mapped.push([val.hours, i]);
					ticks.push([i, val.pname]);
					if (parseFloat(val.hours, 10) > max) {
						max = val.hours;
					}
				});

				var options = {
					series: {
						bars: {
							show: true,
							barWidth: 0.6,
							align: "center",
							horizontal: true
						}
					},
					yaxis: {
						tickLength: 0,
						ticks: ticks
					},
					xaxis: {
						show: true,
						tickSize: (max > 15) ? Math.round(max / 15) : max,
						tickFormatter: function formatter(val, axis) {
							if (val === 0) {
								return val + ' hours(s)';
							} else {
								return val;
							}
						}
					},
					grid: {
						borderColor: 'CCCCCC',
						borderWidth: 0,
						hoverable: true,
						clickable: true
					}
				};

				plot = $.plot('.charts .tasks-bar', [mapped], options);
			} else {
				plot.resize();
				plot.setupGrid();
				plot.setData([mapped]);
				plot.draw();
			}
		};

		// Get data
		var hub_id  = $('#hub_id').val();
		var task_id = $('#task_id').val();
		var start   = $('#start_date').val();
		var end     = $('#end_date').val();
		var url     = "/time/reports?report_type=summary&method=getTimePerTask&hub_id=" + hub_id + "&task_id=" + task_id + "&start_date=" + start + "&end_date=" + end;
		$.ajax({
			url: url,
			dataType: "json",
			cache: false,
			success: function( json ) {
				data = json;
				draw();
			}
		});

		$(window).resize(draw);
	}
});