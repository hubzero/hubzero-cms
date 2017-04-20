<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->app->sess)
{
	echo '<p class="error"><strong>' . Lang::txt('PLG_TOOLS_NOVNC_ERROR') . '</strong><br />' . implode('<br />', $this->output) . '</p>';
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
				appwrap = appcontent.parent(),
				footermenu = $('<ul></ul>');

			$('#noVNC-control-bar').removeClass('hidden');

			if (!app.hasClass('no-refresh')) {
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

			footermenu.appendTo(appfooter);

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

						if (w < 513) {
							$('.ui-resizable-handle').css('bottom', '-5em');
						} else {
							$('.ui-resizable-handle').css('bottom', '-3em');
						}

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
		});
		/*window.onscriptsload = function () {
			UI.load();
		};*/

		//Final attachment for onload
		window.onload = UI.load;
	</script>
<?php } ?>