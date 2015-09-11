/**
 * @package     hubzero-cms
 * @file        plugins/courses/announcements/assets/js/announcements.dashboard.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.announcement a.delete').each(function(i, el) {
		$(el).on('click', function(e) {
			var res = confirm('Are you sure you wish to delete this item?');
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	if ($('#announcementForm').length > 0) {
		$('.announcements')
			.on('submit', '#announcementForm', function(e) {
				e.preventDefault();
				$.post($(this).attr('action').nohtml(), $(this).serialize(), function(response){
					var json = jQuery.parseJSON(response);
					if (json.code == 0) {
						/*$('<div class="hubzero_notification"></div>')
							.text('Announcement posted.')
							.hide()
							.fadeIn()
							.appendTo('body');

						setTimeout(function(){
							$('.hubzero_notification').fadeOut();
						}, 2000);*/
						window.location.reload();
					}
				});
			});
	}
});