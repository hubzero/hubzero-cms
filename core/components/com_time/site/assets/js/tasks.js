/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/tasks.js
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
		var column   = $('#filter-column');
		var operator = $('#filter-operator');

		// Show add filters box
		$('#add-filters').css('display', 'block');

		HUB.Plugins.TimeTasks.col_change();

		// Capture change events on the column field
		column.on('change', HUB.Plugins.TimeTasks.col_change);
		operator.on('change', HUB.Plugins.TimeTasks.operator_change);
	}, // end initialize

	col_change: function() {
		var $ = HUB.Plugins.TimeTasks.jQuery;

		// Initialize variables
		var table    = $('#filter-table');
		var column   = $('#filter-column');
		var value    = $('#filter-value');
		var operator = $('#filter-operator');

		if(operator.val() != "like") {
			if(column.val().search("date") <= 0) {
				// Create a ajax call to get relevent value options
				$.ajax({
					url: "/api/time/getValues",
					data: "table="+table.val()+"&column="+column.val(),
					dataType: "json",
					cache: false,
					success: function(json){
						// If success, update the list of values based on the chosen column
						var options = '';
						if(json.values.length > 0) {
							for (var i = 0; i < json.values.length; i++) {
								options += '<option value="' + json.values[i].value + '">' + json.values[i].display + '</option>';
							}
						} else {
							options = '<option value="">No values are available</option>';
						}
						value.replaceWith("<select name=\"q[value]\" id=\"filter-value\"></select>");
						$('#filter-value').html(options);
					}
				});
			} else {
				value.replaceWith("<input name=\"q[value]\" id=\"filter-value\" class=\"hadDatepicker\" type=text />");
				$('.hadDatepicker').datepicker({ dateFormat: 'yy-mm-dd' });
			}
		}
	}, // end col_change

	operator_change: function() {
		var $ = HUB.Plugins.TimeTasks.jQuery;

		// Initialize variables
		var operator = $('#filter-operator');
		var value    = $('#filter-value');

		if (operator.val() == "like") {
			value.replaceWith("<input name=\"q[value]\" id=\"filter-value\" type=text />");
		} else {
			value.replaceWith("<select name=\"q[value]\" id=\"filter-value\"></select>");
			HUB.Plugins.TimeTasks.col_change();
		}
	} // end operator_change
};

jQuery(document).ready(function($){
	HUB.Plugins.TimeTasks.initialize();
});