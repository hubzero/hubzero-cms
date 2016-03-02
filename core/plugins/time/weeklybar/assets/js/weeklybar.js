/**
 * @package     hubzero-cms
 * @file        core/plugins/time/weeklybar/weeklybar.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function( $ ) {
	Hubzero.initApi(function() {
		// Flot
		if ($('.chart').length) {
			var plot    = false;
			var data    = [];
			var mapped  = [];
			var labels  = [];
			var people  = [];
			var peeps   = [];
			var getData = function ( week ) {
				// Build the url
				var url  = "/time/reports?report_type=weeklybar&method=getTimeForWeeklyBar";
					url += "&week=" + week;

				$.ajax({
					url: url,
					dataType: "json",
					cache: false,
					success: function( json ) {
						data = json;
						plot = false;

						if (json.length > 0) {
							draw();
						} else {
							var today     = moment($('.daterange').data('week'));
							var dayOfWeek = parseInt(today.format('d'), 10) === 0 ? 6 : parseInt(today.format('d'), 10) - 1;
							var base      = today.subtract(dayOfWeek, 'days');

							var next = base.add(1, 'week').format('YYYY-MM-DD');
							var prev = base.subtract(2, 'week').format('YYYY-MM-DD');

							$('.forwardweek').data('week', next);
							$('.backweek').data('week', prev);

							var rangeText = base.add(1, 'week').format('M/D') + ' - ' + base.add(6, 'days').format('M/D');
							$('.daterange').html(rangeText);
							$('.chart').html('<p class="warning">No data available for this range</p>');
						}
					}
				});
			};
			var draw = function () {
				if (!plot) {
					var today     = moment($('.daterange').data('week'));
					var dayOfWeek = parseInt(today.format('d'), 10) === 0 ? 6 : parseInt(today.format('d'), 10) - 1;
					var base      = today.subtract(dayOfWeek, 'days');

					// Build list of dates for the days of the current week
					var mon = base.format('YYYY-MM-DD');
					var tue = base.add(1, 'day').format('YYYY-MM-DD');
					var wed = base.add(1, 'day').format('YYYY-MM-DD');
					var thu = base.add(1, 'day').format('YYYY-MM-DD');
					var fri = base.add(1, 'day').format('YYYY-MM-DD');
					var sat = base.add(1, 'day').format('YYYY-MM-DD');
					var sun = base.add(1, 'day').format('YYYY-MM-DD');

					// Also get last monday and next monday for our  prev and next buttons
					var next = base.add(1, 'day').format('YYYY-MM-DD');
					var prev = base.subtract(2, 'week').format('YYYY-MM-DD');

					$('.forwardweek').data('week', next);
					$('.backweek').data('week', prev);

					var rangeText = moment(mon).format('M/D') + ' - ' + moment(sun).format('M/D');
					$('.daterange').html(rangeText);

					var days = {};

					// Build a zero index of days
					days[mon] = 0;
					days[tue] = 1;
					days[wed] = 2;
					days[thu] = 3;
					days[fri] = 4;
					days[sat] = 5;
					days[sun] = 6;

					// Build a zero based index of people
					var k = 0;

					// Seed our mapped data with days of the week
					// This makes sure we don't try to access empty arrays
					mapped = [
						{label: mon, data: []},
						{label: tue, data: []},
						{label: wed, data: []},
						{label: thu, data: []},
						{label: fri, data: []},
						{label: sat, data: []},
						{label: sun, data: []},
					];

					// Seed our data further with data points for each person
					// This ensures we have an equal number of entries per bar,
					// which seems to make flot happier
					$.each(mapped, function ( i, val ) {
						if (mapped[i].data.length < people.length) {
							$.each(people, function ( j, val ) {
								mapped[i].data[j] = [0, j];
							});
						}
					});

					// Build our mapped data
					$.each(data, function ( i, val ) {
						mapped[days[val.day]].data[peeps[val.user_id]] = [val.time, peeps[val.user_id]];
					});

					// Establish our options/format the graph
					var options = {
						series: {
							stack: true,
							bars: {
								show: true,
								barWidth: 0.6,
								align: "center",
								horizontal: true
							}
						},
						yaxis: {
							tickSize: 1,
							tickFormatter: function formatter( val, axis ) {
								if (val % 1 === 0 && undefined !== people[val]) {
									return people[val];
								}

								return '';
							},
							show: true
						},
						xaxis: {
							show: true,
							tickFormatter: function formatter( val, axis ) {
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
						legend: {
							show: true,
							container: $('.legend-content')
						}
					};

					$('.chart').css({
						'height' : (Object.keys(peeps).length > 1) ? Object.keys(peeps).length*50 : 75
					});

					plot = $.plot('.chart', mapped, options);
				} else {
					plot.resize();
					plot.setupGrid();
					plot.setData(mapped);
					plot.draw();
				}
			};

			// Pull our list of peopls
			// This ensures that we have an bar for everyone that needs one,
			// even if they have 0 records for this particular week
			var idx = 0;
			$.each(persons, function ( i, val ) {
				peeps[val.id] = idx;
				people[idx]   = val.name;
				idx++;
			});

			getData($('.daterange').data('week'));

			$(window).resize(draw);

			// And functionality for moving forward and backward in the weeks
			$('.backweek, .forwardweek').click(function ( ) {
				var week = $(this).data('week');
				$('.daterange').data('week', week);
				getData(week);
			});
		}
	});
});