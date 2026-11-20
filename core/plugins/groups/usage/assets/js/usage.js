/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
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
