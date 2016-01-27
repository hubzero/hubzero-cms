/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/records.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
		var hub      = $("#hub_id");
		var column   = $('#filter-column');
		var operator = $('#filter-operator');

		if (!!$.prototype.datetimepicker) {
			$('.hadTimepicker').datetimepicker({
				step: 15,
				time24h: true,
				format: 'Y-m-d H:i',
				defaultTime: '08:00'
			});
		}

		// Show add filters box
		$('#add-filters').css('display', 'block');

		// Add change event to hub select box (filter tasks list by selected hub) - ('edit' view)
		hub.change(function(event) {
			// First, grab the currently select task
			var task = $('#task_id').val();

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
							options += '<option value="';
							options += json.tasks[i].id;
							options += '"';
							if (json.tasks[i].id == task) {
								options += ' selected="selected"';
							}
							options += '>';
							options += json.tasks[i].name;
							options += '</option>';
						}
					} else {
						options = '<option value="">No tasks for this hub</option>';
					}
					$("#task_id").html(options);

					if (!!$.prototype.select2) {
						$('#task_id').select2({
							placeholder : "search...",
							width       : "100%"
						});
					}
				}
			});
		});

		if ($('#filter-table').length) {
			HUB.Plugins.TimeRecords.col_change();

			// Capture change events on the column field
			column.on('change', HUB.Plugins.TimeRecords.col_change);
			operator.on('change', HUB.Plugins.TimeRecords.operator_change);
		}
	}, // end initialize

	col_change: function() {
		var $ = HUB.Plugins.TimeRecords.jQuery;

		// Initialize variables
		var table    = $('#filter-table');
		var column   = $('#filter-column');
		var value    = $('#filter-value');
		var operator = $('#filter-operator');

		if(operator.val() != "like") {
			if(column.val().search("date") < 0) {
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
		var $ = HUB.Plugins.TimeRecords.jQuery;

		// Initialize variables
		var operator = $('#filter-operator');
		var value    = $('#filter-value');

		if (operator.val() == "like") {
			value.replaceWith("<input name=\"q[value]\" id=\"filter-value\" type=text />");
		} else {
			value.replaceWith("<select name=\"q[value]\" id=\"filter-value\"></select>");
			HUB.Plugins.TimeRecords.col_change();
		}
	} // end operator_change
};

jQuery(document).ready(function($){
	Hubzero.initApi(HUB.Plugins.TimeRecords.initialize);
});