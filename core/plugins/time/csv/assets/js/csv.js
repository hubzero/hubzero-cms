/**
 * @package     hubzero-cms
 * @file        plugins/time/csv/csv.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function( $ ) {
	// Fancy select boxes
	if (!!$.prototype.HUBfancyselect) {
		$('.plg_time_csv select').HUBfancyselect({
			'showSearch'          : true,
			'searchPlaceholder'   : 'search...',
			'maxHeightWithSearch' : 300
		});
	}

	// Date picker for date input field
	$(".hadDatepicker").datepicker({
		// Set a unix/MySQL friendly date format
		dateFormat: 'yy-mm-dd'
	});
});