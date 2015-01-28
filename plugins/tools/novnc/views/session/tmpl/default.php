<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

if (!$this->app->sess)
{
	echo '<p class="error"><strong>' . JText::_('PLG_TOOLS_NOVNC_ERROR') . '</strong><br />' . implode('<br />', $this->output) . '</p>';
}
else
{
	// Invlude NoVNC
	$this->css('./novnc/base.css')
	     ->js('./novnc/jquery.ui.touch-punch.min.js') // This allows for touch events to be translated to click events on mobile devices
	     ->js('./novnc/util.js')
	     ->js('./novnc/ui-hubzero.js');

	$base = rtrim(JURI::base(true), '/');

	$img = $base . '/plugins/tools/novnc/assets/novnc/images';
?>
	<div id="noVNC-control-bar" class="hidden">
		<!--noVNC Mobile Device only Buttons-->
		<div class="noVNC-buttons-left">
			<input type="image" src="<?php echo $img; ?>/drag.png" id="noVNC_view_drag_button" class="noVNC_status_button" title="<?php echo JText::_('PLG_TOOLS_NOVNC_MOVE_VIEWPORT'); ?>" onclick="UI.setViewDrag();" />
			<div id="noVNC_mobile_buttons">
				<input type="image" src="<?php echo $img; ?>/mouse_none.png" id="noVNC_mouse_button0" class="noVNC_status_button" onclick="UI.setMouseButton(1);" />
				<input type="image" src="<?php echo $img; ?>/mouse_left.png" id="noVNC_mouse_button1" class="noVNC_status_button" onclick="UI.setMouseButton(2);" />
				<input type="image" src="<?php echo $img; ?>/mouse_middle.png" id="noVNC_mouse_button2" class="noVNC_status_button" onclick="UI.setMouseButton(4);" />
				<input type="image" src="<?php echo $img; ?>/mouse_right.png" id="noVNC_mouse_button4" class="noVNC_status_button" onclick="UI.setMouseButton(0);" />
				<input type="image" src="<?php echo $img; ?>/keyboard.png" id="showKeyboard" class="noVNC_status_button" value="<?php echo JText::_('PLG_TOOLS_NOVNC_KEYBOARD'); ?>" title="<?php echo JText::_('PLG_TOOLS_NOVNC_SHOW_KEYBOARD'); ?>" onclick="UI.showKeyboard()" />
				<input type="email" autocapitalize="off" autocorrect="off" id="keyboardinput" class="noVNC_status_button" onKeyDown="onKeyDown(event);" onblur="UI.keyInputBlur();" />
			</div>
		</div>

		<!--noVNC Buttons-->
		<div class="noVNC-buttons-right">
			<input type="image" src="<?php echo $img; ?>/ctrlaltdel.png" id="sendCtrlAltDelButton" class="noVNC_status_button" title="<?php echo JText::_('PLG_TOOLS_NOVNC_CTRLALTDEL'); ?>" onclick="UI.sendCtrlAltDel();" />
			<input type="image" src="<?php echo $img; ?>/clipboard.png" id="clipboardButton" class="noVNC_status_button" title="<?php echo JText::_('PLG_TOOLS_NOVNC_CLIPBOARD'); ?>" onclick="UI.toggleClipboardPanel();" />
		</div>

		<!-- Clipboard Panel -->
		<div id="noVNC_clipboard" class="triangle-right top">
			<textarea id="noVNC_clipboard_text" rows="5" onfocus="UI.displayBlur();" onblur="UI.displayFocus();" onchange="UI.clipSend();"></textarea>
			<br />
			<input id="noVNC_clipboard_clear_button" type="button" value="<?php echo JText::_('PLG_TOOLS_NOVNC_CLEAR'); ?>" onclick="UI.clipClear();" />
		</div>
	</div> <!-- End of noVNC-control-bar -->

	<div id="noVNC_screen">
		<div id="noVNC_screen_pad"></div>

		<div id="noVNC_status_bar" class="noVNC_status_bar">
			<div id="noVNC_status"><?php echo JText::_('PLG_TOOLS_NOVNC_LOADING'); ?></div>
		</div>

		<div id="noVNC_container">
			<canvas id="noVNC_canvas" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>"><?php echo JText::_('PLG_TOOLS_NOVNC_ERROR_NO_CANVAS'); ?></canvas>
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
			var app = $('#noVNC_canvas'),
				appfooter = $('#app-footer'),
				appcontent = $('#app-content'),
				appcontainer = $('#noVNC_container'),
				footermenu = $('<ul></ul>');

			$('#noVNC-control-bar').removeClass('hidden');

			if (!app.hasClass('no-refresh')) {
				var li = $('<li></li>');
				$('<a class="refresh" id="app-btn-refresh" alt="<?php echo JText::_('PLG_TOOLS_NOVNC_REFRESH'); ?>" title="<?php echo JText::_('PLG_TOOLS_NOVNC_REFRESH'); ?>"><span><?php echo JText::_('PLG_TOOLS_NOVNC_REFRESH'); ?></span></a>')
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
				$('<a class="resize" id="app-btn-resizehandle" alt="<?php echo JText::_('PLG_TOOLS_NOVNC_RESIZE'); ?>" title="<?php echo JText::_('PLG_TOOLS_NOVNC_RESIZE'); ?>"><span id="app-size">' + app.attr('width').toString() + ' x ' + app.attr('height').toString() + '</span></a>')
					.appendTo(li);
				li.appendTo(footermenu);

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