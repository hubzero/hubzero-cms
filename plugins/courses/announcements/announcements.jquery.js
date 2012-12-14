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
if (!jq) {
	var jq = $;
}

HUB.Plugins.CoursesMembers = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

	} //end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesMembers.initialize();
});
