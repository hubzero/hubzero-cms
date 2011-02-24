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
HUB.Plugins.GroupCalendar = {
	
	positionCalEvent: function ( popup ) {
		var threshold = 10;
		
		var calBox = $('calendar-box');
		var calBoxCoords = calBox.getCoordinates();
		
		var popup = $(popup);
		var popupCoords = popup.getCoordinates();
		
		var tableCell = $('box-1');
		var tableCellCoords = tableCell.getCoordinates();
		
		if(popupCoords.bottom > calBoxCoords.bottom) {
			repoBottom = (calBoxCoords.bottom - popupCoords.bottom - threshold);
			popup.setStyle('margin-top', repoBottom + 'px');
		}
		
		if(popupCoords.right > calBoxCoords.right) {
			repoRight = (calBoxCoords.right - popupCoords.right);
			if(repoRight < -150) {
				repoRight = repoRight - tableCellCoords.width;
			} else {
				repoRight = repoRight - ( 2 * tableCellCoords.width);
			}
			popup.setStyle('margin-left', repoRight + 'px');
		}
	},
	
	resetCalEventPopup: function() {
		$$('.calendar-row a.active').each(function(el) {
			el.removeClass('active');
			el.getNext('ul').setStyle('left','-9999px');
		});
	},
	
	showCalEventPopup: function() {
		$$('.calendar-row .event').each(function(el) {
			var popup = el.getNext('ul');
			if(popup) {
				
				el.addEvent('mouseenter',function(e) {
					new Event(e).stop();
					if(!el.hasClass('active')) {
						el.getNext('ul').setStyle('left','-9999px');
					}	
				});

				el.addEvent('click',function(e) {
					new Event(e).stop();

					if(el.hasClass('active')) {
						el.removeClass('active');
						el.getNext('ul').setStyle('left','-9999px');
					} else {
						HUB.Plugins.GroupCalendar.resetCalEventPopup();
						el.addClass('active');
						el.getNext('ul').setStyle('left','100px');
						HUB.Plugins.GroupCalendar.positionCalEvent(popup);
					}
				});
				
			}
		});
	},
	
	displayDatePicker: function() {
		if($('event_start_date')) {
			var myCal1 = new Calendar(
				{ event_start_date: 'm/d/Y' }, 
				{ classes: ['mini-cal'], direction: 1 }
			);
		
			var myCal2 = new Calendar(
				{ event_end_date: 'm/d/Y' }, 
				{ classes: ['mini-cal'], direction: 1 }
			);
		
			if(myCal1 && myCal2) {
				$$('.cal-date-help').addClass('hide');
			}
		}
	},
	
	initialize: function() {
		HUB.Plugins.GroupCalendar.showCalEventPopup();
		HUB.Plugins.GroupCalendar.displayDatePicker();
	}
}

document.addEvent('click', HUB.Plugins.GroupCalendar.resetCalEventPopup)
window.addEvent('domready', HUB.Plugins.GroupCalendar.initialize);