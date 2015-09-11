/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/asset.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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