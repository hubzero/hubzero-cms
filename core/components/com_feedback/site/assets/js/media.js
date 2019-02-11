/**
 * @package     hubzero-cms
 * @file        components/com_feedback/assets/js/media.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	parent.document.getElementById('picture').value = this.document.forms[0].conimg.value;

	$('#filelist').on('submit', function(e){
		e.preventDefault();

		var apuf = document.getElementById('file');
		return apuf.value ? true : false;
	});
});
