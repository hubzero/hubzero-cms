
/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/wiki.jquery.js
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
//  Courses outline javascript
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($) {
	$('.fileupload').fileupload({
		dropZone: $('.wiki-files-upload'),
		dataType: 'json',
		singleFileUploads: false,
		add: function ( e, data ) {
			if ($('.wiki-title').val() === '') {
				$('.title-error').html('Please provide a title first').show();
				$('html, body').scrollTop(0);
			} else {
				$('.title-error').hide();
				data.submit();
			}
		},
		done: function ( e, data ) {
			var html = '';
			if ($('.wiki-files-available ul').length) {
				$.each(data.files, function ( i, file ) {
					html += '<li class="wiki-file">'+file.name+"</li>";
				});

				$('.wiki-files-list').prepend(html);
			} else {
				html  = '<ul class="wiki-files-list">';
				$.each(data.files, function ( i, file ) {
					html += '<li class="wiki-file">'+file.name+"</li>";
				});
				html += '</ul>';
				$('.wiki-files-available').html(html);
			}

			if ($('#asset_id').val() === '') {
				$('#asset_id').val(data.result.assets.assets.asset_id);
			}
		},
		fail: function ( e, data ) {
			$('.title-error').html(data.jqXHR.responseText).show();
			$('html, body').scrollTop(0);
		}
	})
	.hover(function() {
		$(this).siblings('.wiki-files-upload').css('opacity', 1);
	}, function() {
		$(this).siblings('.wiki-files-upload').css('opacity', 0.7);
	});

	$('.edit-form').submit(function ( e ) {
		for ( var instance in CKEDITOR.instances ) {
			CKEDITOR.instances[instance].updateElement();
		}
	});
});