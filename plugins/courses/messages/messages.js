/**
 * @package     hubzero-cms
 * @file        plugins/courses/messages/messages.js
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
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.MembersMsg = {
	checkAll: function( ele, clsName ) {
		if (ele.checked) {
			var val = true;
		} else {
			var val = false;
		}
		
		$$('input.'+clsName).each(function(el) {
			if (el.checked) {
				el.checked = val;
			} else {
				el.checked = val;
			}
		});
	}
}

