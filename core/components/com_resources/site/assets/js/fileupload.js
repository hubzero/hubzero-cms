/**
 * @package     hubzero-cms
 * @file        components/com_resources/site/assets/js/fileupload.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Resources scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		iframe = $('#attaches'),
		attach = $("#ajax-uploader");

	if (attach.length) {
		iframe.parent().append('<div class="processing-indicator"></div>');

		if ($('#link-adder').length > 0) {
			$('#link-adder')
				.on('click', function(){
					iframe.parent().find('p.error').remove();

					var fname = prompt("Please provide a link:", "http://");

					if (fname) {
						iframe.parent().find('div.processing-indicator').show();
						$.getJSON($(this).attr('data-action') + encodeURIComponent(fname), {}, function(data) {
							iframe.parent().find('div.processing-indicator').hide();
							if (data.success) {
								iframe.attr('src', iframe.attr('src') + '1');
							} else {
								iframe.before('<p class="error">' + data.errors.join('<br />') + '</p>');
							}
						});
					}
				})
				.append(
					'<div class="linker">' +
						'<div class="linker-button"><span>Click to add link</span></div>' + 
					'</div>'
				);

			iframe.attr('src', iframe.attr('src') + '&hideform=1&t=');
		}

		var uploader = new qq.FileUploader({
			element: attach[0],
			action: attach.attr("data-action"),
			multiple: true,
			debug: false,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.attr('data-instructions') + '</span></div>' + 
						'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				iframe.parent().find('p.error').remove();
				iframe.parent().find('div.processing-indicator').show();
			},
			onComplete: function(id, file, response) {
				iframe.parent().find('div.processing-indicator').hide();
				if (response.success) {
					iframe.attr('src', iframe.attr('src') + '1');
				} else {
					iframe.before('<p class="error">' + response.errors.join('<br />') + '</p>');
				}
			}
		});
	}
});
