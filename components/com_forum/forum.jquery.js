/**
 * @package     hubzero-cms
 * @file        components/com_forum/forum.js
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
//  Forum scripts
//----------------------------------------------------------
HUB.Forum = {
	initialize: function() {
		$('a.delete').each(function(i, el) {
			$(el).on('click', function(e) {
				e.preventDefault();

				return confirm('Are you sure you wish to delete this item?');
			});
		});
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Forum.initialize();
});
