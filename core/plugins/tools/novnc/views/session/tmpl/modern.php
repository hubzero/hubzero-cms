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

// Copy and paste can be off for tools on hosts nothing may leave (plugin config)
$clipboard = !$this->readOnly && (!isset($this->clipboard) || $this->clipboard);

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
	/* Passing notices: the bell rang while away, text left out of a paste */
	#novnc-modern-note {
		position: absolute; left: 50%; top: 14px; z-index: 6; transform: translateX(-50%);
		max-width: calc(100% - 28px); padding: 0.5rem 0.9rem; border-radius: 8px;
		background: #1f2329; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4); color: #f3f4f6;
		font: 400 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	}
	#novnc-modern-note[hidden] { display: none; }
	#novnc-modern-action a { color: #8cc4ff; font-weight: 600; white-space: nowrap; }
	/* Clipboard panel: the always-works path when the browser won't share its clipboard */
	#novnc-modern-clip {
		position: absolute; right: 12px; bottom: 12px; z-index: 7;
		width: min(22rem, calc(100% - 24px)); padding: 0.75rem;
		border-radius: 10px; background: #1f2329; box-shadow: 0 8px 26px rgba(0, 0, 0, 0.45); color: #e6e8eb;
		font: 400 13px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	}
	#novnc-modern-clip[hidden] { display: none; }
	#novnc-modern-clip .nmc-head { display: flex; align-items: center; margin: -0.2rem -0.3rem 0.35rem 0; }
	#novnc-modern-clip .nmc-title { flex: 1 1 auto; font-weight: 600; }
	#novnc-modern-clip button.nmc-close {
		padding: 0.1rem 0.45rem; border: 0; background: none; color: #9aa1ab; font-size: 18px; line-height: 1;
	}
	#novnc-modern-clip button.nmc-close:hover { color: #f3f4f6; filter: none; }
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
	#app-footer a.clipboard.pending::before {
		content: ""; position: absolute; top: 5px; right: 5px; width: 7px; height: 7px;
		border-radius: 50%; background: #2f6db3; box-shadow: 0 0 0 1.5px rgba(255, 255, 255, 0.8);
	}
	#app-footer a.clipboard.active {
		background: linear-gradient(180deg, rgba(47, 109, 179, 0.30), rgba(47, 109, 179, 0.12));
		border-color: rgba(47, 109, 179, 0.55);
		color: #1f5591;
	}
	/* Zoom: a level button in the footer with a menu that opens above it */
	/* legacy tools.css clips the footer list; the zoom menu opens out of it */
	#app-footer > ul.app-controls { overflow: visible; }
	#app-footer li.zoom-li,
	#app-footer li.keys-li { position: relative; }
	#app-footer > ul li a.zoom {
		width: auto;
		gap: 0.3rem;
		padding: 0 0.5rem;
		font: 600 0.75rem/1 Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		font-variant-numeric: tabular-nums;
	}
	#app-footer > ul li a.zoom.active,
	#app-footer > ul li a.keys.active,
	#app-footer > ul li a.keys.held {
		background: linear-gradient(180deg, rgba(47, 109, 179, 0.30), rgba(47, 109, 179, 0.12));
		border-color: rgba(47, 109, 179, 0.55);
		color: #1f5591;
	}
	#novnc-zoom-menu,
	#novnc-keys-menu {
		position: absolute; right: 0; bottom: calc(100% + 8px); z-index: 960;
		min-width: 7.5rem; margin: 0; padding: 0.3rem; list-style: none;
		border-radius: 8px; background: #1f2329; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
	}
	#novnc-zoom-menu[hidden],
	#novnc-keys-menu[hidden] { display: none; }
	#novnc-zoom-menu button,
	#novnc-keys-menu button {
		display: flex; justify-content: space-between; gap: 1rem; width: 100%;
		padding: 0.35rem 0.6rem; border: 0; border-radius: 5px; background: none; color: #e6e8eb;
		font: 500 13px/1.2 Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		font-variant-numeric: tabular-nums; text-align: left; cursor: pointer;
	}
	#novnc-zoom-menu button:hover,
	#novnc-zoom-menu button:focus-visible,
	#novnc-keys-menu button:hover,
	#novnc-keys-menu button:focus-visible { background: #2f3640; outline: none; }
	#novnc-zoom-menu button[aria-checked="true"]::after,
	#novnc-keys-menu button[aria-checked="true"]::after { content: "\2713"; color: #8cc4ff; }
	#novnc-zoom-menu .nzm-sep,
	#novnc-keys-menu .nzm-sep { height: 1px; margin: 0.25rem 0.2rem; background: #3a4048; }
	/* Special keys: the browser or the computer keeps these for itself */
	#novnc-keys-menu { min-width: 11rem; }
	#novnc-keys-menu .nkm-head { padding: 0.3rem 0.6rem 0.15rem; color: #8b929c; font: 500 11px/1.3 Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
	/* File transfer (a tool's exportfile/importfile) */
	dialog.novnc-xfer {
		display: flex; flex-direction: column; box-sizing: border-box;
		width: min(1100px, 92vw); height: min(820px, 88vh); max-width: none; max-height: none;
		margin: auto; padding: 0; border: 0; border-radius: 10px; overflow: hidden;
		background: #fff; box-shadow: 0 18px 50px rgba(0, 0, 0, 0.45);
	}
	dialog.novnc-xfer:not([open]) { display: none; }
	dialog.novnc-xfer.is-upload { width: min(560px, 92vw); height: min(420px, 88vh); }
	dialog.novnc-xfer::backdrop { background: rgba(10, 12, 16, 0.55); }
	.novnc-xfer-bar {
		flex: none; display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.5rem 0.5rem 0.9rem;
		background: #1f2329; color: #f3f4f6;
		font: 500 13px/1.4 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
	}
	.novnc-xfer-name { flex: 1 1 auto; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; }
	.novnc-xfer-bar button {
		flex: none; padding: 0.3rem 0.7rem; border: 1px solid #4a515b; border-radius: 6px;
		background: #2b3038; color: #f3f4f6; font: inherit; font-size: 12px; cursor: pointer;
	}
	.novnc-xfer-bar button:hover { filter: brightness(1.15); }
	.novnc-xfer-bar button[hidden] { display: none; }
	.novnc-xfer-bar button.nx-close { border: 0; background: none; color: #9aa1ab; font-size: 18px; line-height: 1; padding: 0.15rem 0.45rem; }
	.novnc-xfer iframe { flex: 1 1 auto; width: 100%; min-height: 0; border: 0; background: #fff; }
	#novnc-modern-action button {
		flex: none; padding: 0 0.4rem; border: 0; background: none; color: #9aa1ab;
		font: inherit; font-size: 16px; line-height: 1; cursor: pointer;
	}
</style>

<div id="novnc-modern">
	<div id="novnc-modern-status"><?php echo Lang::txt('PLG_TOOLS_NOVNC_LOADING'); ?></div>
	<div id="novnc-modern-screen"></div>
	<div id="novnc-modern-note" role="status" aria-live="polite" hidden></div>
	<?php if ($clipboard) { ?>
	<div id="novnc-modern-clip" role="dialog" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>" hidden>
		<div class="nmc-head">
			<span class="nmc-title"><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?></span>
			<button type="button" class="nmc-close" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_CLOSE'); ?>" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_CLOSE'); ?>">&times;</button>
		</div>
		<p><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_HINT'); ?></p>
		<textarea spellcheck="false" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>"></textarea>
		<div class="nmc-row">
			<span class="nmc-msg" aria-live="polite"></span>
			<button type="button" class="nmc-copy"><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_COPY'); ?></button>
			<button type="button" class="nmc-send"><?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_SEND'); ?></button>
		</div>
	</div>
	<?php } ?>
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
	const clipboardOn = <?php echo $clipboard ? 'true' : 'false'; ?>;

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

	// ----- Zoom --------------------------------------------------------------
	// The frame is the framebuffer times the zoom; noVNC scales the display to
	// fill it (scaleViewport). The tool keeps its own size: the grip and the
	// connect-time request work in framebuffer pixels (frame / zoom).
	const ZOOMS = [1, 1.25, 1.5, 1.75, 2];
	const ZOOM_KEY = 'hz-novnc-zoom';
	let zoom = 1;              // a number, or 'fit'
	try {
		const z = localStorage.getItem(ZOOM_KEY);
		if (z === 'fit') { zoom = 'fit'; }
		else if (z && parseFloat(z) >= 0.5 && parseFloat(z) <= 3) { zoom = parseFloat(z); }
	} catch (e) {}
	let fbW = 0, fbH = 0;      // the framebuffer, as last reported

	// The tool's size limits are in framebuffer pixels, so a zoom changes the
	// grip's limits rather than letting a small frame at 200% become a tiny
	// tool at 100%. Below 100% the frame keeps the minimum the footer needs.
	const FB_MIN_W = 345, FB_MIN_H = 200, FB_MAX = 3900;

	// Largest zoom that fits the page's width and the window's height
	function fitZoom() {
		if (!(fbW > 0 && fbH > 0)) { return 1; }
		const holder = appwrap ? appwrap.parentNode : content.parentNode;
		const chromeW = appwrap ? (appwrap.offsetWidth - content.offsetWidth) : 0;
		const footer = document.getElementById('app-footer');
		const header = document.getElementById('app-header');
		const chromeH = (footer ? footer.offsetHeight : 46) + (header ? header.offsetHeight : 46) + 24;
		const availW = holder.clientWidth - chromeW;
		const availH = window.innerHeight - chromeH;
		const z = Math.min(availW / fbW, availH / fbH);
		return Math.max(0.5, Math.min(3, Math.floor(z * 100) / 100));
	}
	function effZoom() {
		return (zoom === 'fit') ? fitZoom() : zoom;
	}

	// ----- Frame sizing ------------------------------------------------------
	// The frame follows the server's framebuffer; the user resizes it with the
	// corner grip, which asks the server for a framebuffer of that size.
	function sizeFrame(w, h) {
		// While the recovery/terminal overlay is up, a dead connection's empty
		// framebuffer must not collapse the frame the overlay fills.
		if (reco && reco.isHolding && reco.isHolding()) { return; }
		if (!(w > 0 && h > 0)) { return; }
		fbW = w;
		fbH = h;
		const z = effZoom();
		const cw = Math.round(w * z), ch = Math.round(h * z);
		content.style.width  = cw + 'px';
		content.style.height = ch + 'px';
		if (appwrap) { appwrap.style.width = cw + 'px'; }
		// Whole-number zooms stay crisp pixel for pixel; others smooth
		const canvas = screen.querySelector('canvas');
		if (canvas) { canvas.style.imageRendering = (z === Math.round(z)) ? 'pixelated' : 'auto'; }
		const size = document.getElementById('app-size');
		if (size) { size.textContent = w + ' x ' + h; }
		gripLimits(z);
		if (zoomUI) { zoomUI.update(); }
	}

	// The tool's size for the frame as it stands: frame / zoom
	function requestFrameSize() {
		if (!rfb) { return; }
		const z = effZoom();
		const clamp = (v, lo) => Math.max(lo, Math.min(FB_MAX, Math.round(v / z)));
		rfb.requestHubzeroResize(clamp(content.clientWidth, FB_MIN_W), clamp(content.clientHeight, FB_MIN_H));
	}

	function setZoom(z) {
		zoom = z;
		try { localStorage.setItem(ZOOM_KEY, String(z)); } catch (e) {}
		if (fbW) { sizeFrame(fbW, fbH); }
		else if (zoomUI) { zoomUI.update(); }
	}
	function stepZoom(dir) {
		const cur = effZoom();
		let next = cur;
		if (dir > 0) { next = ZOOMS.find((z) => z > cur + 0.001) || ZOOMS[ZOOMS.length - 1]; }
		else { next = ZOOMS.slice().reverse().find((z) => z < cur - 0.001) || ZOOMS[0]; }
		setZoom(next);
	}
	let zoomUI = null;   // set by the footer code
	let keysUI = null;   // likewise

	window.addEventListener('resize', () => {
		if (zoom === 'fit' && fbW) { sizeFrame(fbW, fbH); }
	});

	let resizableAttached = false;
	function attachResizable() {
		if (resizableAttached || readOnly || !$ || !$.fn.resizable) { return; }
		resizableAttached = true;
		$(content).resizable({
			handles:   'se',
			resize: function () {
				if (appwrap) { appwrap.style.width = content.clientWidth + 'px'; }
			},
			stop: function () {
				// Resizing pins Fit at the zoom it was showing
				if (zoom === 'fit') { setZoom(effZoom()); }
				requestFrameSize();
			}
		});
		gripLimits(effZoom());
	}
	function gripLimits(z) {
		if (!resizableAttached) { return; }
		$(content).resizable('option', {
			minWidth:  Math.round(FB_MIN_W * Math.max(z, 1)),
			minHeight: Math.round(FB_MIN_H * Math.max(z, 1)),
			maxWidth:  Math.round(FB_MAX * z),
			maxHeight: Math.round(FB_MAX * z)
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

		// File transfer opens in our own dialog
		if (href.indexOf('filexfer') !== -1 && window.HTMLDialogElement) {
			openFileTransfer(href);
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

	// ----- File transfer -----------------------------------------------------
	// A tool's exportfile/importfile arrives as a ClientAction for a /filexfer/
	// page, served by the tool host through the hub, so same-origin. The
	// download page is a two-row frameset, a toolbar over the file, sized for
	// a window rather than an overlay, and its own Save As/Print buttons reach
	// for window.top and fail inside one. So rather than show it as it comes:
	// the frame holding the file gets the whole frameset (text arrives in the
	// lower frame, an image is laid out in the toolbar frame, so it is decided
	// by looking), and the header above it names the file and carries Save As
	// and Print. The upload page is a plain form with its own buttons and is
	// shown as it is. Anything not recognised is left exactly as it came.
	const xfer = { dlg: null, content: null };

	function xferHasContent(win) {
		try {
			const doc = win && win.document;
			if (!doc || !doc.body) { return false; }
			if (doc.body.innerText && doc.body.innerText.trim()) { return true; }
			return !!doc.querySelector('img, pre, table, embed, object, canvas, svg');
		} catch (e) { return false; }
	}
	function xferDress(win, toolbarFrame) {
		try {
			const doc = win.document;
			if (!doc || doc.getElementById('hz-xfer-skin')) { return; }
			const st = doc.createElement('style');
			st.id = 'hz-xfer-skin';
			st.textContent = toolbarFrame
				// the image case: hide the page's logo and buttons beside it
				? '#logo,#main form{display:none!important}' +
				  '#main{display:block!important;min-height:0!important;padding:16px!important;border:0!important}' +
				  '#main img,#main embed,#main object,#main canvas{max-width:100%;height:auto}'
				// a bare text/plain document: some margin and a proper monospace
				: 'body{margin:0;padding:14px 18px;' +
				  'font:13px/1.55 ui-monospace,SFMono-Regular,Menlo,Consolas,"Liberation Mono",monospace}' +
				  'pre{margin:0;font:inherit}';
			(doc.head || doc.documentElement).appendChild(st);
		} catch (e) {}
	}
	// { view, file }: the frame showing the file, and the lower frame whose
	// URL is the /dlfile/<name> the transfer is of (empty for an image)
	function xferClaim(frame) {
		const win = frame.contentWindow;
		const doc = frame.contentDocument;
		const container = doc && (doc.getElementById('container') || doc.querySelector('frameset'));
		if (!container || !win.frames.length) { return null; }   // the upload page
		const toolbar = win.frames[0];
		const lower = win.frames['file'] || win.frames[1];
		// One frame is hidden below; its border would be a stray line
		container.setAttribute('border', '0');
		container.setAttribute('frameborder', '0');
		container.setAttribute('framespacing', '0');
		if (lower && xferHasContent(lower)) {
			container.rows = '0,100%';
			xferDress(lower, false);
			return { view: lower, file: lower };
		}
		try {
			if (toolbar && toolbar.document.querySelector('#main img, #main embed, #main object, #main canvas')) {
				container.rows = '100%,0';
				xferDress(toolbar, true);
				return { view: toolbar, file: lower || toolbar };
			}
		} catch (e) {}
		const any = lower || toolbar;
		return any ? { view: any, file: any } : null;
	}
	function xferName(win) {
		try {
			const last = decodeURIComponent(win.location.pathname.split('/').filter(Boolean).pop() || '');
			return /\.[A-Za-z0-9]{1,8}$/.test(last) ? last : '';
		} catch (e) { return ''; }
	}

	function xferDialog() {
		if (xfer.dlg) { return xfer.dlg; }
		const d = document.createElement('dialog');
		d.className = 'novnc-xfer';
		d.setAttribute('aria-label', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER')); ?>);
		d.innerHTML = '<div class="novnc-xfer-bar"><span class="novnc-xfer-name"></span>' +
			'<button type="button" data-act="save"></button>' +
			'<button type="button" data-act="print"></button>' +
			'<button type="button" class="nx-close" data-act="close">&times;</button></div>';
		d.querySelector('[data-act="save"]').textContent  = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER_SAVE')); ?>;
		d.querySelector('[data-act="print"]').textContent = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER_PRINT')); ?>;
		d.querySelector('[data-act="close"]').setAttribute('aria-label', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER_CLOSE')); ?>);

		d.querySelector('.novnc-xfer-bar').addEventListener('click', (e) => {
			const act = e.target.closest('button') && e.target.closest('button').getAttribute('data-act');
			const c = xfer.content;
			if (act === 'close') { d.close(); }
			else if (act === 'print' && c) {
				try { c.view.focus(); } catch (err) {}
				c.view.print();
			} else if (act === 'save' && c) {
				// What the page's own Save As does: the file again, as an attachment
				let href;
				try { href = c.file.location.href; } catch (err) { href = xfer.url; }
				window.open(href + (href.indexOf('?') === -1 ? '?' : '&') + 'saveas=1', '_blank');
			}
		});
		// A click on the backdrop closes, as the old overlay did
		d.addEventListener('click', (e) => { if (e.target === d) { d.close(); } });
		// Take the frame away rather than blanking it: a navigation to
		// about:blank left in flight can win against the next transfer's URL
		d.addEventListener('close', () => {
			const f = d.querySelector('iframe');
			if (f) { f.remove(); }
			xfer.content = null;
			if (rfb) { rfb.focus(); }
		});
		// The upload page may close itself the way it did inside fancybox
		if ($ && $.fancybox && $.fancybox.close) {
			const orig = $.fancybox.close;
			$.fancybox.close = function () {
				if (d.open) { d.close(); }
				return orig.apply(this, arguments);
			};
		}
		document.body.appendChild(d);
		return (xfer.dlg = d);
	}

	function openFileTransfer(href) {
		const d = xferDialog();
		const name = d.querySelector('.novnc-xfer-name');
		const btns = d.querySelectorAll('[data-act="save"], [data-act="print"]');
		const old = d.querySelector('iframe');
		if (old) { old.remove(); }

		xfer.url = href;
		xfer.content = null;
		name.textContent = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER')); ?>;
		btns.forEach((b) => { b.hidden = true; });
		d.classList.remove('is-upload');

		const frame = document.createElement('iframe');
		frame.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER')); ?>;
		d.appendChild(frame);

		const settle = () => {
			try {
				if (!frame.isConnected || !frame.contentWindow || frame.contentWindow.location.href === 'about:blank') { return; }
				const c = xferClaim(frame);
				xfer.content = c;
				if (!c) {
					d.classList.add('is-upload');
					name.textContent = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_XFER_UPLOAD')); ?>;
					btns.forEach((b) => { b.hidden = true; });
					return;
				}
				btns.forEach((b) => { b.hidden = false; });
				name.textContent = xferName(c.file) || name.textContent;
			} catch (e) {}   // not ours to read after all: leave it as it came
		};
		// Timers, not animation frames, which pause in a background tab; twice,
		// because the inner frames can still be laying out at load
		frame.addEventListener('load', () => { setTimeout(settle, 0); setTimeout(settle, 250); });

		// Open first: a frame inside a closed (display:none) dialog never
		// starts navigating
		if (!d.open) { d.showModal(); }
		frame.src = href;
	}

	// ----- Notices -----------------------------------------------------------
	const noteEl = document.getElementById('novnc-modern-note');
	let noteTimer = null;
	function note(text) {
		noteEl.textContent = text;
		noteEl.hidden = false;
		clearTimeout(noteTimer);
		noteTimer = setTimeout(() => { noteEl.hidden = true; }, 6000);
	}

	// ----- Bell: the session calls the user back -----------------------------
	// RFB's Bell message: anything in the session writing \a rings it, so a
	// tool can end a long run with `echo -e '\a'`. Terminals also ring it for
	// trivia (a failed completion), so it only acts while the user is away
	// from the tab: the title flashes, a desktop notification shows if the hub
	// already has permission for them (never asked for here), and a burst is
	// one alert. Coming back stops the flash and says how often it rang.
	const bell = (function () {
		const MARK = '\u25cf ';
		let flashing = null, base = '', rang = 0, lastAlert = 0;
		const away = () => document.hidden || !document.hasFocus();

		function ring() {
			if (!away()) { return; }
			rang++;
			if (flashing || Date.now() - lastAlert < 3000) { return; }
			lastAlert = Date.now();
			base = document.title;
			let on = false;
			flashing = setInterval(() => {
				on = !on;
				document.title = on ? MARK + base : base;
			}, 1200);
			try {
				if (window.Notification && Notification.permission === 'granted') {
					const n = new Notification(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_BELL_TITLE')); ?>, {
						body: <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_BELL_BODY')); ?>,
						tag:  'hz-session-bell'
					});
					n.onclick = () => { window.focus(); n.close(); };
				}
			} catch (e) {}
		}
		function back() {
			if (away()) { return; }
			if (flashing) {
				clearInterval(flashing);
				flashing = null;
				document.title = base;
			}
			if (rang) {
				note(rang === 1
					? <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_BELL_ONE')); ?>
					: <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_BELL_MANY')); ?>.replace('%d', rang));
				rang = 0;
			}
		}
		document.addEventListener('visibilitychange', back);
		window.addEventListener('focus', back);
		return { ring: ring };
	})();

	// ----- Clipboard ---------------------------------------------------------
	// The VNC server syncs its clipboard over RFB; this bridges that to the
	// computer's clipboard as far as the browser allows, with a panel in the
	// footer as the path that always works. Not at all when the tool's host
	// is one nothing may be copied off.
	const clip = !clipboardOn ? { fromSession() {}, toggle() {}, setButton() {} } : (function () {
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

		// Computer -> session. The wire wants LF line ends (a Windows CRLF
		// arrives in a terminal as two Enters per line), and a classic VNC
		// server takes Latin-1 only, where anything else turns into '?'. Common
		// typographic characters get their plain forms; what is left is
		// dropped and the user told, rather than pasted quietly mangled.
		const PLAIN = {
			'\u2018': "'", '\u2019': "'", '\u201a': "'", '\u201b': "'", '\u2032': "'",
			'\u201c': '"', '\u201d': '"', '\u201e': '"', '\u2033': '"',
			'\u2010': '-', '\u2011': '-', '\u2012': '-', '\u2013': '-', '\u2014': '-', '\u2212': '-',
			'\u2026': '...', '\u2022': '*', '\u2009': ' ', '\u202f': ' ', '\u200b': '', '\ufeff': ''
		};
		function utf8Clipboard() {
			// noVNC keeps the server's extended-clipboard formats private;
			// with UTF-8 text support anything can go as it is
			const f = rfb && rfb._clipboardServerCapabilitiesFormats;
			return !!(f && f[1]);
		}
		function forWire(text) {
			text = text.replace(/\r\n?/g, '\n');
			if (utf8Clipboard()) { return { text: text, dropped: 0 }; }
			let out = '', dropped = 0;
			for (const ch of text) {
				if (ch.codePointAt(0) <= 0xff) { out += ch; }
				else if (ch in PLAIN) { out += PLAIN[ch]; }
				else { dropped++; }
			}
			return { text: out, dropped: dropped };
		}
		function toSession(text) {
			if (!rfb || !live || readOnly || typeof text !== 'string') { return false; }
			const w = forWire(text);
			if (w.dropped) {
				note(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD_DROPPED')); ?>.replace('%d', w.dropped));
			}
			if (w.text !== remote) {
				rfb.clipboardPasteFrom(w.text);
				remote = w.text;
			}
			return true;
		}
		function readLocal() {
			if (!api || !api.readText) { return Promise.reject(new Error('unavailable')); }
			return api.readText();
		}

		// Once clipboard reading is allowed it can be done without a prompt
		let readGranted = false;
		if (navigator.permissions && navigator.permissions.query) {
			navigator.permissions.query({ name: 'clipboard-read' }).then((st) => {
				readGranted = (st.state === 'granted');
				st.onchange = () => { readGranted = (st.state === 'granted'); };
			}, () => {});
		}

		// Paste keys in the display: Ctrl+V (Cmd+V on a Mac) and Shift+Insert.
		// The text comes from the browser's own paste event, which every browser
		// delivers without a permission prompt (Firefox has no silent clipboard
		// read at all) -- but only to something that takes text, so the key
		// moves focus to an offscreen catcher for it. The key is kept from
		// noVNC but not cancelled, or the browser would not paste, and is
		// replayed once the session has the text. If no paste arrives the
		// clipboard is read directly where that needs no prompt, and the key
		// goes through regardless, so it never does nothing.
		const CATCH_MS = 400;
		const catcher = document.createElement('textarea');
		catcher.tabIndex = -1;
		catcher.setAttribute('aria-hidden', 'true');
		catcher.style.cssText = 'position:fixed;left:-9999px;top:0;width:1px;height:1px;opacity:0;';
		document.body.appendChild(catcher);
		let cycle = null;   // the paste key being handled

		function sendChord(shiftIns) {
			if (!rfb || !live) { return; }
			// The keys the user is holding already reached the session -- Cmd
			// as Super, so a bare replay would land as Super+Ctrl+V -- and
			// Windows' AltGr detection may still be holding Ctrl back. Let
			// noVNC release everything (private in 1.7.0; it drops the
			// physical releases that follow) and send the whole chord here.
			const kb = rfb._keyboard;
			if (kb && typeof kb._allKeysUp === 'function') { kb._allKeysUp(); }
			const mod = shiftIns ? [0xffe1, 'ShiftLeft'] : [0xffe3, 'ControlLeft'];   // XK_Shift_L, XK_Control_L
			const key = shiftIns ? [0xff63, 'Insert'] : [0x0076, 'KeyV'];             // XK_Insert, XK_v
			rfb.sendKey(mod[0], mod[1], true);
			rfb.sendKey(key[0], key[1], true);
			rfb.sendKey(key[0], key[1], false);
			rfb.sendKey(mod[0], mod[1], false);
		}
		function finish(c, text) {
			if (c.done) { return; }
			c.done = true;
			clearTimeout(c.timer);
			if (document.activeElement === catcher && rfb) { rfb.focus(); }
			if (text) { toSession(text); }
			// Classic clipboard text is on the wire ahead of the key; the
			// extended protocol's notify/request round trip needs a moment
			setTimeout(() => sendChord(c.shiftIns), utf8Clipboard() ? 300 : 0);
		}
		function rescue(c) {
			if (c.done) { return; }
			if (readGranted) { readLocal().then((t) => finish(c, t), () => finish(c, null)); }
			else { finish(c, null); }
		}
		catcher.addEventListener('paste', function (e) {
			e.preventDefault();
			const c = cycle;
			// A paste after the rescue took over is the same key arriving late
			if (!c || c.done) { return; }
			finish(c, e.clipboardData ? e.clipboardData.getData('text/plain') : null);
		});
		screen.addEventListener('keydown', function (e) {
			if (readOnly || !rfb || !live || e.repeat || e.altKey) { return; }
			const isV      = (e.key === 'v' || e.key === 'V');
			const ctrlV    = isV && (e.ctrlKey || e.metaKey) && !e.shiftKey;
			const shiftIns = (e.key === 'Insert' || e.code === 'Insert') && e.shiftKey && !e.ctrlKey && !e.metaKey;
			if (!ctrlV && !shiftIns) { return; }
			e.stopImmediatePropagation();
			if (cycle && !cycle.done) { finish(cycle, null); }
			const c = cycle = { shiftIns: shiftIns, done: false };
			catcher.value = '';
			catcher.focus({ preventScroll: true });
			c.timer = setTimeout(() => rescue(c), CATCH_MS);
		}, true);

		// Zoom keys inside the display (instead of zooming the whole page)
		screen.addEventListener('keydown', function (e) {
			if (!(e.ctrlKey || e.metaKey) || e.altKey) { return; }
			let dir = 0;
			if (e.key === '=' || e.key === '+') { dir = 1; }
			else if (e.key === '-' || e.key === '_') { dir = -1; }
			else if (e.key === '0') { dir = 0; }
			else { return; }
			e.preventDefault();
			e.stopImmediatePropagation();
			if (dir === 0) { setZoom(1); } else { stepZoom(dir); }
		}, true);

		// Once clipboard reading is allowed, sync on every click into the display,
		// so the tool's own Paste menu and middle-click have the latest text too.
		// Never prompts: only runs if permission was already granted.
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
		panel.querySelector('.nmc-close').addEventListener('click', () => toggle(false));

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
			rfb.scaleViewport = true;    // fills the frame: framebuffer x zoom
			rfb.resizeSession = false;   // resizing is HUBzero's own request
			rfb.clipViewport  = false;
			rfb.background    = '#0b0d10';
			live = true;

			rfb.addEventListener('connect', () => {
				if (myGen !== gen) { return; }
				// Back after a drop: the session may have been restarted or
				// extended meanwhile, so re-read its deadline
				if (everConnected && window.SessionTimer) { window.SessionTimer.refresh(); }
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
				if (keysUI) { keysUI.reset(); }
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
			rfb.addEventListener('bell', () => {
				if (myGen !== gen) { return; }
				bell.ring();
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

		// Zoom: the level, with a menu of levels above it
		(function () {
			const li = document.createElement('li');
			li.className = 'zoom-li';
			const btn = document.createElement('a');
			btn.className = 'zoom';
			btn.id = 'app-btn-zoom';
			btn.href = '#';
			btn.setAttribute('role', 'button');
			btn.setAttribute('aria-haspopup', 'menu');
			btn.setAttribute('aria-expanded', 'false');
			btn.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_ZOOM')); ?>;
			btn.setAttribute('data-help', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_ZOOM')); ?>);
			btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg><span class="zoom-level"></span>';
			const menu = document.createElement('div');
			menu.id = 'novnc-zoom-menu';
			menu.setAttribute('role', 'menu');
			menu.hidden = true;
			const items = [];
			function add(value, label) {
				const b = document.createElement('button');
				b.type = 'button';
				b.setAttribute('role', 'menuitemradio');
				b.textContent = label;
				b.addEventListener('click', () => { setZoom(value); close(); if (rfb) { rfb.focus(); } });
				items.push([value, b]);
				menu.appendChild(b);
			}
			add('fit', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_ZOOM_FIT')); ?>);
			const sep = document.createElement('div');
			sep.className = 'nzm-sep';
			menu.appendChild(sep);
			ZOOMS.forEach((z) => add(z, Math.round(z * 100) + '%'));

			function open() {
				menu.hidden = false;
				btn.classList.add('active');
				btn.setAttribute('aria-expanded', 'true');
				const cur = items.find(([v, b]) => b.getAttribute('aria-checked') === 'true');
				(cur ? cur[1] : items[0][1]).focus();
			}
			function close() {
				menu.hidden = true;
				btn.classList.remove('active');
				btn.setAttribute('aria-expanded', 'false');
			}
			btn.addEventListener('click', (e) => { e.preventDefault(); if (menu.hidden) { open(); } else { close(); } });
			menu.addEventListener('keydown', (e) => {
				const list = items.map(([v, b]) => b);
				const i = list.indexOf(document.activeElement);
				if (e.key === 'Escape') { close(); btn.focus(); }
				else if (e.key === 'ArrowDown') { e.preventDefault(); list[(i + 1) % list.length].focus(); }
				else if (e.key === 'ArrowUp') { e.preventDefault(); list[(i - 1 + list.length) % list.length].focus(); }
			});
			document.addEventListener('pointerdown', (e) => { if (!menu.hidden && !li.contains(e.target)) { close(); } }, true);

			li.appendChild(btn);
			li.appendChild(menu);
			ul.appendChild(li);

			zoomUI = {
				update: function () {
					const z = effZoom();
					btn.querySelector('.zoom-level').textContent = Math.round(z * 100) + '%';
					const label = (zoom === 'fit')
						? <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_ZOOM_FIT')); ?> + ' (' + Math.round(z * 100) + '%)'
						: Math.round(z * 100) + '%';
					btn.setAttribute('aria-label', btn.title + ': ' + label);
					items.forEach(([v, b]) => b.setAttribute('aria-checked', (v === zoom) ? 'true' : 'false'));
				}
			};
			zoomUI.update();
		})();

		// Reconnect: drops and re-establishes the connection. Icon-only; hover
		// for the label.
		const li = document.createElement('li');
		const a  = document.createElement('a');
		a.className = 'reconnect';
		a.id = 'app-btn-reconnect';
		a.href = '#';
		a.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_BTN')); ?>;
		a.setAttribute('aria-label', a.title);
		a.setAttribute('data-help', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_RECONNECT')); ?>);
		a.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>';
		a.addEventListener('click', function (e) {
			e.preventDefault();
			if (window.SessionTimer) { window.SessionTimer.refresh(); }
			if (reco) { reco.pending(); }
			if (rfb && live) { try { rfb.disconnect(); } catch (err) {} }
		});
		li.appendChild(a);
		ul.appendChild(li);

		// Special keys: ones the browser or the computer keeps for itself
		// (Ctrl+Alt+Del, Esc out of full screen, Tab), and Ctrl, Alt and Super
		// held down for the next key or click, for Ctrl+W, Alt+Tab and the like
		if (!readOnly) (function () {
			const KEYS = {
				ctrl:  [0xffe3, 'ControlLeft'],
				alt:   [0xffe9, 'AltLeft'],
				super: [0xffeb, 'MetaLeft'],
				esc:   [0xff1b, 'Escape'],
				tab:   [0xff09, 'Tab']
			};
			const held = new Set();

			const li = document.createElement('li');
			li.className = 'keys-li';
			const btn = document.createElement('a');
			btn.className = 'keys';
			btn.id = 'app-btn-keys';
			btn.href = '#';
			btn.setAttribute('role', 'button');
			btn.setAttribute('aria-haspopup', 'menu');
			btn.setAttribute('aria-expanded', 'false');
			btn.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS')); ?>;
			btn.setAttribute('aria-label', btn.title);
			btn.setAttribute('data-help', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_KEYS')); ?>);
			btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="6" y1="9" x2="6" y2="9"/><line x1="10" y1="9" x2="10" y2="9"/><line x1="14" y1="9" x2="14" y2="9"/><line x1="18" y1="9" x2="18" y2="9"/><line x1="6" y1="13" x2="6" y2="13"/><line x1="18" y1="13" x2="18" y2="13"/><line x1="10" y1="13" x2="14" y2="13"/><line x1="7" y1="16" x2="17" y2="16"/></svg>';
			const menu = document.createElement('div');
			menu.id = 'novnc-keys-menu';
			menu.setAttribute('role', 'menu');
			menu.hidden = true;
			const items = [];
			function add(label, act, check) {
				const b = document.createElement('button');
				b.type = 'button';
				b.setAttribute('role', check ? 'menuitemcheckbox' : 'menuitem');
				b.textContent = label;
				b.addEventListener('click', () => { act(); close(); if (rfb) { rfb.focus(); } });
				items.push(b);
				menu.appendChild(b);
				return b;
			}
			function tap(k) {
				if (!rfb || !live) { return; }
				rfb.sendKey(KEYS[k][0], KEYS[k][1], true);
				rfb.sendKey(KEYS[k][0], KEYS[k][1], false);
			}
			function hold(k, on) {
				if (on === held.has(k)) { return; }
				if (on) { held.add(k); } else { held.delete(k); }
				if (rfb && live) { rfb.sendKey(KEYS[k][0], KEYS[k][1], on); }
				update();
			}
			function releaseAll() { held.forEach((k) => hold(k, false)); }

			add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_CAD')); ?>, () => { if (rfb && live) { rfb.sendCtrlAltDel(); } });
			add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_ESC')); ?>, () => tap('esc'));
			add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_TAB')); ?>, () => tap('tab'));
			const sep = document.createElement('div');
			sep.className = 'nzm-sep';
			menu.appendChild(sep);
			const head = document.createElement('div');
			head.className = 'nkm-head';
			head.textContent = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_HOLD')); ?>;
			menu.appendChild(head);
			const toggles = {
				ctrl:  add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_CTRL')); ?>, () => hold('ctrl', !held.has('ctrl')), true),
				alt:   add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_ALT')); ?>, () => hold('alt', !held.has('alt')), true),
				super: add(<?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS_SUPER')); ?>, () => hold('super', !held.has('super')), true)
			};

			function update() {
				Object.keys(toggles).forEach((k) => toggles[k].setAttribute('aria-checked', held.has(k) ? 'true' : 'false'));
				btn.classList.toggle('held', held.size > 0);
				const names = Object.keys(toggles).filter((k) => held.has(k)).map((k) => toggles[k].textContent);
				btn.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_KEYS')); ?> + (names.length ? ' (' + names.join('+') + ')' : '');
				btn.setAttribute('aria-label', btn.title);
			}

			// Held keys let go after the next key or click in the display. The
			// release waits a turn, so the display sends that key or click first.
			function afterUse(e) {
				if (!held.size) { return; }
				if (e.type === 'keyup' && /^(Control|Alt|Meta|Shift|OS)/.test(e.code || '')) { return; }
				setTimeout(releaseAll, 0);
			}
			screen.addEventListener('keyup', afterUse, true);
			screen.addEventListener('pointerup', afterUse, true);

			function open() {
				menu.hidden = false;
				btn.classList.add('active');
				btn.setAttribute('aria-expanded', 'true');
				items[0].focus();
			}
			function close() {
				menu.hidden = true;
				btn.classList.remove('active');
				btn.setAttribute('aria-expanded', 'false');
			}
			btn.addEventListener('click', (e) => { e.preventDefault(); if (menu.hidden) { open(); } else { close(); } });
			menu.addEventListener('keydown', (e) => {
				const i = items.indexOf(document.activeElement);
				if (e.key === 'Escape') { close(); btn.focus(); }
				else if (e.key === 'ArrowDown') { e.preventDefault(); items[(i + 1) % items.length].focus(); }
				else if (e.key === 'ArrowUp') { e.preventDefault(); items[(i - 1 + items.length) % items.length].focus(); }
			});
			document.addEventListener('pointerdown', (e) => { if (!menu.hidden && !li.contains(e.target)) { close(); } }, true);

			li.appendChild(btn);
			li.appendChild(menu);
			ul.appendChild(li);
			update();

			// A dropped connection drops whatever the server had held
			keysUI = { reset: () => { held.clear(); update(); } };
		})();

		// Clipboard panel (not for view-only sessions, nothing to paste into,
		// nor for tools on hosts nothing may be copied off)
		if (clipboardOn) {
			const liClip = document.createElement('li');
			const c = document.createElement('a');
			c.className = 'clipboard';
			c.id = 'app-btn-clipboard';
			c.href = '#';
			c.setAttribute('role', 'button');
			c.setAttribute('aria-pressed', 'false');
			c.title = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD')); ?>;
			c.setAttribute('aria-label', c.title);
			c.setAttribute('data-help', <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_CLIPBOARD')); ?>);
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
		const helpHide  = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_HIDE')); ?>;
		const helpShow  = <?php echo json_encode(Lang::txt('PLG_TOOLS_NOVNC_HELP_SHOW')); ?>;
		const liHide = document.createElement('li');
		const hide = document.createElement('a');
		hide.className = 'hide-display';
		hide.id = 'app-btn-hide';
		hide.href = '#';
		hide.setAttribute('role', 'button');
		hide.setAttribute('aria-pressed', 'false');
		hide.title = titleHide;
		hide.setAttribute('aria-label', titleHide);
		hide.setAttribute('data-help', helpHide);
		hide.innerHTML = eyeOpen;
		hide.addEventListener('click', function (e) {
			e.preventDefault();
			const on = content.classList.toggle('privacy-on');
			hide.classList.toggle('active', on);
			hide.setAttribute('aria-pressed', on ? 'true' : 'false');
			hide.title = on ? titleShow : titleHide;
			hide.setAttribute('aria-label', on ? titleShow : titleHide);
			hide.setAttribute('data-help', on ? helpShow : helpHide);
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
