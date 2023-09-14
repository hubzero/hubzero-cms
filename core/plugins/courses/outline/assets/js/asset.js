/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	$('.xapp-list').HUBfancyselect({
		'showSearch'         : true,
		'searchPlaceholder'  : 'Seach for an external app...',
		'maxHeightWithSearch': 300
	});

	$('.xapp-param').click(function() {
		$('.fs-dropdown').toggle();
	});

	if ($('.xapp-param').is(':checked')) {
		$('.fs-dropdown').show();
	}
});