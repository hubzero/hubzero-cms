/**
 * @package     hubzero-cms
 * @file        plugins/courses/notes/notes.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

HUB.Plugins.CoursesNotes = {
	initialize: function() {
		window.console && console.log('No implementation');
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.CoursesNotes.initialize);