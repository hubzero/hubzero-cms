/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			if (_DEBUG) {
				window.console && console.log(list.attr('data-url') + list.children().first().attr('data-time'));
			}
			$.get(list.attr('data-url') + list.children().first().attr('data-time'), {}, function(data){
				if (!data || !data.match('/<body(.*?)>/i') || !data.match('/<form(.*?)>/i')) {
					clearInterval();
				}
				if (_DEBUG) {
					//window.console && console.log(data);
				}
				data = data.replace('<div id="panel-sliders" class="pane-sliders">', '');

				list.prepend($(data).hide().fadeIn());
				list.attr('data-start', list.children().first().attr('data-time'));
			});
		}, 1 * 60 * 1000);
	});
});