<?php
/**
 * Modern noVNC engine for the tools/novnc viewer.
 *
 * Runs the upstream noVNC client vendored in assets/novnc-1.7.0, with the
 * HUBzero protocol patches (assets/novnc-1.7.0/hubzero.patch) reapplied:
 * the HUBzero framebuffer resize request, the ClientAction server message,
 * and an "fbresize" event. This page ports what the legacy engine's
 * ui-hubzero.js did on top of them: a resizable frame that resizes the
 * server's framebuffer, ClientAction URLs, the footer controls, connection
 * recovery and session title editing. Selected via the plugin's vnc_engine
 * config or the &vncengine=modern request override.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->js('jquery.editable.min.js', 'system');

// Shared recovery overlay: registers assets and emits #novnc-reconnect-config
require __DIR__ . DIRECTORY_SEPARATOR . '_reconnect.php';

$novncVersion = '1.7.0';
$novncBase    = rtrim(Request::base(true), '/') . '/core/plugins/tools/novnc/assets/novnc-' . $novncVersion;
?>
<style>
	#novnc-modern { position: relative; width: 100%; height: 100%; background: #0b0d10; }
	#novnc-modern-screen { width: 100%; height: 100%; }
	#novnc-modern-status {
		position: absolute; left: 0; right: 0; top: 0; z-index: 5;
		padding: 8px 12px; text-align: center;
		font: 500 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
		color: #cdd3da; background: rgba(0, 0, 0, 0.55);
	}
	#novnc-modern-status[hidden] { display: none; }
	/* A ClientAction the browser's popup blocker stopped: offer it as a link */
	#novnc-modern-action {
		position: absolute; left: 50%; bottom: 14px; z-index: 6; transform: translateX(-50%);
		display: flex; align-items: center; gap: 0.75rem; max-width: calc(100% - 28px);
		padding: 0.55rem 0.6rem 0.55rem 0.9rem; border-radius: 8px;
		background: #1f2329; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4); color: #f3f4f6;
		font: 400 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	}
	#novnc-modern-action[hidden] { display: none; }
	#novnc-modern-action a { color: #8cc4ff; font-weight: 600; white-space: nowrap; }
	/* Clipboard panel: the always-works path when the browser won't share its clipboard */
	#novnc-modern-clip {
		position: absolute; right: 12px; bottom: 12px; z-index: 7;
		width: min(22rem, calc(100% - 24px)); padding: 0.75rem;
		border-radius: 10px; background: #1f2329; box-shadow: 0 8px 26px rgba(0, 0, 0, 0.45); color: #e6e8eb;
		font: 400 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	}
	#novnc-modern-clip[hidden] { display: none; }
	#novnc-modern-clip p { margin: 0 0 0.5rem; color: #aab1ba; font-size: 12px; }
	#novnc-modern-clip textarea {
		box-sizing: border-box; width: 100%; height: 6.5rem; margin: 0; padding: 0.45rem 0.55rem; resize: vertical;
		border: 1px solid #3a4048; border-radius: 6px; background: #121418; color: #f3f4f6;
		font: 12px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
	}
	#novnc-modern-clip .nmc-row { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.55rem; }
	#novnc-modern-clip .nmc-msg { flex: 1 1 auto; color: #8fd19e; font-size: 12px; }
	#novnc-modern-clip button {
		padding: 0.35rem 0.7rem; border: 1px solid #4a515b; border-radius: 6px;
		background: #2b3038; color: #f3f4f6; font: inherit; font-size: 12px; cursor: pointer;
	}
	#novnc-modern-clip button.nmc-send { border-color: #2f6db3; background: #2f6db3; }
	#novnc-modern-clip button:hover { filter: brightness(1.12); }
	/* The tool copied something the browser could not take yet */
	#app-footer a.clipboard.pending { position: relative; }
	#app-footer a.clipboard.pending::after {
		content: ""; position: absolute; top: 5px; right: 5px; width: 7px; height: 7px;
		border-radius: 50%; background: #2f6db3; box-shadow: 0 0 0 1.5px rgba(255, 255, 255, 0.8);
	}
	#app-footer a.clipboard.active {
		background: linear-gradient(180deg, rgba(47, 109, 179, 0.30), rgba(47, 109, 179, 0.12));
		border-color: rgba(47, 109, 179, 0.55);
		color: #1f5591;
	}
	#novnc-modern-action button {
		flex: none; padding: 0 0.4rem; border: 0; background: none; color: #9aa1ab;
		font: inherit; font-size: 16px; line-height: 1; cursor: pointer;
	}
