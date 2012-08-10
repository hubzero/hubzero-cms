/**
 * @package     hubzero-cms
 * @file        plugins/groups/projects/projects.js
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
//  Group Calendar Code
//----------------------------------------------------------
HUB.Plugins.GroupProjects = {
	
	initialize: function() {
		
	}
	
}

window.addEvent('domready', HUB.Plugins.GroupProjects.initialize);
