// Monthly Equipment Schedule Calender
// Copyright University of Texas at Austin
// Written by Christopher Stanton
//
// This JavaScript pulls feeds from Google Calendars and displays them in
// in a compact calendar format. The start, finish, and title fields are
// inserted into a hover box that appears when each event is moused over.
//
// Currently configured to support 1-5 calendar feeds.
//


var feed_titles = new Array();

var calendar_url_hash;
var event_text_hash;

var month_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
var day_names = new Array("Sunday", "Monday", "Tuesday","Wednesday","Thursday","Friday","Saturday");
var day_codes = new Array("Su", "M", "Tu", "W", "Th", "F", "Sa");

var colors = new Array("#FFA500", "rgb(102,139,217)", "#800080" , "rgb(140,191,64)", "rgb(200,0,0)" );

var start_on_monday = false;
var weeks = 6;

var d = new Date();

var startTime = -1;
var endTime = -1;
var maxResults = 1000;

var requestObj;

var event_info_div;
var event_info_header;
var event_info_body;

function change_month(year, month){
	d = new Date(year, month);

	init_calendar();
}

function init_calendar(){

	event_info_div = document.getElementById('event-info');
	event_info_header = document.getElementById('event-info-header');
	event_info_body = document.getElementById('event-info-body');

	
	var calendar_table = '\t<table id="calendar-table" class="calendar">';

	startTime = -1;
	endTime = -1;

	calendar_table += get_title_html(d);
	calendar_table += get_days_html();
	calendar_table += get_weeks_html(d);


	calendar_table += "\t</table>\n";

	document.getElementById('calendar').innerHTML = calendar_table;	

	load_events();
}

function load_events(){
	calendar_url_hash = new Array();
	event_text_hash = new Array();

	for (var i = 0; i < calendar_feeds.length; ++i){
                // One url is used to fetch the feed securely, the other is used in the feed to identify it
                // in our code. Unfrotunatley, they can't be the same value
		var url = 'https://www.google.com/calendar/feeds/' + calendar_feeds[i] + '/public/full';
		var url2 = 'http://www.google.com/calendar/feeds/' + calendar_feeds[i] + '/public/full';

		calendar_url_hash[url2] = i;

		url += '?callback=feed_handler&orderby=starttime&singleevents=true';
		url += '&start-min=' + startTime;
		url += '&start-max=' + endTime;
		url += '&max-results=' + maxResults;
		url += '&alt=json-in-script';

		var s = document.createElement("script");
		s.src = url;
		s.charset = "utf-8";
		document.body.appendChild(s);
	}
}


function feed_handler(response){

	var feed = response.feed;

	var feed_id = feed['id'].$t;
	var feed_title = feed['title'].$t;
	var section_index = calendar_url_hash[feed['id'].$t];

	feed_titles[section_index] = feed_title;

	if ( document.getElementById('calendar-key') ){
		document.getElementById('key-color-' + section_index).style.backgroundColor = colors[section_index];
		document.getElementById('key-' + section_index).innerHTML = feed_title;
	}

	if(feed.entry) {
		for (var i = 0; i < feed.entry.length; ++i) {
			var entry = feed.entry[i];
			var entryTitle = entry['title'].$t;
			var entryLink = entry['link'][0].href;
			var start_time = entry['gd$when'][0].startTime;
			var end_time = entry['gd$when'][0].endTime;

			var year = start_time.substr(0,4);
			var month = start_time.substr(5,2);
			var day = start_time.substr(8,2);
			var end_year = end_time.substr(0,4);
			var end_month = end_time.substr(5,2);
			var end_day = end_time.substr(8,2);

			var iso8601 = year + '-' + month + '-' + day;
			var date = new Date(year, month-1, day);
			var epoch = date.getTime();

			var end_iso8601 = end_year + '-' + end_month + '-' + end_day;
			var end_date = new Date(end_year, end_month-1, end_day);
			var end_epoch = end_date.getTime();

			end_day -= 1;
			if ( end_day == 0 ){
				end_month -= 1;
				if (end_month == 0){
					end_year -= 1;
					end_month = 12;
				} else if (end_month < 10){
					end_month = "0" + end_month;
				}
				end_day = days_in_month(end_year, end_month-1); 
			} else if ( end_day < 10 ){
				end_day = "0" + end_day;
			}

			var content = "<u>Start</u>: " + month + "/" + day + "/" + year + "<br/><u>Finish</u>: " + end_month + "/" + end_day + "/" + end_year + "<br/><u>Description</u>: " + entry['title'].$t;

//			while ((epoch <= end_epoch) && document.getElementById('section-' + iso8601 + '-' + section_index)){
			while ( date <= end_date ){

				year = date.getFullYear();
				month = date.getMonth() + 1;
				day = date.getDate();

				var id = 'section-' + iso8601 + '-' + section_index;

				if (entry['title']){
					event_text_hash[id] = content;;
				} else {
					event_text_hash[id] = "none";
				}

				if ( document.getElementById(id) ){
					document.getElementById(id).style.backgroundColor = colors[section_index];
					//document.getElementById(id).title = entryTitle;
				}
        
				epoch += ((60*60*24) * 1000);
				date = new Date(1970,0,0,0,0,0,epoch);

				year = date.getFullYear();
				month = date.getMonth() + 1;
				day = date.getDate();

				if ( day < 10 ){
					day = "0" + day;
				}

				if ( month < 10 ){
					month = "0" + month;
				}

				iso8601 = year + '-' + month + '-' + day; 
			}
		}
	}
}

