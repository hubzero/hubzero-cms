/**
 * @package     hubzero-cms
 * @file        plugins/courses/forum/forum.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
var HUB = HUB || {};

if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
//  Forum scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;
	
	$('ul.manager-options a.delete').each(function(i, el) {
		$(el).on('click', function(e) {
			var res = confirm('Are you sure you wish to delete this document?');
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
			$('#file-uploader').attr('data-section', section);
			console.log($('#file-uploader').attr('data-list') + section);
			$.get($('#file-uploader').attr('data-list') + section, {}, function(data) {
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
			var uploader = new qq.FileUploader({
				element: uploader[0],
				action: uploader.attr('data-action') + uploader.attr('data-section'),
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
					$('.qq-upload-list').empty();
					$.get($('#file-uploader').attr('data-list') + uploader.attr('data-section'), {}, function(data) {
						filelist.html(data);
					});
				}
			});
		}
	}
});