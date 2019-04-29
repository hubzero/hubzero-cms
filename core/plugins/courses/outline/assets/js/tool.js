/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	Hubzero.initApi();
	$('.tool-edit').on('submit', '#select-form', function(e){
		e.preventDefault();
		var projectId = $('#file-selector').data('projectid');
		var selectedItems = $(this).find('input[name="selecteditems"]');
		var formFields = $('.edit-form').serialize();
		formFields += '&project_id=' + projectId + '&selecteditems=' + selectedItems.val();
		$.ajax({
			url: '/api/courses/asset/new',
			data: formFields,
			contentType: 'application/json',
			success: function(response){
				var projectFiles = response.assets.projectFiles;
				var asset_id = response.assets.assets.asset_id;
				addAssetItems(projectFiles, asset_id);
				$('#b-filesave').removeClass('disabled');
				$('#b-filesave').text('Add selected');
				$('#file-selector').find('.type-file').removeClass('selectedfilter');
				selectedItems.val('');
			}
		});
	});
	// Send data
	$('#b-filesave').on('click', function(e){
		e.preventDefault();
		if (!$(this).hasClass('disabled'))
		{
			$(this).addClass('disabled');
			$(this).text('Adding file(s)');
			$('#selecteditems').val(HUB.ProjectFilesFileSelect.collectSelections());
			$('#select-form').submit();
		}
	});

	$('#project-selector').on('change', function(e){
		var projectAlias = $(this).val();
		if (projectAlias == ''){
			$('#content-selector').html('');
			$('#b-filesave').hide();
		} else {
			var url = '/projects/' + projectAlias + '/files';
			var data = {
				action: 'filter',
				ajax: '1',
				no_html: '1'
			}

			if ($('#content-selector').length){
				data.partial = '1';
			}
			$.ajax({
				url: url,
				data: data,
				success: function(response){
					if ($('#content-selector').length) {
						$('#content-selector').html(response);
						url += '?action=filter&ajax=1&no_html=1';
						$('#filterUrl').val(url);
						HUB.ProjectFilesFileSelect.selector();
						HUB.ProjectFilesFileSelect.collapse();
					} else {
						$('#project-selector').after(response);
						$('.abox-controls').html('');
					}
				}
			});
			$('#b-filesave').show();
		}
	});

	$('.fileupload').fileupload({
		dropZone: $('.tool-files-upload'),
		dataType: 'json',
		singleFileUploads: false,
		add: function ( e, data ) {
				data.submit();
		},
		done: function ( e, data ) {
			var files = data.files;
			var asset_id = data.result.assets.assets.asset_id
			addAssetItems(files, asset_id);
		},
		fail: function ( e, data ) {
			$('.title-error').html($.parseJSON(data.jqXHR.responseText).message).show();
			$('html, body').scrollTop(0);
		}
	})
	.hover(function() {
		$(this).siblings('.tool-files-upload').css('opacity', 1);
	}, function() {
		$(this).siblings('.tool-files-upload').css('opacity', 0.7);
	});

	$('.edit-form').submit(function ( e ) {
		e.preventDefault();
		if (typeof CKEDITOR !== 'undefined') {
			for (var instance in CKEDITOR.instances) {
				CKEDITOR.instances[instance].updateElement();
			}
		}
	});

	$('.tool-files-available').on('click', '.tool-files-delete', function() {
		var t    = $(this),
			data = t.parents('.edit-form').serializeArray();

		data.push({name : "filename", value : t.prev('.tool-files-filename').html()});

		$.ajax({
			url        : '/api/courses/asset/deletefile',
			dataType   : "json",
			type       : 'POST',
			cache      : false,
			data       : data,
			success    : function ( data, textStatus, jqXHR ) {
				t.parents('.tool-file').fadeOut(500, function() {
					$(this).remove();
				});
			}
		});
	});
});

function addAssetItems(files, asset_id) {
	var html = '';
	if ($('.tool-files-available ul').length) {
		$.each(files, function ( i, file ) {
			html += '<li class="tool-file">';
			html += '<span class="tool-files-filename">' + file.name + '</span>';
			html += '<div class="tool-files-delete"></div>';
			html += '</li>';
		});

		$('.tool-files-list').prepend(html);
	} else {
		html  = '<ul class="tool-files-list">';
		$.each(files, function ( i, file ) {
			html += '<li class="tool-file">';
			html += '<span class="tool-files-filename">' + file.name + '</span>';
			html += '<div class="tool-files-delete"></div>';
			html += '</li>';
		});
		html += '</ul>';
		$('.tool-files-available').html(html);
	}

	if ($('#asset_id').val() === '') {
		$('#asset_id').val(asset_id);
	}
}

