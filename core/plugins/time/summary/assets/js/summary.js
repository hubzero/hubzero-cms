/**
 * @package     hubzero-cms
 * @file        plugins/time/summary/summary.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function( $ ) {
	Hubzero.initApi(function() {
		// Fancy select boxes
		if (!!$.prototype.select2) {
			$('.plg_time_summary select').select2({
				placeholder : "search...",
				width       : "100%"
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
			var ticks  = [];
			var name   = function ( plot, canvasContext ) {
				var xaxis  = plot.getXAxes()[0],
					yaxis  = plot.getYAxes()[0],
					offset = plot.getPlotOffset();

				for (var i = 0; i < ticks.length; i++) {
					var text    = ticks[i][1];
					var y       = ticks[i][0];
					var yPos    = yaxis.p2c(y) + offset.top + 4;

					canvasContext.fillText(text, 8, yPos);
				}
			};
			var draw   = function () {
				$('.charts .tasks-bar').css({
					'width'  : $('.charts').width(),
					'height' : (data.length > 1) ? data.length*35 : 50
				});

				if (!plot) {
					var max = 0;

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
							ticks: ticks,
							show: false
						},
						xaxis: {
							show: true,
							tickSize: (max > 15) ? Math.round(max / 15) : max,
							tickFormatter: function formatter(val, axis) {
								if (val === 0) {
									return val + ' hrs(s)';
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
						},
						hooks: {
							draw: [name]
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
					if (!!$.prototype.select2) {
						$("#task_id").select2({
							placeholder : "search...",
							width       : "100%"
						});
					}
				}
			});
		});
	});
});