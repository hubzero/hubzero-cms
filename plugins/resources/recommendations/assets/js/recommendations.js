/**
 * @package     hubzero-cms
 * @file        plugins/members/recommendations/recommendations.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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