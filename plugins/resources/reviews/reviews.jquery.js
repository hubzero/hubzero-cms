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

HUB.Plugins.ResourcesReviews = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		// Reply to review or comment
		$('.reply').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();
				
				var f = $(this).closest('.addcomment');
				if (f.hasClass('hide')) {
					f.removeClass('hide');
				} else {
					f.addClass('hide');
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
				$(item.parentNode.parentNode.parentNode.parentNode).addClass('hide');
			});
		});
		
		// review ratings
		$('.thumbsvote').each(function(i, v) {
			$(v).on('mouseover', function() {
				var el = $($(this).last());
				var el = $(el.last());
				el.css('display', "inline");
			});
			$(v).on('mouseout', function() {
				var el = $($(this).last());
				var el = $(el.last());
				el.css('display', "none");
			});
		});
		
		$('.revvote').each(function(i, item) {
			$(item).on('click', function(e) {
				pn = $(this.parentNode.parentNode.parentNode);
				if ($(this.parentNode).hasClass('gooditem')) {
					var s = 'yes';
				} else {
					var s = 'no';
				}
			
				var id = $(this.parentNode.parentNode.parentNode).attr('id').replace('reviews_','');

				var rid = $(this.parentNode.parentNode).attr('id').replace('rev'+id+'_','');

				$.get('/index.php?option=com_resources&task=plugin&trigger=onResourcesRateItem&action=rateitem&no_html=1&rid='+id+'&refid='+id+'&ajax=1&vote='+s, {}, function(data) {
					$(pn).html(data);
				});
			});
		});
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.ResourcesReviews.initialize);
