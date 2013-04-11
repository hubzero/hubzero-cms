/**
 * @package     hubzero-cms
 * @file        components/com_answers/assets/js/answers.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Answers Scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.reply').each(function(i, item) {
		$(item).on('click', function (e) {
			e.preventDefault();
			var cfrm = $('#' + $(this).attr('rel'));
			if (cfrm.hasClass('hide')) {
				cfrm.removeClass('hide');
			} else {
				cfrm.addClass('hide');
			}
		});
	});

	$('.cancelreply').each(function(i, item) {
		$(item).on('click', function (e) {
			e.preventDefault();
			$(this).closest('.addcomment').addClass('hide');
		});
	});
});
