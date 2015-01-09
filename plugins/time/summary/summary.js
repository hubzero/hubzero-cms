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
			'showSearch'          : true,
			'searchPlaceholder'   : 'search...',
			'maxHeightWithSearch' : 300
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
					ticks.push([i, val.name]);
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

	$('#hub_id').change(function(event) {
		// First, grab the currently select task
		var task = $('#task_id').val();

		// Create a ajax call to get the tasks
		$.ajax({
			url: "/api/time/indexTasks",
			data: "hid="+$(this).val()+"&pactive=1",
			dataType: "json",
			cache: false,
			success: function(json){
				// If success, update the list of tasks based on the chosen hub
				var options = '';

				if(json.tasks.length > 0) {
					options = '<option value="">no task selected...</option>';
					for (var i = 0; i < json.tasks.length; i++) {
						options += '<option value="';
						options += json.tasks[i].id;
						options += '"';
						if (json.tasks[i].id == task) {
							options += ' selected="selected"';
						}
						options += '>';
						options += json.tasks[i].name;
						options += '</option>';
					}
				} else {
					options = '<option value="">No tasks for this hub</option>';
				}
				$("#task_id").html(options);

				if (!!$.prototype.HUBfancyselect) {
					$('#task_id').prev('.fs-dropdown').remove();
					$('#task_id').HUBfancyselect({
						'showSearch'          : true,
						'searchPlaceholder'   : 'search...',
						'maxHeightWithSearch' : 200
					});
				}
			}
		});
	});
});