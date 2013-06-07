/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		ellipsestext = "...",
		moretext = "more",
		lesstext = "less",
		container = $("div.course-instructors");

	if (!container.length) {
		return;
	}

	var showChar = container.attr('data-bio-length');

	container.find('p.course-instructor-bio').each(function() {
		var content = $(this).html();

		if (content.length > showChar) {
			var c = content.substr(0, showChar),
				h = content.substr(showChar, content.length - showChar);

			var html = c + 
						'<span class="moreellipses">' + ellipsestext + '&nbsp;</span>' + 
						'<span class="morecontent">' + 
							'<span class="hide">' + h + '</span>&nbsp;&nbsp;<a href="#" class="more">' + moretext + '</a>' + 
						'</span>';
			$(this).html(html);
		}
	});

	container.find("a.more").on('click', function (e) {
		e.preventDefault();

		if ($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}

		$(this).parent().prev().toggle();
		$(this).prev().toggle();

		return false;
	});
});
