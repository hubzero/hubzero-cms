
/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/asset.jquery.js
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
//  Courses outline javascript
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($) {
	$('.tool-list').HUBfancyselect({
		'showSearch'         : true,
		'searchPlaceholder'  : 'Seach for a tool...',
		'maxHeightWithSearch': 300
	});

	$('.tool-param').click(function() {
		$('.fs-dropdown').toggle();
	});

	if ($('.tool-param').is(':checked')) {
		$('.fs-dropdown').show();
	}
});