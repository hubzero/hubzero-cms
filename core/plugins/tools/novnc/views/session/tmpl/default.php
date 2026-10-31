<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->app->sess)
{
	echo '<p class="error"><strong>' . Lang::txt('PLG_TOOLS_NOVNC_ERROR') . '</strong><br />' . implode('<br />', $this->output) . '</p>';
}
else
{
	// Which VNC JS engine to run: the bundled 2014 noVNC with HUBzero extensions
	// ("legacy"), current noVNC with the HUBzero patches reapplied ("modern"), or
	// stock upstream noVNC from a CDN ("vanilla"). The plugin's vnc_engine config
	// sets the default; &vncengine=legacy|modern|vanilla overrides it per-request.
	$engine = strtolower(Request::getWord('vncengine', ''));
	if (!$engine)
	{
		$engine = strtolower($this->params->get('vnc_engine', 'modern'));
	}
	if (!in_array($engine, array('legacy', 'modern', 'vanilla')))
	{
		$engine = 'modern';
	}

	if ($engine == 'modern')
	{
		require __DIR__ . DIRECTORY_SEPARATOR . 'modern.php';
	}
	else if ($engine == 'vanilla')
	{
		require __DIR__ . DIRECTORY_SEPARATOR . 'vanilla.php';
	}
	else
	{
	$this->js('jquery.editable.min.js', 'system');

	// Invlude NoVNC
	$this->css('./novnc/base.css')
	     ->js('./novnc/jquery.ui.touch-punch.min.js') // This allows for touch events to be translated to click events on mobile devices
	     ->js('./novnc/util.js')
	     ->js('./novnc/webutil.js')
	     ->js('./novnc/base64.js')
	     ->js('./novnc/websock.js')
	     ->js('./novnc/des.js')
	     ->js('./novnc/keysymdef.js')
	     ->js('./novnc/keyboard.js')
	     ->js('./novnc/input.js')
	     ->js('./novnc/display.js')
	     ->js('./novnc/jsunzip.js')
	     ->js('./novnc/rfb.js')
	     ->js('./novnc/keysym.js')
	     ->js('./novnc/ui-hubzero.js');

	// Shared recovery overlay: registers assets and emits #novnc-reconnect-config
	require __DIR__ . DIRECTORY_SEPARATOR . '_reconnect.php';

	$base = rtrim(Request::base(true), '/');

	$img = $base . '/core/plugins/tools/novnc/assets/novnc/images';
?>
	<div id="noVNC-control-bar" class="hidden">
		<!--noVNC Mobile Device only Buttons-->
		<div class="noVNC-buttons-left">
			<input type="image" src="<?php echo $img; ?>/drag.png" id="noVNC_view_drag_button" class="noVNC_status_button" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_MOVE_VIEWPORT'); ?>" onclick="UI.setViewDrag();" />
			<div id="noVNC_mobile_buttons">
				<input type="image" src="<?php echo $img; ?>/mouse_none.png" id="noVNC_mouse_button0" class="noVNC_status_button" onclick="UI.setMouseButton(1);" />
				<input type="image" src="<?php echo $img; ?>/mouse_left.png" id="noVNC_mouse_button1" class="noVNC_status_button" onclick="UI.setMouseButton(2);" />
				<input type="image" src="<?php echo $img; ?>/mouse_middle.png" id="noVNC_mouse_button2" class="noVNC_status_button" onclick="UI.setMouseButton(4);" />
				<input type="image" src="<?php echo $img; ?>/mouse_right.png" id="noVNC_mouse_button4" class="noVNC_status_button" onclick="UI.setMouseButton(0);" />
				<input type="image" src="<?php echo $img; ?>/keyboard.png" id="showKeyboard" class="noVNC_status_button" value="<?php echo Lang::txt('PLG_TOOLS_NOVNC_KEYBOARD'); ?>" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_SHOW_KEYBOARD'); ?>" onclick="UI.showKeyboard()" />
				<input type="email" autocapitalize="off" autocorrect="off" id="keyboardinput" class="noVNC_status_button" onKeyDown="onKeyDown(event);" onblur="UI.keyInputBlur();" />
			</div>
		</div>

		<!--noVNC Buttons-->
		<div class="noVNC-buttons-right">
			<input type="image" src="<?php echo $img; ?>/ctrlaltdel.png" id="sendCtrlAltDelButton" class="noVNC_status_button" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CTRLALTDEL'); ?>" onclick="UI.sendCtrlAltDel();" />
			<input type="image" src="<?php echo $img; ?>/clipboard.png" id="clipboardButton" class="noVNC_status_button" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>" onclick="UI.toggleClipboardPanel();" />
		</div>

		<!-- Clipboard Panel -->
		<div id="noVNC_clipboard" class="triangle-right top">
			<textarea id="noVNC_clipboard_text" rows="5" onfocus="UI.displayBlur();" onblur="UI.displayFocus();" onchange="UI.clipSend();"></textarea>
			<br />
			<input id="noVNC_clipboard_clear_button" type="button" value="<?php echo Lang::txt('PLG_TOOLS_NOVNC_CLEAR'); ?>" onclick="UI.clipClear();" />
		</div>
	</div> <!-- End of noVNC-control-bar -->

	<div id="noVNC_screen">
		<div id="noVNC_screen_pad"></div>

		<div id="noVNC_status_bar" class="noVNC_status_bar">
			<div id="noVNC_status"><?php echo Lang::txt('PLG_TOOLS_NOVNC_LOADING'); ?></div>
		</div>

		<div id="noVNC_container">
			<canvas id="noVNC_canvas" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>"><?php echo Lang::txt('PLG_TOOLS_NOVNC_ERROR_NO_CANVAS'); ?></canvas>
		</div>
	</div>

	<script type="text/javascript">
		//JS globals for noVNC
		var host     = '<?php echo $this->output->wsproxy_host; ?>',
			port     = '<?php echo $this->output->wsproxy_port; ?>',
			password = "<?php echo addslashes($this->output->password); ?>",
			token    = '<?php echo $this->output->token; ?>',
			encrypt  = ('<?php echo $this->output->wsproxy_encrypt; ?>' == 'Yes' ? true : false),
			connectPath,
			decryptPath;

		connectPath = 'websockify?token=' + token;

		//Wire up the resizable element for this page
		var resizeTimeout;
		var resizeAttached = false;
		var hPadding = 5;

		UI.normalStateAchieved = function() {
			var app = $('#noVNC_canvas'),
				appfooter = $('#app-footer'),
				appcontent = $('#app-content'),
				appcontainer = $('#noVNC_container'),
				appwrap = appcontent.parent();

			// normalStateAchieved fires on EVERY transition to 'normal' (every
			// reconnect included), so reuse the existing controls menu and guard
			// each button by id -- appending a fresh menu each time duplicated
			// the footer buttons across the frame.
			var footermenu = appfooter.find('ul.app-controls');
			if (!footermenu.length) {
				footermenu = $('<ul class="app-controls"></ul>').appendTo(appfooter);
			}

			$('#noVNC-control-bar').removeClass('hidden');

			if (!app.hasClass('no-refresh') && !$('#app-btn-refresh').length) {
				var li = $('<li></li>');
				$('<a class="refresh" id="app-btn-refresh" alt="<?php echo Lang::txt('PLG_TOOLS_NOVNC_REFRESH'); ?>" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_REFRESH'); ?>"><span><?php echo Lang::txt('PLG_TOOLS_NOVNC_REFRESH'); ?></span></a>')
					.on('click', function(event) {
						//UI.requestRefresh();
						var w = parseFloat(appcontent.width()),
							h = parseFloat(appcontent.height());
						appcontent.trigger("resize");
						doResize(w, h + 1);
						doResize(w, h);
					})
					.appendTo(li);
				li.appendTo(footermenu);
			}

			// Reconnect: drops and re-establishes the VNC connection (also the way
			// to exercise the recovery sequence). Icon-only; hover for the label.
			if (!app.hasClass('no-reconnect') && !$('#app-btn-reconnect').length) {
				var liRe = $('<li></li>');
				$('<a class="reconnect" id="app-btn-reconnect" href="#" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_BTN'); ?>" aria-label="<?php echo Lang::txt('PLG_TOOLS_NOVNC_RECONNECT_BTN'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></a>')
					.on('click', function(event) {
						event.preventDefault();
						if (window.SessionTimer) { window.SessionTimer.refresh(); }
						if (window.NoVNCReconnect) { NoVNCReconnect.pending(); }
						if (UI.rfb) {
							try { UI.rfb.disconnect(); } catch (e) {}
						}
					})
					.appendTo(liRe);
				liRe.appendTo(footermenu);
			}

			// Privacy: blank the display so a passer-by can't read it. Icon-only;
			// the eye is open when visible and crossed-out when hidden.
			if (!app.hasClass('no-hide') && !$('#app-btn-hide').length) {
				var eyeOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
				var eyeOff  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
				var titleHide = '<?php echo Lang::txt('PLG_TOOLS_NOVNC_HIDE'); ?>';
				var titleShow = '<?php echo Lang::txt('PLG_TOOLS_NOVNC_SHOW'); ?>';
				var liHide = $('<li></li>');
				var aHide = $('<a class="hide-display" id="app-btn-hide" href="#" role="button" aria-pressed="false"></a>')
					.attr({ title: titleHide, 'aria-label': titleHide })
					.html(eyeOpen);
				aHide.on('click', function(event) {
						event.preventDefault();
						var content = document.getElementById('app-content');
						if (!content) { return; }
						var on = content.classList.toggle('privacy-on');
						$(this).toggleClass('active', on)
							.attr({ 'aria-pressed': on ? 'true' : 'false', title: on ? titleShow : titleHide, 'aria-label': on ? titleShow : titleHide })
							.html(on ? eyeOff : eyeOpen);
					})
					.appendTo(liHide);
				liHide.appendTo(footermenu);
			}

			if (!app.hasClass('no-resize') && !$('#app-btn-resizehandle').length) {
				var li = $('<li></li>');
				$('<a class="resize" id="app-btn-resizehandle" alt="<?php echo Lang::txt('PLG_TOOLS_NOVNC_RESIZE'); ?>" title="<?php echo Lang::txt('PLG_TOOLS_NOVNC_RESIZE'); ?>"><span id="app-size">' + app.attr('width').toString() + ' x ' + app.attr('height').toString() + '</span></a>')
					.appendTo(li);
				li.appendTo(footermenu);

				appcontent.resizable({
					minHeight: 200,
					maxHeight: 3900,
					minWidth: 345,
					maxWidth: 3900,
					handles: 'se',
					resize: function(event, ui) {
						appwrap
							.width(appcontent.width());
						appcontainer
							.width(appcontent.width())
							.height(appcontent.height());
						$('#app-size').html(appcontent.width() + ' x ' + appcontent.height());
					},
					stop: function(event, ui) {
						var w = parseFloat(appcontent.width()),
							h = parseFloat(appcontent.height());

						$('#app-size').html(w.toString() + ' x ' + h.toString());

						// (The grip is pinned to the content's bottom-right corner
						// via CSS; no negative-offset repositioning needed.)

						// We need to set the width of the wrapping container
						// Otherwise some elements may overflow, pushing the
						// wrap to larger than it should be
						appwrap
							.width(w);

						app
							.attr('width', w)
							.attr('height', h);

						appcontainer
							.width(w)
							.height(h);

						doResize(w, h);
					}
				});
			}

			if (!resizeAttached) {
				resizeAttached = true;
				//Setup a handler to track window resizes
				$(window).resize(function(e) {
					if (resizeTimeout) {
						clearTimeout(resizeTimeout);
					}
					//Do the resize in a timeout incase we are dragging slowly to avoid bombarding the server
					resizeTimeout = setTimeout(function() {
						doResize($('#noVNC_canvas').attr('width'), $('#noVNC_canvas').attr('height'));
					}, 1000);
				});

				// Setup handler for tracking window focus events (delayed resize if not focused)
				$(window).focus(function() {
					$(window).resize();
				});

				// When the page first loads, fire a resize event to get the current screen size
				$(window).resize();
			}
		};

		function doResize(w, h) {
			if (!document.hasFocus) {
				return;
			}
			UI.requestResize(w, h); //Invoke resize on the server
			UI.resizeContainers(w, h); // Update container elements' dimensions
		}

		function getScrollBarDimensions() {
			var elm = document.documentElement.offsetHeight ? document.documentElement : document.body,

			curX = elm.clientWidth,
			curY = elm.clientHeight,

			hasScrollX = elm.scrollWidth > curX,
			hasScrollY = elm.scrollHeight > curY,

			prev = elm.style.overflow,

			r = {
				vertical: 0,
				horizontal: 0
			};

			if (!hasScrollY && !hasScrollX) {
				return r;
			}

			elm.style.overflow = "hidden";
			if (hasScrollY) {
				r.vertical = elm.clientWidth - curX;
			}
			if (hasScrollX) {
				r.horizontal = elm.clientHeight - curY;
			}
			elm.style.overflow = prev;
			return r;
		}

		jQuery(document).ready(function($) {
			// Inititate session title editing
			$('#session-title.editable').editable('index.php?option=com_tools&controller=sessions&task=rename&no_html=1&id=' + $('#session-title').attr('rel'), {
				id   : 'title',
				name : 'name',
				width : '200px',
				submit : 'OK',
				cancel : 'cancel'
			});

			// Connection recovery: drive the shared overlay from the legacy UI
			// state machine. Wrapping UI.updateState here (before window.onload
			// runs UI.load) lets us observe connect/disconnect without touching
			// ui-hubzero.js. Recovery only engages after a working connection.
			if (window.NoVNCReconnect) {
				var everConnected = false;
				try {
					var rcfg = JSON.parse(document.getElementById('novnc-reconnect-config').textContent);
					NoVNCReconnect.init({
						mount:    document.getElementById('noVNC_screen'),
						sess:     rcfg.sess,
						apiBase:  rcfg.apiBase,
						links:    rcfg.links,
						strings:  rcfg.strings,
						reconnect: function() {
							try {
								UI.rfb.connect(host, port, password, connectPath);
							} catch (e) {
								if (window.console) { console.warn('[novnc-reconnect] legacy reconnect failed', e); }
							}
						}
					});

					var _origUpdateState = UI.updateState;
					UI.updateState = function(rfb, state, oldstate, msg) {
						_origUpdateState.call(UI, rfb, state, oldstate, msg);
						if (state === 'normal') {
							everConnected = true;
							NoVNCReconnect.connected();
						} else if (everConnected &&
							(state === 'failed' || state === 'fatal' || state === 'disconnected')) {
							NoVNCReconnect.disconnected(state === 'disconnected');
						}
					};

					// A dead connection clears the framebuffer, which fires
					// onFBResize -> resizeContainers(0,0) and collapses
					// #app-content / #app-wrap down to the empty canvas. While the
					// recovery/terminal overlay is up, freeze the frame so the
					// viewport (and the overlay filling it) keep their size.
					var _origResizeContainers = UI.resizeContainers;
					UI.resizeContainers = function(w, h) {
						if (NoVNCReconnect.isHolding()) {
							return;
						}
						return _origResizeContainers.call(UI, w, h);
					};
				} catch (e) {
					if (window.console) { console.warn('[novnc-reconnect] init failed', e); }
				}
			}
		});
		/*window.onscriptsload = function () {
			UI.load();
		};*/

		//Final attachment for onload
		window.onload = UI.load;
	</script>
<?php
	} // end legacy engine
} // end has-session
