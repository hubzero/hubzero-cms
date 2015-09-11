/**
 * @package     hubzero-cms
 * @file        plugins/courses/pages/assets/js/pages.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('ul.manager-options a.delete').each(function(i, el) {
		$(el).on('click', function(e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	var uploader = $('#file-uploader'),
		filelist = $('#file-uploader-list');

	if (uploader.length) {

		$('#field-section_id').on('change', function(e){
			var section = $('#field-section_id').val();
			uploader.attr('data-section', section);

			var fuploader = new qq.FileUploader({
				element: uploader[0],
				action: uploader.attr('data-action') + uploader.attr('data-section'),
				multiple: true,
				debug: false,
				template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + uploader.attr('data-instructions') + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + uploader.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
				onComplete: function(id, file, response) {
					$('.qq-upload-list').empty();
					$.get(uploader.attr('data-list') + uploader.attr('data-section'), {}, function(data) {
						filelist.html(data);
					});
				}
			});

			$.get(uploader.attr('data-list') + section, {}, function(data) {
				filelist.html(data);
			});
		});

		filelist.on('click', 'a.delete', function(e){
			e.preventDefault();
			$.get($(this).attr('href'), {}, function(data) {
				filelist.html(data);
			});
		})

		$.get(uploader.attr('data-list') + $('#field-section_id').val(), {}, function(data) {
			filelist.html(data);
		});

		if (typeof(qq) != 'undefined') {
			var fuploader = new qq.FileUploader({
				element: uploader[0],
				action: uploader.attr('data-action') + uploader.attr('data-section'),
				multiple: true,
				debug: false,
				template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + uploader.attr('data-instructions') + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + uploader.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
				onComplete: function(id, file, response) {
					$('.qq-upload-list').empty();
					$.get(uploader.attr('data-list') + uploader.attr('data-section'), {}, function(data) {
						filelist.html(data);
					});
				}
			});
		}
	}
});