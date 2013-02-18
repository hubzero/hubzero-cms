/**
 * @package     hubzero-cms
 * @file        plugins/courses/announcements/announcements.js
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
// Announcements scripts
//----------------------------------------------------------
HUB.Plugins.CoursesDashboard = {
	initialize: function() {
		$$('a.delete').each(function(el) {
			el.addEvent('click', function(e) {
				var val = confirm('Are you sure you wish to delete this item?');
				if (!val) {
					new Event(e).stop();
				}
				return val;
			});
		});
	} //end initialize
}

window.addEvent('domready', HUB.Plugins.CoursesDashboard.initialize);
