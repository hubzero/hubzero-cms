/**
 * @package     hubzero-cms
 * @file        plugins/time/records/records.js
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
// Time Records
//----------------------------------------------------------
HUB.Plugins.TimeRecords = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Set a variable for our hub_id select box
		var hub        = $("#hub_id");
		var col_filter = $("#filter-column");
		var filter_sub = $("#filter-submit");

		// Expand the submit button on hover (not necessary, just fun...)
		if($.isFunction($().hoverIntent)){
			filter_sub.hoverIntent({
				over: function(){
					$(this).animate({'width': '95px', 'margin-right': '0'}, 100);
				},
				timeout: 500,
				interval: 300,
				out: function(){
					$(this).animate({'width': '16px', 'margin-right': '95px'}, 100);
				}
			});
		}

		// Add change event to hub select box (filter tasks list by selected hub)
		hub.change(function(event) {

			// Set hub = -1 if hub value is empty (so the ajax call returns nothing, instead of everything)
			// @FIXME: this is sortof 'hacky'
			if(hub.val() == '') { hid = -1 } else { hid = hub.val() }

			// Create a ajax call to get the tasks
			$.ajax({
				url: "index.php?option=com_time&task=ajax&active=ajax&action=tasks.json",
				data: "hid="+hid,
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
						options = '<option value="">No tasks for this hub</option>';
					}
					$("#task").html(options);
				}
			});
		});

		col_filter.change(function(event){
			// Update the filter values when column is changed
			HUB.Plugins.TimeRecords.col_change();
		});

		// Update the filter values on page load too
		HUB.Plugins.TimeRecords.col_change();

	}, // end initialize

	col_change: function() {
		var $       = this.jQuery;
		var col_val = $('#filter-column').val();

		if(col_val == 'user_id') {
			HUB.Plugins.TimeRecords.get_users();
		}
		else if(col_val == 'task_id') {
			HUB.Plugins.TimeRecords.get_tasks();
		}
	}, // end col_change

	get_users: function() {
		var $     = this.jQuery;
		var value = $('#filter-value');

		// Create a ajax call to get the users
		$.ajax({
			url: "index.php?option=com_time&task=ajax&active=ajax&action=users.json",
			dataType: "json",
			cache: false,
			success: function(json){
				// If success, update the list of users
				var options = '';
				if(json.length > 0) {
					for (var i = 0; i < json.length; i++) {
						options += '<option value="' + json[i].id + '">' + json[i].name + '</option>';
					}
				} else {
					options = '<option value="">No users are available</option>';
				}
				value.html(options);
			}
		});
	}, // end get_users

	get_tasks: function() {
		var $     = this.jQuery;
		var value = $('#filter-value');

		// Create a ajax call to get the tasks
		$.ajax({
			url: "index.php?option=com_time&task=ajax&active=ajax&action=tasks.json",
			data: "hid=0",
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
					options = '<option value="">No tasks are available</option>';
				}
				value.html(options);
			}
		});
	} // end get_tasks
}

jQuery(document).ready(function($){
	HUB.Plugins.TimeRecords.initialize();
});