function D_Calendar_DateActivate(day,month,year) {
	var x = 0;
	for (x=0;x<42;x++) {
		var thisLink = document.getElementById("link_" + x).innerHTML;
		if((thisLink == day) && (month == (today.getMonth() + 1)) && (year == today.getFullYear())) {
			drawActiveCell(x);
		}
	}
}

function D_Calendar_DateMark(day,month,year,titleText) {
	var x = 0;
	for (x=0;x<42;x++) {
		var thisLink = document.getElementById("link_" + x).innerHTML;
		if((thisLink == day) && (month == (today.getMonth() + 1)) && (year == today.getFullYear())) {
			drawActiveCell(x);
			document.getElementById("link_" + x).title = titleText
		}
	}
}

function D_Calendar_DateSelect(day,month,year) {
	var x = 0;
	for (x=0;x<42;x++) {
		var thisLink = document.getElementById("link_" + x).innerHTML;
		if((thisLink == day) && (month == (today.getMonth() + 1)) && (year == today.getFullYear())) {
			setSelectedDay(x);
		}
	}
}

function dateSelected(d,m,y,t) {
	D_Calendar_DateSelected(d,m,y,t);
}

function monthChanged() {
	D_Calendar_MonthChanged((today.getMonth() + 1),today.getFullYear());
}