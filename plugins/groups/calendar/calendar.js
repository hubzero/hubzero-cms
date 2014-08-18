/**
 * @package     hubzero-cms
 * @file        plugins/groups/calendar/calendar.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
//  Group Calendar Code
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.GroupCalendar = {
	jQuery: jq,
	
	initialize: function() {
		// create calendar
		HUB.Plugins.GroupCalendar.calendar();

		// fancy calendar picker
		HUB.Plugins.GroupCalendar.calendarPicker();
		
		// edit event js
		HUB.Plugins.GroupCalendar.editEvent();
		
		// handle subscribe url changing
		HUB.Plugins.GroupCalendar.subscribeUrl();

		HUB.Plugins.GroupCalendar.calendarDelete();
	},

	calendar: function()
	{
		var $ = this.jQuery;

		var $calendar = $('#calendar'),
			$base     = $calendar.attr('data-base'),
			$month    = $calendar.attr('data-month') - 1,
			$year     = $calendar.attr('data-year'),
			_click    = null;

		// make sure we have the calendar
		if (!$calendar.length)
		{
			return;
		}

		// hide event list
		$('.event-list').hide();

		// setup full calendar
		$calendar.fullCalendar({
			month: $month,
			year: $year,
			selectable: true,
			selectHelper: true,
			unselectAuto: true,
			header: {
				left: 'title prev,next',
				center: '',
				right: 'today'
			},
			weekMode: 'variable',
			eventSources: [],
			loading: function( isLoading, view ) {
				if (isLoading)
				{
					$('.fc-header-center').html('<div class="fc-loading"></div>');
				}
				else
				{
					$('.fc-header-center').html('');
				}
			},
			viewRender: function(view, element) {
				var dateString = $calendar.fullCalendar('getDate'),
					date = $.fullCalendar.formatDate( dateString, 'yyyy/MM' );

				//write date change to history
				if (window.history && window.history.pushState)
				{
					window.history.pushState(null,null, $base + '/' + date);
				}
			},
			eventAfterAllRender: function(view) {
				// filter events
				HUB.Plugins.GroupCalendar.filterEvents();
			},
			dayClick: function(date, allDay, jsEvent, view) {
				var start = $.fullCalendar.formatDate( date, 'yyyy-MM-dd HH:mm:00' );
				if (view.name == 'month')
				{
					start = $.fullCalendar.formatDate( date, 'yyyy-MM-dd 12:00:00' );
				}
				
				if (_click)
				{
					var diff = jsEvent.timeStamp - _click;
					if (diff < 300)
					{
						_click = null;
						window.location.href = $base + '/add?start=' + start;
					}
				}
				_click = jsEvent.timeStamp;
			},
			select: function(startDate, endDate, allDay, jsEvent, view)
			{
				var start, end,
					viewName   = view.name,
					startYear  = startDate.getUTCFullYear(),
					startMonth = HUB.Plugins.GroupCalendar.pad(startDate.getUTCMonth()+1,2),
					startDay   = HUB.Plugins.GroupCalendar.pad(startDate.getUTCDate(),2),
					startHours = HUB.Plugins.GroupCalendar.pad(startDate.getUTCHours(),2),
					startMins  = HUB.Plugins.GroupCalendar.pad(startDate.getUTCMinutes(),2),
					endYear    = endDate.getUTCFullYear(),
					endMonth   = HUB.Plugins.GroupCalendar.pad(endDate.getUTCMonth()+1,2),
					endDay     = HUB.Plugins.GroupCalendar.pad(endDate.getUTCDate(),2),
					endHours   = HUB.Plugins.GroupCalendar.pad(endDate.getUTCHours(),2),
					endMins    = HUB.Plugins.GroupCalendar.pad(endDate.getUTCMinutes(),2);

				// build event start and end
				start = startYear + '-' + startMonth + '-' + startDay + ' ' + startHours + ':' + startMins + ':00';
				end   = endYear + '-' + endMonth + '-' + endDay + ' ' + endHours + ':' + endMins + ':00';

				// month select handled by dayclick event
				if (viewName == 'month' && start == end)
				{
					return;
				}
				
				// go to edit
				window.location.href = $base + '/add?start=' + start + '&end=' + end;
			}
		});

		// async load sources
		$.getJSON($base + '/eventsources', function(sources) {
			jQuery.each(sources, function(index, source) {
				$calendar.fullCalendar('addEventSource', source);
			});

			// refresh calendars after sources are loaded
			HUB.Plugins.GroupCalendar.refreshCalendars();
		});

		// add calendar picker to header
		$('.fc-header-right').prepend($('#calendar-picker'));
	},

	pad: function(value, length)
	{
    	return (value.toString().length < length) ? HUB.Plugins.GroupCalendar.pad("0"+value, length):value;
	},

	refreshCalendars: function()
	{
		var $ = this.jQuery;

		var $calendar = $('#calendar'),
			$base     = $calendar.attr('data-base');

		//async refresh calendars
		$.post($base + '/refreshcalendars', function(data) {
			if (data.refreshed > 0)
			{
				$calendar.fullCalendar('refetchEvents');
			}
		}, 'json');
	},

	calendarDelete: function()
	{
		var $ = this.jQuery;

		$('.group-calendars .delete').on('click', function(event) {
			event.preventDefault();
			$(this).parents('tr').next('.delete-confirm').toggle();
		});

		$('.group-calendars .delete-cancel').on('click', function(event) {
			event.preventDefault();
			$(this).parents('tr').toggle();
		});
	},
	
	calendarPicker: function()
	{
		var $ = this.jQuery;
	
		//fancy select box for cal picker
		if ($('#calendar-picker').length)
		{
			$('#calendar-picker').HUBfancyselect({
				onSelected: function() {
					HUB.Plugins.GroupCalendar.filterEvents();
				}
			});
		}
	},

	filterEvents: function()
	{
		var $ = this.jQuery;
		
		var value = $('#calendar-picker').val();
		
		if (value == 0)
		{
			$('.fc-event').show();
		}
		else
		{
			$('.fc-event').hide();
			$('.calendar-' + value).show();
		}
	},
	
	subscribeUrl: function()
	{
		var $ = this.jQuery;
		
		//select subscribe url when clicking in field
		$('.group_calendar').on('click', '#subscribe-link input[type=text]', function(event) {
			$(this).select();
		});
		
		//adjust url and subscribe button url when editing calendar choices
		$('.group_calendar').on('click', '.subscribe-content input[type=checkbox]', function(event) {
			var calendars = [],
				calendarParamString = '';
			$('.subscribe-content :checkbox:checked').map(function() { 
				calendars.push( $(this).val() );
			});
			
			//build calendar param string from selected calendars
			calendarParamString = calendars.join(',');
			
			//we can only subscribe to a calendar if we have one selected
			if (calendarParamString == '')
			{
				$('#subscribe-link').slideUp();
			}
			else
			{
				$('#subscribe-link').slideDown();
			}
			
			//get rid of protocol off current url
			var newSubscribeUrl = window.location.href.replace(window.location.protocol + '//', '');
			
			//remove anything after /calendar in current url
			newSubscribeUrl = newSubscribeUrl.replace(/calendar.+/, 'calendar');
			
			//append subscribe and calendar param string
			newSubscribeUrl = newSubscribeUrl + '/subscribe/' + calendarParamString + '.ics';
			
			//set the subscribe value and webcal button
			$('#subscribe-link input[type=text]').val('https://' + newSubscribeUrl);
			$('#subscribe-link a.https').attr('href', 'https://' + newSubscribeUrl)
			$('#subscribe-link a.webcal').attr('href', 'webcal://' + newSubscribeUrl)
		});
	},
	
	editEvent: function()
	{
		var $ = this.jQuery;

		// handle repeating events details
		HUB.Plugins.GroupCalendar.repeatingEvents();
		
		//show date picker for end and start
		if ($('#event_start_date, #event_end_date').length)
		{
			$('#event_start_date, #event_end_date').attr('autocomplete', 'OFF');
			$('#event_start_date, #event_end_date').datetimepicker({
				controlType: 'slider',
				dateFormat: 'mm/dd/yy',
				timeFormat: '@ h:mm tt',
				onClose: function( selectedDate ) {
					if ($(this).attr('id') == 'event_start_date')
					{
						$('#event_end_date').datepicker( "option", "minDate", selectedDate );
					}
    			}
			});
		}
		
		//show date picker for register by
		if ($('#event_registerby').length)
		{
			$('#event_registerby').attr('autocomplete', 'OFF');
			$('#event_registerby').datetimepicker({
				controlType: 'slider',
				dateFormat: 'mm/dd/yy',
				timeFormat: '@ h:mm tt'
			});
		}
		
		//make calendar picker fancy select
		if ($('#event-calendar-picker').length)
		{
			$('#event-calendar-picker').HUBfancyselect();
		}
		
		//show/hide registration fields
		$('#include-registration-toggle').on('change', function(event) {
			$('#registration-fields').slideToggle();
		});
		
		//remove tooltips from registration fields labels
		$('.group_calendar #hubForm table.paramlist td label').removeClass('hasTip');
		
		//file uploader
		if ($('#import').length)
		{
			$('#import').fileupload({
				autoUpload: true,
				dataType: 'json',
				dropzone: $('.upload'),
				forceIframeTransport: false,
				add: function(e, data) {
					var file = data.files[0];
					
					//make sure we only allow ics files
					if (file.type != 'text/calendar')
					{
						hub.alert('Pleas upload a valid iCalendar File (.ics)');
					}
					else
					{
						data.submit();
					}
				},
				done: function(e, data) {
					var eventDetails = data.result.event;

					//set inputs
					$('#event_title').val( eventDetails.title );
					$('#event_content').val( eventDetails.content );
					$('#event_location').val( eventDetails.location );
					$('#event_website').val( eventDetails.website );
					$('#event_start_date').val( eventDetails.start );
					$('#event_end_date').val( eventDetails.end );
				},
				fail: function(e, data) {
					console.log('fail');
					console.log(data);
				}
			});
			
			//drag & drop
			$(document)
				.on('dragenter', '.upload', function(event) {
					$('.upload').addClass('over');
				})
				.on('dragleave drop', '.upload', function(event) {
					$('.upload').removeClass('over');
				});
		}
	},

	repeatingEvents: function()
	{
		var $ = this.jQuery;

		// make sure we have repeating events
		if (!$('fieldset .reccurance').length)
		{
			return;
		}

		// show 
		$('.reccurance .ends').hide();

		// repeating events end
		HUB.Plugins.GroupCalendar._repeatingEventsInterval();

		// repeating events end
		HUB.Plugins.GroupCalendar._repeatingEventsEnd();
	},

	_repeatingEventsInterval: function()
	{
		var $ = this.jQuery;

		// hide all options to start
		$(".reccurance-options").hide();

		// add event handler for changing reccurance
		$('select[name="reccurance[freq]"]').on('change', function(event) {
			if ($(this).val() != '')
			{
				$('.reccurance .ends').show();
			}
			else
			{
				$('.reccurance .ends').hide();
			}
			$(".reccurance-options").hide();
			$('.options-' + $(this).val().toLowerCase()).show();
		});

		// fire change event if we have a freq set (editing)
		if ($('select[name="reccurance[freq]"]').val() != '')
		{
			$('select[name="reccurance[freq]"]').change();
		}
	},

	_repeatingEventsEnd: function()
	{
		var $ = this.jQuery;

		// repeating end on date formatter
		$('.reccurance .on-input').attr('autocomplete', 'OFF');
		$('.reccurance .on-input').datepicker({
			controlType: 'slider',
			dateFormat: 'mm/dd/yy'
		});

		// disable all inputs
		HUB.Plugins.GroupCalendar._repeatingEventsDisableEnd();

		// end inputs
		$('input[name="reccurance[ends][when]"]').on('click', function(event) {
			// disable all other inputs
			HUB.Plugins.GroupCalendar._repeatingEventsDisableEnd();

			// focus on next input
			$(this).parent('label')
				.find('input[type=text]')
				.focus();
		});
	},

	_repeatingEventsDisableEnd: function()
	{
		var $ = this.jQuery;

		$('input[name="reccurance[ends][when]"]').each(function(index) {
			if (!$(this).is(':checked'))
			{
				$(this).parent('label')
					.find('input[type=text]')
					.attr('disabled', 'disabled');
			}
			else
			{
				$(this).parent('label')
					.find('input[type=text]')
					.removeAttr('disabled');
			}
		});
	}
};

//----------------------------------------------------------

jQuery(document).ready(function(){
	HUB.Plugins.GroupCalendar.initialize();
});