/**
 * @package	 hubzero-cms
 * @file		components/com_tools/tools.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-------------------------------------------------------------
// MW scripts
//
// NOTE: 'theapp' is a java app and using the MooTools method
// of $('theapp') to retrieve it seems to throw errors, so
// we use document.getElementById instead.
//-------------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Mw = {
	jQuery: jq,

	// Inform Mambo whether session needs signed applet.
	sessionUsesSignedApplet: function(value) {
		var $ = this.jQuery;
		// Value should be either true or false.

		// This function doesn't do anything yet.
		// It will be called from the middleware.
		if (value) {
			$.get('/index.php?option=com_tools&controller=sessions&task=signed&no_html=1', {});
			$('signedapplet').val(1);
		}
	},

	// Clear the static troubleshooting message
	clearTroubleshoot: function() {
		var $ = this.jQuery;
		var trouble = $('#troubleshoot');
		if (trouble) {
			$(trouble).remove();
		}
	},

	// Tell user that we're connecting to the tool session.
	connectingTool: function() {
		//document.getElementById('theapp').style.visibility = 'hidden';
		//$('app-content').setStyle('visibility', 'hidden');
	},

	// Delete the "Connecting..." message.
	cancelConnecting: function() {
		HUB.Mw.cancelTimeout();
	},

	// Start a timer to show Java failure.
	appletTimeoutID: 0,

	// Show a message saying that Java didn't appear to work.
	appletTimeout: function() {
		HUB.Mw.clearTroubleshoot();
		HUB.Mw.cancelConnecting();

		var theapp = jq('#theapp');
		if (theapp) {
			var par = theapp.parent();
			theapp.remove();
		}

		$('<div id="theapp"><p class="error">' +
				JavaNotStarted +
			'</p></div>'
		).appendTo(par);
	},

	startAppletTimeout: function() {
		var timeout = 30;
		HUB.Mw.appletTimeoutID = self.setTimeout("HUB.Mw.appletTimeout()", timeout * 1000);
	},

	// Cancel the timer to show Java failure.
	cancelTimeout: function() {
		clearTimeout(HUB.Mw.appletTimeoutID);
	},

	// Show a message explaining that Java is not enabled.
	noJava: function() {
		HUB.Mw.cancelConnecting();

		var trouble = jq('#troubleshoot');
		if (!trouble.length) {
			return;
		}

		trouble
			.html(JavaNotFound)
			.removeClass('help')
			.addClass('error');
	},

	// Show a message explaining that there is a browser/Java bug.
	javaBug: function() {
		HUB.Mw.cancelConnecting();

		var trouble = jq('#troubleshoot');
		if (!trouble.length) {
			return;
		}

		trouble
			.html(JavaBug)
			.removeClass('help')
			.addClass('error');
	},

	// Check for any Java bugs.
	checkJavaBug: function() {
		// A return value of 1 means there's a bug.
		var bv = navigator.userAgent.toLowerCase();
		if (bv.indexOf('firefox') == -1 &&
			bv.indexOf('iceweasel') == -1) {
			// So far the only problems have been with Firefox.
			// If this is not Firefox, assume no problem.
			// Avoid future Javascript calls to invoke Java.
			return 0;
		}

		// If there's no Java, then there's a big problem.
		if (!navigator.javaEnabled || typeof java == 'undefined') {
			HUB.Mw.noJava();
			return 1;
		}

		// If the Java version is 1.6.0_{03,04} then it might not
		// work with Firefox while Javascript is enabled.  Bah.
		var jv = java.lang.System.getProperty('java.version');
		if (jv == '1.6.0_03' || jv == '1.6.0_04') {
			HUB.Mw.javaBug();
			return 1;
		}
		return 0;
	},

	// Helper function for filexfer and user-initiated alerts.
	clientAction: function(action) {
		if (action.slice(0,4) == "url ") {
			if (action.match(/.*?\/filexfer\/.*?\/download\/.*?\?token=.*/)) {
				actionurl = window.location.protocol + "//" + window.location.host + action.slice( action.indexOf("/filexfer/") )
			}
			else {
				actionurl = action.slice(4);
			}
			document.open(actionurl, '_blank', 'width=600,height=600,toolbar=no,menubar=no,scrollbars=yes,resizable=yes');
		} else if (action.slice(0,6) == "alert ") {
			alert(action.slice(6));
		} else {
			alert("Unknown action: " + action);
		}
	},

	// Helper function called by applet when the VNC server exits.
	serverExit: function() {
		window.location = "/myhub";
	},

	// Helper function called by applet to explain signed applets.
	explainSignedApplet: function() {
		window.open('/kb/tools/signed_applet/', '_',
				'width=600,height=600,' +
				'toolbar=no,location=no,directories=no,' +
				'status=no,menubar=no,copyhistory=no,scrollbars=yes,resizable=yes');
	},

	// Force the size of the appwrap to the size of the app (plus some padding)
	forceSize: function(w,h) {
		var $ = this.jQuery;
		HUB.Mw.clearTroubleshoot();
		HUB.Mw.cancelConnecting();
		
		if ($('#theapp')) {
			if (w == 0 && h == 0) {
				w = $('#app-orig-width').val();
				h = $('#app-orig-height').val();
			}
			if (w < 100) { w = 100; }
			if (h < 100) { h = 100; }

			$('#app-content')
				.css({
					'width': w.toString() + 'px',
					'height': h.toString() + 'px'
				})
				.parent()
				.css('width', w.toString() + 'px');

			$('#app-size').html(w.toString()+' x '+h.toString());

			if ((document.all)&&(navigator.appVersion.indexOf("MSIE 7.")!=-1)) {
				if ($('#app-header')) {
					$('#app-header').css('width', w.toString() + 'px');
				}
				if ($('#app-footer')) {
					$('#app-footer').css('width', w.toString() + 'px');
				}
			}

			if (w < 513) {
				$('.ui-resizable-handle').css('bottom', '-5em');
			} else {
				$('.ui-resizable-handle').css('bottom', '-3em');
			}

			$('#theapp').css('width', w.toString() + 'px')
				.css('height', h.toString() + 'px')
				.attr('width', w.toString())
				.attr('height', h.toString());
		}
	},

	initialize: function() {
		var $ = this.jQuery;
		if (!$('#theapp') || !$('#app-wrap')) {
			return;
		}
		
		var appwrap = $('#app-wrap');
		var appfooter = $('#app-footer');

		var app = $('#theapp');
		var w = app.attr('width');
		var h = app.attr('height');

		if (w < 345) { w = 345; }
		if (h < 200) { h = 200; }

		if (w > 3900) { w = 3900; }
		if (h > 3900) { h = 3900; }

		//appwrap.css('width', w.toString() + 'px');
		
		var footermenu = $('<ul></ul>');
		
		if (!app.hasClass('no-popout')) {
			var li = $('<li></li>');
			$('<a class="popout" id="app-btn-newwindow" alt="New Window" title="New Window"><span>New Window</span></a>')
				.click(function(event) {
					document.theapp.popout();
				})
				.appendTo(li);
			li.appendTo(footermenu);
		}

		if (!app.hasClass('no-refresh')) {
			var li = $('<li></li>');
			$('<a class="refresh" id="app-btn-refresh" alt="Refresh Window" title="Refresh Window"><span>Refresh Window</span></a>')
				.click(function(event) {
					document.theapp.refresh();
				})
				.appendTo(li);
			li.appendTo(footermenu);
		}
		
		if (!app.hasClass('no-resize')) {
			var li = $('<li></li>');
			$('<a class="resize" id="app-btn-resizehandle" alt="Resize" title="Resize"><span id="app-size">'+w.toString() + ' x ' + h.toString()+'</span></a>')
				.appendTo(li);
			li.appendTo(footermenu);
			
			// Init the resizing capabilities
			var wh = $('#app-content').height(),
				ah = $('#theapp').height(),
				os = wh - ah;

			$('#app-content').resizable({
				minHeight: 200,
				maxHeight: 3900,
				minWidth: 345,
				maxWidth: 3900,
				handles: 'se',
				resize: function(event, ui) {
					$('#app-size').html($('#app-content').width()+' x '+$('#app-content').height());
				},
				stop: function(event, ui) {
					if ($('#theapp').length) {
						var w = parseFloat($('#app-content').width()),
							h = parseFloat($('#app-content').height());

						$('#app-content').parent().css('width', w + 'px');

						if ((document.all)&&(navigator.appVersion.indexOf("MSIE 7.")!=-1)) {
							if ($('#app-header')) {
								$('#app-header').css('width', w + 'px');
							}
							if ($('#app-footer')) {
								$('#app-footer').css('width', w + 'px');
							}
						}

						$('#app-size').html(w.toString()+' x '+h.toString());

						if (w < 513) {
							$('.ui-resizable-handle').css('bottom', '-5em');
						} else {
							$('.ui-resizable-handle').css('bottom', '-3em');
						}

						$('#theapp')
							.css('width', (w) + 'px')
							.css('height', (h - os) + 'px')
							.attr('width', (w))
							.attr('height', (h - os));
						document.getElementById('theapp').requestResize(w, h);
					}
				}
			});
		}
		
		footermenu.appendTo(appfooter);
		
		// Inititate session title editing
		$('#session-title.editable').editable('index.php?option=com_tools&controller=sessions&task=rename&no_html=1&id=' + $('#session-title').attr('rel'), {
			id   : 'title',
			name : 'name',
			width : '200px',
			submit : 'OK',
			cancel : 'cancel'
		});
	}
}

function clientAction(action) 
{
	HUB.Mw.clientAction(action);
}

function startAppletTimeout() 
{
	HUB.Mw.startAppletTimeout();
}

function cancelTimeout()
{
	HUB.Mw.cancelTimeout();
}

function connectingTool()
{
	HUB.Mw.connectingTool();
}

function forceSize(w,h)
{
	HUB.Mw.forceSize(w,h);
}

jQuery(document).ready(function($){
	HUB.Mw.initialize();
});

