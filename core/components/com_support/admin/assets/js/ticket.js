/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	if ($('#comment-field-template').length) {
		$('#comment-field-template').on('change', function() {
			var co = $('#comment-field-comment');

			if ($(this).val() != 'mc') {
				var hi = $('#' + $(this).val()).val();
				co.val(hi);
			} else {
				co.val('');
			}
		});
	}

	if ($('#comment-field-access').length) {
		$('#comment-field-access').on('click', function() {
			var es = $('#email_submitter');

			if ($(this).prop('checked')) {
				if (es.prop('checked') == true) {
					es.prop('checked', false);
					es.prop('disabled', true);
				}
			} else {
				es.prop('disabled', false);
			}
		});
	}

	var attach = $("#ajax-uploader");
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
			action: attach.attr("data-action"),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.attr('data-instructions') + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				running++;
			},
			onComplete: function(id, file, response) {
				running--;

				// HTML entities had to be encoded for the JSON or IE 8 went nuts. So, now we have to decode it.
				response.html = response.html.replace(/&gt;/g, '>');
				response.html = response.html.replace(/&lt;/g, '<');
				$('#ajax-uploader-list').append(response.html);

				if (running == 0) {
					$('ul.qq-upload-list').empty();
				}
			}
		});
	}
});
