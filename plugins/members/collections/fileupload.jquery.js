/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	if ($("#ajax-uploader").length) {

		$('#ajax-uploader-list')
			.on('click', 'a.delete', function (e){
				e.preventDefault();
				if ($(this).attr('data-id')) {
					$.get($(this).attr('href'), {}, function(data) {});
				}
				$(this).parent().parent().remove();
			});

		if ($('#link-adder').length > 0) {
			$('#link-adder').append(
				'<div class="linker">' +
					'<div class="linker-button"><span>Click to add link</span></div>' + 
				'</div>'
			);
			$('.linker-button').on('click', function(){
				var i = $('.item-asset').length;
				$('#ajax-uploader-list').append(
					'<p class="item-asset">' +
						'<span class="asset-handle"></span>' +
						'<span class="asset-file">' +
							'<input type="text" name="assets[' + i + '][filename]" size="35" value="http://" placeholder="http://" />' +
						'</span>' +
						'<span class="asset-description">' +
							'<input type="hidden" name="assets[' + i + '][type]" value="link" />' +
							'<input type="hidden" name="assets[' + i + '][id]" value="0" />' +
							'<a class="delete" href="/collections/delete/asset/" data-id="" title="Delete this asset">delete</a>' +
						'</span>' +
					'</p>'
				);
			});
		}

		var uploader = new qq.FileUploader({
			element: $("#ajax-uploader")[0],
			action: $("#ajax-uploader").attr("data-action"), // + $('#field-dir').val()
			params: {dir: $('#field-dir').val(), i: $('.item-asset').length},
			multiple: true,
			debug: false,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
						'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				//$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
			},
			onComplete: function(id, file, response) {
				if (response.id != $('#field-dir').val()) {
					$('#field-id').val(response.id);
					$('#field-dir').val(response.id);

					uploader.setParams({dir: $('#field-dir').val()});
				}
				// HTML entities had to be encoded for the JSON or IE 8 went nuts. So, now we have to decode it.
				response.html = response.html.replace(/&gt;/g, '>');
				response.html = response.html.replace(/&lt;/g, '<');
				$('#ajax-uploader-list').append(response.html);
			}
		});
	}
});