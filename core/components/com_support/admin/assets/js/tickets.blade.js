/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

String.prototype.tmpl = function (tmpl) {
	if (typeof (tmpl) == 'undefined' || !tmpl) {
		tmpl = 'component';
	}
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'tmpl=' + tmpl;
};
String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

document.addEventListener('DOMContentLoaded', function () {
	_DEBUG = document.getElementById('system-debug') ? 1 : 0;

	// Modal (batch) save
	var btnSave = document.getElementById('btn-save');
	if (btnSave) {
		btnSave.addEventListener('click', function () {
			var form = document.getElementById('component-form');
			var formData = new FormData(form);
			var params = new URLSearchParams(formData).toString();

			fetch(this.getAttribute('data-action'), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: params
			})
			.then(function (response) { return response.text(); })
			.then(function (data) {
				var temp = document.createElement('div');
				temp.innerHTML = data;

				var queriesEl = temp.querySelector('#query-list');
				var ticketsEl = temp.querySelector('#tktlist');

				var parentQueryList = window.parent.document.getElementById('query-list');
				var parentTktList = window.parent.document.getElementById('tktlist');

				if (parentQueryList && queriesEl) {
					parentQueryList.innerHTML = queriesEl.innerHTML;
				}
				if (parentTktList && ticketsEl) {
					parentTktList.innerHTML = ticketsEl.innerHTML;
				}

				window.top.setTimeout(function () {
					window.parent.postMessage('admin-popup-close', '*');
				}, 700);
			});
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function () {
			window.parent.postMessage('admin-popup-close', '*');
		});
	}

	// Ticket list pane sizing
	var panes = document.getElementById('panes');

	if (panes) {
		var top = panes.getBoundingClientRect().top + window.pageYOffset,
			h = window.innerHeight;
		var paneEls = document.querySelectorAll('.pane');
		for (var p = 0; p < paneEls.length; p++) {
			paneEls[p].style.height = (h - top) + 'px';
		}
	}

	var queries = document.getElementById('queries');
	if (queries) {
		// Delegated click handlers on #queries
		queries.addEventListener('click', function (e) {
			var target = e.target;

			// Folder toggle
			var folder = target.closest('span.folder');
			if (folder) {
				var parent = folder.parentNode;
				if (parent.classList.contains('open')) {
					parent.classList.remove('open');
				} else {
					parent.classList.add('open');
				}
				return;
			}

			// Delete query
			var deleteLink = target.closest('a.delete');
			if (deleteLink) {
				e.preventDefault();

				var res = confirm(deleteLink.getAttribute('data-confirm'));
				if (!res) {
					return false;
				}

				if (_DEBUG) {
					window.console && console.log('Calling: ' + deleteLink.getAttribute('href').nohtml());
				}

				fetch(deleteLink.getAttribute('href').nohtml())
					.then(function (response) { return response.text(); })
					.then(function (responseText) {
						if (_DEBUG) {
							window.console && console.log(responseText);
						}
						var queryList = document.getElementById('query-list');
						if (queryList) {
							queryList.innerHTML = responseText;
						}
					});

				return false;
			}

			// Edit folder
			var editFolder = target.closest('a.editfolder');
			if (editFolder) {
				e.preventDefault();

				var folderId = editFolder.getAttribute('data-id');
				var folderTitle = document.getElementById(folderId + '-title');

				var title = prompt(editFolder.getAttribute('data-prompt'), folderTitle ? folderTitle.textContent : '');
				if (title) {
					fetch(editFolder.getAttribute('data-href').nohtml() + '&fields[title]=' + encodeURIComponent(title))
						.then(function () {
							if (folderTitle) {
								folderTitle.textContent = title;
							}
						});
				}
				return;
			}
		});
	}

	// New folder
	var newFolder = document.getElementById('new-folder');
	if (newFolder) {
		newFolder.addEventListener('click', function (e) {
			e.preventDefault();

			var title = prompt(this.getAttribute('data-prompt'));
			if (title) {
				if (_DEBUG) {
					window.console && console.log('Calling: ' + this.getAttribute('data-href').nohtml() + '&fields[title]=' + title);
				}

				fetch(this.getAttribute('data-href').nohtml() + '&fields[title]=' + encodeURIComponent(title))
					.then(function (response) { return response.text(); })
					.then(function (responseText) {
						if (_DEBUG) {
							window.console && console.log(responseText);
						}
						var queryList = document.getElementById('query-list');
						if (queryList) {
							queryList.innerHTML = responseText;
						}
					});
			}
		});
	}

	// Ticket list checkbox toggle
	var tktlist = document.getElementById('tktlist');
	if (tktlist) {
		tktlist.addEventListener('change', function (e) {
			var input = e.target;
			if (input.tagName !== 'INPUT') return;

			var parent = input.closest('li');
			if (!parent) return;

			if (input.checked) {
				if (!parent.classList.contains('ui-selected')) {
					parent.classList.add('ui-selected');
				}
			} else {
				if (parent.classList.contains('ui-selected')) {
					parent.classList.remove('ui-selected');
				}
			}
		});
	}

	// Ticket panel — delegated events
	var ticket = document.getElementById('ticket');
	if (ticket) {
		ticket.addEventListener('change', function (e) {
			var target = e.target;
			if (target.id === 'comment-field-template') {
				var co = document.getElementById('comment-field-comment');

				if (target.value != 'mc') {
					var tmplEl = document.getElementById(target.value);
					if (tmplEl) {
						co.value = tmplEl.value;
					}
				} else {
					co.value = '';
				}
			}
		});

		ticket.addEventListener('click', function (e) {
			var target = e.target;
			if (target.id === 'comment-field-access') {
				var es = document.getElementById('email_submitter');
				if (!es) return;

				if (target.checked) {
					if (es.checked === true) {
						es.checked = false;
						es.disabled = true;
					}
				} else {
					es.disabled = false;
				}
			}
		});
	}

	// Search clear button
	var clear = document.getElementById('clear-search'),
		sinput = document.getElementById('filter_search');

	if (sinput) {
		if (!clear) {
			clear = document.createElement('span');
			clear.id = 'clear-search';
			clear.style.display = 'none';
			clear.addEventListener('click', function () {
				sinput.value = '';
				var ticketForm = document.getElementById('ticketForm');
				if (ticketForm) {
					ticketForm.submit();
				}
			});
			var filterBar = document.getElementById('filter-bar');
			if (filterBar) {
				filterBar.appendChild(clear);
			}
		}

		if (sinput.value != '') {
			clear.style.display = '';
		}

		sinput.addEventListener('keyup', function () {
			if (this.value != '') {
				if (clear.style.display === 'none') {
					clear.style.display = '';
				}
			} else {
				clear.style.display = 'none';
			}
		});
	}
});

function applySortable() {
	// Sortable requires jQuery UI — gracefully skip if unavailable.
	// Drag-and-drop reordering is a non-essential enhancement.
	if (typeof jQuery === 'undefined' || !jQuery.ui || !jQuery.ui.sortable) {
		return;
	}

	jQuery('ul.queries').sortable({
		connectWith: 'ul.queries',
		update: function (e, ui) {
			var col = [];

			jQuery('ul.queries').each(function (i, el) {
				var ul = jQuery(el),
					folder = parseInt(ul.attr('id').split('_')[1]);

				ul.find('li').each(function (k, elm) {
					col.push(folder + '_' + jQuery(elm).attr('id').split('_')[1]);
				});
			});

			var queriesEl = document.getElementById('queries');
			var updateUrl = queriesEl ? queriesEl.getAttribute('data-update') : '';

			if (_DEBUG) {
				window.console && console.log('Calling: ' + updateUrl.nohtml() + '&queries[]=' + col.join('&queries[]='));
			}

			fetch(updateUrl.nohtml() + '&queries[]=' + col.join('&queries[]='))
				.then(function (response) { return response.json(); })
				.then(function (response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
		}
	});
}
