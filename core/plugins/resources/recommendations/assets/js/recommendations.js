/**
 * @package     hubzero-cms
 * @file        plugins/members/recommendations/recommendations.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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