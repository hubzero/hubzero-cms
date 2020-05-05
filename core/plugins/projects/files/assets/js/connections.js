/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('.layout-control').click(function (e) {
		e.preventDefault();

		var target     = $(e.target),
			connection = $('.connection-wrap'),
			classname  = target.data('class');

		connection.removeClass('large-icon small-icon list').addClass(classname);

		$('.layout-control').removeClass('active');
		$(this).addClass('active');
	});

	$('.connection-type').change(function (e) {
		var form = $(this).parents('form');

		form.submit();
	});

	$('.connections')
		// Add confirm dialog to delete links
		.on('click', 'a.connection-delete', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
});