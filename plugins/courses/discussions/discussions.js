/**
 * @package     hubzero-cms
 * @file        plugins/courses/forum/forum.js
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
//  Forum scripts
//----------------------------------------------------------

window.addEvent('domready', function(){
	$$('a.delete').each(function(el) {
		el.addEvent('click', function(e) {
			var val = confirm('Are you sure you wish to delete this item?');
			if (!val) {
				new Event(e).stop();
			}
			return val;
		});
	});
	
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
});