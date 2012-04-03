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
		if ($('#pollid')) {
			$('#pollid').bind('change', HUB.XPoll.changePoll);
		}
	},
	
	changePoll: function() {
		if ($('#pollid').val() != '') {
			document.location.href = $('#poll').attr('action') + '/view/' + $('#pollid').val();
		}
	}
}

jQuery(document).ready(function($){
	HUB.XPoll.initialize();
});

