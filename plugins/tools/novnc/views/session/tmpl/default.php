<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

if (!$this->app->sess)
{
	echo '<p class="error"><strong>' . JText::_('ERROR') . '</strong><br />' . implode('<br />', $this->output) . '</p>';
}
else
{
	// This allows for touch events to be translated to click events on mobile devices
	/*\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/novnc/jquery.ui.touch-punch.min.js');

	// Invlude NoVNC
	\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/novnc/util');
	\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/novnc/ui-hubzero');
	\Hubzero\Document\Assets::addComponentStylesheet('com_tools', 'assets/novnc/base');*/

	$this->css('./novnc/base.css')
	     ->js('./novnc/jquery.ui.touch-punch.min.js') // This allows for touch events to be translated to click events on mobile devices
	     ->js('./novnc/util.js')
	     ->js('./novnc/ui-hubzero.js');

	$base = rtrim(JURI::base(true), '/');

	$img = $base . '/plugins/tools/novnc/assets/novnc/images';
?>
	<div id="noVNC-control-bar">
		<!--noVNC Mobile Device only Buttons-->
		<div class="noVNC-buttons-left">
			<input type="image" src="<?php echo $img; ?>/drag.png" id="noVNC_view_drag_button" class="noVNC_status_button" title="Move/Drag Viewport" onclick="UI.setViewDrag();" />
			<div id="noVNC_mobile_buttons">
				<input type="image" src="<?php echo $img; ?>/mouse_none.png" id="noVNC_mouse_button0" class="noVNC_status_button" onclick="UI.setMouseButton(1);" />
				<input type="image" src="<?php echo $img; ?>/mouse_left.png" id="noVNC_mouse_button1" class="noVNC_status_button" onclick="UI.setMouseButton(2);" />
				<input type="image" src="<?php echo $img; ?>/mouse_middle.png" id="noVNC_mouse_button2" class="noVNC_status_button" onclick="UI.setMouseButton(4);" />
				<input type="image" src="<?php echo $img; ?>/mouse_right.png" id="noVNC_mouse_button4" class="noVNC_status_button" onclick="UI.setMouseButton(0);" />
				<input type="image" src="<?php echo $img; ?>/keyboard.png" id="showKeyboard" class="noVNC_status_button" value="Keyboard" title="Show Keyboard" onclick="UI.showKeyboard()" />
				<input type="email" autocapitalize="off" autocorrect="off" id="keyboardinput" class="noVNC_status_button" onKeyDown="onKeyDown(event);" onblur="UI.keyInputBlur();" />
			</div>
		</div>

		<!--noVNC Buttons-->
		<div class="noVNC-buttons-right">
			<input type="image" src="<?php echo $img; ?>/ctrlaltdel.png" id="sendCtrlAltDelButton" class="noVNC_status_button" title="Send Ctrl-Alt-Del" onclick="UI.sendCtrlAltDel();" />
			<input type="image" src="<?php echo $img; ?>/clipboard.png" id="clipboardButton" class="noVNC_status_button" title="Clipboard" onclick="UI.toggleClipboardPanel();" />
		</div>

		<!-- Clipboard Panel -->
		<div id="noVNC_clipboard" class="triangle-right top">
			<textarea id="noVNC_clipboard_text" rows="5" onfocus="UI.displayBlur();" onblur="UI.displayFocus();" onchange="UI.clipSend();"></textarea>
			<br />
			<input id="noVNC_clipboard_clear_button" type="button" value="Clear" onclick="UI.clipClear();" />
		</div>
	</div> <!-- End of noVNC-control-bar -->

	<div id="noVNC_screen">
		<div id="noVNC_screen_pad"></div>

		<div id="noVNC_status_bar" class="noVNC_status_bar">
			<div id="noVNC_status">Loading</div>
		</div>

		<div id="noVNC_container">
			<canvas id="noVNC_canvas" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>">Canvas not supported.</canvas>
		</div>
	</div>

	<script type="text/javascript">
		//JS globals for noVNC
		var host     = '<?php echo $this->output->wsproxy_host; ?>',
			port     = '<?php echo $this->output->wsproxy_port; ?>',
			password = '<?php echo $this->output->password; ?>',
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
			var app = $('#noVNC_canvas');

			if (!app.hasClass('no-resize') && !$('#app-btn-resizehandle').length) {
				var appfooter = $('#app-footer'),
					appcontent = $('#app-content'),
					appcontainer = $('#noVNC_container'),
					footermenu = $('<ul></ul>'),
					li = $('<li></li>');

				$('<a class="resize" id="app-btn-resizehandle" alt="Resize" title="Resize"><span id="app-size">' + app.attr('width').toString() + ' x ' + app.attr('height').toString() + '</span></a>')
					.appendTo(li);
				li.appendTo(footermenu);

				footermenu.appendTo(appfooter);

				appcontent.resizable({
					minHeight: 200,
					maxHeight: 3900,
					minWidth: 345,
					maxWidth: 3900,
					handles: 'se',
					resize: function(event, ui) {
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
				$(window).resize(function(e){
					if (resizeTimeout) {
						clearTimeout(resizeTimeout);
					}
					//Do the resize in a timeout incase we are dragging slowly to avoid bombarding the server
					resizeTimeout = setTimeout(function(){
						doResize($('#noVNC_canvas').attr('width'), $('#noVNC_canvas').attr('height'));
					}, 1000);
				});

				// Setup handler for tracking window focus events (delayed resize if not focused)
				$(window).focus(function(){
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

		//Final attachment for onload
		//window.onload = UI.load;
	</script>
<?php } ?>