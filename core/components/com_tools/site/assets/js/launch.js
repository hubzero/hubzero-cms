/**
 * Async tool-launch interstitial driver.
 *
 * Flow (mode comes from the tool version's displayProxyType, set server-side):
 *   1. start   -> GET /api/tools/invoke        (the blocking spawn, behind XHR)
 *   2. poll    -> GET /api/tools/{sess}/state  (DB-only readiness)
 *   3. connect -> vnc:      go to the legacy session viewer page
 *                 redirect: GET /api/tools/{sess} for redirect_url, then go
 *
 * Verbose progress is logged to the browser console under "[tool-launch]".
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function ($) {
	$(function () {
		var el = document.getElementById('tool-launch');
		if (!el) { return; }

		var cfg = {};
		try {
			var raw = document.getElementById('tool-launch-config');
			cfg = JSON.parse((raw.textContent || raw.innerText || '{}'));
		} catch (e) { cfg = {}; }

		var S       = cfg.strings || {};
		var base    = (cfg.base || '').replace(/\/+$/, '');
		var app     = cfg.app || '';
		var version = cfg.version || 'default';
		var mode    = (cfg.mode === 'redirect') ? 'redirect' : 'vnc';
		var api     = base + '/api/tools';
		var viewerTpl = cfg.viewerUrl || '';
		// PROTOTYPE: embed weber/Jupyter in an iframe (via the session viewer)
		// instead of redirecting away. Triggered by &embed=1 on the launch URL.
		var embed = /[?&]embed=1(?:&|$)/.test(window.location.search);

		var sess       = null;
		var timer      = null;
		var startedAt  = 0;
		var connecting = false;
		var pollCount  = 0;
		var POLL_MS    = 2000;
		var MAX_MS     = 5 * 60 * 1000;

		function log() {
			if (window.console && console.log) {
				console.log.apply(console, ['[tool-launch]'].concat([].slice.call(arguments)));
			}
		}

		log('init', { app: app, version: version, mode: mode, api: api });

		function setStatus(m) { if (m) { $('#launch-status').text(m); } }
		function setStep(k, s) { $('#step-' + k).removeClass('wait pass broken').addClass(s); }
		function stopPoll() { if (timer) { clearInterval(timer); timer = null; } }

		function fail(msg) {
			stopPoll();
			log('FAILED:', msg);
			$('#launch-spinner').css('display', 'none');
			$('#launch-error-msg').text(msg || S.errStart);
			$('#launch-error').prop('hidden', false);
		}

		function errMsg(xhr, fallback) {
			try {
				var j = JSON.parse(xhr.responseText);
				if (j && j.message) { return j.message; }
			} catch (e) {}
			return fallback;
		}

		// Replace (not push) so the interstitial does not stay in history --
		// otherwise Back from the running tool lands here and launches again.
		function go(url) {
			stopPoll();
			log('navigating to', url);
			window.location.replace(url);
		}

		// Before handing off to the weber proxy, try to wait until it actually
		// accepts a connection -- otherwise the user lands on a not-yet-ready
		// page. The URL is cross-origin, so this is a connection check via
		// no-cors (resolves on any HTTP response; rejects on connection refused /
		// DNS / TLS / timeout), not an HTTP-status read.
		//
		// We probe with GET, not HEAD: the backend here is a Tornado server
		// (Jupyter/weber) that returns 405 for HEAD but a real 302 -> /tree for
		// GET. GET is also the exact request the browser makes on navigation, so
		// probing it tests the same path the handoff will take. (405 travelled
		// all the way through the transparent maxwell proxy to Tornado -- the
		// proxy is not answering for itself.)
		//
		// If the page's CSP (connect-src) blocks the probe, we cannot check this
		// way at all -- a CSP-blocked fetch rejects just like a network failure,
		// so we listen for the securitypolicyviolation event to tell them apart
		// and fall back to redirecting immediately (the pre-probe behavior).
		// After PROBE_MAX we hand off anyway so a slow tool is never stranded.
		function probeThenGo(url) {
			if (!window.fetch) { setStatus(S.opening); go(url); return; }

			var probeStart = Date.now();
			var PROBE_MAX  = 120 * 1000;
			var done       = false;
			var retryTimer = null;

			function finish(reason) {
				if (done) { return; }
				done = true;
				if (retryTimer) { clearTimeout(retryTimer); }
				document.removeEventListener('securitypolicyviolation', onCsp);
				log('PROBE finish (' + reason + ') -> handing off');
				setStep('connect', 'pass');
				setStatus(S.opening);
				go(url);
			}

			function onCsp(e) {
				// A connect-src (or default-src) violation means the browser is
				// blocking our probe; we can't check readiness, so behave as
				// before and redirect right away.
				var dir = (e && e.violatedDirective) || '';
				if (/connect-src|default-src/.test(dir)) {
					log('PROBE: blocked by CSP (' + dir + ', ' + (e.blockedURI || '') + '); redirecting without probing');
					finish('csp');
				}
			}

			document.addEventListener('securitypolicyviolation', onCsp);
			setStatus(S.connecting);

			(function probe(attempt) {
				if (done) { return; }
				log('PROBE #' + attempt + ' GET', url);
				window.fetch(url, { method: 'GET', mode: 'no-cors', credentials: 'include', cache: 'no-store' })
					.then(function () {
						log('PROBE #' + attempt + ': weber accepted a connection');
						finish('connected');
					})
					.catch(function (err) {
						if (done) { return; }
						log('PROBE #' + attempt + ': not ready yet', (err && err.message) || err);
						if (Date.now() - probeStart > PROBE_MAX) { finish('timeout'); return; }
						retryTimer = setTimeout(function () { probe(attempt + 1); }, 2000);
					});
			})(1);
		}

		function viewerUrl() {
			// Prefer the routed (SEF) template from the server; fall back to the
			// raw index.php form if it is missing.
			if (viewerTpl && viewerTpl.indexOf('__SESS__') !== -1) {
				return viewerTpl.replace('__SESS__', sess);
			}
			return base + '/index.php?option=com_tools&controller=sessions'
				+ '&app=' + encodeURIComponent(app)
				+ '&task=session&sess=' + sess;
		}

		// 1. START -- the blocking spawn happens inside this XHR; the page is
		// already painted, so the user sees progress rather than a hung tab.
		function start() {
			$('#launch-error').prop('hidden', true);
			// Set flex explicitly -- jQuery .show() would force display:block,
			// which makes the dot spans inline (transform/bounce then ignored).
			$('#launch-spinner').css('display', 'flex');
			['session', 'token', 'display', 'connect'].forEach(function (k) { setStep(k, 'wait'); });
			setStatus(S.starting);
			startedAt  = Date.now();
			connecting = false;
			pollCount  = 0;
			sess       = null;

			// tool version goes as `appversion`; `version` is reserved by the
			// API Loader for the controller version.
			var url = api + '/invoke?app=' + encodeURIComponent(app) + '&appversion=' + encodeURIComponent(version);
			log('START -> GET', url);

			$.ajax({
				url: url,
				dataType: 'json',
				xhrFields: { withCredentials: true }
			}).done(function (data) {
				log('START response', data);
				if (data && data.session) {
					sess = parseInt(data.session, 10);
					log('session created:', sess, '(mode=' + mode + ')');
					setStep('session', 'pass');
					setStatus(S.waiting);
					stopPoll();
					timer = setInterval(tick, POLL_MS);
					tick();
				} else {
					fail(S.errStart);
				}
			}).fail(function (xhr) {
				log('START error', xhr.status, xhr.responseText);
				fail(errMsg(xhr, S.errStart));
			});
		}

		// 2. POLL readiness (DB-only)
		function tick() {
			if (Date.now() - startedAt > MAX_MS) { fail(S.errTimeout); return; }

			pollCount++;
			var url = api + '/' + sess + '/state?mode=' + mode;
			log('POLL #' + pollCount + ' -> GET', url);

			$.ajax({
				url: url,
				dataType: 'json',
				xhrFields: { withCredentials: true }
			}).done(function (data) {
				log('POLL #' + pollCount + ' response', data);
				if (!data) { return; }
				if (data.exited) { fail(S.errExited); return; }

				var st = data.steps || {};
				if (st.session === 'pass') { setStep('session', 'pass'); }
				if (st.token === 'pass')   { setStep('token', 'pass'); }
				if (st.display === 'broken') { setStep('display', 'broken'); fail(S.errDisplay); return; }
				if (st.display === 'pass')   { setStep('display', 'pass'); }

				if (data.ready && !connecting) {
					log('session READY after ' + pollCount + ' poll(s):', data.state);
					connecting = true;
					connect();
				}
			}).fail(function (xhr) {
				// Transient poll failures during startup are logged but ignored;
				// the overall MAX_MS cap bounds how long we keep trying.
				log('POLL #' + pollCount + ' transient error', xhr.status);
			});
		}

		// 3. CONNECT
		function connect() {
			stopPoll();

			if (mode === 'redirect') {
				if (embed) {
					// PROTOTYPE: hand off to the session viewer, which embeds the
					// weber page in an iframe (so we can overlay our own UI).
					setStep('connect', 'pass');
					setStatus(S.loadingView);
					var vu = viewerUrl();
					go(vu + (vu.indexOf('?') !== -1 ? '&' : '?') + 'embed=1');
					return;
				}
				// Fetch the proxy URL (only the view call knows it) and hand off
				setStatus(S.connecting);
				var url = api + '/' + sess;
				log('CONNECT (redirect) -> GET', url);
				$.ajax({
					url: url,
					dataType: 'json',
					xhrFields: { withCredentials: true }
				}).done(function (data) {
					log('CONNECT response', data);
					if (data && data.redirect_url) {
						log('redirect_url =', data.redirect_url);
						probeThenGo(data.redirect_url);
					} else {
						log('CONNECT: no redirect_url in response');
						fail(S.errStart);
					}
				}).fail(function (xhr) {
					log('CONNECT error', xhr.status, xhr.responseText);
					fail(errMsg(xhr, S.errStart));
				});
			} else {
				// VNC: the legacy session page renders the viewer
				log('CONNECT (vnc) -> viewer page');
				setStep('connect', 'pass');
				setStatus(S.loadingView);
				go(viewerUrl());
			}
		}

		$('#launch-retry').on('click', function (e) { e.preventDefault(); log('retry clicked'); start(); });

		// Cancel: stop polling, best-effort terminate any session we created, and
		// go back where the user came from (or the hub root as a fallback).
		$('#launch-cancel').on('click', function (e) {
			e.preventDefault();
			log('cancel clicked; sess =', sess);
			stopPoll();
			if (sess && window.fetch) {
				try {
					window.fetch(api + '/' + sess, { method: 'DELETE', mode: 'cors', credentials: 'include', keepalive: true });
				} catch (err) { log('cancel terminate failed', err); }
			}
			if (window.history.length > 1) {
				window.history.back();
			} else {
				window.location.href = base + '/';
			}
		});

		start();
	});
})(jQuery);
