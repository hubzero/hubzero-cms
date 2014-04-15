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

HUB.CollectionsFileUpload = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		if ($("#ajax-uploader").length) {

			$('a.delete')
				.unbind('click')
				.on('click', function(event){
					event.preventDefault();
					$.get($(this).attr('href'), {}, function(data) {
						HUB.CollectionsFileUpload.updateFileList();
						//$('#ajax-uploader-list').sortable('enable');
					});
				});

			if ($('#link-adder').length > 0) {
				$('#link-adder').append(
					'<div class="linker">' +
						'<div class="linker-button"><span>Click to add link</span></div>' + 
					'</div>'
				);
				$('.linker-button').on('click', function(){
					/*$('#ajax-uploader-list').append(
						'<p class="item-asset">' +
							'<span class="asset-file">' + 
								'<input type="text" name="asset[' + i + '][description]" size="35" value="http://" />' +
								'<input type="text" name="asset[' + i + '][description]" size="35" value="http://" />' +
							'</span>' + 
						'</p>'
					);*/
					//console.log($('#link-adder').attr('data-action') + $('#field-dir').val());
					$.get($('#link-adder').attr('data-action') + $('#field-dir').val(), {}, function(data){
						var response = jQuery.parseJSON(data);

						if (response.id != $('#field-dir').val()) {
							$('#field-id').val(response.id);
							$('#field-dir').val(response.id);
						}

						HUB.CollectionsFileUpload.updateFileList();
					});
				});
			}

			var uploader = new qq.FileUploader({
				element: $("#ajax-uploader")[0],
				action: $("#ajax-uploader").attr("data-action"), // + $('#field-dir').val()
				params: {dir: $('#field-dir').val()},
				multiple: true,
				debug: true,
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

					HUB.CollectionsFileUpload.updateFileList();
				}
			});
		}
	},
	
	updateFileList: function() {
		var $ = HUB.CollectionsFileUpload.jQuery;

		if ($('#ajax-uploader')) {
			$('#ajax-uploader-list').sortable('enable');

			$.get($('#ajax-uploader').attr('data-list') + $('#field-dir').val(), {}, function(data) {
				$('#ajax-uploader-list')
					.html(data)
					.sortable('enable');
				$('a.delete')
					.unbind('click')
					.on('click', function(event){
						event.preventDefault();
						//console.log($(this).attr('href'));
						$.get($(this).attr('href'), {}, function(data) {
							//console.log(data);
							HUB.CollectionsFileUpload.updateFileList();
						});
					});
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.CollectionsFileUpload.initialize();
});