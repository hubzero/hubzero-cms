/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

function bindContextModals()
{
	$('.media-opt-path,.media-opt-info').fancybox({
		type: 'ajax',
		width: 700,
		height: 'auto',
		autoSize: false,
		fitToView: false,
		titleShow: false,
		beforeLoad: function() {
			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('href').nohtml());
			}
			$(this).attr('href', $(this).attr('href').nohtml());
		}
	});
}

jQuery(document).ready(function($){
	var contents = $('#media-items'),
		layout = $('#layout'),
		folder = $('#folder');

	_DEBUG = $('#system-debug').length;

	if (!contents.length) {
		return;
	}

	var isModal = (contents.attr('data-tmpl') == 'component');

	var views = $('.media-files-view');
	$('.media-files-view').on('click', function(e){
		e.preventDefault();

		views.removeClass('active');
		$('.media-files').removeClass('active');

		$(this).addClass('active');

		var view = $(this).attr('data-view');
		$('#media-' + view).addClass('active');

		layout.val(view);
	});

	$('.media-folder-new').on('click', function(e){
		e.preventDefault();

		var title = prompt($(this).attr('data-prompt'));
		if (title) {
			var href = $(this).attr('href').nohtml() + '&layout=' + layout.val() + '&parent=' + folder.val() + '&foldername=' + title;
			if (_DEBUG) {
				window.console && console.log('Creating folder: ' + href);
			}

			$.get(href, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$.get(contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=' + folder.val(), function(data){
					if (_DEBUG) {
						window.console && console.log(data);
					}
					contents.html(data);

					bindContextModals();
				});
			});
		}
	});

	contents
		.on('click', '.folder-item', function(e){
			e.preventDefault();

			folder.val($(this).attr('data-folder'));

			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('href').nohtml() + '&layout=' + layout.val());
			}

			var trail = $(this).attr('data-folder').split('/'),
				crumbs = '',
				href = contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=';

			for (var i = 0; i < trail.length; i++)
			{
				if (trail[i] == '')
				{
					continue;
				}

				href += '/'  + trail[i];

				crumbs += '<span class="icon-chevron-right dir-separator">/</span>';
				crumbs += '<a href="' + href + '" class="media-breadcrumbs folder has-next-button" id="path_' + trail[i] + '">' + trail[i] + '</a>';
			}

			$('#media-breadcrumbs').html(crumbs);

			$.get($(this).attr('href').nohtml() + '&layout=' + layout.val(), function(data){
				contents.html(data);

				bindContextModals();
			});
		})
		.on('click', '.doc-item', function(e){
			if (isModal) {
				e.preventDefault();

				// Get the image tag field information
				var url = $(this).attr('href');

				if (url == '') {
					return false;
				}

				if ($('#e_name').length) {
					var alt = $(this).attr('title');
					var tag = '<img src="' + url + '" ';

					// Set alt attribute
					if (alt != '') {
						tag += 'alt="' + alt + '" ';
					} else {
						tag += 'alt="" ';
					}

					tag += '/>';

					window.parent.jInsertEditorText(tag, $('#e_name').val());
				}
				if ($('#fieldid').length) {
					var id = $('#fieldid').val();
					window.parent.document.getElementById(id).value = url;
					// Update preview area
					//window.parent.document.getElementById(id + '_preview_empty').style.display = 'hidden';
					//window.parent.document.getElementById(id + '_preview_img').style.display = 'block';
					//window.parent.document.getElementById(id + '_preview').src = url;
				}
				window.parent.$.fancybox.close();
				return false;
			}
		})
		.on('click', '.media-options-btn', function(e){
			e.preventDefault();

			var item = $(this).closest('.media-item');

			item.toggleClass('ui-activated');
		})
		.on('click', '.media-opt-delete', function(e){
			e.preventDefault();

			var href = $(this).attr('href').nohtml();
			if (_DEBUG) {
				window.console && console.log('Deleting: ' + href);
			}

			$.get(href, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$.get(contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=' + folder.val(), function(data){
					if (_DEBUG) {
						window.console && console.log(data);
					}
					contents.html(data);

					bindContextModals();
				});
			});
		});

	bindContextModals();

	$('#media-tree')
		.find('a')
		.on('click', function(e){
			e.preventDefault();

			folder.val($(this).attr('data-folder'));

			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('href').nohtml() + '&layout=' + layout.val());
			}

			var trail = $(this).attr('data-folder').split('/'),
				crumbs = '',
				href = contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=';

			for (var i = 0; i < trail.length; i++)
			{
				if (trail[i] == '')
				{
					continue;
				}

				href += '/'  + trail[i];

				crumbs += '<span class="icon-chevron-right dir-separator">/</span>';
				crumbs += '<a href="' + href + '" class="media-breadcrumbs folder has-next-button" id="path_' + trail[i] + '">' + trail[i] + '</a>';
			}

			$('#media-breadcrumbs').html(crumbs);

			$.get($(this).attr('href').nohtml() + '&layout=' + layout.val(), function(data){
				contents.html(data);

				bindContextModals();
			});
		});

	$('#media-tree').treeview({
		collapsed: true
	});

	var attach = $("#ajax-uploader");
	if (attach.length) {
		var running = 0;
		if (_DEBUG) {
			window.console && console.log('Uploading to: ' + attach.attr('data-action').nohtml() + '&layout=' + layout.val() + '&folder=' + folder.val());
		}

		var uploader = new qq.FileUploader({
			element: attach[0],
			action: attach.attr('data-action').nohtml(),
			params: {
				layout: function() {
					return layout.val();
				},
				folder: function() {
					return folder.val();
				}
			},
			multiple: true,
			debug: true,
			template: '<div class="icon-upload qq-upload-button media-files-action hasTip" title="' + attach.attr('data-instructions-btn') + '"><span>' + attach.attr('data-instructions-btn') + '</span></div>' + 
					'<div class="qq-uploader">' +
						'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				running++;
			},
			onComplete: function(id, file, response) {
				running--;

				if (running == 0) {
					$('ul.qq-upload-list').empty();
				}

				if (_DEBUG) {
					window.console && console.log('Calling: ' + contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=' + folder.val());
				}
				$.get(contents.attr('data-list').nohtml() + '&layout=' + layout.val() + '&folder=' + folder.val(), function(data){
					contents.html(data);

					bindContextModals();
				});
			}
		});
	}
});
