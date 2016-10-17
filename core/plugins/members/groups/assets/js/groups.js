/**
 * @package     hubzero-cms
 * @file        plugins/members/groups/groups.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}


jQuery(document).ready(function(jq){
	var $ = jq;

	var lis = $(".groups-container>.group"),
		none = $('.results-none'),
		filters = $('.filter-options a');

	/*$('#search').on("keyup", function() {
		var input = $(this).val();

		lis.show();

		if (input && input.length > 2) {
			lis.not('[data-title*="'+ input +'"]').hide();
		}
	});*/

	filters.on('click', function(e) {
		e.preventDefault();

		none.hide();

		// Set the active filter
		filters.removeClass('active');
		$(this).addClass('active');

		// Get the value
		var input = $(this).attr('data-status');

		lis.hide();

		if (!input || input == 'all') {
			lis.show();
			return;
		}

		lis.filter('[data-status*="'+ input +'"]').show();

		var shown = $(".groups-container>.group:visible").length;
		if (!shown) {
			none.show();
		}
	});
});
