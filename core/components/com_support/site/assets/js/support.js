/**
 * @package     hubzero-cms
 * @file        components/com_support/assets/js/support.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){
	if ($('#messages').length > 0) {
		$('#messages').on('change', function(e){
			if ($(this).val() != 'mc') {
				var hi = $('#' + $(this).val()).val();
				$('#comment').val(hi);
			} else {
				$('#comment').val('');
			}
		});
	}

	var priv = $('#make-private');
	if (priv.length > 0) {
		priv.on('click', function() {
			var es = $('#email_submitter');
			if (priv.attr('checked')) {
				if ($('#email_submitter').attr('checked')) {
					$('#email_submitter').removeAttr('checked').attr('disabled', 'disabled');
				}
				$('#commentform').addClass('private');
			} else {
				$('#email_submitter').removeAttr('disabled').attr('checked', 'checked');
				$('#commentform').removeClass('private');
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
						'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
						'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
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

	if ($('#commentform').length > 0) {
		$('input.datetime-field').datetimepicker({
			controlType: 'slider',
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss',
			timezone: $('input.datetime-field').attr('data-timezone')
		});
	}
});
