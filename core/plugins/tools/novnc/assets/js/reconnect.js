/**
 * noVNC connection-recovery controller (engine-agnostic, no dependencies).
 *
 * When a VNC connection drops, the viewer engine (legacy ui-hubzero or the
 * vanilla stock client) hands control here. We overlay the VNC window with an
 * interstitial-style panel and poll the DB-only session-state API to decide
 * what to do:
 *
 *   - session gone (exited)   -> terminal "session ended" panel + links home
 *   - display broken          -> terminal "display stopped" panel + reload/links
 *   - session ready again      -> ask the engine to reconnect; watch for success
 *   - still coming back         -> keep waiting and polling
 *
 * The engine wires three things via init(): a mount element to overlay, a
 * reconnect() callback that re-establishes its own connection, and calls to
 * connected()/disconnected() as its connection state changes.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function (window, document) {
	'use strict';

	var POLL_BASE_MS       = 1000;   // first wait between state polls
	var POLL_MAX_MS        = 5000;   // cap for the growing poll interval
	var RECONNECT_WATCH_MS = 15000;  // how long to wait for a reconnect to take
	var STALL_MS           = 120000; // after this, surface a manual escape hatch

	var opts    = null;
	var overlay = null;
	var els     = {};

	var active         = false;  // a recovery cycle is in progress (polling)
	var holding        = false;  // the overlay is up (recovery OR terminal) -- the
	                             // frame must stay frozen the whole time it shows
	var reconnectingNow = false; // a reconnect() attempt is outstanding
	var pollTimer      = null;
	var watchTimer     = null;
	var pollAttempt    = 0;
	var startedAt      = 0;

	function log() {
		if (window.console && console.log) {
			console.log.apply(console, ['[novnc-reconnect]'].concat([].slice.call(arguments)));
		}
	}

	function S(key, fallback) {
		return (opts && opts.strings && opts.strings[key]) ? opts.strings[key] : fallback;
	}

	// ----- overlay construction -------------------------------------------

	function buildOverlay() {
		if (overlay) { return; }

		overlay = document.createElement('div');
		overlay.id = 'novnc-reco';
		overlay.className = 'novnc-reco-overlay';
		overlay.setAttribute('role', 'alertdialog');
		overlay.setAttribute('aria-live', 'assertive');

		var card = document.createElement('div');
		card.className = 'novnc-reco-card';

		els.spinner = document.createElement('div');
		els.spinner.className = 'novnc-reco-spinner';
		els.spinner.setAttribute('aria-hidden', 'true');
		els.spinner.innerHTML = '<span></span><span></span><span></span>';

		els.title = document.createElement('h3');
		els.title.className = 'novnc-reco-title';

		els.msg = document.createElement('p');
		els.msg.className = 'novnc-reco-msg';

		els.state = document.createElement('p');
		els.state.className = 'novnc-reco-state';

		els.actions = document.createElement('div');
		els.actions.className = 'novnc-reco-actions';

		card.appendChild(els.spinner);
		card.appendChild(els.title);
		card.appendChild(els.msg);
		card.appendChild(els.state);
		card.appendChild(els.actions);
		overlay.appendChild(card);

		// The overlay is absolutely positioned; make sure the mount can anchor it.
		var mount = opts.mount;
		var pos = window.getComputedStyle(mount).position;
		if (pos === 'static' || !pos) {
			mount.style.position = 'relative';
		}
		mount.appendChild(overlay);
	}

	function showOverlay() {
		buildOverlay();
		holding = true;
		overlay.classList.add('is-visible');
	}

	function hideOverlay() {
		holding = false;
		if (overlay) { overlay.classList.remove('is-visible'); }
	}

	function setSpinner(on) {
		if (els.spinner) { els.spinner.style.display = on ? '' : 'none'; }
	}

	// Render a panel. spec = { title, msg, detail, spinner, actions:[{label, href|action}] }
	function render(spec) {
		buildOverlay();
		els.title.textContent = spec.title || '';
		els.msg.textContent   = spec.msg || '';
		els.state.textContent = spec.detail || '';
		els.state.style.display = spec.detail ? '' : 'none';
		setSpinner(!!spec.spinner);

		els.actions.innerHTML = '';
		(spec.actions || []).forEach(function (a) {
			var b = document.createElement('a');
			b.className = 'novnc-reco-btn' + (a.primary ? ' primary' : '');
			b.textContent = a.label;
			if (a.href) {
				b.href = a.href;
			} else {
				b.href = '#';
				b.addEventListener('click', function (e) { e.preventDefault(); a.action(); });
			}
			els.actions.appendChild(b);
		});
		els.actions.style.display = (spec.actions && spec.actions.length) ? '' : 'none';
	}

	// Common action sets ----------------------------------------------------

	function homeLinks(primaryReload) {
		var links = [];
		if (primaryReload) {
			links.push({ label: S('reload', 'Reload the tool'), primary: true, action: function () { window.location.reload(); } });
		}
		if (opts.links && opts.links.account) {
			// When there is no Reload primary (session ended / signed out), make
			// "My sessions" the emphasized action so the panel has a clear step.
			links.push({ label: S('account', 'My sessions'), href: opts.links.account, primary: !primaryReload });
		}
		if (opts.links && opts.links.home) {
			links.push({ label: S('home', 'Go to the hub'), href: opts.links.home });
		}
		return links;
	}

	// ----- state-poll loop -------------------------------------------------

	function clearTimers() {
		if (pollTimer)  { clearTimeout(pollTimer);  pollTimer  = null; }
		if (watchTimer) { clearTimeout(watchTimer); watchTimer = null; }
	}

	function schedulePoll() {
		if (pollTimer) { clearTimeout(pollTimer); }
		var delay = Math.min(POLL_BASE_MS * (pollAttempt + 1), POLL_MAX_MS);
		pollTimer = setTimeout(tick, delay);
	}

	function stateUrl() {
		return opts.apiBase.replace(/\/+$/, '') + '/' + opts.sess + '/state?mode=vnc';
	}

	function tick() {
		if (!active) { return; }
		pollAttempt++;
		var url = stateUrl();
		log('poll #' + pollAttempt, url);

		if (!window.fetch) {
			// No fetch: we cannot check state, so just keep offering a reload.
			render({
				title: S('lostTitle', 'Connection lost'),
				msg: S('stalled', 'Unable to check the session automatically.'),
				actions: homeLinks(true)
			});
			return;
		}

		window.fetch(url, { credentials: 'include', cache: 'no-store', headers: { 'Accept': 'application/json' } })
			.then(function (resp) {
				if (resp.status === 401 || resp.status === 403) {
					// The hub session expired -- reconnecting won't help.
					terminalLoggedOut();
					throw new Error('halt');
				}
				return resp.json();
			})
			.then(function (data) { route(data); })
			.catch(function (err) {
				if (err && err.message === 'halt') { return; }
				// Transient API/network error: keep waiting, keep polling.
				log('poll error', (err && err.message) || err);
				if (!active) { return; }
				waiting(S('checking', 'Checking your session…'));
				schedulePoll();
			});
	}

	function route(data) {
		if (!active || !data) { return; }
		log('state', data.state, 'ready=' + data.ready, 'exited=' + data.exited);

		if (data.exited) { terminalEnded(); return; }
		if (data.state === 'display_broken') { terminalDisplay(); return; }

		if (data.ready) {
			tryReconnect();
			return;
		}

		// Still coming back up (starting / registered / display_pending)
		var detail = data.state ? (S('statePrefix', 'Session state:') + ' ' + data.state) : '';
		waiting(S('waiting', 'Waiting for the session to come back…'), detail);
		maybeStalled();
		schedulePoll();
	}

	function waiting(msg, detail) {
		render({
			title: S('lostTitle', 'Connection lost'),
			msg: msg,
			detail: detail || '',
			spinner: true,
			actions: (Date.now() - startedAt > STALL_MS) ? homeLinks(true) : []
		});
	}

	function maybeStalled() {
		if (Date.now() - startedAt > STALL_MS) {
			// Keep polling, but the reload/home links are now shown by waiting().
			log('recovery stalled past', STALL_MS, 'ms');
		}
	}

	// ----- reconnect coordination -----------------------------------------

	function tryReconnect() {
		if (reconnectingNow) { return; } // watchdog already running
		reconnectingNow = true;
		pollAttempt = 0;

		render({
			title: S('lostTitle', 'Connection lost'),
			msg: S('reconnecting', 'Reconnecting…'),
			spinner: true
		});

		log('session ready -> asking engine to reconnect');
		try {
			opts.reconnect();
		} catch (e) {
			log('reconnect() threw', e);
		}

		// If the engine does not report connected() within the watchdog window,
		// assume the attempt failed and resume polling.
		if (watchTimer) { clearTimeout(watchTimer); }
		watchTimer = setTimeout(function () {
			if (!active) { return; }
			log('reconnect watchdog elapsed; resuming polling');
			reconnectingNow = false;
			tick();
		}, RECONNECT_WATCH_MS);
	}

	// ----- terminal panels -------------------------------------------------

	function terminalEnded() {
		active = false;
		clearTimers();
		render({
			title: S('endedTitle', 'Your session has ended'),
			msg: S('endedMsg', 'This tool session is no longer running.'),
			spinner: false,
			actions: homeLinks(false)
		});
	}

	function terminalDisplay() {
		active = false;
		clearTimers();
		render({
			title: S('displayTitle', 'Display stopped'),
			msg: S('displayMsg', 'The session’s display stopped responding.'),
			spinner: false,
			actions: homeLinks(true)
		});
	}

	function terminalLoggedOut() {
		active = false;
		clearTimers();
		render({
			title: S('authTitle', 'Please sign in again'),
			msg: S('authMsg', 'Your hub sign-in has expired.'),
			spinner: false,
			actions: homeLinks(true)
		});
	}

	// ----- public API ------------------------------------------------------

	var API = {
		init: function (o) {
			opts = o || {};
			if (!opts.mount || !opts.sess || !opts.apiBase || typeof opts.reconnect !== 'function') {
				log('init: missing required options', opts);
				return API;
			}
			log('init sess=' + opts.sess, 'api=' + opts.apiBase);
			return API;
		},

		// True whenever the overlay is showing (recovery or a terminal panel).
		// Engines use this to freeze frame/canvas sizing so a dead connection
		// cannot collapse the viewport out from under the overlay.
		isHolding: function () {
			return holding;
		},

		// Show the overlay immediately, before the client reports the drop.
		// The framebuffer clears (screen goes black) the instant a connection is
		// torn down, but the 'disconnected' state can lag ~1s behind; calling
		// this on the initiating action covers that gap. It only paints -- the
		// real disconnected() still drives polling/reconnect when it arrives.
		pending: function () {
			if (!opts || active) { return; }
			startedAt = Date.now();
			showOverlay();
			render({
				title: S('lostTitle', 'Connection lost'),
				msg: S('checking', 'Checking your session…'),
				spinner: true
			});
		},

		// The engine established (or re-established) its connection.
		connected: function () {
			if (!opts) { return; }
			log('engine connected');
			active = false;
			reconnectingNow = false;
			clearTimers();
			hideOverlay();
		},

		// The engine's connection dropped. clean=true for an orderly close.
		disconnected: function (clean) {
			if (!opts) { return; }
			log('engine disconnected (clean=' + !!clean + ')');
			if (active) { return; } // already recovering
			active = true;
			reconnectingNow = false;
			pollAttempt = 0;
			startedAt = Date.now();
			showOverlay();
			waiting(S('checking', 'Checking your session…'));
			// Poll immediately -- an orderly close usually means the tool exited.
			tick();
		},

		destroy: function () {
			active = false;
			holding = false;
			clearTimers();
			if (overlay && overlay.parentNode) { overlay.parentNode.removeChild(overlay); }
			overlay = null;
			els = {};
		}
	};

	window.NoVNCReconnect = API;

})(window, document);
