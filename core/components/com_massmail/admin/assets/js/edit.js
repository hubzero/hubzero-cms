/**
 * Mass Mail -- compose / edit a mailing.
 *
 * Loaded as an external file (CSP-friendly). PHP data arrives via the
 * #mm-compose-config element's data-lang / data-state attributes.
 */
(function () {
	function init() {
		var form = document.getElementById('adminForm');

		// Status-panel progress bar (read-only view of a launched mailing).
		var fill = document.querySelector('.mm-progress-fill');
		if (fill) {
			var pct = parseInt(fill.getAttribute('data-pct'), 10) || 0;
			fill.style.width = Math.max(0, Math.min(100, pct)) + '%';
		}

		// --- Config passed from PHP via data attributes ----------------------
		var cfg = document.getElementById('mm-compose-config');
		var LANG = {};
		var state = { all: false, groups: [], users: [], emails: [] };
		if (cfg) {
			try { LANG = JSON.parse(cfg.getAttribute('data-lang') || '{}'); } catch (e) {}
			try { state = JSON.parse(cfg.getAttribute('data-state') || '{}'); } catch (e) {}
		}

		// --- Bottom action buttons (Send / Save draft / Discard) -------------
		var submits = document.querySelectorAll('.mm-submit');
		for (var si = 0; si < submits.length; si++) {
			submits[si].addEventListener('click', function () {
				if (window.Joomla && Joomla.submitbutton) { Joomla.submitbutton(this.getAttribute('data-task')); }
			});
		}
		var confirmers = document.querySelectorAll('.mm-confirm');
		for (var ci = 0; ci < confirmers.length; ci++) {
			confirmers[ci].addEventListener('click', function (e) {
				var msg = this.getAttribute('data-confirm');
				if (msg && !window.confirm(msg)) { e.preventDefault(); }
			});
		}

		// --- Validation feedback: dismiss the error banner + clear field marks
		function mmDismissError() {
			if (window.Hubzero && typeof Hubzero.removeMessages === 'function') { Hubzero.removeMessages(); }
			var box = document.getElementById('system-message-container');
			if (box) { box.innerHTML = ''; }
			// Kameleon shows admin messages as a floating "growl" notice separate
			// from that container -- remove those too, or the error stays visible.
			var notices = document.querySelectorAll('.growl-notice');
			for (var n = 0; n < notices.length; n++) {
				if (notices[n].parentNode) { notices[n].parentNode.removeChild(notices[n]); }
			}
		}
		function mmClearRcptMark() {
			var d = document.getElementById('mm-to-display');
			if (!d) { return; }
			d.classList.remove('mm-invalid');
			var note = d.querySelector('.mm-required-note');
			if (note && note.parentNode) { note.parentNode.removeChild(note); }
		}
		(function () {
			function onEdit(e) {
				var t = e.target;
				if (!t) { return; }
				var tag = t.tagName;
				if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || t.isContentEditable) { mmDismissError(); }
			}
			document.addEventListener('focusin', onEdit, true);
			document.addEventListener('input', onEdit, true);

			// Clear the subject/body highlight the moment there's real content.
			var subj = document.getElementById('field-subject');
			if (subj) { subj.addEventListener('input', function () { if (subj.value.trim() !== '') { subj.classList.remove('mm-invalid'); } }); }
			var mbody = document.querySelector('.mm-mail-body');
			if (mbody) { mbody.addEventListener('input', function () { mbody.classList.remove('mm-invalid'); }, true); }
			// The recipient highlight is cleared by the live count (below), which
			// already accounts for groups, individuals, raw emails and opt-out override.
		})();

		// --- Live audience prediction (reads the hidden recipient fields) ----
		var outs  = document.querySelectorAll('.mm-preview strong');
		var timer = null;
		function collect() {
			var fd = new FormData();
			if (document.getElementById('field-all').value === '1') { fd.append('rcpt_all', '1'); }
			fd.append('groups[]', document.getElementById('field-groups').value);
			fd.append('users', document.getElementById('field-users').value);
			fd.append('emails', document.getElementById('field-emails').value);
			var ig = document.querySelector('input[name="ignore_optout"]');
			if (ig && ig.checked) { fd.append('ignore_optout', '1'); }
			return fd;
		}
		function update() {
			if (!form || !outs.length) { return; }
			fetch(form.getAttribute('action') + '&task=preview', {
				method: 'POST', body: collect(), credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
			.then(function (r) { return r.json(); })
			.then(function (d) {
				if (d && typeof d.text === 'string') { for (var oi = 0; oi < outs.length; oi++) { outs[oi].textContent = d.text; } }
				// Once the selection actually reaches someone (respecting opt-out
				// override), drop any "recipients required" highlight.
				if (d && d.count > 0) { mmClearRcptMark(); }
			})
			.catch(function () {});
		}
		function schedule() { clearTimeout(timer); timer = setTimeout(update, 300); }

		// --- Recipient builder modal -----------------------------------------
		(function () {
			var modal   = document.getElementById('mm-rcpt-modal');
			var summary = document.getElementById('mm-to-summary');
			if (!modal || !summary) { return; }
			var ICON = { all: '★', group: '👥', user: '👤', email: '✉', optout: '⚠' };
			var EVERYONE = LANG.everyone;
			var NONE = LANG.none;
			var EDIT_LABEL = LANG.edit;
			var ADD_LABEL = LANG.add;
			var OPTOUT_PILL = LANG.optout;
			var ignEl = document.getElementById('field-optout');

			var fAll = document.getElementById('field-all');
			var fGroups = document.getElementById('field-groups');
			var fUsers = document.getElementById('field-users');
			var fEmails = document.getElementById('field-emails');

			function serialize() {
				fAll.value = state.all ? '1' : '0';
				fGroups.value = state.groups.map(function (g) { return g.token; }).join(',');
				fUsers.value  = state.users.map(function (u) { return u.token; }).join(',');
				fEmails.value = state.emails.join(',');
				schedule();
			}
			function makeChip(kind, label, onRemove) {
				var li = document.createElement('li');
				li.className = 'mm-chip mm-chip-' + kind;
				var ic = document.createElement('span'); ic.className = 'mm-chip-ic'; ic.textContent = ICON[kind];
				var s = document.createElement('span'); s.textContent = label;
				li.appendChild(ic); li.appendChild(s);
				if (onRemove) {
					var x = document.createElement('button'); x.type = 'button'; x.className = 'mm-chip-x'; x.innerHTML = '&times;';
					x.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); onRemove(); });
					li.appendChild(x);
				}
				return li;
			}
			function renderSummary() {
				summary.innerHTML = '';
				var any = false;
				if (state.all) { summary.appendChild(makeChip('all', EVERYONE, function () { state.all = false; changed(); })); any = true; }
				state.groups.forEach(function (g, i) { summary.appendChild(makeChip('group', g.label, function () { state.groups.splice(i, 1); changed(); })); any = true; });
				state.users.forEach(function (u, i) { summary.appendChild(makeChip('user', u.label, function () { state.users.splice(i, 1); changed(); })); any = true; });
				state.emails.forEach(function (e, i) { summary.appendChild(makeChip('email', e, function () { state.emails.splice(i, 1); changed(); })); any = true; });
				if (ignEl && ignEl.checked) {
					summary.appendChild(makeChip('optout', OPTOUT_PILL, function () { ignEl.checked = false; ignEl.dispatchEvent(new Event('change')); }));
				}
				if (!any) { var none = document.createElement('li'); none.className = 'mm-to-none'; none.textContent = NONE; summary.appendChild(none); }
				var eb = document.getElementById('mm-to-edit'); if (eb) { eb.textContent = any ? EDIT_LABEL : ADD_LABEL; }
			}

			var mbAll = document.getElementById('mb-all');
			var mbGroupChips = document.getElementById('mb-group-chips');
			var mbUserChips = document.getElementById('mb-user-chips');
			var mbEmails = document.getElementById('mb-emails');

			function setSectionDisabled(searchId, off) {
				var s = document.getElementById(searchId);
				if (!s) { return; }
				var sec = s.closest('.mm-rcpt-section');
				if (sec) { sec.classList.toggle('is-disabled', off); }
				s.disabled = off;
			}
			function renderModal() {
				mbAll.checked = !!state.all;
				// Everyone supersedes group/individual targeting, so lock those sections.
				setSectionDisabled('mb-group-search', !!state.all);
				setSectionDisabled('mb-user-search', !!state.all);
				mbGroupChips.innerHTML = '';
				state.groups.forEach(function (g, i) { mbGroupChips.appendChild(makeChip('group', g.label, function () { state.groups.splice(i, 1); changed(); })); });
				mbUserChips.innerHTML = '';
				state.users.forEach(function (u, i) { mbUserChips.appendChild(makeChip('user', u.label, function () { state.users.splice(i, 1); changed(); })); });
				if (document.activeElement !== mbEmails) { mbEmails.value = state.emails.join('\n'); }
			}
			function changed() { serialize(); renderSummary(); renderModal(); }

			function sectionPicker(searchId, acId, task, listKey, tokenIsId) {
				var search = document.getElementById(searchId);
				var ac = document.getElementById(acId);
				if (!search || !ac) { return; }
				var t = null, items = [], active = -1;
				function hide() { ac.hidden = true; ac.innerHTML = ''; items = []; active = -1; }
				function has(token) { return state[listKey].some(function (e) { return e.token === ('' + token); }); }
				function render(list) {
					items = list; active = -1;
					if (!list.length) { hide(); return; }
					ac.innerHTML = '';
					list.forEach(function (it) {
						var li = document.createElement('li');
						var nm = document.createElement('span'); nm.className = 'mm-ac-cn'; nm.textContent = (it.title || it.cn);
						var subv = it.cn || '';
						var sub = document.createElement('span'); sub.className = 'mm-ac-title'; sub.textContent = subv ? (' — ' + subv) : '';
						li.appendChild(nm); li.appendChild(sub);
						li.addEventListener('mousedown', function (e) { e.preventDefault(); addOne(it); });
						ac.appendChild(li);
					});
					ac.hidden = false;
				}
				function addOne(it) {
					var token = '' + (tokenIsId ? it.id : it.cn);
					if (!has(token)) { state[listKey].push({ token: token, label: (it.title || it.cn) }); changed(); }
					search.value = ''; hide(); search.focus();
				}
				function lookup() {
					var q = search.value.trim();
					if (q.length < 2) { hide(); return; }
					fetch(form.getAttribute('action') + '&task=' + task + '&q=' + encodeURIComponent(q), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
						.then(function (r) { return r.json(); }).then(render).catch(hide);
				}
				search.addEventListener('focus', function () { search.removeAttribute('readonly'); });
				search.addEventListener('input', function () { clearTimeout(t); t = setTimeout(lookup, 180); });
				search.addEventListener('keydown', function (e) {
					if (ac.hidden) { return; }
					var lis = ac.querySelectorAll('li');
					if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, lis.length - 1); }
					else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); }
					else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); addOne(items[active]); return; }
					else if (e.key === 'Escape') { hide(); return; }
					else { return; }
					for (var i = 0; i < lis.length; i++) { lis[i].classList.toggle('active', i === active); }
				});
				search.addEventListener('blur', function () { setTimeout(hide, 150); });
			}
			sectionPicker('mb-group-search', 'mb-group-ac', 'grouplookup', 'groups', false);
			sectionPicker('mb-user-search', 'mb-user-ac', 'userlookup', 'users', true);

			mbAll.addEventListener('change', function () { state.all = mbAll.checked; changed(); });
			mbEmails.addEventListener('input', function () {
				var arr = [];
				mbEmails.value.split(/[\s,;]+/).forEach(function (e) { e = e.trim(); if (e !== '') { arr.push(e); } });
				state.emails = arr; serialize(); renderSummary();
			});

			var editBtn = document.getElementById('mm-to-edit');
			var closeBtn = document.getElementById('mm-rcpt-close');
			var doneBtn = document.getElementById('mb-done');
			var display = document.getElementById('mm-to-display');
			function openModal() { renderModal(); modal.classList.add('open'); }
			function closeModal() { modal.classList.remove('open'); }
			if (editBtn) { editBtn.addEventListener('click', function (e) { e.stopPropagation(); openModal(); }); }
			if (display) { display.addEventListener('click', function (e) { if (e.target.closest && e.target.closest('.mm-chip-x')) { return; } openModal(); }); }
			if (ignEl) { ignEl.addEventListener('change', renderSummary); }
			if (closeBtn) { closeBtn.addEventListener('click', closeModal); }
			if (doneBtn) { doneBtn.addEventListener('click', closeModal); }
			modal.addEventListener('click', function (e) { if (e.target === modal) { closeModal(); } });
			document.addEventListener('keydown', function (e) { if ((e.key === 'Escape' || e.keyCode === 27) && modal.classList.contains('open')) { closeModal(); } });

			serialize(); renderSummary();
		})();

		// --- Opt-out note: informational until the override is actually enabled
		var ign = document.querySelector('input[name="ignore_optout"]');
		if (ign) { ign.addEventListener('change', schedule); }
		var note = document.getElementById('mm-optout-note');
		if (ign && note) {
			function syncNote() {
				note.classList.toggle('warning', ign.checked);
				note.classList.toggle('info', !ign.checked);
			}
			ign.addEventListener('change', syncNote);
			syncNote();
		}

		// --- Delivery mode: as-soon-as-possible vs scheduled -----------------
		var dModes = document.querySelectorAll('input[name="delivery_mode"]');
		var schedWrap = document.querySelector('.mm-schedule-field');
		var schedInput = document.getElementById('field-schedule');
		if (dModes.length && schedWrap) {
			function reflectDelivery() {
				var m = document.querySelector('input[name="delivery_mode"]:checked');
				var isSched = !!m && m.value === 'schedule';
				schedWrap.hidden = !isSched;
				if (!isSched && schedInput) { schedInput.value = ''; }
				var opts = document.querySelectorAll('.mm-dopt');
				for (var i = 0; i < opts.length; i++) {
					var r = opts[i].querySelector('input');
					opts[i].classList.toggle('is-active', !!r && r.checked);
				}
				return isSched;
			}
			for (var d = 0; d < dModes.length; d++) {
				dModes[d].addEventListener('change', function () {
					if (reflectDelivery() && schedInput) { schedInput.focus(); }
				});
			}
			reflectDelivery();
		}
		if (schedInput) {
			var idt = schedInput.closest('.input-datetime');
			if (idt) {
				idt.addEventListener('click', function (e) {
					if (e.target !== schedInput) {
						schedInput.focus();
						if (window.jQuery) { try { window.jQuery(schedInput).datepicker('show'); } catch (err) {} }
					}
				});
			}
		}

		// --- Paperclip attach in the editor toolbar --------------------------
		var fileInput = document.getElementById('field-attachment');
		var chips = document.getElementById('mm-chips');
		if (fileInput && chips && window.DataTransfer) {
			var bag = new DataTransfer();
			function esc(s) { var el = document.createElement('div'); el.textContent = s; return el.innerHTML; }
			function renderPicked() {
				var old = chips.querySelectorAll('.mm-chip-new');
				for (var k = 0; k < old.length; k++) { old[k].remove(); }
				Array.prototype.forEach.call(bag.files, function (f, idx) {
					var li = document.createElement('li');
					li.className = 'mm-chip mm-chip-new';
					li.innerHTML = '<span class="mm-clip">📎</span> ' + esc(f.name) + ' <span class="mm-kb">(' + Math.round(f.size / 1024) + ' KB)</span>';
					var x = document.createElement('button');
					x.type = 'button'; x.className = 'mm-chip-x'; x.innerHTML = '&times;';
					x.addEventListener('click', function () {
						var nb = new DataTransfer();
						Array.prototype.forEach.call(bag.files, function (g, m) { if (m !== idx) { nb.items.add(g); } });
						bag = nb; fileInput.files = bag.files; renderPicked();
					});
					li.appendChild(x);
					chips.appendChild(li);
				});
			}
			fileInput.addEventListener('change', function () {
				Array.prototype.forEach.call(fileInput.files, function (f) { bag.items.add(f); });
				fileInput.files = bag.files;
				renderPicked();
			});

			var tries = 0;
			var iv = setInterval(function () {
				var items = document.querySelector('#adminForm .ck-toolbar__items') || document.querySelector('#adminForm .ck-toolbar');
				if (items && !document.getElementById('mm-attach-btn')) {
					var b = document.createElement('button');
					b.type = 'button'; b.id = 'mm-attach-btn';
					b.className = 'ck ck-button ck-off mm-attach-btn';
					b.title = LANG.attach || '';
					b.setAttribute('aria-label', b.title);
					b.innerHTML = '<svg class="ck ck-icon ck-button__icon" viewBox="0 0 24 24"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/></svg>';
					b.addEventListener('click', function (e) { e.preventDefault(); fileInput.click(); });
					items.appendChild(b);
					clearInterval(iv);
				}
				if (++tries > 40) { clearInterval(iv); }
			}, 250);
		}
	}

	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
	else { init(); }
})();
