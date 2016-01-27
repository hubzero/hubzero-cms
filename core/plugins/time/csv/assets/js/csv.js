/**
 * @package     hubzero-cms
 * @file        plugins/time/csv/csv.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function( $ ) {
	// Fancy select boxes
	if (!!$.prototype.select2) {
		$('.plg_time_csv select').select2({
			placeholder : "search...",
			width       : "100%"
		});
	}

	// Date picker for date input field
	$(".hadDatepicker").datepicker({
		// Set a unix/MySQL friendly date format
		dateFormat: 'yy-mm-dd'
	});
});