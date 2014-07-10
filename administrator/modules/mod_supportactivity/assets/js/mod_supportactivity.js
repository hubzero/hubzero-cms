/**
 * @package     hubzero-cms
 * @file        administrator/modules/mod_supportactivity/assets/js/mod_supportactivity.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){
	var _DEBUG = document.getElementById('system-debug') ? true : false;

	$('div.mod_supportactivity').each(function(i, el){
		var lists = $(this).find('ul');

		if (!lists.length) {
			return;
		}

		var list = $(lists[0]);

		// Heartbeat for checking for new posts
		setInterval(function () {
			$.get(list.attr('data-url') + list.children().first().attr('data-time'), {}, function(data){
				if (data) {
					if (_DEBUG) {
						window.console && console.log(data);
					}
					data = data.replace('<div id="panel-sliders" class="pane-sliders">', '');

					list.prepend($(data).hide().fadeIn());
					list.attr('data-start', list.children().first().attr('data-time'));
				}
			});
		}, 1 * 60 * 1000);
	});
});