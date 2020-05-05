/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
