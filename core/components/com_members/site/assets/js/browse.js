/**
 * @package     hubzero-cms
 * @file        components/com_members/assets/js/browse.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){
	$('.filters').find('input,select').on('change', function(e){
		$(this).closest('form').submit();
	});
});