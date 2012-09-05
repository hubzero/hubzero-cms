/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/wikieditortoolbar/wikieditortoolbar.js
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
// Comments
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.HubzeroComments = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		$('.reply').each(function(i, item) {
			$(item).click(function (e) {
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
				$(this).closest('.comment-add').addClass('hide');
			});
		});
		
		$('.vote-button').each(function(i, el) {
			if ($(el).attr('href')) {
				href = $(el).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(el).attr('href', href);
			}
			$(el).click(function (e) {
				e.preventDefault();

				$.get($(this).attr('href'), {}, function(data) {
	            	$($(el).parent().parent()).html(data);
				});
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.Plugins.HubzeroComments.initialize();
});