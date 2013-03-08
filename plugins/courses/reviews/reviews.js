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
		// Reply to review or comment
		var show = $$('.reply');
		if (show) {
			show.each(function(item) {
				item.addEvent('click', function(e) {
					if ($(this).getProperty('href').indexOf('login') == -1) {
						new Event(e).stop();
					
						var f = $(this.parentNode.parentNode).getElement('.addcomment');
						if (f.hasClass('hide')) {
							f.removeClass('hide');
						} else {
							f.addClass('hide');
						}
					}
				});
			});
			if ($$('.commentarea')) {
				$$('.commentarea').each(function(item) {
					// Clear the default text
					item.addEvent('focus', function() {
						if (item.value == 'Enter your comments...') {
							item.value = '';
						}
					});
				});
			}
			if ($$('.cancelreply')) {
				$$('.cancelreply').each(function(item) {
					item.addEvent('click', function(e) {
						new Event(e).stop();
						$(item.parentNode.parentNode.parentNode.parentNode).addClass('hide');
					});
				});
			}
		}
		
		// review ratings
		$$('.thumbsvote').each(function(v) {
			v.addEvent('mouseover', function() {
				var el = this.getLast();
				var el = el.getLast();
				el.style.display = "inline";
			});
			v.addEvent('mouseout', function() {
				var el = this.getLast();
				var el = el.getLast();
				el.style.display = "none";
			});
		});

		$$('.vote-button').each(function(item) {
			if ($(item).getProperty('href')) {
				$(item).addEvent('click', function (e) {
					new Event(e).stop();

					href = $(this).getProperty('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).setProperty('href', href);

					var pn = pn = $(this.parentNode.parentNode.parentNode);

					new Ajax($(this).getProperty('href'),{
						'method' : 'get',
						'update' : $(pn)
					}).request();
				});
			}
		});
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.CoursesReviews.initialize);
