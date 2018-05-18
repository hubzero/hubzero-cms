/**
 * @package     hubzero-cms
 * @file        components/com_blog/assets/js/blog.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('input[id$="_other"]').each(function (i, el){
		var item = $(el);

		if (!item.val()) {
			item.hide();
		}

		var rd = $(item.parent()).find('input[type=radio]');

		if (rd.length) {
			$(item.parent().parent())
				.find('input[name="' + rd.attr('name') + '"]')
				.on('change', function (e){
					if ($(this).attr('id') == rd.attr('id') && $(this).is(':checked')) {
						item.show();
					} else {
						item.hide();
						item.val('');
					}
				});
		}

		var cb = $(item.parent())
			.find('input[type=checkbox]')
			.on('change', function (e){
				if ($(this).is(':checked')) {
					item.show();
				} else {
					item.hide();
					item.val('');
				}
			});
	});
});
