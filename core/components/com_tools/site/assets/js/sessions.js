/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;

	Hubzero.initApi();

	// Session countdown (hard-timeout tools only). Reads /api/tools/{sess}/timeout;
	// idle-timeout tools reset on activity, so they get no countdown.
	(function () {
		var el = document.getElementById('session-timer');
		if (!el) { return; }

		var sess = parseInt(el.getAttribute('data-sess'), 10);
		var base = (el.getAttribute('data-base') || '').replace(/\/+$/, '');
		if (!sess) { return; }

		var url  = base + '/api/tools/' + sess + '/timeout';
		var WARN = 15 * 60, CRIT = 5 * 60;
		var remaining = null, deadline = 0, tick = null;

		// Warning modal (fired once each at the 5- and 1-minute marks). One shared
		// element, so the 1-minute warning REPLACES the 5-minute one in place --
		// even if the earlier popup was left open and never dismissed.
		var warnTitle = el.getAttribute('data-warntitle') || 'Session ending soon';
		var warn5     = el.getAttribute('data-warn5') || 'Your session will end in about 5 minutes.';
		var warn1     = el.getAttribute('data-warn1') || 'Your session will end in about 1 minute.';
		var dismiss   = el.getAttribute('data-dismiss') || 'Dismiss';
		var endingTxt = el.getAttribute('data-ending') || 'Expired, ending soon';
		var endingTip = el.getAttribute('data-endingtip') || 'This session has reached its time limit and will be shut down shortly. Save your work now; anything unsaved will be lost when the session closes.';
		var fired5 = false, fired1 = false;
		var lastBand = null;

		function closePopup() {
			var ov = document.getElementById('session-timeout-popup');
			if (ov) { ov.classList.remove('is-open'); }
		}
		function popup(kind) {
			var ov = document.getElementById('session-timeout-popup');
			if (!ov) {
				ov = document.createElement('div');
				ov.id = 'session-timeout-popup';
				ov.innerHTML =
					'<div class="stp-card" role="alertdialog" aria-live="assertive">' +
						'<svg class="stp-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
						'<div class="stp-body"><h3 class="stp-title"></h3><p class="stp-msg"></p></div>' +
						'<button type="button" class="stp-dismiss"></button>' +
					'</div>';
				(document.getElementById('app-wrap') || document.body).appendChild(ov);
				ov.querySelector('.stp-dismiss').addEventListener('click', closePopup);
				ov.addEventListener('click', function (e) { if (e.target === ov) { closePopup(); } });
				document.addEventListener('keydown', function (e) { if (e.key === 'Escape' || e.keyCode === 27) { closePopup(); } });
			}
			ov.querySelector('.stp-title').textContent = warnTitle;
			ov.querySelector('.stp-msg').textContent = (kind === '1') ? warn1 : warn5;
			ov.querySelector('.stp-dismiss').textContent = dismiss;
			ov.classList.toggle('crit', kind === '1');  // 1-min = red; 5-min = orange
			ov.classList.add('is-open');                 // re-show even if it was dismissed
		}

		function band(r) {
			if (r == null)  { return 'na'; }
			if (r <= 0)     { return 'ended'; }
			if (r <= 60)    { return 'min'; }
			if (r <= CRIT)  { return 'crit'; }
			if (r <= WARN)  { return 'warn'; }
			return 'normal';
		}
		function checkWarnings() {
			if (remaining == null) { return; }
			// If the deadline moved back out (session extended), reset the once-
			// flags so warnings can fire again, and drop any stale popup.
			if (remaining > CRIT) { fired5 = false; closePopup(); }
			if (remaining > 60)   { fired1 = false; }
			if (remaining <= 0)   { return; }
			if (!fired5 && remaining <= CRIT) { fired5 = true; popup('5'); }
			if (!fired1 && remaining <= 60)   { fired1 = true; popup('1'); }
		}

		function pad(n) { return (n < 10 ? '0' : '') + n; }
		function fmt(s) {
			s = Math.max(0, s | 0);
			return pad(Math.floor(s / 3600)) + ':' + pad(Math.floor((s % 3600) / 60)) + ':' + pad(s % 60);
		}
		function render() {
			var b = band(remaining);

			if (b === 'ended') {
				el.classList.remove('warn');
				el.classList.add('crit', 'ended');
				$('#session-timer .st-clock').text(endingTxt);
				// Explain the terse notice on hover, and on focus for keyboard users
				el.setAttribute('data-tip', endingTip);
				el.setAttribute('aria-label', endingTxt + '. ' + endingTip);
				el.setAttribute('tabindex', '0');
			} else {
				el.classList.remove('ended');
				el.removeAttribute('data-tip');
				el.removeAttribute('aria-label');
				el.removeAttribute('tabindex');
				$('#session-timer .st-clock').text(fmt(remaining));
				el.classList.toggle('warn', b === 'warn');
				el.classList.toggle('crit', b === 'crit' || b === 'min');
				checkWarnings();
			}

			// Re-query the authoritative timeout on each band transition, so an
			// extension or early shutdown is picked up promptly (not just on the
			// 60s re-sync). Skip the first render and the terminal state.
			if (b !== lastBand) {
				var prev = lastBand;
				lastBand = b;
				if (prev !== null && prev !== 'na' && b !== 'ended') { fetchTimeout(); }
			}
		}
		function fetchTimeout() {
			$.ajax({ url: url, dataType: 'json', cache: false, xhrFields: { withCredentials: true } })
				.done(function (data) {
					if (!data || !data.hard || data.seconds_remaining == null) {
						if (tick) { clearInterval(tick); tick = null; }
						el.hidden = true;
						return;
					}
					remaining = parseInt(data.seconds_remaining, 10);
					// Count against the clock rather than ticks: a background
					// tab's timers are throttled, and counted ticks fall behind
					deadline = Date.now() + remaining * 1000;
					el.hidden = false;
					render();
					if (!tick) {
						tick = setInterval(function () {
							if (remaining == null) { return; }
							remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
							render();
							if (remaining === 0) { clearInterval(tick); tick = null; closePopup(); }
						}, 1000);
					}
				});
			// A failure (not the owner, or session gone) simply leaves it hidden.
		}

		// Let other components (e.g. the viewer's Reconnect button) force a
		// re-read of the authoritative timeout.
		window.SessionTimer = { refresh: fetchTimeout };

		fetchTimeout();
		setInterval(fetchTimeout, 60 * 1000);  // re-sync to correct drift

		// Coming back to the tab: re-read at once rather than at the next re-sync
		document.addEventListener('visibilitychange', function () {
			if (!document.hidden) { fetchTimeout(); }
		});
	})();

	// Narrow frames. The footer sheds detail one step at a time, only as far as
	// it must to fit: labels, the logo, a shorter meter, then the least-used
	// controls into a "More" menu, then the meter itself. The header goes
	// icon-only once the session title starts to truncate. Re-run whenever the
	// frame changes width or a viewer engine adds controls.
	(function () {
		var wrap   = document.getElementById('app-wrap');
		var footer = document.getElementById('app-footer');
		var header = document.getElementById('app-header');
		if (!wrap || !footer || !window.ResizeObserver) { return; }

		// Least-needed first; the zoom control and the HUBzero mark stay
		var STEPS = [
			{ cls: 'fc-label' },
			{ cls: 'fc-timer' },
			{ cls: 'fc-meter' },
			{ move: 'app-btn-hide' },
			{ move: 'app-btn-clipboard' },
			{ move: 'app-btn-reconnect' },
			{ cls: 'fc-nometer' },
			{ cls: 'fc-tight' }
		];
		var moreText = footer.getAttribute('data-more') || 'More';
		var moreLi = null, moreBtn = null, menu = null, controls = null;
		var busy = false;

		function ensureMore() {
			controls = footer.querySelector('ul.app-controls') || footer.querySelector(':scope > ul');
			if (!controls) { return false; }
			if (moreLi && moreLi.parentNode === controls) { return true; }
			moreLi = document.createElement('li');
			moreLi.id = 'app-more-li';
			moreLi.hidden = true;
			moreBtn = document.createElement('a');
			moreBtn.href = '#';
			moreBtn.id = 'app-btn-more';
			moreBtn.className = 'more';
			moreBtn.setAttribute('role', 'button');
			moreBtn.setAttribute('aria-haspopup', 'menu');
			moreBtn.setAttribute('aria-expanded', 'false');
			moreBtn.title = moreText;
			moreBtn.setAttribute('aria-label', moreText);
			moreBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>';
			menu = document.createElement('ul');
			menu.id = 'app-more-menu';
			menu.setAttribute('role', 'menu');
			menu.hidden = true;
			moreLi.appendChild(moreBtn);
			moreLi.appendChild(menu);
			controls.appendChild(moreLi);

			moreBtn.addEventListener('click', function (e) {
				e.preventDefault();
				setOpen(menu.hidden);
			});
			// Choosing an item closes the menu (the item does its own thing)
			menu.addEventListener('click', function (e) {
				if (e.target.closest('a')) { setTimeout(function () { setOpen(false); }, 0); }
			});
			menu.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') { setOpen(false); moreBtn.focus(); }
			});
			document.addEventListener('pointerdown', function (e) {
				if (!menu.hidden && !moreLi.contains(e.target)) { setOpen(false); }
			}, true);
			return true;
		}
		function setOpen(on) {
			menu.hidden = !on;
			moreBtn.classList.toggle('active', on);
			moreBtn.setAttribute('aria-expanded', on ? 'true' : 'false');
			if (on) {
				// Keep it inside the frame: shift right if it would cross the
				// frame's left edge, and never wider than the frame
				var pad = 6;
				var wr = wrap.getBoundingClientRect();
				menu.style.right = '';
				menu.style.maxWidth = Math.max(160, wr.width - 2 * pad) + 'px';
				var mr = menu.getBoundingClientRect();
				if (mr.left < wr.left + pad) {
					menu.style.right = (mr.left - (wr.left + pad)) + 'px';
				}
				var first = menu.querySelector('a');
				if (first) { first.focus(); }
			}
		}

		// Visible footer items must end before the grip's reserved corner
		function fits() {
			var limit = footer.getBoundingClientRect().right - parseFloat(getComputedStyle(footer).paddingRight || 0) + 1;
			var kids = footer.children;
			for (var i = 0; i < kids.length; i++) {
				var r = kids[i].getBoundingClientRect();
				if (r.width && r.right > limit) { return false; }
			}
			return true;
		}

		function restore() {
			STEPS.forEach(function (s) { if (s.cls) { footer.classList.remove(s.cls); } });
			if (!menu) { return; }
			// Put moved controls back in their original order, ahead of More
			Array.prototype.slice.call(menu.children)
				.sort(function (a, b) { return a.getAttribute('data-order') - b.getAttribute('data-order'); })
				.forEach(function (li) { controls.insertBefore(li, moreLi); });
			moreLi.hidden = true;
		}

		function apply(step) {
			if (step.cls) { footer.classList.add(step.cls); return; }
			var a = document.getElementById(step.move);
			var li = a ? a.closest('li') : null;
			if (!li || li.parentNode !== controls) { return; }
			if (!li.hasAttribute('data-order')) {
				li.setAttribute('data-order', Array.prototype.indexOf.call(controls.children, li));
			}
			li.setAttribute('role', 'none');
			a.setAttribute('role', 'menuitem');
			menu.appendChild(li);
			moreLi.hidden = false;
		}

		function compactFooter() {
			if (!ensureMore()) { return; }
			// Number the controls once, before anything moves
			Array.prototype.forEach.call(controls.children, function (li, i) {
				if (li !== moreLi && !li.hasAttribute('data-order')) { li.setAttribute('data-order', i); }
			});
			restore();
			for (var i = 0; i < STEPS.length && !fits(); i++) {
				apply(STEPS[i]);
			}
			if (moreLi.hidden) { setOpen(false); }
		}

		function compactHeader() {
			if (!header) { return; }
			// Give icon-only buttons their name before their text goes
			header.querySelectorAll('ul.app-toolbar li a').forEach(function (a) {
				var span = a.querySelector('span');
				if (span && !a.getAttribute('aria-label')) {
					var name = span.textContent.trim();
					a.setAttribute('aria-label', name);
					if (!a.title) { a.title = name; }
				}
			});
			header.classList.remove('hc-icons');
			var title = document.getElementById('session-title');
			if (title && title.scrollWidth > title.clientWidth + 1) {
				header.classList.add('hc-icons');
			}
		}

		// Down to icon + percentage, the storage link's tooltip (and name)
		// carries the rest: "Storage: 13% of 10GB. View your storage usage"
		var duView = footer.querySelector('#diskusage a.du-view');
		var duTitle = duView ? (duView.getAttribute('title') || '') : '';
		function storageTitle() {
			if (!duView) { return; }
			var amt = footer.querySelector('#du-amount .du-amount-text');
			var label = duView.querySelector('.du-label');
			var full = (amt && amt.textContent.trim())
				? (label ? label.textContent.trim() + ': ' : '') + amt.textContent.trim() + '. ' + duTitle
				: duTitle;
			var t = footer.classList.contains('fc-nometer') ? full : duTitle;
			duView.setAttribute('title', t);
			duView.setAttribute('aria-label', full);
		}
		if (duView) { duView.addEventListener('pointerenter', storageTitle); }

		var watcher = null;
		function run() {
			if (busy) { return; }
			busy = true;
			try { compactHeader(); compactFooter(); storageTitle(); } finally {
				busy = false;
				// Our own moves are not news
				if (watcher) { watcher.takeRecords(); }
			}
		}

		var pending = null;
		function schedule() {
			if (pending) { return; }
			pending = requestAnimationFrame(function () { pending = null; run(); });
		}
		new ResizeObserver(schedule).observe(wrap);
		// Engines add their controls after load. The countdown's ticking text
		// is not a layout change (its state changes are watched below).
		var timerEl = document.getElementById('session-timer');
		watcher = new MutationObserver(function (records) {
			for (var i = 0; i < records.length; i++) {
				if (!(timerEl && timerEl.contains(records[i].target))) { schedule(); return; }
			}
		});
		watcher.observe(footer, { childList: true, subtree: true });
		// The countdown changes width as it changes state
		var timer = document.getElementById('session-timer');
		if (timer) {
			new MutationObserver(schedule).observe(timer, { attributes: true, attributeFilter: ['class', 'hidden'] });
		}
		run();
	})();

	if ($('#app-btn-options').length) {
		$('#app-btn-options').on('click', function (e){
			e.preventDefault();

			var par = $($(this).parent());
			if (par.hasClass('active')) {
				par.removeClass('active');
			} else {
				par.addClass('active');
			}
			$('#app-options').slideToggle();
		});
	}

	var pollDiskUsage = function(){
			/*$.get('/index.php?option=com_tools&controller=storage&task=diskusage&no_html=1&msgs=0', {}, function(data) {
				$('#diskusage').html(data);
			}, 'html');*/
			/*$.get($('#diskusage').attr('data-base') + '/api/members/tools/diskusage', {}, function(data) {
				if (data && $.type(data.amount) === "number" && $.type(data.total) === "number") {
					$('#diskusage .du-amount-bar').css('width', data.amount+'%');
					$('#diskusage .du-amount-text').html(data.amount+'% of '+data.total+'GB');
				}
			}, 'JSON');*/
			$.ajax({
                                url: $('#diskusage').attr('data-base') + '/api/members/tools/diskusage',
                                data : {}, 
                                dataType: "json"
                        }).done(function(data) {
                                if (data && $.type(data.amount) === "number" && $.type(data.total) === "number") {
                                        // (the template sets the width !important; match it)
                                        $('#diskusage .du-amount-bar').each(function () { this.style.setProperty('width', data.amount + '%', 'important'); });
                                        $('#diskusage .du-amount-text').text(data.amount+'% of '+data.total+'GB');
                                        $('#diskusage .du-pct').text(data.amount+'%');
                                }
                        }).fail(function(jqXHR){
                                // Stop on a rejected or rate-limited token (a renewal
                                // restarts us below); a transient error is left alone
                                if (jqXHR.status == 401 || jqXHR.status == 403 || jqXHR.status == 429) {
                                        clearInterval(holdTheInterval);
                                        holdTheInterval = null;
                                }
                        });
		};

	var holdTheInterval = setInterval(pollDiskUsage, 60 * 1000);

	if (Hubzero.api) {
		Hubzero.api.onRenewed(function(){
			if (holdTheInterval === null) {
				holdTheInterval = setInterval(pollDiskUsage, 60 * 1000);
			}
		});
	}

	var shrbtn = $('#share-btn');

	if (shrbtn.length) {
		shrbtn.on('click', function(event){
			event.preventDefault();

			var shareConfirm = $('#confirm-share');

			if (!shareConfirm.attr('value')) {
				alert('Please check the acknowledgement checkbox indicating your acceptance of the fact that shared sessions may be altered and controlled by the users or groups you share the session with.');
				return;
			}

			// disable button
			$(this).attr('disabled', 'disabled');

			// get the form data
			var share = $("#app-share"),
				url   = share.attr("action").nohtml(),
				data  = share.serialize();

			// show message to user
			share
				.css('position', 'relative')
				.prepend('<div id="app-share-overlay" data-message="Hold on while we make the connections!" class="open" />')
				.hide()
				.fadeIn();

			// make ajax call to add share
			$.ajax({
				url: url,
				type: 'POST',
				data: data,
				//dataType: 'json',
				error: function(jqXHR, textStatus, errorThrown) {
					alert('We have experienced a server error while trying to share this tool session.\n\nYou could be seeing this error if you are trying to share with someone who already has sharing privledges.');

					// fade out message
					$("#app-share-overlay").delay(2000).fadeOut('slow', function(){
						// enable button
						shrbtn.removeAttr('disabled');

						// remove items from token list and clear actual hidden input
						$("#acmembers").tokenInput('clear');

						// reset group select box
						$("#group").val(0);

						// uncheck readonly 
						$("#readonly").removeAttr('checked');

						// uncheck readonly 
						$("#confirm-share").removeAttr('checked');
					});
				},
				success: function(data, status, jqXHR) {
					// reload share table
					$("#app-share .entries").html($(data).find('.entries > *'));

					// fade out message
					$("#app-share-overlay").delay(2000).fadeOut('slow', function(){
						// enable button
						shrbtn.removeAttr('disabled');

						// remove items from token list and clear actual hidden input
						$("#acmembers").tokenInput('clear');

						// reset group select box
						$("#group").val(0);

						// uncheck readonly 
						$("#readonly").removeAttr('checked');
					});
				}
			});
		});
	}

	$(".entries").on('click', '.entry-remove', function(event) {
		event.preventDefault();

		// get the url from link
		var url = $(this).attr('href').nohtml();

		// show message to user
		$("#app-share")
			.css('position', 'relative')
			.prepend('<div id="app-share-overlay" data-message="Closing Connections..." class="close" />')
			.hide()
			.fadeIn();

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data, status, jqXHR) {
				//reload share table
				$("#app-share .entries").html($(data).find('.entries > *'));

				//fade out message
				$("#app-share-overlay").delay(1500).fadeOut('slow');
			}
		});
	});
});

