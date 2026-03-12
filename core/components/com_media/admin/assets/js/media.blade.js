/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

function bindContextModals() {
	// No-op: fancybox modals removed in Blade mode.
	// Path/info links work as regular links.
}

document.addEventListener('DOMContentLoaded', function () {
	var contents = document.getElementById('media-items'),
		layout = document.getElementById('layout'),
		folder = document.getElementById('folder');

	_DEBUG = document.getElementById('system-debug') ? 1 : 0;

	if (!contents) {
		return;
	}

	var isModal = (contents.getAttribute('data-tmpl') == 'component');

	// View switcher
	var views = document.querySelectorAll('.media-files-view');
	views.forEach(function (viewBtn) {
		viewBtn.addEventListener('click', function (e) {
			e.preventDefault();

			views.forEach(function (v) { v.classList.remove('active'); });
			document.querySelectorAll('.media-files').forEach(function (f) {
				f.classList.remove('active');
			});

			this.classList.add('active');

			var view = this.getAttribute('data-view');
			var target = document.getElementById('media-' + view);
			if (target) {
				target.classList.add('active');
			}

			layout.value = view;
		});
	});

	// New folder
	document.querySelectorAll('.media-folder-new').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();

			var title = prompt(this.getAttribute('data-prompt'));
			if (title) {
				var href = this.getAttribute('href').nohtml()
					+ '&layout=' + layout.value
					+ '&parent=' + folder.value
					+ '&foldername=' + title;

				if (_DEBUG) {
					console.log('Creating folder: ' + href);
				}

				fetch(href)
					.then(function (r) { return r.text(); })
					.then(function (response) {
						if (_DEBUG) {
							console.log(response);
						}

						var listUrl = contents.getAttribute('data-list').nohtml()
							+ '&layout=' + layout.value
							+ '&folder=' + folder.value;

						fetch(listUrl)
							.then(function (r) { return r.text(); })
							.then(function (data) {
								if (_DEBUG) {
									console.log(data);
								}
								contents.innerHTML = data;
								bindContextModals();
							});
					});
			}
		});
	});

	// Breadcrumbs navigation
	var breadcrumbBlock = document.querySelector('.media-breadcrumbs-block');
	if (breadcrumbBlock) {
		breadcrumbBlock.addEventListener('click', function (e) {
			var crumb = e.target.closest('.media-breadcrumbs');
			if (!crumb) return;

			e.preventDefault();

			folder.value = crumb.getAttribute('data-folder');

			if (_DEBUG) {
				console.log('Calling: ' + crumb.getAttribute('href').nohtml() + '&layout=' + layout.value);
			}

			var trail = crumb.getAttribute('data-folder').split('/'),
				crumbs = '',
				fld = '',
				href = contents.getAttribute('data-list').nohtml()
					+ '&layout=' + layout.value + '&folder=';

			for (var i = 0; i < trail.length; i++) {
				if (trail[i] == '') {
					continue;
				}

				href += '/' + trail[i];
				fld += '/' + trail[i];

				crumbs += '<span class="icon-chevron-right dir-separator">/</span>';
				crumbs += '<a href="' + href + '" data-folder="' + fld
					+ '" class="media-breadcrumbs folder has-next-button" id="path_'
					+ trail[i] + '">' + trail[i] + '</a>';
			}

			var breadcrumbsEl = document.getElementById('media-breadcrumbs');
			if (breadcrumbsEl) {
				breadcrumbsEl.innerHTML = crumbs;
			}

			fetch(crumb.getAttribute('href').nohtml() + '&layout=' + layout.value)
				.then(function (r) { return r.text(); })
				.then(function (data) {
					contents.innerHTML = data;
					bindContextModals();
				});
		});
	}

	// Delegated events on contents area
	contents.addEventListener('click', function (e) {
		// Folder item click
		var folderItem = e.target.closest('.folder-item');
		if (folderItem) {
			e.preventDefault();

			folder.value = folderItem.getAttribute('data-folder');

			if (_DEBUG) {
				console.log('Calling: ' + folderItem.getAttribute('href').nohtml() + '&layout=' + layout.value);
			}

			var trail = folderItem.getAttribute('data-folder').split('/'),
				crumbs = '',
				fld = '',
				href = contents.getAttribute('data-list').nohtml()
					+ '&layout=' + layout.value + '&folder=';

			for (var i = 0; i < trail.length; i++) {
				if (trail[i] == '') {
					continue;
				}

				href += '/' + trail[i];
				fld += '/' + trail[i];

				crumbs += '<span class="icon-chevron-right dir-separator">/</span>';
				crumbs += '<a href="' + href + '" data-folder="' + fld
					+ '" class="media-breadcrumbs folder has-next-button" id="path_'
					+ trail[i] + '">' + trail[i] + '</a>';
			}

			var breadcrumbsEl = document.getElementById('media-breadcrumbs');
			if (breadcrumbsEl) {
				breadcrumbsEl.innerHTML = crumbs;
			}

			fetch(folderItem.getAttribute('href').nohtml() + '&layout=' + layout.value)
				.then(function (r) { return r.text(); })
				.then(function (data) {
					contents.innerHTML = data;
					bindContextModals();
				});
			return;
		}

		// Document item click (modal mode)
		var docItem = e.target.closest('.doc-item');
		if (docItem && isModal) {
			e.preventDefault();

			var url = docItem.getAttribute('href');

			if (url == '') {
				return;
			}

			var eName = document.getElementById('e_name');
			if (eName) {
				var alt = docItem.getAttribute('title');
				var tag = '<img src="' + url + '" ';

				if (alt != '') {
					tag += 'alt="' + alt + '" ';
				} else {
					tag += 'alt="" ';
				}

				tag += '/>';

				window.parent.jInsertEditorText(tag, eName.value);
			}

			var fieldId = document.getElementById('fieldid');
			if (fieldId) {
				var id = fieldId.value;
				window.parent.document.getElementById(id).value = url;
			}

			// Close the modal if a parent dialog exists
			if (window.parent && window.parent !== window) {
				var dialog = window.frameElement ? window.frameElement.closest('dialog') : null;
				if (dialog) {
					dialog.close();
				}
			}
			return;
		}

		// Options button toggle
		var optionsBtn = e.target.closest('.media-options-btn');
		if (optionsBtn) {
			e.preventDefault();

			var item = optionsBtn.closest('.media-item');
			if (item) {
				item.classList.toggle('ui-activated');
			}
			return;
		}

		// Delete button
		var deleteBtn = e.target.closest('.media-opt-delete');
		if (deleteBtn) {
			e.preventDefault();

			var deleteHref = deleteBtn.getAttribute('href').nohtml();
			if (_DEBUG) {
				console.log('Deleting: ' + deleteHref);
			}

			fetch(deleteHref)
				.then(function (r) { return r.text(); })
				.then(function (response) {
					if (_DEBUG) {
						console.log(response);
					}

					var listUrl = contents.getAttribute('data-list').nohtml()
						+ '&layout=' + layout.value
						+ '&folder=' + folder.value;

					fetch(listUrl)
						.then(function (r) { return r.text(); })
						.then(function (data) {
							if (_DEBUG) {
								console.log(data);
							}
							contents.innerHTML = data;
							bindContextModals();
						});
				});
			return;
		}
	});

	bindContextModals();

	// Sidebar tree navigation
	var mediaTree = document.getElementById('media-tree');
	if (mediaTree) {
		mediaTree.querySelectorAll('a').forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();

				folder.value = this.getAttribute('data-folder');

				if (_DEBUG) {
					console.log('Calling: ' + this.getAttribute('href').nohtml() + '&layout=' + layout.value);
				}

				var trail = this.getAttribute('data-folder').split('/'),
					crumbs = '',
					href = contents.getAttribute('data-list').nohtml()
						+ '&layout=' + layout.value + '&folder=';

				for (var i = 0; i < trail.length; i++) {
					if (trail[i] == '') {
						continue;
					}

					href += '/' + trail[i];

					crumbs += '<span class="icon-chevron-right dir-separator">/</span>';
					crumbs += '<a href="' + href
						+ '" class="media-breadcrumbs folder has-next-button" id="path_'
						+ trail[i] + '">' + trail[i] + '</a>';
				}

				var breadcrumbsEl = document.getElementById('media-breadcrumbs');
				if (breadcrumbsEl) {
					breadcrumbsEl.innerHTML = crumbs;
				}

				fetch(this.getAttribute('href').nohtml() + '&layout=' + layout.value)
					.then(function (r) { return r.text(); })
					.then(function (data) {
						contents.innerHTML = data;
						bindContextModals();
					});
			});
		});

		// Simple tree toggle (replaces jQuery treeview plugin)
		mediaTree.querySelectorAll('li').forEach(function (li) {
			var sublist = li.querySelector('ul');
			if (sublist) {
				sublist.style.display = 'none';
				li.classList.add('collapsed');

				var toggle = li.querySelector('a');
				if (toggle) {
					var toggler = document.createElement('span');
					toggler.className = 'tree-toggle';
					toggler.textContent = '+';
					toggler.style.cursor = 'pointer';
					toggler.style.marginRight = '4px';
					toggler.addEventListener('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						if (sublist.style.display === 'none') {
							sublist.style.display = '';
							li.classList.remove('collapsed');
							toggler.textContent = '\u2212';
						} else {
							sublist.style.display = 'none';
							li.classList.add('collapsed');
							toggler.textContent = '+';
						}
					});
					toggle.parentElement.insertBefore(toggler, toggle);
				}
			}
		});
	}

	// File uploader
	var attach = document.getElementById('ajax-uploader');
	if (attach) {
		var running = 0;
		if (_DEBUG) {
			console.log('Uploading to: ' + attach.getAttribute('data-action').nohtml()
				+ '&layout=' + layout.value + '&folder=' + folder.value);
		}

		var uploader = new qq.FileUploader({
			element: attach,
			action: attach.getAttribute('data-action').nohtml(),
			params: {
				layout: function () {
					return layout.value;
				},
				folder: function () {
					return folder.value;
				}
			},
			multiple: true,
			debug: true,
			template: '<span class="media-btn-tip" data-tip="'
				+ attach.getAttribute('data-instructions-btn')
				+ '"><div class="icon-upload qq-upload-button media-files-action" aria-label="'
				+ attach.getAttribute('data-instructions-btn')
				+ '"><span>' + attach.getAttribute('data-instructions-btn')
				+ '</span></div></span>'
				+ '<div class="qq-uploader">'
				+ '<div class="qq-upload-drop-area"><span>'
				+ attach.getAttribute('data-instructions') + '</span></div>'
				+ '<ul class="qq-upload-list"></ul>'
				+ '</div>',
			onSubmit: function (id, file) {
				running++;
			},
			onComplete: function (id, file, response) {
				running--;

				if (running == 0) {
					var uploadList = document.querySelector('ul.qq-upload-list');
					if (uploadList) {
						uploadList.innerHTML = '';
					}
				}

				if (_DEBUG) {
					console.log('Calling: ' + contents.getAttribute('data-list').nohtml()
						+ '&layout=' + layout.value + '&folder=' + folder.value);
				}

				var listUrl = contents.getAttribute('data-list').nohtml()
					+ '&layout=' + layout.value
					+ '&folder=' + folder.value;

				fetch(listUrl)
					.then(function (r) { return r.text(); })
					.then(function (data) {
						contents.innerHTML = data;
						bindContextModals();
					});
			}
		});
	}
});
