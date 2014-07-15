/**
 * @package     hubzero-cms
 * @file        components/com_time/assets/js/overview.js
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
// Time Overview
//----------------------------------------------------------
HUB.Plugins.TimeOverview = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;
		// nothing...
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.TimeOverview.initialize();
});