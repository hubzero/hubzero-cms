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
HUB.Plugins.CoursesReviews = {
	initialize: function() {
		$$('.reply').each(function(item) {
			$(item).addEvent('click', function (e) {
				new Event(e).stop();
				var frm = $(this).getProperty('rel');
				if ($(frm).hasClass('hide')) {
					$(frm).removeClass('hide');
				} else {
					$(frm).addClass('hide');
				}
			});
		});
		
		$$('.cancelreply').each(function(item) {
			$(item).addEvent('click', function (e) {
				new Event(e).stop();
				$($(this).parentNode.parentNode.parentNode.parentNode).addClass('hide');
			});
		});
		
		$$('.vote-button').each(function(el) {
			if ($(el).getProperty('href')) {
				href = $(el).getProperty('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(el).setProperty('href', href);
			}
			$(el).addEvent('click', function (e) {
				new Event(e).stop();

				var myAjax1 = new Ajax($(this).getProperty('href'),{
					update: $($(el).parentNode.parentNode)
				}).request();
			});
		});
	}
}

window.addEvent('domready', HUB.Plugins.CoursesReviews.initialize);