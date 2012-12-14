/**
 * @package     hubzero-cms
 * @file        plugins/courses/members/members.js
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
HUB.Plugins.CoursesMembers = {
	initialize: function() {
		
	} //end initialize
}
//-----------
window.addEvent('domready', HUB.Plugins.CoursesMembers.initialize);
