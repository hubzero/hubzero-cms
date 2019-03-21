/**
 * @package     hubzero-cms
 * @file        components/com_media/admin/assets/js/media.js
 * @copyright   Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

jQuery(document).ready(function($){
	var contents = $('#media-items'),
		layout = $('#layout'),
		folder = $('#folder');

	_DEBUG = $('#system-debug').length;

	if (!contents.length) {
		return;
	}

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
				});
			});
		}
	});

	contents
		/*.on('click', '.media-preview', function(e){
			e.preventDefault();

			$(this).closest('.media-item').toggleClass('ui-selected');
		})*/
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
			});
		})
		.on('click', '.media-options-btn', function(e){
			e.preventDefault();

			var item = $(this).closest('.media-item');

			/*if (!item.hasClass('ui-activated')) {
				$('.media-item').removeClass('ui-activated');
				item.toggleClass('ui-selected');
			}*/

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
				});
			});
		});

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
			action: attach.attr('data-action').nohtml(), // + '&layout=' + layout.val() + '&folder=' + folder.val(),
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
				});
			}
		});
	}
});
