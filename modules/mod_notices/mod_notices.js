/**
 * @package     hubzero-cms
 * @file        modules/mod_mysessions/mod_mysessions.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-------------------------------------------------------------
// Add an event to close the notice
//-------------------------------------------------------------
window.addEvent('domready', function(){
	$$('.modnotices .close').each(function(item){
		$(item).addEvent('click', function(e) {
			new Event(e).stop();

			var id = $($(this).parentNode.parentNode).getProperty('id');
			var days = $(this).getProperty('data-duration');

			$($(this).parentNode.parentNode).remove();

			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));

			document.cookie = id + '=closed; expires=' + date.toGMTString() + ';';
		});
	})
});

