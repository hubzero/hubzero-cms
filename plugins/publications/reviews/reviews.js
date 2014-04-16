/**
 * @package     hubzero-cms
 * @file        plugins/publications/reviews/reviews.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Publication reviews
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// Reply to review or comment
	$('.reply').on('click', function(e) {
		e.preventDefault();

		var f = $(item).parent().next('.addcomment');
		if (f.hasClass('hide')) {
			f.removeClass('hide');
		} else {
			f.addClass('hide');
		}
	});

	$('.commentarea').on('focus', function() {
		if ($(this).val() == 'Enter your comments...') {
			$(this).val('');
		}
	});

	$('.cancelreply').on('click', function(e) {
		e.preventDefault();
		$(item.parentNode.parentNode.parentNode.parentNode).addClass('hide');
	});

	$('.revvote').on('click', function(e) {
		e.preventDefault();
		pn = $(this.parentNode.parentNode.parentNode);
		if ($(this.parentNode).hasClass('gooditem')) {
			var s = 'yes';
		} else {
			var s = 'no';
		}

		var id = $(this.parentNode.parentNode.parentNode).attr('id').replace('reviews_','');

		var rid = $(this.parentNode.parentNode).attr('id').replace('rev'+id+'_','');

		$.get('/index.php?option=com_publications&task=plugin&trigger=onPublicationRateItem&action=rateitem&no_html=1&rid='+rid+'&refid='+id+'&ajax=1&vote='+s, {}, function(data) {
			$(pn).html(data);
		});
	});
});
