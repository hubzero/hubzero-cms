/**
 * @package     hubzero-cms
 * @file        plugins/groups/citations/citations.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	// toggle download markers.
	$('.checkall-download').click(function() {
		var checked = $(this).prop('checked');
		$('.download-marker').each(function() {
			$(this).prop('checked', checked);
			});
		});
	
	$('.protected').click(function(e) {
		var prompt = confirm('Are you sure you want to delete this citation?');
		console.log(prompt);
		var url = $(this).attr('href');
		console.log(url);
		if (prompt === true)
		{
			
		}
		else
		{
			e.preventDefault();
		}

	});
		
});
