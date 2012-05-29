/**
 * @package     hubzero-cms
 * @file        plugins/groups/blog/blog.js
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

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.MembersBlog = {
	jQuery: jq,
	
	initialize: function() {
		
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.MembersBlog.initialize();
});
