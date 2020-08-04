/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	if (!!$.prototype.HUBfancyselect) {
		$('select.site').HUBfancyselect({
			'showSearch'          : true,
			'searchPlaceholder'   : 'search...',
			'maxHeightWithSearch' : 300
		});
	}
});
