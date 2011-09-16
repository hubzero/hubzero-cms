/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/wikieditortoolbar/wikieditortoolbar.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Ensure we have our namespace
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

// Plugin scripts
HUB.Plugins.HubzeroImagecaptcha = {
	initialize: function() {
		
	}
}

// Initialize script
window.addEvent('domready', HUB.Plugins.HubzeroImagecaptcha.initialize);