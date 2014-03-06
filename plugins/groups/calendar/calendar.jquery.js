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
		HUB.Plugins.GroupCalendar.calendar();

		//fancy calendar picker
		//HUB.Plugins.GroupCalendar.calendarPicker();
		
		//double click to create event
		//HUB.Plugins.GroupCalendar.quickEventCreate();
		
		//edit event js
		HUB.Plugins.GroupCalendar.editEvent();
		
		//handle subscribe url changing
		HUB.Plugins.GroupCalendar.subscribeUrl();
	},

	calendar: function()
	{
		if (!$('#calendar').length)
		{
			return;
		}

		$('#calendar').fullCalendar({
			header: {
				left: 'title prev,next',
				center: '',
				right: 'month,agendaWeek,agendaDay today'
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
			dayClick: function(date, allDay, jsEvent, view) {}
		});

		// async load sources
		$.getJSON('/groups/smoakey/calendar/eventsources', function(sources) {
			jQuery.each(sources, function(index, source) {
				$('#calendar').fullCalendar('addEventSource', source);
			});
		});

		//async refresh calendars
		
	},
	
	calendarPicker: function()
	{
		var $ = this.jQuery;
		
		//refresh cal on change
		$(".group_calendar").on('change', '#month-picker, #year-picker', function(event) {
			if (!$('html').hasClass('ie8'))
			{
				HUB.Plugins.GroupCalendar.refresh( HUB.Plugins.GroupCalendar.calendarPicker );
			}
		});
		
		//fancy select box for cal picker
		if ($('#calendar-picker').length)
		{
			$('#calendar-picker').HUBfancyselect({
				onSelected: function() {
					//refresh calendar
					if (!$('html').hasClass('ie8'))
					{
						HUB.Plugins.GroupCalendar.refresh( HUB.Plugins.GroupCalendar.calendarPicker );
					}
				}
			});
		}
	},
	
	quickEventCreate: function()
	{
		var $ = this.jQuery;
		
		//quick create event - double click
		$('#calendar.quick-create').disableSelection();
		$('.group_calendar')
			.on('click', '#calendar.quick-create tbody .calendar-row td:not(.no-date)', function(event) {
				var clickedElement = (event.srcElement) ? event.srcElement : event.target,
					clickedElementType = $(clickedElement).prop('tagName').toLowerCase();
				
				if (clickedElementType == 'td')
				{
					event.preventDefault();
					$('.calendar-row td').removeClass('active');
					$(clickedElement).addClass('active');
				}
			})
			.on('dblclick', '#calendar.quick-create tbody .calendar-row td:not(.no-date)', function(event) {
				event.preventDefault();
				
				//get the current location
				var redirectPath = '',
					redirectHref = '',
					protocol = window.location.protocol,
					host = window.location.host,
					path = window.location.pathname
					start = $(this).attr('data-date');
				
				//get needed part of path
				redirectPath = path.replace(/calendar\/[\d]{4}\/[\d]{2}/gi, 'calendar')
				
				//create href for adding event
				redirectLocation = protocol + '//' + host + redirectPath + '/add?start=' + start;
				
				//redirect user
				window.location.href = redirectLocation;
			});
	},
	
	subscribeUrl: function()
	{
		var $ = this.jQuery;
		
		//select subscribe url when clicking in field
		$('.group_calendar').on('click', '#subscribe-link input[type=text]', function(event) {
			$(this).select();
		});
		
		//adjust url and subscribe button url when editing calendar choices
		$('.group_calendar').on('click', '#subscribe input[type=checkbox]', function(event) {
			var calendars = [],
				calendarParamString = '';
			$('#subscribe :checkbox:checked').map(function() { 
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
	
	refresh: function( callback )
	{
		var $ = this.jQuery;
		
		//show activity indicator
		$("#calendar-box").css('position', 'relative').append('<div id="calendar-update" />');
		
		//get values of month and year pickers
		var monthVal = $('#month-picker').val(),
			yearVal = $('#year-picker').val(),
			calendarVal = $('#calendar-picker').val();
		
		var protocol = window.location.protocol,
			host = window.location.host,
			path = window.location.pathname;
		
		//build new url
		newUrl = protocol + '//' + host + path.replace(/calendar\/[\d]{4}\/[\d]{2}/gi, 'calendar') + '/' + yearVal + '/' + monthVal;
		if(calendarVal != '' && calendarVal != 0)
		{
			newUrl += '?calendar=' + calendarVal;
		}
		
		//write date change to history
		if (window.history && window.history.pushState)
		{
			window.history.pushState(null,null, newUrl);
		}
		
		//load new cal
		$(".group_calendar").load( newUrl + ' .group_calendar > *', function(){
			$("#calendar-box").css('position', 'static').find("#calendar-update").remove();
			
			if (typeof callback == 'function')
			{
				callback();
			}
			return false;
		});
	},
	
	editEvent: function()
	{
		var $ = this.jQuery;
		
		//show date picker for end and start
		if ($('#event_start_date, #event_end_date').length)
		{
			$('#event_start_date, #event_end_date').attr('autocomplete', 'OFF');
			$('#event_start_date, #event_end_date').datetimepicker({
				controlType: 'slider',
				dateFormat: 'mm/dd/yy',
				timeFormat: '@ h:mm tt'
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
	}
};

//----------------------------------------------------------

jQuery(document).ready(function(){
	HUB.Plugins.GroupCalendar.initialize();
});