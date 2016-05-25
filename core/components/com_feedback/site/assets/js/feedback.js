/**
 * @package     hubzero-cms
 * @file        components/com_feedback/assets/js/feedback.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		attach = $("#ajax-uploader");

	if (attach.length) {
		$('#ajax-uploader-list')
			.on('click', 'a.delete', function (e){
				e.preventDefault();
				if ($(this).attr('data-id')) {
					$.get($(this).attr('href'), {}, function(data) {});
				}
				$(this).parent().parent().remove();
			});
		var running = 0;

		var uploader = new qq.FileUploader({
			element: attach[0],
			//dataType: 'html',
			action: attach.attr("data-action"),
			multiple: true,
			debug: true,
			//formData: false,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.attr('data-instructions') + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul><span id="uploadImages"></span>' + 
					'</div>',
			onSubmit: function(id, file) {
				running++;
			},
			onComplete: function (e, data, response) {
				running--;

				var newImageDom = document.createElement('img');
				newImageDom.src = response.directory + '/' + response.file;
				newImageDom.width = '100';
				newImageDom.height = '100';

				$('#uploadImages').append(newImageDom);

				if (running == 0) {
					$('ul.qq-upload-list').empty();
				}
			}
		});
	}
});