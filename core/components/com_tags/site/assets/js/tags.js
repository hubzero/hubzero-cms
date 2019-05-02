/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		form = $('#hubForm');

	if (form.length) {
		form.on('submit', function (e){
			// do field validation
			if ($('#field-raw_tag').val() == ''){
				alert($('#field-raw_tag').attr('data-error'));
				e.preventDefault();
				return false;
			}
			return true;
		});
	}

	//add count to url
	$(".delete-tag").each(function(i, el) {
		var count = i + 1,
			url = $(el).attr("href");

		url += (url.indexOf("?") == -1 ? "?" : "&") + "count=" + count;
		$(el).attr("href", url);

		$(el).on('click', function (e) {
			var res = confirm($(el).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	//do we need to scroll down
	if (window.location.hash) {
		var row_id = window.location.hash.replace("#count", ""),
			row = $($(".entries tr")[row_id]);

		$("body").animate({
			scrollTop: row.offset().top
		}, 500);
	}
});
