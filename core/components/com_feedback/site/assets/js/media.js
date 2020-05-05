/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
