/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	$('.advertise-popup').fancybox({
		type: 'iframe',
		height:($(window).height())*5/6,
		autoSize: false
	});
});