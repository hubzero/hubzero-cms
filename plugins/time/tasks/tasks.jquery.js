/**
 * @package     hubzero-cms
 * @file        plugins/time/tasks/tasks.js
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
// Time Tasks
//----------------------------------------------------------
HUB.Plugins.TimeTasks = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Initialize variables
		var column = $('#filter-column');

		HUB.Plugins.TimeTasks.col_change();

		// Capture change events on the column field
		column.on('change', HUB.Plugins.TimeTasks.col_change);
	}, // end initialize

	col_change: function() {
		var $ = HUB.Plugins.TimeTasks.jQuery;

		// Initialize variables
		var table  = $('#filter-table');
		var column = $('#filter-column');
		var value  = $('#filter-value');

		// Create a ajax call to get relevent value options
		$.ajax({
			url: "index.php?option=com_time&task=ajax&active=ajax&action=get_values.json",
			data: "table="+table.val()+"&column="+column.val(),
			dataType: "json",
			cache: false,
			success: function(json){
				// If success, update the list of values based on the chosen column
				var options = '';
				if(json.length > 0) {
					console.log(json);
					for (var i = 0; i < json.length; i++) {
						options += '<option value="' + json[i].val + '">' + json[i].val + '</option>';
					}
				} else {
					options = '<option value="">No values are available</option>';
				}
				value.html(options);
			}
		});
	} // end col_change
}

jQuery(document).ready(function($){
	HUB.Plugins.TimeTasks.initialize();
});