var selectedIndex = -1;
var selectedDate = -1;
var selectedMonth = -1;
var selectedYear = -1;
var todayIndex = -1;

function renderFunctions() {
	clearCellFormatting();
	drawTodayCell();
	drawSelectedCell();
	drawEventCell();
}

function drawEventCell() {
	lstCommands = new Array();
	if (txtCommands != "") {
		lstCommands = txtCommands.split("|");
		for (k=0; k<lstCommands.length; k++) {
			eval (lstCommands[k]);
		}
	}
}

function drawSelectedCell() {
	if(selectedIndex > -1) {
		document.getElementById("day_" + selectedIndex).className = "selected";
	}
}

function drawTodayCell() {
	if(todayIndex > -1) {
		document.getElementById("day_" + todayIndex).className = "today";
	}
}

function drawActiveCell(d) {
	document.getElementById("link_" + d).style.fontWeight = 'bold';	
}

function setSelectedDay(d) {
	selectedIndex = d;
	dateSelected(document.getElementById("link_" + d).innerHTML ,(today.getMonth() + 1),today.getFullYear(), document.getElementById("link_" + d).title);
	renderCalendar();
}

function clearCellFormatting() {
	var x = 0;
	for(x=0; x < 42; x++) {
		document.getElementById("day_" + x).className = "day";	
	}
}

function clearLinkFormatting() {
	var x = 0;
	for(x=0; x < 42; x++) {
	    document.getElementById("link_" + x).style.fontWeight = 'normal';
	    document.getElementById("link_" + x).title = '';
	}
}