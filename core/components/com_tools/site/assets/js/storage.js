/**
 * Storage manager -- usage gauge animation + live refresh.
 *
 * The page renders correct usage server-side (the ring arc and stat values are
 * already right with JS off); this only animates them in, and can re-sync from
 * the read-only usage API (GET /api/tools/storage/usage). No behavior depends
 * on it -- progressive enhancement over the plain page.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
	'use strict';

	var DURATION = 900;
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function ready(fn) {
		if (document.readyState !== 'loading') { fn(); }
		else { document.addEventListener('DOMContentLoaded', fn); }
	}

	function fmtBytes(b) {
		if (!b || b < 0) { return '0 B'; }
		var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		var i = Math.floor(Math.log(b) / Math.log(1024));
		i = Math.max(0, Math.min(i, units.length - 1));
		var v = b / Math.pow(1024, i);
		return (i === 0 ? v : v.toFixed(v < 10 ? 1 : 0)) + ' ' + units[i];
	}

	function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

	var ICONS = {
		folder: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
		file:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>',
		trash:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>',
		pencil: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>',
		up:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 17v-2a4 4 0 0 0-4-4H6"/><path d="M10 7 5 11l5 4"/></svg>'
	};

	function icon(name) {
		var s = document.createElement('span');
		s.className = 'sb-ico';
		s.setAttribute('aria-hidden', 'true');
		s.innerHTML = ICONS[name] || '';
		return s;
	}

	// AJAX file browser over GET /files and DELETE /delete.
	function initBrowser(apiBase, refreshUsage) {
		var wrap = document.getElementById('storage-files');
		if (!wrap || !apiBase || !window.fetch) {
			// No fetch: reveal the <noscript> iframe fallback instead of a dead panel.
			if (wrap) {
				var ns = wrap.querySelector('noscript');
				if (ns) { wrap.innerHTML = ns.textContent; }
			}
			return;
		}

		var crumbsEl  = document.getElementById('sb-crumbs');
		var listEl    = document.getElementById('sb-list');
		var loadingEl = document.getElementById('sb-loading');
		var emptyEl   = document.getElementById('sb-empty');
		var nomatchEl = document.getElementById('sb-nomatch');
		var errorEl   = document.getElementById('sb-error');
		var refreshEl = document.getElementById('sb-refresh');
		var filterEl  = document.getElementById('sb-filter');
		var bulkEl    = document.getElementById('sb-bulk');
		var selallEl  = document.getElementById('sb-selall');
		var countEl   = document.getElementById('sb-count');
		var bulkDelEl = document.getElementById('sb-bulk-del');
		var newFolderEl = document.getElementById('sb-newfolder');
		var uploadBtnEl = document.getElementById('sb-upload-btn');
		var fileInputEl = document.getElementById('sb-file-input');
		var dropEl      = document.getElementById('sb-drop');
		var progressEl  = document.getElementById('sb-progress');
		var progBarEl   = document.getElementById('sb-progress-bar');
		var progTextEl  = document.getElementById('sb-progress-text');

		var S = {};
		try { S = JSON.parse(document.getElementById('storage-strings').textContent); } catch (e) { S = {}; }

		var currentDir = '';
		var lastData   = { folders: [], files: [] };
		var selected   = {};      // key -> { name, type }
		var filterText = '';

		function show(el, on) { if (el) { el.hidden = !on; } }
		function fmt(tmpl, n) { return String(tmpl || '%s').replace('%s', n); }
		function key(type, name) { return type + '' + name; }

		function noAction(extra) {
			var s = document.createElement('span');
			s.className = 'sb-noaction' + (extra ? ' ' + extra : '');
			return s;
		}

		function crumb(name, dir, isCurrent) {
			var el;
			if (isCurrent) {
				el = document.createElement('span');
			} else {
				el = document.createElement('button');
				el.type = 'button';
				el.className = 'sb-crumb';
				el.addEventListener('click', function () { load(dir); });
			}
			el.textContent = name;
			return el;
		}

		function renderCrumbs(breadcrumb) {
			crumbsEl.textContent = '';
			var atRoot = !breadcrumb || breadcrumb.length === 0;
			crumbsEl.appendChild(crumb(S.home || 'Home', '', atRoot));
			(breadcrumb || []).forEach(function (c, i) {
				var sep = document.createElement('span');
				sep.className = 'sb-sep';
				sep.textContent = '/';
				sep.setAttribute('aria-hidden', 'true');
				crumbsEl.appendChild(sep);
				crumbsEl.appendChild(crumb(c.name, c.dir, i === breadcrumb.length - 1));
			});
		}

		function delBtn(name, type) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'sb-del';
			b.title = S['delete'] || 'Delete';
			b.setAttribute('aria-label', (S['delete'] || 'Delete') + ' ' + name);
			b.appendChild(icon('trash'));
			b.addEventListener('click', function (e) {
				e.stopPropagation();
				var tmpl = (type === 'folder') ? (S.confirmFolder || 'Delete the folder %s?') : (S.confirmFile || 'Delete the file %s?');
				if (!window.confirm(fmt(tmpl, name))) { return; }
				del(name, type, b);
			});
			return b;
		}

		function renameBtn(name, type) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'sb-ren';
			b.title = S.rename || 'Rename';
			b.setAttribute('aria-label', (S.rename || 'Rename') + ' ' + name);
			b.appendChild(icon('pencil'));
			b.addEventListener('click', function (e) {
				e.stopPropagation();
				var nn = window.prompt(S.renamePrompt || 'New name:', name);
				if (nn === null) { return; }
				nn = nn.trim();
				if (!nn || nn === name) { return; }
				b.disabled = true;
				renameReq(name, nn, type)
					.then(function () { load(currentDir); })
					.catch(function () { b.disabled = false; show(errorEl, true); });
			});
			return b;
		}

		// Selection is index-based over the currently rendered rows, so both the
		// checkboxes (keyboard: Tab + Space) and mouse multiselect (click /
		// Ctrl-click / Shift-range) drive the same state -- WCAG 2.1 keyboard
		// operability comes from the checkboxes + buttons; the mouse gestures are
		// a pure enhancement layered on top.
		var rows = [];
		var anchorIdx = -1;

		function rowKey(r) { return key(r.type, r.name); }
		function isSel(r) { return !!selected[rowKey(r)]; }

		function applyRowUI() {
			rows.forEach(function (r) {
				if (r.checkbox) { r.checkbox.checked = isSel(r); }
				r.el.classList.toggle('is-selected', isSel(r));
			});
			updateBulk();
		}

		function setSel(idx, on) {
			var r = rows[idx];
			if (!r || !r.selectable) { return; }
			if (on) { selected[rowKey(r)] = { name: r.name, type: r.type }; }
			else { delete selected[rowKey(r)]; }
		}

		function selectOnly(idx) { selected = {}; setSel(idx, true); anchorIdx = idx; applyRowUI(); }

		function toggleIdx(idx) {
			var r = rows[idx];
			if (!r || !r.selectable) { return; }
			setSel(idx, !isSel(r)); anchorIdx = idx; applyRowUI();
		}

		function selectRange(idx) {
			if (anchorIdx < 0) { selectOnly(idx); return; }
			var a = Math.min(anchorIdx, idx), b = Math.max(anchorIdx, idx);
			selected = {};
			for (var i = a; i <= b; i++) { setSel(i, true); }
			applyRowUI();
		}

		function selectAllVisible(on) {
			for (var i = 0; i < rows.length; i++) { setSel(i, on); }
			applyRowUI();
		}

		// One row: [checkbox] [delete] [icon+name] [size]
		function buildRow(item, type, idx) {
			var deletable = (type === 'file') ? true : !!item.deletable;

			var li = document.createElement('li');
			li.className = 'sb-item sb-' + type;

			var rec = { el: li, name: item.name, type: type, selectable: deletable, checkbox: null };

			// checkbox (selection; keyboard path)
			if (deletable) {
				var cb = document.createElement('input');
				cb.type = 'checkbox';
				cb.className = 'sb-check';
				cb.setAttribute('aria-label', 'Select ' + item.name);
				cb.addEventListener('change', function () { setSel(idx, cb.checked); anchorIdx = idx; applyRowUI(); });
				li.appendChild(cb);
				rec.checkbox = cb;
			} else {
				li.appendChild(noAction('sb-check-none'));
			}

			// actions (rename + delete), leading, right next to the name
			if (deletable) {
				var actions = document.createElement('span');
				actions.className = 'sb-actions';
				actions.appendChild(renameBtn(item.name, type));
				actions.appendChild(delBtn(item.name, type));
				li.appendChild(actions);
			} else {
				li.appendChild(noAction('sb-actions-none'));
			}

			// name (folder navigates; file is static)
			var name;
			if (type === 'folder') {
				name = document.createElement('button');
				name.type = 'button';
				name.className = 'sb-open';
				name.addEventListener('click', function (e) {
					// modified click selects instead of opening (row handler does it)
					if (e.shiftKey || e.ctrlKey || e.metaKey) { return; }
					load(item.dir);
				});
			} else {
				name = document.createElement('span');
				name.className = 'sb-open sb-static';
			}
			name.appendChild(icon(type === 'folder' ? 'folder' : 'file'));
			var nm = document.createElement('span');
			nm.className = 'sb-name';
			nm.textContent = item.name;
			name.appendChild(nm);
			li.appendChild(name);

			// size (files only)
			var size = document.createElement('span');
			size.className = 'sb-size';
			size.textContent = (type === 'file') ? (item.size_h || '') : '';
			li.appendChild(size);

			// Mouse multiselect (enhancement)
			li.addEventListener('mousedown', function (e) { if (e.shiftKey) { e.preventDefault(); } });
			li.addEventListener('click', function (e) {
				if (e.target.closest('.sb-check') || e.target.closest('.sb-del')) { return; }
				var onOpen = e.target.closest('.sb-open');
				var mod = e.shiftKey || e.ctrlKey || e.metaKey;
				if (type === 'folder' && onOpen && !mod) { return; } // plain click opens the folder
				if (!deletable) { return; }                          // data/sessions: not selectable
				e.preventDefault();
				if (e.shiftKey) { selectRange(idx); }
				else if (e.ctrlKey || e.metaKey) { toggleIdx(idx); }
				else { selectOnly(idx); }
			});

			rows.push(rec);
			return li;
		}

		// A ".." row to step up to the parent (nav only; not selectable/filtered).
		function parentRow(parentDir) {
			var li = document.createElement('li');
			li.className = 'sb-item sb-folder sb-parent';
			li.appendChild(noAction('sb-check-none'));   // checkbox column
			li.appendChild(noAction('sb-actions-none'));  // actions column

			var open = document.createElement('button');
			open.type = 'button';
			open.className = 'sb-open';
			open.setAttribute('aria-label', S.parent || 'Go to parent folder');
			open.appendChild(icon('up'));
			var nm = document.createElement('span');
			nm.className = 'sb-name';
			nm.textContent = '..';
			open.appendChild(nm);
			open.addEventListener('click', function () { load(parentDir); });
			li.appendChild(open);

			li.appendChild(document.createElement('span')).className = 'sb-size';
			return li;
		}

		// (Re)build the list from lastData applying the current filter.
		function buildList() {
			listEl.textContent = '';
			rows = [];
			anchorIdx = -1;

			// Parent link (always shown when not at the storage root)
			if (currentDir !== '') {
				var parent = (currentDir.indexOf('/') !== -1) ? currentDir.slice(0, currentDir.lastIndexOf('/')) : '';
				listEl.appendChild(parentRow(parent));
			}

			var q = filterText;
			var total = 0, shown = 0, idx = 0;

			function add(item, type) {
				total++;
				if (q && item.name.toLowerCase().indexOf(q) === -1) { return; }
				shown++;
				listEl.appendChild(buildRow(item, type, idx++));
			}
			(lastData.folders || []).forEach(function (f) { add(f, 'folder'); });
			(lastData.files || []).forEach(function (f) { add(f, 'file'); });

			show(emptyEl, total === 0);
			show(nomatchEl, total > 0 && shown === 0);
			applyRowUI();
		}

		function visibleChecks() { return listEl.querySelectorAll('.sb-check'); }

		function updateBulk() {
			var sel = Object.keys(selected).length;
			var vis = visibleChecks().length;

			show(bulkEl, vis > 0);
			if (countEl) { countEl.textContent = fmt(S.selected || '%s selected', sel); }
			if (bulkDelEl) { bulkDelEl.disabled = (sel === 0); }

			if (selallEl) {
				var checkedVis = listEl.querySelectorAll('.sb-check:checked').length;
				selallEl.checked = (vis > 0 && checkedVis === vis);
				selallEl.indeterminate = (checkedVis > 0 && checkedVis < vis);
			}
		}

		function render(data) {
			lastData = { folders: data.folders || [], files: data.files || [] };
			renderCrumbs(data.breadcrumb);
			buildList();
		}

		function load(dir) {
			currentDir = dir || '';
			selected = {};
			filterText = '';
			if (filterEl) { filterEl.value = ''; }
			show(errorEl, false);
			show(emptyEl, false);
			show(nomatchEl, false);
			show(loadingEl, true);
			listEl.classList.add('is-loading');

			window.fetch(apiBase + '/files?dir=' + encodeURIComponent(currentDir), {
				credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' }
			})
				.then(function (r) { if (!r.ok) { throw new Error('http ' + r.status); } return r.json(); })
				.then(function (d) {
					show(loadingEl, false);
					listEl.classList.remove('is-loading');
					render(d);
				})
				.catch(function () {
					show(loadingEl, false);
					listEl.classList.remove('is-loading');
					listEl.textContent = '';
					show(errorEl, true);
				});
		}

		function delReq(name, type) {
			var qs = 'dir=' + encodeURIComponent(currentDir) + '&name=' + encodeURIComponent(name) + '&type=' + encodeURIComponent(type);
			return window.fetch(apiBase + '/delete?' + qs, {
				method: 'DELETE', credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' }
			}).then(function (r) { if (!r.ok) { throw new Error('http ' + r.status); } return r.json(); });
		}

		function del(name, type, btn) {
			if (btn) { btn.disabled = true; btn.classList.add('is-busy'); }
			delReq(name, type)
				.then(function () {
					load(currentDir);
					if (typeof refreshUsage === 'function') { refreshUsage(); }
				})
				.catch(function () {
					if (btn) { btn.disabled = false; btn.classList.remove('is-busy'); }
					show(errorEl, true);
				});
		}

		function bulkDelete() {
			var items = Object.keys(selected).map(function (k) { return selected[k]; });
			if (items.length === 0) { return; }
			if (!window.confirm(fmt(S.confirmBulk || 'Delete %s selected item(s)? This cannot be undone.', items.length))) { return; }

			if (bulkDelEl) { bulkDelEl.disabled = true; bulkDelEl.classList.add('is-busy'); }

			// Delete sequentially; continue past individual errors, then reload
			// so the list and gauge reflect whatever actually got removed.
			var i = 0;
			(function next() {
				if (i >= items.length) {
					selected = {};
					load(currentDir);
					if (typeof refreshUsage === 'function') { refreshUsage(); }
					return;
				}
				var it = items[i++];
				delReq(it.name, it.type).then(next, next);
			})();
		}

		function mkdirReq(name) {
			var qs = 'dir=' + encodeURIComponent(currentDir) + '&name=' + encodeURIComponent(name);
			return window.fetch(apiBase + '/mkdir?' + qs, {
				method: 'POST', credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' }
			}).then(function (r) { if (!r.ok) { throw new Error('http ' + r.status); } return r.json(); });
		}

		function renameReq(name, newname, type) {
			var qs = 'dir=' + encodeURIComponent(currentDir)
				+ '&name=' + encodeURIComponent(name)
				+ '&newname=' + encodeURIComponent(newname)
				+ '&type=' + encodeURIComponent(type);
			return window.fetch(apiBase + '/rename?' + qs, {
				method: 'POST', credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' }
			}).then(function (r) { if (!r.ok) { throw new Error('http ' + r.status); } return r.json(); });
		}

		function setProgress(pct, n) {
			if (progBarEl) { progBarEl.style.width = pct + '%'; }
			if (progTextEl) { progTextEl.textContent = fmt(S.uploading || 'Uploading %s…', n) + ' ' + pct + '%'; }
		}

		// Upload via XHR so we can show progress (fetch has no upload progress).
		function uploadFiles(fileList) {
			var files = [].slice.call(fileList || []);
			if (!files.length) { return; }

			var fd = new FormData();
			fd.append('dir', currentDir);
			files.forEach(function (f) { fd.append('files[]', f, f.name); });

			var xhr = new XMLHttpRequest();
			xhr.open('POST', apiBase + '/upload', true);
			xhr.withCredentials = true;
			xhr.setRequestHeader('Accept', 'application/json');

			show(progressEl, true);
			setProgress(0, files.length);

			if (xhr.upload) {
				xhr.upload.addEventListener('progress', function (e) {
					if (e.lengthComputable) { setProgress(Math.round(e.loaded / e.total * 100), files.length); }
				});
			}
			xhr.addEventListener('load', function () {
				show(progressEl, false);
				if (xhr.status >= 200 && xhr.status < 300) {
					load(currentDir);
					if (typeof refreshUsage === 'function') { refreshUsage(); }
				} else if (xhr.status === 413) {
					window.alert(S.quota || 'This upload would exceed your storage quota.');
				} else {
					window.alert(S.uploadFailed || 'The upload failed.');
				}
			});
			xhr.addEventListener('error', function () {
				show(progressEl, false);
				window.alert(S.uploadFailed || 'The upload failed.');
			});
			xhr.send(fd);
		}

		if (refreshEl) { refreshEl.addEventListener('click', function () { load(currentDir); }); }
		if (filterEl)  { filterEl.addEventListener('input', function () { filterText = filterEl.value.trim().toLowerCase(); buildList(); }); }
		if (selallEl)  { selallEl.addEventListener('change', function () { selectAllVisible(selallEl.checked); }); }
		if (bulkDelEl) { bulkDelEl.addEventListener('click', bulkDelete); }

		if (newFolderEl) {
			newFolderEl.addEventListener('click', function () {
				var nm = window.prompt(S.newFolder || 'Name for the new folder:', '');
				if (nm === null) { return; }
				nm = nm.trim();
				if (!nm) { return; }
				newFolderEl.disabled = true;
				mkdirReq(nm)
					.then(function () { load(currentDir); })
					.catch(function () { show(errorEl, true); })
					.then(function () { newFolderEl.disabled = false; });
			});
		}

		// Upload: button opens the file picker (keyboard path); the drop zone is
		// an equivalent enhancement for pointer users.
		if (uploadBtnEl && fileInputEl) {
			uploadBtnEl.addEventListener('click', function () { fileInputEl.click(); });
			fileInputEl.addEventListener('change', function () { uploadFiles(fileInputEl.files); fileInputEl.value = ''; });
		}

		if (dropEl) {
			var dragDepth = 0;
			function dragShow(on) { dropEl.classList.toggle('is-over', on); }
			['dragenter', 'dragover'].forEach(function (ev) {
				wrap.addEventListener(ev, function (e) {
					if (e.dataTransfer) { e.dataTransfer.dropEffect = 'copy'; }
					e.preventDefault();
				});
			});
			wrap.addEventListener('dragenter', function () { dragDepth++; dragShow(true); });
			wrap.addEventListener('dragleave', function () { dragDepth--; if (dragDepth <= 0) { dragDepth = 0; dragShow(false); } });
			wrap.addEventListener('drop', function (e) {
				e.preventDefault();
				dragDepth = 0;
				dragShow(false);
				if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
					uploadFiles(e.dataTransfer.files);
				}
			});
		}

		load('');
	}

	// Animate a numeric value 0->target, calling render(value) each frame.
	function count(target, render, done) {
		if (reduce) { render(target); if (done) { done(); } return; }
		var start = null;
		function step(ts) {
			if (start === null) { start = ts; }
			var p = Math.min((ts - start) / DURATION, 1);
			render(target * easeOut(p));
			if (p < 1) { requestAnimationFrame(step); }
			else if (done) { done(); }
		}
		requestAnimationFrame(step);
	}

	ready(function () {
		var root = document.getElementById('storage-manager');
		if (!root) { return; }

		var ring     = root.querySelector('.gauge-value');
		var pctEl    = root.querySelector('.gauge-percent');
		var statEls  = [].slice.call(root.querySelectorAll('.stat-value'));
		var circ     = ring ? parseFloat(ring.getAttribute('stroke-dasharray')) : 0;

		function stateFor(pct) {
			return (pct >= 90) ? 'crit' : (pct >= 70) ? 'warn' : 'ok';
		}

		// Animate the ring + percent + each stat to a set of values.
		function animateTo(percent, statBytes) {
			root.setAttribute('data-state', stateFor(percent));

			if (ring && circ) {
				var targetOffset = circ * (1 - percent / 100);
				if (reduce) {
					ring.style.strokeDashoffset = targetOffset;
				} else {
					// start empty, then transition to the target arc
					ring.style.transition = 'none';
					ring.style.strokeDashoffset = circ;
					// force reflow so the starting point takes effect
					void ring.getBoundingClientRect();
					ring.style.transition = 'stroke-dashoffset ' + DURATION + 'ms cubic-bezier(0.22,1,0.36,1)';
					ring.style.strokeDashoffset = targetOffset;
				}
			}

			if (pctEl) {
				count(percent, function (v) { pctEl.textContent = Math.round(v); },
					function () { pctEl.textContent = Math.round(percent); });
			}

			statEls.forEach(function (el, i) {
				var bytes = (statBytes && statBytes.length > i) ? statBytes[i] : parseFloat(el.getAttribute('data-bytes')) || 0;
				var finalText = el.getAttribute('data-final') || el.textContent;
				el.setAttribute('data-final', finalText);
				el.setAttribute('data-bytes', bytes);
				count(bytes, function (v) { el.textContent = fmtBytes(v); },
					function () { el.textContent = (statBytes ? fmtBytes(bytes) : finalText); });
			});
		}

		// Initial animation from the server-rendered values.
		var pct0 = parseInt(root.getAttribute('data-percent'), 10) || 0;
		animateTo(pct0, null);

		var apiBase = (root.getAttribute('data-api-base') || '').replace(/\/+$/, '');

		// Re-sync from the API. Exposed for later actions (purge/delete) to call.
		function refresh() {
			if (!apiBase || !window.fetch) { return; }
			window.fetch(apiBase + '/usage', { credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' } })
				.then(function (r) { return r.ok ? r.json() : null; })
				.then(function (d) {
					if (!d) { return; }
					animateTo(d.percent, [d.used, d.soft, d.free]);
				})
				.catch(function () { /* keep server-rendered values */ });
		}
		window.StorageUsage = { refresh: refresh, animateTo: animateTo };

		// ---- AJAX file browser ------------------------------------------------
		initBrowser(apiBase, refresh);

		// Confirm before a purge, and give the button a working state.
		var form = document.getElementById('storage-purge-form');
		if (form) {
			form.addEventListener('submit', function (e) {
				var degree = form.querySelector('[name="degree"]');
				var all = degree && degree.value === 'all';
				var msg = all
					? 'Remove ALL of your session/result files? This cannot be undone.'
					: 'Free up space by removing the selected old files?';
				if (!window.confirm(msg)) { e.preventDefault(); return; }
				var btn = document.getElementById('storage-purge-btn');
				if (btn) { btn.disabled = true; btn.classList.add('is-busy'); }
			});
		}
	});
})();
