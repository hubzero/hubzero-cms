/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/tasks.js
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
// Time Tasks
//----------------------------------------------------------
HUB.Plugins.TimeTasks = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Initialize variables
		var column    = $('#filter-column'),
		    operator  = $('#filter-operator'),
		    selection = [];

		// Show add filters box
		$('#add-filters').css('display', 'block');

		HUB.Plugins.TimeTasks.col_change();

		// Capture change events on the column field
		column.on('change', HUB.Plugins.TimeTasks.col_change);
		operator.on('change', HUB.Plugins.TimeTasks.operator_change);

		// Create the selectable rows
		$('.tbody').bind('mousedown', function (e) {
			// This causes the selection before to function more like a toggle
			// Otherwise, clicking an already selected row would just reselect it
			// and you would have to use the meta key to unselect.  This behavior
			// seems more natural
			e.metaKey = true;
		}).selectable({
			filter: ".tr",
			cancel: ".not-selectable",
			stop: function() {
				selection = [];
				$(".ui-selected").each(function ( idx, item ) {
					selection.push($(item).data('id'));
				});

				if (selection.length == 1) {
					// Start by hiding all buttons
					$('.actions .action').hide();

					// Show the edit and delete buttons
					var edit = $('.action.edit'),
					    del  = $('.action.delete');

					edit.attr('href', edit.data('target') + '/' + parseInt(selection[0], 10)).show();
					del.attr('href', del.data('target') + '/' + parseInt(selection[0], 10)).show();

					// Show the action area if it isn't already visible
					$('.actions').slideDown('fast');
				} else if (selection.length >= 2) {
					// Start by hiding all buttons
					$('.actions .action').hide();

					// Show merge and delete buttons
					var merge = $('.action.merge'),
					    dels  = $('.action.delete');

					merge.attr('href', merge.data('target') + "?ids[]=" + selection.join('&ids[]=')).show();
					dels.attr('href', dels.data('target') + "?id[]=" + selection.join('&id[]=')).show();

					// Show the action area if it isn't already visible
					$('.actions').slideDown('fast');
				} else {
					// Hide the actions area and hide all buttons
					$('.actions').slideUp('fast');
					$('.actions .action').hide();
				}
			}
		});

		// We need to ask for a primary before completing the merge itself
		$('.merge').click(function ( e ) {
			// Prevent delete action
			e.preventDefault();

			// Grab the merge url
			var action = $(this).attr("href");

			// This is the confirmation dialog box message
			var msg = '';
			msg += '<p>Which task would you like to remain as the primary?<?p>';
			$.each(selection, function ( index, id ) {
				var name = $('.tr[data-id=' + id + ']').data('name');
				msg += '<div class="primary-option"><a href="' + action + '&primary=' + id + '">' + name + '</a></div>';
			});

			// Set dialog box message and title
			var dc = $("#dialog-confirm");
			dc.html(msg);
			dc.attr('title','Select a primary');

			// Create the dialog box
			dc.dialog({
				resizable: false,
				height: 250,
				width: 450,
				modal: true,
				buttons: {
					Cancel: function() {
						// Close the dialog box, as if nothing happened
						$(this).dialog("close");
					}
				}
			});
		});
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
	Hubzero.initApi(HUB.Plugins.TimeTasks.initialize);
});