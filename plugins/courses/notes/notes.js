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
HUB.Plugins.CoursesNotes = {
	initialize: function() {
		console.log('No implementation');
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.CoursesNotes.initialize);