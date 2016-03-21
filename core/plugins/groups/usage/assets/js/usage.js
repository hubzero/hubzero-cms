/**
 * @package     hubzero-cms
 * @file        plugins/groups/usage/assets/js/usage.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

//on document ready
jQuery(document).ready(function(jq){
	var $ = jq;

	//setup datepicker
	$('.datepicker').datepicker({
		format: 'm/d/Y'
	});
});
