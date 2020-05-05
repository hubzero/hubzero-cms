/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		sbjt = $('#recommendations-subject');

	if (sbjt.length) {
		var rid = $('#rid');
		if (rid.length) {
			$.get(sbjt.attr('data-src') + '/index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid=' + rid.val(), {}, function(data) {
				$(sbjt).html(data);
			});
		}
	}
});