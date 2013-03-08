/**
 * @package     hubzero-cms
 * @file        plugins/resources/reviews/reviews.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.CoursesReviews = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		// Reply to review or comment
		$('.reply').each(function(i, item) {
			$(item).on('click', function(e) {
				if ($(this).attr('href').indexOf('login') == -1) {
					e.preventDefault();

					var f = $(this).parent().parent().find('.addcomment');
					if (f.hasClass('hide')) {
						f.removeClass('hide');
					} else {
						f.addClass('hide');
					}
				}
			});
		});
		$('.commentarea').each(function(i, item) {
			// Clear the default text
			$(item).on('focus', function() {
				if ($(this).val() == 'Enter your comments...') {
					$(this).val('');
				}
			});
		});
		$('.cancelreply').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();
				$($(this).parent().parent().parent().parent()).addClass('hide');
			});
		});
		
		// review ratings
		$('.vote-button').each(function(i, item) {
			if ($(item).attr('href')) {
				$(item).on('click', function (e) {
					e.preventDefault();

					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);

					$.get($(this).attr('href'), {}, function(data) {
						$(item).closest('.voting').html(data);
					});
				});
			}
		});
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesReviews.initialize();
});
