/**
 * @package     hubzero-cms
 * @file        plugins/time/reports/reports.js
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
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Time Reports
//----------------------------------------------------------
HUB.Plugins.TimeReports = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Set a few variables
		var hub       = $("#hub_id");
		var task      = $("#task");
		var startdate = $("#startdate");
		var enddate   = $("#enddate");

		// Input some defaults for the date range
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth();
		var day   = today.getDate();

		// Set values
		startdate.val(year+'-'+month+'-'+day);
		enddate.val(year+'-'+(month+1)+'-'+day);

		// Add change event to task select box
		task.change(function(event) {
			// Call getRecords function
			HUB.Plugins.TimeReports.getRecords();
		});

		// Add change event to start date
		startdate.change(function(event) {
			// Call getRecords function
			HUB.Plugins.TimeReports.getRecords();
		});

		// Add change event to end date
		enddate.change(function(event) {
			// Call getRecords function
			HUB.Plugins.TimeReports.getRecords();
		});

		// Add change event to hub select box (filter tasks list by selected hub)
		hub.change(function(event) {

			// Set hub = -1 if hub value is empty (so the ajax call returns nothing, instead of everything)
			// @FIXME: this is sortof 'hacky'
			if(hub.val() == '') { hid = -1 } else { hid = hub.val() }

			// Create a ajax call to get the tasks
			$.ajax({
				url: "index.php?option=com_time&task=ajax&active=ajax&action=tasks.json",
				data: "hid="+hid+"&pactive=0",
				dataType: "json",
				cache: false,
				success: function(json){
					// If success, update the list of tasks based on the chosen hub
					var options = '';
					if(json.length > 0) {
						for (var i = 0; i < json.length; i++) {
							options += '<option value="' + json[i].objValue + '">' + json[i].objText + '</option>';
						}
					} else {
						options = '<option value="none">No tasks for this hub</option>';
					}
					$("#task").html(options);

					// Call getRecords function
					HUB.Plugins.TimeReports.getRecords();
				}
			});
		});
	}, // end initialize

	//-------------------------------------------------------------

	getRecords: function() {
		var $ = this.jQuery;

		// Set a few variables
		var hub       = $("#hub_id");
		var task      = $("#task");
		var startdate = $("#startdate");
		var enddate   = $("#enddate");

		if(hub.val() != "" && task.val() != "" && startdate.val() != "" && enddate.val() != "") {

			// Set task = -1 if task value is empty (so the ajax call returns nothing, instead of everything)
			// @FIXME: this is sortof 'hacky'
			if(task.val() == 'none') { pid = -1 } else { pid = task.val() }

			// Create a ajax call to get the records
			$.ajax({
				url: "index.php?option=com_time&task=ajax&active=ajax&action=report_records.json",
				data: "pid="+pid+"&startdate="+startdate.val()+"&enddate="+enddate.val(),
				dataType: "json",
				cache: false,
				success: function(json){
					// If success, update the list of records
					// First, create a few placeholder variables
					var tablerows  = '';
					var total_time = 0;
					// First make sure we got something back
					if(json.length > 0) {
						// Then loop through the array of users
						for (var i = 0; i < json.length; i++) {
							// Create placeholder for sum of users' time
							var sum_user_time = 0;
							// Then make sure we got data with that user (we shouldn't even have the user if they don't have data!)
							if(json[i][1].length > 0) {
								// Now loop through the records for that user
								for (var j = 0; j < json[i][1].length; j++) {
									// And add a row for the username (only once)
									if(j == 0) {
										tablerows += '<tr class="report_user_subsection"><td colspan="4">'
												  + '<div class="user_header">'
												  + json[i][1][j].user
												  + '</div>'
												  + '</td></tr>';
									}
									// First, trim the description if it's too long (should we do this here?)
									if(json[i][1][j].rdescription.length > 75) {
										var description = json[i][1][j].rdescription.substring(0,75)+"...";
									} else {
										var description = json[i][1][j].rdescription;
									}
									// Write out the table row for the user
									tablerows += '<tr class="select-me" id="' + json[i][1][j].rid + '">'
											  +  '<td class="report_time">' + json[i][1][j].rtime + '</td>'
											  +  '<td class="report_date">' + json[i][1][j].rdate + '</td>'
											  +  '<td>' + json[i][1][j].task  + '</td>'
											  +  '<td>' + description    + '</td></tr>';

									// Create a sum of the users time and the total time
									sum_user_time += parseFloat(json[i][1][j].rtime);
									total_time    += parseFloat(json[i][1][j].rtime);

									// Add a row for the sum of the users time (only once per user)
									if(j == (json[i][1].length-1)) {
										tablerows += '<tr><td class="report_user_total">total time for ' 
												  + json[i][1][j].user + ': ' 
												  + sum_user_time + '</td><td colspan="3"></td></tr>';
									}
								}
							}
						}
						tablerows += '<tr><td class="report_total_time">Total time (hours): ' + total_time + '</td><td colspan="3"></td></tr>';
					} else {
						tablerows = '<tr><td colspan="4" class="no_records">No records matching your current criteria</td></tr>';
						$("#results").val('');
					}
					$("#records").html(tablerows);

					// Make the records selectable
					$("#records").attr('class', 'selectable');

					// Make table rows selectable
					$(function() {
						$(".selectable").selectable({
							filter: ".select-me",
							stop: function() {
								var result = new Array();
								$(".ui-selected", this).each(function() {
									var id = $(this).attr('id');
									result.push(id);
								});

								// Write out the result as a hidden form array
								$("#results").val(result);
							}
						});
					});
				}
			});
		}
	} // end getRecords
}

jQuery(document).ready(function($){
	HUB.Plugins.TimeReports.initialize();
});