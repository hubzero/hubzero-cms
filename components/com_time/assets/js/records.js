/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/records.js
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

		// Initialize variables
		var hub        = $("#hub_id");
		var col_filter = $("#filter-column");
		var filter_sub = $("#filter-submit");

		// Show add filters box
		$('#add-filters').css('display', 'block');

		// Add change event to hub select box (filter tasks list by selected hub) - ('edit' view)
		hub.change(function(event) {
			// Create a ajax call to get the tasks
			$.ajax({
				url: "/api/time/indexTasks",
				data: "hid="+hub.val()+"&pactive=1",
				dataType: "json",
				cache: false,
				success: function(json){
					// If success, update the list of tasks based on the chosen hub
					var options = '';

					if(json.tasks.length > 0) {
						for (var i = 0; i < json.tasks.length; i++) {
							options += '<option value="' + json.tasks[i].id + '">' + json.tasks[i].name + '</option>';
						}
					} else {
						options = '<option value="">No tasks for this hub</option>';
					}
					$("#task").html(options);
				}
			});
		});

		// Add change event to the column filters select box ('view' view)
		col_filter.change(function(event){
			// Update the filter values when column is changed
			HUB.Plugins.TimeRecords.col_change();
		});

		// Update the filter values on initial page load too
		HUB.Plugins.TimeRecords.col_change();

	}, // end initialize

	col_change: function() {
		var $ = this.jQuery;

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
			url: "/api/time/indexTimeUsers",
			dataType: "json",
			cache: false,
			success: function(json){
				// If success, update the list of users
				var options = '';
				if(json.users.length > 0) {
					for (var i = 0; i < json.users.length; i++) {
						options += '<option value="' + json.users[i].id + '">' + json.users[i].name + '</option>';
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
			url: "/api/time/indexTasks",
			dataType: "json",
			data: "pactive=1",
			cache: false,
			success: function(json){
				// If success, update the list of tasks based on the chosen hub
				var options = '';
				if(json.tasks.length > 0) {
					for (var i = 0; i < json.tasks.length; i++) {
						options += '<option value="' + json.tasks[i].id + '">' + json.tasks[i].name + '</option>';
					}
				} else {
					options = '<option value="">No tasks are available</option>';
				}
				value.html(options);
			}
		});
	} // end get_tasks
};

jQuery(document).ready(function($){
	HUB.Plugins.TimeRecords.initialize();
});