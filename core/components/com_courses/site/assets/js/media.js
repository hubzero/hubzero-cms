/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	var attach = $("#ajax-uploader");

	if (attach.length) {
		var uploader = new qq.FileUploader({
			element: attach[0],
			action: attach.attr("data-action"),
			multiple: false,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.attr("data-instructions") + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + attach.attr("data-instructions") + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onComplete: function(id, file, response) {

				// HTML entities had to be encoded for the JSON or IE 8 went nuts. So, now we have to decode it.
				if (response.error !== undefined) {
					alert(response.error);
					return;
				}

				$('.course-identity>span').replaceWith('<img src="' + response.file + '" />');
			}
		});
	}

	$('a.delete-file')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});

	$('a.delete-folder')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			var numFiles = parseInt($(this).attr('data-files'));
			if (numFiles > 0) {
				e.preventDefault();
				alert($(this).attr('data-notempty'));
				return false;
			}
			return res;
		});

	$('.filepath').on('click', function (e) {
		e.preventDefault();
		var path = prompt('The file path is:', file);
		return false;
	});
});
