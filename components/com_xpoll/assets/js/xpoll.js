/**
 * @package     hubzero-cms
 * @file        components/com_xpoll/xpoll.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Tag Browser
//----------------------------------------------------------
HUB.XPoll = {
	initialize: function() {
		var pollid = $('pollid');
		if (pollid) {
			pollid.addEvent('change', HUB.XPoll.changePoll);
		}
	},
	
	changePoll: function() {
		var pollid = $('pollid');
		if (pollid.value != '') {
			document.location.href = $('poll').action + '/view/' + pollid.value;
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.XPoll.initialize);

