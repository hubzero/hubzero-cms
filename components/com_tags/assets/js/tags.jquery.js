/**
 * @package     hubzero-cms
 * @file        components/com_tags/tags.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
				alert( 'You must fill in a tag name' );
				e.preventDefault();
				return false;
			}
			return true;
		});
	}

	//add count to url
	$(".delete-tag").each(function(index) {
		var count = index + 1,
			url = $(this).attr("href");

		url += (url.indexOf("?") == -1) ? "?count="+count : "&count="+count;
		$(this).attr("href", url);

		$(this).on('click', function (e) {
			var res = confirm('Are you sure you wish to delete this tag?');
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