function get_title_html(d){

	var cur_month = d.getMonth();
	var cur_year = d.getFullYear();
	var prev_month = cur_month - 1;
	var prev_year = cur_year;
	var next_month = cur_month + 1;
	var next_year = cur_year;

	if ( prev_month < 0 ){
		prev_month = 11;
		--prev_year;
	}

	if ( next_month > 11 ){
		next_month = 0;
		++next_year;
	}


	var title = '\t\t<tr id="title" class="title">\n';
	title += '\t\t\t<td><input onclick="change_month(' + prev_year + ',' + prev_month + ')" value="&lt;" type="button"/></td>\n';
	title += '\t\t\t<td id="month-title" colspan="5">' + month_names[cur_month] + ' ' + cur_year + '</td>\n'
	title += '\t\t\t<td><input onclick="change_month(' + next_year + ',' + next_month + ')" value="&gt;" type="button"/></td>\n';
	title += '\t\t</tr>\n';

	return title;
}

function get_days_html(){

	var days = '\t\t<tr class="days">';
	var day = "";

	for (var i = 0; i < day_codes.length; ++i){
		if ( start_on_monday == true){
			if (i == day_codes.length-1){
				day = day_codes[0];
			} else {
				day = day_codes[i+1];
			}
		} else {
			day = day_codes[i];
		}

		days += '\t\t\t<td class="day" >' + day + '</td>\n';
	}
	days += '\t\t</tr>\n';

	return days;
}

function get_weeks_html(d){
	var week_html = "";

	var cur_day = -1;

	var real_date = new Date();
	if ( real_date.getYear() == d.getYear() & real_date.getMonth() == d.getMonth() ){
		cur_day = real_date.getDate();
	} 

	var cur_month = d.getMonth();;
	var cur_year = d.getFullYear();
	var cur_days = days_in_month(cur_year, cur_month);


	var first_date = new Date(cur_year, cur_month, 1);
	var first_day = first_date.getDay();

	var prev_month = cur_month - 1;
	var prev_year = cur_year;
	if ( prev_month < 0 ){
		prev_month = 11;
		--prev_year;
	}
	var prev_days = days_in_month(prev_year, prev_month);

	var next_month = cur_month + 1;
	var next_year = cur_year;
	if ( next_month == 12 ){
		next_month = 0;
		++next_year;
	}
	var next_days = days_in_month(next_year, next_month);

	var index = 1 - first_day;
	if ( start_on_monday ){
		++index;
	}

	for ( var i = 0; i < weeks; ++i ){
		week_html += '\t<tr class="week">\n';
		for (var j = 0; j < 7; ++j ){
			var date_year = -1;
			var date_month = -1;
			var date_day = -1;

			if (index <= 0){
				date_day = prev_days + index;
				date_month = prev_month + 1;
				date_year = prev_year;

				date_class = "other_month";
			} else if (index > 0 && index <= cur_days){
				date_day = index;
				date_month = cur_month + 1;
				date_year = cur_year;

				if ( cur_day >= 0 && index == cur_day ){
					date_class = "today";
				} else {
					date_class = "weekday";
				}
			} else {
				date_day = index - cur_days;
				date_month = next_month + 1;
				date_year = next_year;

				date_class = "other_month";
			}


			var padded_day = "";
			var padded_month = "";

			if ( date_day < 10 ){
				padded_day = "0" + date_day;
			} else {
				padded_day = date_day;
			}

			if ( date_month < 10 ){
				padded_month = "0" + date_month;
			} else {
				padded_month = date_month;
			}

			if ( i == 0 && j == 0 ){
				startTime = date_year + '-' + padded_month + '-' + padded_day + 'T00:00:00';
			} else if ( i == weeks-1 &&  j == 6 ){
				endTime = date_year + '-' + padded_month + '-' + padded_day + 'T23:59:59';
			}

			var div_id = date_year + '-' + padded_month + '-' + padded_day;

			week_html += '\t\t<td class="' + date_class + '">\n';
			week_html += '\t\t\t\t<div class="date-day">' + date_day + '</div>\n';
			week_html += '\t\t\t<div id="' + div_id + '" class="events" onmousemove="mouse_over(this, event)" onmouseout="mouse_out()" onclick="select_event(this, event)">';
			week_html += '\t\t\t\t<div id="section-' + div_id + '-0" class="event-base event-0" ></div>\n';
			week_html += '\t\t\t\t<div id="section-' + div_id + '-1" class="event-base event-1" ></div>\n';
			week_html += '\t\t\t\t<div id="section-' + div_id + '-2" class="event-base event-2" ></div>\n';
			week_html += '\t\t\t\t<div id="section-' + div_id + '-3" class="event-base event-3" ></div>\n';
			week_html += '\t\t\t\t<div id="section-' + div_id + '-4" class="event-base event-4" ></div>\n';
			week_html += '\t\t\t</div>\n';
			week_html += '\t\t</td>\n';

			++index;
		}
		week_html += '\t</tr>\n';
	}



	return week_html;		
}

