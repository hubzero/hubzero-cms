/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/overview.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function ( jq ) {
	Hubzero.initApi(function() {
		var $        = jq,
			calendar = $('.calendar'),
			explain  = $('.details-explanation'),
			data     = $('.details-data'),
			fancy    = function ( selector ) {
				$(selector).select2({
					placeholder : "search...",
					width       : "100%"
				});
			},
			showDetails = function ( ) {
				if ($('.details-id').val()) {
					$('.details-delete').show();
				} else {
					$('.details-delete').hide();
				}
				explain.fadeOut();
				data.fadeIn();
			},
			hideDetails = function ( show ) {
				data.fadeOut();
				explain.fadeIn();
			},
			setData = function ( event ) {
				if (event.id) {
					$('.details-id').val(event.id);
				} else {
					$('.details-id').val('');
				}
				if (event.start) {
					$('.details-start').val(event.start);
				}
				if (event.end) {
					$('.details-end').val(event.end);
				}
				if (event.task_id) {
					$('#task_id').val(event.task_id);
					fancy('#task_id');
				}
				if (event.hub_id) {
					$('#hub_id').val(event.hub_id);
					fancy('#hub_id');
				}
				if (event.description) {
					$('#description').val(event.description);
				} else {
					$('#description').val('');
				}
			},
			graphs = function ( today ) {
				var points = [[0,0], [1,0], [2,0], [3,0], [4,0], [5,0], [6,0]],
					total  = 0;

				$.ajax({
					url: "/api/time/week",
					dataType: "json",
					data: "today=" + today,
					cache: false,
					success: function ( json ) {
						$.each(json, function ( i, val ) {
							points[i] = [i, val.reduce(function(a, b) { return parseFloat(a) + parseFloat(b); }, 0)];
							total += Math.round(val.reduce(function(a, b) { return parseFloat(a) + parseFloat(b); }, 0));
						});

						$('.hours').html(total + 'hr' + ((total != 1) ? 's' : ''));

						var bg1  = '';
						var bg2  = '';
						var perc = 0;

						if (total > 0) {
							perc = total / 40 * 360;
						}

						if (total > 0 && total < 20) {
							perc = -90 + perc;
							bg1 = 'linear-gradient(90deg, transparent 50%, red 50%)';
							bg2 = 'linear-gradient(' + perc + 'deg, white 50%, transparent 50%)';
						} else if (total >= 20 && total < 40) {
							perc = 90 + perc;
							bg1 = 'linear-gradient(' + perc + 'deg, red 50%, transparent 50%), linear-gradient(90deg, transparent 50%, red 50%)';
						} else if (total >= 40) {
							bg1 = 'linear-gradient(90deg, red 50%, transparent 50%), linear-gradient(90deg, transparent 50%, red 50%)';
						}

						$('.hourly .pie-half1').css(
							'background-image', bg1
						);
						$('.hourly .pie-half2').css(
							'background-image', bg2
						);

						var options = {
							legend : {
								show : false
							},
							yaxis : {
								tickFormatter : function ( val, axis ) {
									if (val % 2 === 0) {
										return parseInt(val, 10);
									} else {
										return '';
									}
								}
							},
							xaxis : {
								ticks : [
									[0, 'MON'],
									[1, 'TUE'],
									[2, 'WED'],
									[3, 'THU'],
									[4, 'FRI'],
									[5, 'SAT'],
									[6, 'SUN']
								]
							},
							series : {
								lines  : {
									show : true,
									fill : 0.2
								},
								points : {
									show   : false,
									radius : 3,
									symbol : "circle"
								},
								shadowSize : 0
							},
							grid : {
								show : true,
								borderColor : "#FFFFFF",
								minBorderMargin : 30,
								color : '#AAAAAA'
							},
							colors : ['red']
						};
						$.plot(".week-overview", [{data : points}], options);
					}
				});
			};

		fancy('#task_id');
		fancy('#hub_id');

		calendar.fullCalendar({
			header         : {
				left       : 'title',
				center     : '',
				right      : 'today,prev,next'
			},
			titleFormat    : 'ddd, MMM D, YYYY',
			timezone       : 'local',
			defaultView    : 'agendaDay',
			contentHeight  : 370,
			scrollTime     : '8:00',
			snapDuration   : '00:15:00',
			allDaySlot     : false,
			selectable     : true,
			editable       : true,
			unselectCancel : '.details-inner',
			selectHelper   : true,
			select : function ( start, end, jsEvent, view ) {
				setData({
					start : start,
					end   : end
				});
				showDetails();
			},
			unselect : function ( view, jsEvent ) {
				hideDetails();
			},
			eventClick : function ( event, jsEvent, view ) {
				setData(event);
				showDetails();
			},
			eventDrop : function ( event, delta, revertFunc ) {
				setData(event);
				data.submit();
			},
			eventResize : function ( event, delta, revertFunc ) {
				setData(event);
				data.submit();
			},
			eventAfterAllRender : function ( view ) {
				// Set height of input box (minus margin and borders)
				$('.details-inner').height(calendar.height() - 32);

				// Be sure not to modify the actual start date moment
				var today = view.start.clone();
				graphs(today.format());

				var dayOfWeek = parseInt(today.format('d'), 10) === 0 ? 6 : parseInt(today.format('d'), 10) - 1;
				var monday    = today.subtract(dayOfWeek, 'days').format('M/D');
				var sunday    = today.add(dayOfWeek, 'days').add((6-dayOfWeek), 'day').format('M/D');

				$('.date-range').html('(' + monday + ' - ' + sunday + ')');
			},
			events : '/api/time/today'
		});

		$('.details-cancel').click(function ( e ) {
			e.preventDefault();
			hideDetails();
			calendar.fullCalendar( 'unselect' );
			$('.error-message').fadeOut();
		});

		data.submit(function ( e ) {
			e.preventDefault();
			var form = $(this);

			if (!$('#hub_id').val()) {
				$('.hub-error').fadeIn();
				$('.task-error').fadeOut();
			} else if (!$('#task_id').val()) {
				$('.task-error').fadeIn();
				$('.hub-error').fadeOut();
			} else {
				$('.error-message').fadeOut();
				$.ajax({
					url      : form.attr('action'),
					data     : form.serializeArray(),
					method   : 'POST',
					dataType : "json",
					cache    : false,
					success  : function ( json ) {
						calendar.fullCalendar( 'unselect' );
						calendar.fullCalendar( 'refetchEvents' );
						hideDetails();
					}
				});
			}
		});

		$('.details').on('focus', '.select2-container', function ( e ) {
			$(this).prev().select2('open');
		});

		$('.details-delete').on('click', function ( e ) {
			e.preventDefault();

			$('.error-message').fadeOut();
			$.ajax({
				url      : '/api/time/records/' + $('.details-id').val(),
				method   : 'DELETE',
				dataType : "json",
				cache    : false,
				success  : function ( json ) {
					calendar.fullCalendar( 'unselect' );
					calendar.fullCalendar( 'refetchEvents' );
					hideDetails();
				}
			});
		});

		// Add change event to hub select box (filter tasks list by selected hub)
		$('#hub_id').change(function ( event ) {
			// First, grab the currently select task
			var task = $('#task_id');

			// Create a ajax call to get the tasks
			$.ajax({
				url: "/api/time/indexTasks",
				data: "hid="+$(this).val()+"&pactive=1",
				dataType: "json",
				cache: false,
				statusCode: {
					404: function() {
						location.reload();
					}
				},
				success: function ( json ) {
					// If success, update the list of tasks based on the chosen hub
					var options = '';

					if(json.tasks.length > 0) {
						for (var i = 0; i < json.tasks.length; i++) {
							options += '<option value="';
							options += json.tasks[i].id;
							options += '"';
							if (json.tasks[i].id == task.val()) {
								options += ' selected="selected"';
							}
							options += '>';
							options += json.tasks[i].name;
							options += '</option>';
						}
					} else {
						options = '<option value="">No tasks for this hub</option>';
					}
					var focused = false;

					if (task.next().hasClass('select2-container--open')) {
						focused = true;
					}

					task.html(options);
					fancy('#task_id');

					if (focused) {
						task.select2('open');
					}
				}
			});
		});
	});
});