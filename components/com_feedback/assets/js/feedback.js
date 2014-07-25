/**
 * @package     hubzero-cms
 * @file        components/com_feedback/assets/js/feedback.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		fls = $("#field-files");

	if (fls.length) {
		function readURL(input) {
			var files = Array.prototype.slice.call($(input)[0].files);

			files.forEach(function(file) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#uploadImages').append('<img src="' + e.target.result + '" width="100" height="100" alt="">');
				}
				reader.readAsDataURL(file); 
			});
		}

		fls.on('change', function(e){
			$('#uploadImages').html("");
			readURL(this);
		});
	}
});
