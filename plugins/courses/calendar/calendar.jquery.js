/**
 * @package     hubzero-cms
 * @file        plugins/courses/calendar/calendar.js
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
//  Course Calendar Code
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.CourseCalendar = {
	jQuery: jq,
	
	initialize: function() {
		HUB.Plugins.CourseCalendar.datePicker();
		HUB.Plugins.CourseCalendar.calendarPopup();
	},
	
	datePicker: function()
	{
		var $ = this.jQuery;
		
		//hide helpers
		$('.cal-date-help').hide();
		
		//show date picker
		$('#event_start_date, #event_end_date').datepicker({
			showAnim: 'slideDown',
			showOn: "button",
			buttonImage: "/plugins/courses/calendar/images/calendar_icon.png",
			buttonImageOnly: true
		});
	},
	
	calendarPopup: function()
	{
		var $ = this.jQuery;
		
		$(".calendar-row .event")
			.on("mouseover", function(event){
				event.preventDefault();
				if(!$(this).hasClass("active"))
				{
					$(this).next("ul").css("left", "-9999px");
				}
			})
			.on("click", function(event){
				event.preventDefault();
				if($(this).hasClass("active"))
				{
					$(this).removeClass("active");
					$(this).next("ul").css("left", "-9999px");
				}
				else
				{
					HUB.Plugins.CourseCalendar.hideCalendarPopup();
					$(this).addClass("active");
					var popup = $(this).next("ul");
					popup.css("left", "100px");
					HUB.Plugins.CourseCalendar.repositionCalendarPopup(popup);
				}
			});
	},
	
	repositionCalendarPopup: function(popup)
	{
		var $ = this.jQuery;
		
		var threshold = 10,
			marginTop = 0,
			marginLeft = 0,
			popup = $(popup),
			popupCoordinates = {
				top: popup.offset().top,
				right: (popup.offset().left + popup.outerWidth(true)),
				bottom: (popup.offset().top + popup.outerHeight(true)),
				left: popup.offset().left
				},
			calendarBox = $("#calendar-box"),
			calendarBoxCoordinates = {
				top: calendarBox.offset().top,
				right: (calendarBox.offset().left + calendarBox.outerWidth(true)),
				bottom: (calendarBox.offset().top + calendarBox.outerHeight(true)),
				left: calendarBox.offset().left
				};
		
		if(popupCoordinates.bottom > calendarBoxCoordinates.bottom)
		{
			marginTop = calendarBoxCoordinates.bottom - popupCoordinates.bottom - threshold;
			popup.css("margin-top", marginTop);
		}
		
		if(popupCoordinates.right > calendarBoxCoordinates.right)
		{
			marginLeft = calendarBoxCoordinates.right - popupCoordinates.right - threshold;
			popup.css("margin-left", marginLeft);
		}
	},
	
	hideCalendarPopup: function()
	{
		var $ = this.jQuery;
		
		$(".calendar-row a.active").each(function(i, el) {
			$(el).removeClass("active");
			$(el).next("ul").css("left", "-9999px");
		});
	}
};

//----------------------------------------------------------

jQuery(document).on("click", function(event) {
	var cls = event.srcElement.className;
	if(!cls.match(/event/gi))
	{
		HUB.Plugins.CourseCalendar.hideCalendarPopup();
	}
});

jQuery(document).ready(function(){
	HUB.Plugins.CourseCalendar.initialize();
});