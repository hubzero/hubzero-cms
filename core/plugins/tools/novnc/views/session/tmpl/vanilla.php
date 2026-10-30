<?php
/**
 * Vanilla (stock) noVNC engine for the tools/novnc viewer.
 *
 * Loads the current upstream noVNC client from a CDN (allowed by the site CSP)
 * and connects with the same middleware-provided connection info as the legacy
 * engine -- no HubZero ui-hubzero.js extensions. Selected via the plugin's
 * vnc_engine config or the &vncengine=vanilla request override.
 *
 * Connection recovery is delegated to NoVNCReconnect (reconnect.js): the RFB
 * connect/disconnect events drive the shared overlay, and a fresh RFB is built
 * on demand when the session becomes ready again.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// Shared recovery overlay: registers assets and emits #novnc-reconnect-config
require __DIR__ . DIRECTORY_SEPARATOR . '_reconnect.php';

// Pin an exact upstream noVNC release (served from jsdelivr, allowed by the CSP)
$novncVersion = '1.7.0';
?>
<style>
	#novnc-vanilla { position: relative; width: 100%; height: 100%; background: #0b0d10; }
	#novnc-vanilla-screen { width: 100%; height: 100%; overflow: hidden; }
	#novnc-vanilla-status {
		position: absolute; left: 0; right: 0; top: 0; z-index: 5;
		padding: 8px 12px; text-align: center;
		font: 500 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
		color: #cdd3da; background: rgba(0, 0, 0, 0.55);
	}
	#novnc-vanilla-status.hidden { display: none; }
	#novnc-vanilla-status .eng { opacity: 0.6; font-size: 11px; }
</style>

<div id="novnc-vanilla">
	<div id="novnc-vanilla-status">
		<span id="nvs-msg"><?php echo Lang::txt('PLG_TOOLS_NOVNC_LOADING'); ?></span>
		<span class="eng">(vanilla noVNC <?php echo $this->escape($novncVersion); ?>)</span>
	</div>
	<div id="novnc-vanilla-screen"></div>
</div>

<script type="module">
	import RFB from 'https://cdn.jsdelivr.net/npm/@novnc/novnc@<?php echo $this->escape($novncVersion); ?>/core/rfb.js';

	const host     = <?php echo json_encode((string) $this->output->wsproxy_host); ?>;
	const port     = <?php echo json_encode((string) $this->output->wsproxy_port); ?>;
	const token    = <?php echo json_encode((string) $this->output->token); ?>;
	const password = <?php echo json_encode((string) $this->output->password); ?>;
	const encrypt  = <?php echo ($this->output->wsproxy_encrypt == 'Yes') ? 'true' : 'false'; ?>;
	const readOnly = <?php echo $this->readOnly ? 'true' : 'false'; ?>;

	const scheme = encrypt ? 'wss' : 'ws';
	const url = scheme + '://' + host + ':' + port + '/websockify?token=' + encodeURIComponent(token);

	const screen   = document.getElementById('novnc-vanilla-screen');
	const msgEl    = document.getElementById('nvs-msg');
	const statusEl = document.getElementById('novnc-vanilla-status');
	const setStatus = (m) => {
		if (m === null) { statusEl.classList.add('hidden'); return; }
		statusEl.classList.remove('hidden');
		msgEl.textContent = m;
	};

	// Wire up connection recovery (shared with the legacy engine).
	let reco = null;
	try {
		const rcfg = JSON.parse(document.getElementById('novnc-reconnect-config').textContent);
		if (window.NoVNCReconnect) {
			reco = window.NoVNCReconnect.init({
				mount:    document.getElementById('novnc-vanilla'),
				sess:     rcfg.sess,
				apiBase:  rcfg.apiBase,
				links:    rcfg.links,
				strings:  rcfg.strings,
				reconnect: connect
			});
		}
	} catch (e) { console.warn('[vanilla-novnc] reconnect init failed', e); }

	let rfb = null;
	let gen = 0;              // guards against a torn-down RFB's late events
	let everConnected = false; // recovery only kicks in after a working connection

	function connect() {
		const myGen = ++gen;

		if (rfb) {
			try { rfb.disconnect(); } catch (e) {}
			rfb = null;
		}

		try {
			rfb = new RFB(screen, url, { credentials: { password: password } });
			rfb.viewOnly = readOnly;
			rfb.scaleViewport = true;
			rfb.background = '#0b0d10';

			rfb.addEventListener('connect', () => {
				if (myGen !== gen) { return; }
				everConnected = true;
				setStatus(null);
				if (reco) { reco.connected(); }
			});
			rfb.addEventListener('disconnect', (e) => {
				if (myGen !== gen) { return; }
				const clean = !!(e.detail && e.detail.clean);
				setStatus(clean ? 'Disconnected.' : 'Connection lost.');
				// Only recover from drops after a working connection; the initial
				// connect is gated upstream by the launch interstitial.
				if (reco && everConnected) { reco.disconnected(clean); }
			});
			rfb.addEventListener('credentialsrequired', () => rfb.sendCredentials({ password: password }));
			rfb.addEventListener('securityfailure', (e) => setStatus('Security failure' + (e.detail && e.detail.reason ? ': ' + e.detail.reason : '') + '.'));

			window.__vanillaRFB = rfb;
			console.log('[vanilla-novnc] connecting to', url);
		} catch (err) {
			console.error('[vanilla-novnc]', err);
			setStatus('Failed to start the noVNC client.');
		}
	}

	connect();

	// Test affordance: drop the live connection to exercise recovery. The
	// session stays alive, so recovery polls state -> ready -> connect() rebuilds
	// the RFB. Mirrors the legacy engine's footer "Drop" button.
	(function () {
		const footer = document.getElementById('app-footer');
		if (!footer) { return; }
		const ul = document.createElement('ul');

		// Reconnect: drops and re-establishes the connection. Icon-only (sync);
		// hover for the label.
		const li = document.createElement('li');
		const a  = document.createElement('a');
		a.className = 'reconnect';
		a.id = 'app-btn-reconnect';
		a.href = '#';
		a.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_BTN')); ?>;
		a.setAttribute('aria-label', a.title);
		a.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>';
		a.addEventListener('click', function (e) {
			e.preventDefault();
			if (window.SessionTimer) { window.SessionTimer.refresh(); }
			if (reco) { reco.pending(); }
			if (rfb) { try { rfb.disconnect(); } catch (err) {} }
		});
		li.appendChild(a);
		ul.appendChild(li);

		// Privacy: blank the display for passers-by. Icon-only; the eye is open
		// when visible and crossed-out when hidden; button highlights while on.
		const eyeOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
		const eyeOff  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
		const titleHide = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HIDE')); ?>;
		const titleShow = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_SHOW')); ?>;
		const liHide = document.createElement('li');
		const hide = document.createElement('a');
		hide.className = 'hide-display';
		hide.id = 'app-btn-hide';
		hide.href = '#';
		hide.setAttribute('role', 'button');
		hide.setAttribute('aria-pressed', 'false');
		hide.title = titleHide;
		hide.setAttribute('aria-label', titleHide);
		hide.innerHTML = eyeOpen;
		hide.addEventListener('click', function (e) {
			e.preventDefault();
			var content = document.getElementById('app-content');
			if (!content) { return; }
			var on = content.classList.toggle('privacy-on');
			hide.classList.toggle('active', on);
			hide.setAttribute('aria-pressed', on ? 'true' : 'false');
			hide.title = on ? titleShow : titleHide;
			hide.setAttribute('aria-label', on ? titleShow : titleHide);
			hide.innerHTML = on ? eyeOff : eyeOpen;
		});
		liHide.appendChild(hide);
		ul.appendChild(liHide);

		footer.appendChild(ul);
	})();
</script>
