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

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
	//return this;
};

HUB.Plugins.CoursesReviews = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		// Reply to review or comment
		$('.reply').each(function(i, item) {
			$(item).on('click', function (e) {
				e.preventDefault();

				var frm = '#' + $(this).attr('rel');
				if ($(frm).hasClass('hide')) {
					$(frm).removeClass('hide');
				} else {
					$(frm).addClass('hide');
				}
			});
		});

		$('.cancelreply').each(function(i, item) {
			$(item).click(function (e) {
				e.preventDefault();
				$(this).closest('.addcomment').addClass('hide');
			});
		});

		// review ratings
		$('.vote-button').each(function(i, item) {
			if ($(item).attr('href')) {
				$(item).on('click', function (e) {
					e.preventDefault();

					$.get($(this).attr('href').nohtml(), {}, function(data) {
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
