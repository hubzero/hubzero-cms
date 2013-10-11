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

		if ($('#link-adder').length > 0) {
			$('#link-adder')
				.on('click', function(){
					var fname = prompt("Please provide a link:", "http://");

					if (fname) {
						$.getJSON($(this).attr('data-action') + encodeURIComponent(fname), {}, function(data) {
							if (data.success) {
								iframe.attr('src', iframe.attr('src') + '1');
							}
						});
					}
				})
				.append(
					'<div class="linker">' +
						'<div class="linker-button"><span>Click to add link</span></div>' + 
					'</div>'
				);

			iframe.attr('src', iframe.attr('src') + '&amp;hideform=1&amp;t=');
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
			},
			onComplete: function(id, file, response) {

				iframe.attr('src', iframe.attr('src') + '1');
			}
		});
	}
});