</style>

<div id="novnc-modern">
	<div id="novnc-modern-status"><?php echo Lang::txt('PLG_TOOLS_NOVNC_LOADING'); ?></div>
	<div id="novnc-modern-screen"></div>
	<div id="novnc-modern-clip" role="dialog" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>" hidden>
		<p><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_HINT'); ?></p>
		<textarea spellcheck="false" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>"></textarea>
		<div class="nmc-row">
			<span class="nmc-msg" aria-live="polite"></span>
			<button type="button" class="nmc-copy"><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_COPY'); ?></button>
			<button type="button" class="nmc-send"><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_SEND'); ?></button>
		</div>
	</div>
	<div id="novnc-modern-action" role="status" hidden>
		<span><?php echo Lang::txt('PLG_TOOLS_NOVNC_ACTION_BLOCKED'); ?></span>
		<a href="#" target="_blank" rel="noopener"><?php echo Lang::txt('PLG_TOOLS_NOVNC_ACTION_OPEN'); ?></a>
		<button type="button" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_ACTION_DISMISS'); ?>">&times;</button>
	</div>
</div>

<script type="module">
	import RFB from <?php echo json_encode($novncBase . '/core/rfb.js'); ?>;

	const $ = window.jQuery;

	const host     = <?php echo json_encode((string) $this->output->wsproxy_host); ?>;
	const port     = <?php echo json_encode((string) $this->output->wsproxy_port); ?>;
	const token    = <?php echo json_encode((string) $this->output->token); ?>;
	const password = <?php echo json_encode((string) $this->output->password); ?>;
	const encrypt  = <?php echo ($this->output->wsproxy_encrypt == 'Yes') ? 'true' : 'false'; ?>;
	const readOnly = <?php echo $this->readOnly ? 'true' : 'false'; ?>;

	const url = (encrypt ? 'wss' : 'ws') + '://' + host + ':' + port + '/websockify?token=' + encodeURIComponent(token);

	const screen   = document.getElementById('novnc-modern-screen');
	const statusEl = document.getElementById('novnc-modern-status');
	const content  = document.getElementById('app-content');
	const appwrap  = document.getElementById('app-wrap');

	function setStatus(m) {
		statusEl.hidden = (m === null);
		if (m !== null) { statusEl.textContent = m; }
	}

	// ----- Connection recovery (shared with the other engines) ---------------
	let reco = null;
	try {
		const rcfg = JSON.parse(document.getElementById('novnc-reconnect-config').textContent);
		if (window.NoVNCReconnect) {
			reco = window.NoVNCReconnect.init({
				mount:     document.getElementById('novnc-modern'),
				sess:      rcfg.sess,
				apiBase:   rcfg.apiBase,
				links:     rcfg.links,
				strings:   rcfg.strings,
				reconnect: connect
			});
		}
	} catch (e) { console.warn('[novnc-modern] reconnect init failed', e); }

	// ----- Frame sizing ------------------------------------------------------
	// The frame follows the server's framebuffer; the user resizes it with the
	// corner grip, which asks the server for a framebuffer of that size.
	function sizeFrame(w, h) {
		// While the recovery/terminal overlay is up, a dead connection's empty
		// framebuffer must not collapse the frame the overlay fills.
		if (reco && reco.isHolding && reco.isHolding()) { return; }
		if (!(w > 0 && h > 0)) { return; }
		content.style.width  = w + 'px';
		content.style.height = h + 'px';
		if (appwrap) { appwrap.style.width = w + 'px'; }
		const size = document.getElementById('app-size');
		if (size) { size.textContent = w + ' x ' + h; }
	}

	function requestFrameSize() {
		if (rfb) { rfb.requestHubzeroResize(content.clientWidth, content.clientHeight); }
	}

	let resizableAttached = false;
	function attachResizable() {
		if (resizableAttached || readOnly || !$ || !$.fn.resizable) { return; }
		resizableAttached = true;
		$(content).resizable({
			minHeight: 200,
			maxHeight: 3900,
			minWidth:  345,
			maxWidth:  3900,
			handles:   'se',
			resize: function () {
				if (appwrap) { appwrap.style.width = content.clientWidth + 'px'; }
			},
			stop: function () {
				sizeFrame(content.clientWidth, content.clientHeight);
				requestFrameSize();
			}
		});
	}

	// ----- ClientAction: the tool asks the browser to open a URL -------------
	const actionEl = document.getElementById('novnc-modern-action');
	actionEl.querySelector('button').addEventListener('click', () => { actionEl.hidden = true; });

	function openClientAction(raw) {
		let target;
		try {
			target = new URL(raw, window.location.href);
		} catch (e) {
			console.warn('[novnc-modern] ignoring malformed ClientAction', raw);
			return;
		}
		// Only web pages; never javascript:, data: or the like
		if (target.protocol !== 'http:' && target.protocol !== 'https:') {
			console.warn('[novnc-modern] ignoring non-web ClientAction', raw);
			return;
		}
		// A hub URL in the wrong scheme would be blocked as mixed content
		if (target.hostname === window.location.hostname) {
			target.protocol = window.location.protocol;
		}
		const href = target.href;

		// File transfer opens in an overlay when fancybox is on the page
		if (href.indexOf('filexfer') !== -1 && $ && $.fancybox) {
			$.fancybox({
				type: 'iframe', href: href, width: 500, height: 500,
				autoSize: false, fitToView: true, titleShow: false
			});
			return;
		}

		// The server, not a click, triggers this, so a popup blocker may stop it
		const win = window.open(href, '_blank');
		if (!win) {
			const a = actionEl.querySelector('a');
			a.href = href;
			a.title = href;
			actionEl.hidden = false;
		}
	}

	// ----- Clipboard ---------------------------------------------------------
	// The VNC server syncs its clipboard over RFB; this bridges that to the
	// computer's clipboard as far as the browser allows, with a panel in the
	// footer as the path that always works.
	const clip = (function () {
		const panel    = document.getElementById('novnc-modern-clip');
		const textarea = panel.querySelector('textarea');
		const msg      = panel.querySelector('.nmc-msg');
		const api      = (window.isSecureContext && navigator.clipboard) ? navigator.clipboard : null;
		let button     = null;   // footer button, set by the footer code
		let remote     = null;   // the session's clipboard, as last seen
		let pending    = null;   // a tool copy the browser refused to take yet
		let msgTimer   = null;

		function say(text) {
			msg.textContent = text;
			clearTimeout(msgTimer);
			msgTimer = setTimeout(() => { msg.textContent = ''; }, 2500);
		}
		function markPending(on) {
			if (button) { button.classList.toggle('pending', on); }
		}

		// Session -> computer
		function writeLocal(text) {
			if (!api || !api.writeText) { pending = text; markPending(true); return; }
			api.writeText(text).then(() => {
				if (pending === text) { pending = null; markPending(false); }
			}, () => {
				// Not focused, or no user gesture (Firefox, Safari): retry on
				// the next click or key press in the page
				pending = text;
				markPending(true);
			});
		}
		function flushPending() {
			if (pending !== null) { writeLocal(pending); }
		}
		document.addEventListener('pointerdown', flushPending, true);
		document.addEventListener('keydown', flushPending, true);

		function fromSession(text) {
			if (text === remote) { return; }   // the server repeats itself
			remote = text;
			if (document.activeElement !== textarea) { textarea.value = text; }
			writeLocal(text);
		}

		// Computer -> session
		function toSession(text) {
			if (!rfb || !live || readOnly || typeof text !== 'string') { return false; }
			if (text !== remote) {
				rfb.clipboardPasteFrom(text);
				remote = text;
			}
			return true;
		}
		function readLocal() {
			if (!api || !api.readText) { return Promise.reject(new Error('unavailable')); }
			return api.readText();
		}

		// Paste keys in the display: sync the computer's clipboard first, then
		// send the paste key. Ctrl+V (Cmd+V on a Mac, sent as Ctrl+V -- the
		// tools are X applications) and Shift+Insert (sent as typed: xterm and
		// friends paste with it). Copy and cut keys (Ctrl+C, Ctrl+Insert,
		// Shift+Delete) need nothing here: they reach the tool as typed and its
		// copy comes back through the "clipboard" event.
		screen.addEventListener('keydown', function (e) {
			if (readOnly || !rfb || !live || e.repeat || e.altKey) { return; }
			const isV      = (e.key === 'v' || e.key === 'V');
			const ctrlV    = isV && (e.ctrlKey || e.metaKey) && !e.shiftKey;
			const shiftIns = (e.key === 'Insert' || e.code === 'Insert') && e.shiftKey && !e.ctrlKey && !e.metaKey;
			if (!ctrlV && !shiftIns) { return; }
			if (!api || !api.readText) { return; }   // let it through as typed
			e.preventDefault();
			e.stopImmediatePropagation();
			const meta = ctrlV && e.metaKey && !e.ctrlKey;
			readLocal().then(toSession, () => { if (button) { button.classList.add('pending'); } }).then(() => {
				if (!rfb || !live) { return; }
				if (shiftIns) {
					// Shift already went to the server when it was pressed
					rfb.sendKey(0xff63, 'Insert', true);    // XK_Insert
					rfb.sendKey(0xff63, 'Insert', false);
					return;
				}
				if (meta) { rfb.sendKey(0xffe3, 'ControlLeft', true); }   // XK_Control_L
				rfb.sendKey(0x0076, 'KeyV', true);                           // XK_v
				rfb.sendKey(0x0076, 'KeyV', false);
				if (meta) { rfb.sendKey(0xffe3, 'ControlLeft', false); }
			});
		}, true);

		// Once clipboard reading is allowed, sync on every click into the display,
		// so the tool's own Paste menu and middle-click have the latest text too.
		// Never prompts: only runs if permission was already granted.
		let readGranted = false;
		if (navigator.permissions && navigator.permissions.query) {
			navigator.permissions.query({ name: 'clipboard-read' }).then((st) => {
				readGranted = (st.state === 'granted');
				st.onchange = () => { readGranted = (st.state === 'granted'); };
			}, () => {});
		}
		screen.addEventListener('pointerdown', function () {
			if (readGranted && !readOnly) { readLocal().then(toSession, () => {}); }
		}, true);

		// Panel
		panel.querySelector('.nmc-send').addEventListener('click', function () {
			if (toSession(textarea.value)) {
				say(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_SENT')); ?>);
			}
		});
		panel.querySelector('.nmc-copy').addEventListener('click', function () {
			const text = textarea.value;
			const done = () => { pending = null; markPending(false); say(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_COPIED')); ?>); };
			if (api && api.writeText) {
				api.writeText(text).then(done, () => { textarea.select(); document.execCommand('copy'); done(); });
			} else {
				textarea.select(); document.execCommand('copy'); done();
			}
		});
		function toggle(on) {
			on = (typeof on === 'boolean') ? on : panel.hidden;
			panel.hidden = !on;
			if (button) {
				button.classList.toggle('active', on);
				button.setAttribute('aria-pressed', on ? 'true' : 'false');
			}
			if (on) {
				if (remote !== null) { textarea.value = remote; }
				textarea.focus();
				textarea.select();
			} else if (rfb) {
				rfb.focus();
			}
		}
		panel.addEventListener('keydown', (e) => { if (e.key === 'Escape') { toggle(false); } });

		return {
			fromSession: fromSession,
			toggle:      toggle,
			setButton:   (b) => { button = b; }
		};
	})();

	// ----- The connection ----------------------------------------------------
	let rfb = null;
	let live = false;          // rfb is connecting or connected (not yet disconnected)
	let gen = 0;               // guards against a torn-down RFB's late events
	let everConnected = false; // recovery only kicks in after a working connection

	function connect() {
		const myGen = ++gen;

		if (rfb && live) {
			try { rfb.disconnect(); } catch (e) {}
		}
		rfb = null;
		live = false;

		try {
			// The HUBzero proxy (websockify-style) refuses a handshake without a
			// subprotocol; the legacy client offered binary/base64
			rfb = new RFB(screen, url, { credentials: { password: password }, shared: true, wsProtocols: ['binary'] });
			rfb.viewOnly      = readOnly;
			rfb.scaleViewport = false;   // the frame follows the framebuffer instead
			rfb.resizeSession = false;   // resizing is HUBzero's own request
			rfb.clipViewport  = false;
			rfb.background    = '#0b0d10';
			live = true;

			rfb.addEventListener('connect', () => {
				if (myGen !== gen) { return; }
				everConnected = true;
				setStatus(null);
				if (reco) { reco.connected(); }
				attachResizable();
				// Ask for the frame's size: the session's initial geometry, or
				// the size the user resized to before a reconnect
				requestFrameSize();
				rfb.focus();
			});
			rfb.addEventListener('disconnect', (e) => {
				if (myGen !== gen) { return; }
				live = false;
				const clean = !!(e.detail && e.detail.clean);
				setStatus(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_DISCONNECTED')); ?>);
				if (reco && everConnected) { reco.disconnected(clean); }
			});
			rfb.addEventListener('fbresize', (e) => {
				if (myGen !== gen) { return; }
				sizeFrame(e.detail.width, e.detail.height);
			});
			rfb.addEventListener('clipboard', (e) => {
				if (myGen !== gen) { return; }
				clip.fromSession(e.detail.text);
			});
			rfb.addEventListener('clientaction', (e) => {
				if (myGen !== gen) { return; }
				openClientAction(e.detail.url);
			});
			rfb.addEventListener('credentialsrequired', () => rfb.sendCredentials({ password: password }));
			rfb.addEventListener('securityfailure', (e) => {
				setStatus(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_SECURITY_FAILURE')); ?> + (e.detail && e.detail.reason ? ': ' + e.detail.reason : ''));
			});

			window.__novncRFB = rfb;
		} catch (err) {
			console.error('[novnc-modern]', err);
			setStatus(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_START_FAILED')); ?>);
		}
	}

	connect();

	// ----- Footer controls ---------------------------------------------------
	(function () {
		const footer = document.getElementById('app-footer');
		if (!footer) { return; }
		let ul = footer.querySelector('ul.app-controls');
		if (!ul) {
			ul = document.createElement('ul');
			ul.className = 'app-controls';
			footer.appendChild(ul);
		}

		// Reconnect: drops and re-establishes the connection. Icon-only; hover
		// for the label.
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
			if (rfb && live) { try { rfb.disconnect(); } catch (err) {} }
		});
		li.appendChild(a);
		ul.appendChild(li);

		// Clipboard panel (not for view-only sessions: nothing to paste into)
		if (!readOnly) {
			const liClip = document.createElement('li');
			const c = document.createElement('a');
			c.className = 'clipboard';
			c.id = 'app-btn-clipboard';
			c.href = '#';
			c.setAttribute('role', 'button');
			c.setAttribute('aria-pressed', 'false');
			c.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD')); ?>;
			c.setAttribute('aria-label', c.title);
			c.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>';
			c.addEventListener('click', function (e) {
				e.preventDefault();
				clip.toggle();
			});
			clip.setButton(c);
			liClip.appendChild(c);
			ul.appendChild(liClip);
		}

		// Privacy: blank the display for passers-by. The eye is open when
		// visible and crossed-out when hidden; the button highlights while on.
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
			const on = content.classList.toggle('privacy-on');
			hide.classList.toggle('active', on);
			hide.setAttribute('aria-pressed', on ? 'true' : 'false');
			hide.title = on ? titleShow : titleHide;
			hide.setAttribute('aria-label', on ? titleShow : titleHide);
			hide.innerHTML = on ? eyeOff : eyeOpen;
		});
		liHide.appendChild(hide);
		ul.appendChild(liHide);
	})();

	// ----- Session title editing (as in the legacy engine) -------------------
	if ($ && $.fn.editable) {
		$('#session-title.editable').editable('index.php?option=com_tools&controller=sessions&task=rename&no_html=1&id=' + $('#session-title').attr('rel'), {
			id:     'title',
			name:   'name',
			width:  '200px',
			submit: 'OK',
			cancel: 'cancel'
		});
	}
</script>
