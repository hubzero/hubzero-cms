/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//----------------------------------------------------------
// Recommendations
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// Recommendations web service
	if (!$('#recommendations-section').length) {
		return;
	}

	var sbjt = $('#recommendations-subject');
	if (sbjt.length) {
		var rid = $('#rid');
		if (rid.length) {
			$.get('/index.php?option=com_publications&task=plugin&trigger=onPublicationsRecoms&no_html=1&rid='+rid.val(), {}, function(data) {
				$(sbjt).html(data);
			});
		}
	}
});