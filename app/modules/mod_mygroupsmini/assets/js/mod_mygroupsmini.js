/**
 * @package     hubzero-cms
 * @file        modules/mod_mygroups/assets/js/mod_mygroups.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		containers = $('.mod_mygroups');

	if (containers.length <= 0) {
		return;
	}

	containers.each(function(i, container) {
		var tabs   = $(container).find('.tab_title'),
			panels = $(container).find('.tab_panel');

		tabs.each(function(i, item) {
			$(item).on('click', function () {
				var tab = $(this);

				tabs.each(function(i, item) {
					$(this).removeClass('active');
				});
				panels.each(function(i, item) {
					$(this).removeClass('active');
				});

				var panel = $('#' + tab.attr('rel'));
				panel.addClass('active');
				tab.addClass('active');
			});
		});
	});
});
