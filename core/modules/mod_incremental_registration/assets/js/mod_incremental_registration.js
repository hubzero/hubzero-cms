/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/* For compatibility mode */
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		timer = null;

	if ($('#curl').length > 0) {
		var img = $('#curl-img');

		$('#curl')
			.on('mouseover', function() {
				if (timer) {
					clearInterval(timer);
				}
				img.attr('src', img.attr('data-img-big'));
			})
			.on('mouseout', function() {
				timer = setTimeout(function() {
					img.attr('src', img.attr('data-img-small'));
				}, 800);
			});
	}

	if ($('#questions').length > 0) {
		if ($('#questions').attr('data-redirect')) {
			setTimeout(function() {
				var divs = ['overlay', 'questions'];
				for (var idx = 0; idx < divs.length; ++idx) {
					var div = document.getElementById(divs[idx]);
					div.parentNode.removeChild(div);
				}
			}, 4000);
		}
	}
});