function days_in_month(year, month) {
	var dd = new Date(year, month+1, 0);
	return dd.getDate();
}


function select_event(obj, evt){
    var text;
    
    var mouse_position = mousePos(evt);
    var obj_position = objPos(obj);
    var obj_index = [];   
 
	if (obj_position[0] >= 0 & obj_position[1] >= 0 & mouse_position[0] >= 0 & mouse_position[1] >= 0){
		var relative_mouse_x = mouse_position[0] - obj_position[0];
		var relative_mouse_y = mouse_position[1] - obj_position[1];

		var field_num = Math.floor( (relative_mouse_y + 1) / 6) - 1;
		if ( (relative_mouse_y + 1) % 6 > 0 ){
			++field_num;
		}

	}

}

function mouse_over(obj, evt){
    var mouse_position = mousePos(evt);
    var obj_position = objPos(obj);
    var obj_index = [];   

	if (obj_position[0] >= 0 & obj_position[1] >= 0 & mouse_position[0] >= 0 & mouse_position[1] >= 0){
		var relative_mouse_x = mouse_position[0] - obj_position[0];
		var relative_mouse_y = mouse_position[1] - obj_position[1];

		var field_num = Math.floor( (relative_mouse_y + 1) / 4) - 1;
		if ( (relative_mouse_y + 1) % 4 > 0 ){
			++field_num;
		}

		
		if ( event_text_hash['section-' + obj.id + '-' + field_num] ){
		
			
			event_info_div.style.left=mouse_position[0] + "px";
			event_info_div.style.top=mouse_position[1] - 180 + "px";
			

			//event_info_div.style.left=100 + 'px';
			//event_info_div.style.top=100 + 'px';

			
			event_info_header.innerHTML = feed_titles[field_num];
			event_info_body.innerHTML = event_text_hash['section-' + obj.id + '-' + field_num] + "<br/>";
			
			//event_info_body.innerHTML += mouse_position[0] + ' ' + mouse_position[1];
			
		
			obj.style.cursor = 'pointer';
			event_info_div.style.visibility='visible';
		} else {
			obj.style.cursor = 'auto';
			event_info_div.style.visibility='hidden';
		}
	}
}

function mouse_out() {
	event_info_div.style.visibility='hidden';
}

function objPos(obj) {
	var curleft = curtop = -1;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

function mousePos(evt) {
	var mouse_x = mouse_y = -1;
	
	if (evt.pageX & evt.pageY) {
		mouse_x = evt.pageX;
		mouse_y = evt.pageY;
	} else if (evt.clientX & evt.clientY) {
   		mouse_x = evt.clientX + (document.documentElement.scrollLeft ?
			document.documentElement.scrollLeft :
			document.body.scrollLeft);
   		mouse_y = evt.clientY + (document.documentElement.scrollTop ?
			document.documentElement.scrollTop :
			document.body.scrollTop);
	}

	return [mouse_x, mouse_y];
}


