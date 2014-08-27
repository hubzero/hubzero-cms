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
	$('#field-files').fileupload({
		dataType: 'html',
		url: '/feedback/success_story?controller=feedback&task=uploadimage&option=com_feedback',
		formData: false,
		done: function (e, data) {
			var returnObject = jQuery.parseJSON(data.result);
			$.each(returnObject.files, function (index, file) {
				var newImageDom = document.createElement('img');
				newImageDom.src = file.name;
				newImageDom.width = '100';
				newImageDom.height = '100';
				$('#uploadImages').append(newImageDom);
			});
		}
	});
});
