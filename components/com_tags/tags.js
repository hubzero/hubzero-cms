/**
 * @package     hubzero-cms
 * @file        components/com_tags/tags.js
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
// Tags scripts
//----------------------------------------------------------
HUB.Tags = {
	submitbutton: function(pressbutton) {
		var form = $('hubForm');

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.raw_tag.value == ''){
			alert( 'You must fill in a tag name' );
		} else {
			submitform( pressbutton );
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Tags.submitbutton);

