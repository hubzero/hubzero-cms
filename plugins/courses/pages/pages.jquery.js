/**
 * @package     hubzero-cms
 * @file        plugins/courses/forum/forum.js
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
//  Forum scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;
	
	$('a.delete').each(function(i, el) {
		$(el).on('click', function(e) {
			var res = confirm('Are you sure you wish to delete this document?');
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});
});