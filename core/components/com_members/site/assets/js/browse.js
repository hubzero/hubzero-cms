/**
 * @package     hubzero-cms
 * @file        components/com_members/assets/js/browse.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Members) {
	HUB.Members = {};
}
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Members browse
//----------------------------------------------------------
HUB.Members.Browse = {
	initialize: function() {
		var $ = jq;

		// Initialize variables
		var column   = $('#filter-field');
		var operator = $('#filter-operator');

		// Show add filters box
		$('#add-filters').css('display', 'block');

		HUB.Members.Browse.col_change();

		// Capture change events on the column field
		column.on('change', HUB.Members.Browse.col_change);
		operator.on('change', HUB.Members.Browse.operator_change);
	}, // end initialize

	col_change: function() {
		var $ = jq;

		// Initialize variables
		var column   = $('#filter-field');
		var value    = $('#filter-value');
		var operator = $('#filter-operator');

		if (operator.val() != "like") {
			// Create a ajax call to get relevent value options
			$.ajax({
				url: column.attr('data-base') + "/members", //"/api/members/fieldValues",
				data: "task=fieldValues&no_html=1&field="+column.val(),
				dataType: "json",
				cache: false,
				success: function(json){
					// If success, update the list of values based on the chosen column
					if (json.type == 'checkboxes' || json.type == 'select' || json.type == 'radio' || json.type == 'country') {

						var options = '';
						if (json.values.length > 0) {
							for (var i = 0; i < json.values.length; i++) {
								options += '<option value="' + json.values[i].value + '">' + json.values[i].label + '</option>';
							}
						} else {
							options = '<option value="">No values are available</option>';
						}
						value.replaceWith('<select name="q[0][value]" id="filter-value"></select>');
						$('#filter-value').html(options);

					} else {
						value.replaceWith('<input name="q[0][value]" id="filter-value" type="text" />');
					}
				}
			});
		}
	}, // end col_change

	operator_change: function() {
		var $ = jq;

		// Initialize variables
		var operator = $('#filter-operator');
		var value    = $('#filter-value');

		if (operator.val() == "like") {
			value.replaceWith('<input name="q[0][value]" id="filter-value" type="text" />');
		} else {
			value.replaceWith('<select name="q[0][value]" id="filter-value"></select>');
			HUB.Members.Browse.col_change();
		}
	} // end operator_change
};

jQuery(document).ready(function($){
	HUB.Members.Browse.initialize();
	//Hubzero.initApi(HUB.Members.Browse.initialize);
});