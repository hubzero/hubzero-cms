/**
 * @package     hubzero-cms
 * @file        components/com_resources/assets/js/fileupload.jquery.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		iframe = $('#attaches');

	if ($("#ajax-uploader").length) {
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

			iframe.on('load', function() {
				iframe.contents().find('.list .up').each(function(index) {
					$(this).attr('href',  $(this).attr('href') + '&hideform=1')
				});
				iframe.contents().find('.list .down').each(function(index) {
					$(this).attr('href',  $(this).attr('href') + '&hideform=1')
				});
			});

			iframe.attr('src', iframe.attr('src') + '&hideform=1&t=');
		}

		var uploader = new qq.FileUploader({
			element: $("#ajax-uploader")[0],
			action: $("#ajax-uploader").attr("data-action"),
			multiple: true,
			debug: false,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
						'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				iframe.parent().find('p.error').remove();
				iframe.parent().find('div.processing-indicator').show();
			},
			onComplete: function(id, file, response) {
				console.log(response);
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