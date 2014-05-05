/**
 * @package     hubzero-cms
 * @file        plugins/time/records/records.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){
	// Add change event to hub select box (filter tasks list by selected hub) - ('edit' view)
	$("#hub_id").on('change', function(event) {
		// Create a ajax call to get the tasks
		$.ajax({
			url: "/api/time/indexTasks",
			data: "hid="+$(this).val()+"&pactive=1",
			dataType: "json",
			cache: false,
			success: function(json){
				// If success, update the list of tasks based on the chosen hub
				var options = '';

				if (json.tasks.length > 0) {
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
});