/**
 * @package	 hubzero-cms
 * @file		components/com_tools/tools.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license	 http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-------------------------------------------------------------
// Edit session titles
//-------------------------------------------------------------
/*
 * Jeditable - jQuery in place edit plugin
 *
 * Copyright (c) 2006-2009 Mika Tuupola, Dylan Verheul
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Based on editable by Dylan Verheul <dylan_at_dyve.net>:
 *	http://www.dyve.net/jquery/?editable
 *
 */

/**
  * Version 1.7.1
  *
  * ** means there is basic unit tests for this parameter. 
  *
  * @name  Jeditable
  * @type  jQuery
  * @param String  target			 (POST) URL or function to send edited content to **
  * @param Hash	options			additional options 
  * @param String  options[method]	method to use to send edited content (POST or PUT) **
  * @param Function options[callback] Function to run after submitting edited content **
  * @param String  options[name]	  POST parameter name of edited content
  * @param String  options[id]		POST parameter name of edited div id
  * @param Hash	options[submitdata] Extra parameters to send when submitting edited content.
  * @param String  options[type]	  text, textarea or select (or any 3rd party input type) **
  * @param Integer options[rows]	  number of rows if using textarea ** 
  * @param Integer options[cols]	  number of columns if using textarea **
  * @param Mixed   options[height]	'auto', 'none' or height in pixels **
  * @param Mixed   options[width]	 'auto', 'none' or width in pixels **
  * @param String  options[loadurl]   URL to fetch input content before editing **
  * @param String  options[loadtype]  Request type for load url. Should be GET or POST.
  * @param String  options[loadtext]  Text to display while loading external content.
  * @param Mixed   options[loaddata]  Extra parameters to pass when fetching content before editing.
  * @param Mixed   options[data]	  Or content given as paramameter. String or function.**
  * @param String  options[indicator] indicator html to show when saving
  * @param String  options[tooltip]   optional tooltip text via title attribute **
  * @param String  options[event]	 jQuery event such as 'click' of 'dblclick' **
  * @param String  options[submit]	submit button value, empty means no button **
  * @param String  options[cancel]	cancel button value, empty means no button **
  * @param String  options[cssclass]  CSS class to apply to input form. 'inherit' to copy from parent. **
  * @param String  options[style]	 Style to apply to input form 'inherit' to copy from parent. **
  * @param String  options[select]	true or false, when true text is highlighted ??
  * @param String  options[placeholder] Placeholder text or html to insert when element is empty. **
  * @param String  options[onblur]	'cancel', 'submit', 'ignore' or function ??
  *			 
  * @param Function options[onsubmit] function(settings, original) { ... } called before submit
  * @param Function options[onreset]  function(settings, original) { ... } called before reset
  * @param Function options[onerror]  function(settings, original, xhr) { ... } called on error
  *			 
  * @param Hash	options[ajaxoptions]  jQuery Ajax options. See docs.jquery.com.
  *			 
  */

(function($) {

	$.fn.editable = function(target, options) {

		if ('disable' == target) {
			$(this).data('disabled.editable', true);
			return;
		}
		if ('enable' == target) {
			$(this).data('disabled.editable', false);
			return;
		}
		if ('destroy' == target) {
			$(this)
				.unbind($(this).data('event.editable'))
				.removeData('disabled.editable')
				.removeData('event.editable');
			return;
		}

		var settings = $.extend({}, $.fn.editable.defaults, {target:target}, options);

		/* setup some functions */
		var plugin   = $.editable.types[settings.type].plugin || function() { };
		var submit   = $.editable.types[settings.type].submit || function() { };
		var buttons  = $.editable.types[settings.type].buttons 
					|| $.editable.types['defaults'].buttons;
		var content  = $.editable.types[settings.type].content 
					|| $.editable.types['defaults'].content;
		var element  = $.editable.types[settings.type].element 
					|| $.editable.types['defaults'].element;
		var reset	= $.editable.types[settings.type].reset 
					|| $.editable.types['defaults'].reset;
		var callback = settings.callback || function() { };
		var onedit   = settings.onedit   || function() { }; 
		var onsubmit = settings.onsubmit || function() { };
		var onreset  = settings.onreset  || function() { };
		var onerror  = settings.onerror  || reset;

		/* show tooltip */
		if (settings.tooltip) {
			$(this).attr('title', settings.tooltip);
		}

		settings.autowidth  = 'auto' == settings.width;
		settings.autoheight = 'auto' == settings.height;

		return this.each(function() {

			/* save this to self because this changes when scope changes */
			var self = this;

			/* inlined block elements lose their width and height after first edit */
			/* save them for later use as workaround */
			var savedwidth  = $(self).width();
			var savedheight = $(self).height();

			/* save so it can be later used by $.editable('destroy') */
			$(this).data('event.editable', settings.event);

			/* if element is empty add something clickable (if requested) */
			if (!$.trim($(this).html())) {
				$(this).html(settings.placeholder);
			}

			$(this).bind(settings.event, function(e) {

				/* abort if disabled for this element */
				if (true === $(this).data('disabled.editable')) {
					return;
				}

				/* prevent throwing an exeption if edit field is clicked again */
				if (self.editing) {
					return;
				}

				/* abort if onedit hook returns false */
				if (false === onedit.apply(this, [settings, self])) {
				   return;
				}

				/* prevent default action and bubbling */
				e.preventDefault();
				e.stopPropagation();

				/* remove tooltip */
				if (settings.tooltip) {
					$(self).removeAttr('title');
				}

				/* figure out how wide and tall we are, saved width and height */
				/* are workaround for http://dev.jquery.com/ticket/2190 */
				if (0 == $(self).width()) {
					//$(self).css('visibility', 'hidden');
					settings.width  = savedwidth;
					settings.height = savedheight;
				} else {
					if (settings.width != 'none') {
						settings.width = 
							settings.autowidth ? $(self).width()  : settings.width;
					}
					if (settings.height != 'none') {
						settings.height = 
							settings.autoheight ? $(self).height() : settings.height;
					}
				}
				//$(this).css('visibility', '');

				/* remove placeholder text, replace is here because of IE */
				if ($(this).html().toLowerCase().replace(/(;|")/g, '') == 
					settings.placeholder.toLowerCase().replace(/(;|")/g, '')) {
						$(this).html('');
				}

				self.editing	= true;
				self.revert	 = $(self).html();
				$(self).html('');

				/* create the form object */
				var form = $('<form />');
				
				/* apply css or style or both */
				if (settings.cssclass) {
					if ('inherit' == settings.cssclass) {
						form.attr('class', $(self).attr('class'));
					} else {
						form.attr('class', settings.cssclass);
					}
				}

				if (settings.style) {
					if ('inherit' == settings.style) {
						form.attr('style', $(self).attr('style'));
						/* IE needs the second line or display wont be inherited */
						form.css('display', $(self).css('display'));
					} else {
						form.attr('style', settings.style);
					}
				}

				/* add main input element to form and store it in input */
				var input = element.apply(form, [settings, self]);

				/* set input content via POST, GET, given data or existing value */
				var input_content;
				
				if (settings.loadurl) {
					var t = setTimeout(function() {
						input.disabled = true;
						content.apply(form, [settings.loadtext, settings, self]);
					}, 100);

					var loaddata = {};
					loaddata[settings.id] = self.id;
					if ($.isFunction(settings.loaddata)) {
						$.extend(loaddata, settings.loaddata.apply(self, [self.revert, settings]));
					} else {
						$.extend(loaddata, settings.loaddata);
					}
					$.ajax({
					   type : settings.loadtype,
					   url  : settings.loadurl,
					   data : loaddata,
					   async : false,
					   success: function(result) {
						  window.clearTimeout(t);
						  input_content = result;
						  input.disabled = false;
					   }
					});
				} else if (settings.data) {
					input_content = settings.data;
					if ($.isFunction(settings.data)) {
						input_content = settings.data.apply(self, [self.revert, settings]);
					}
				} else {
					input_content = self.revert; 
				}
				content.apply(form, [input_content, settings, self]);

				input.attr('name', settings.name);

				/* add buttons to the form */
				buttons.apply(form, [settings, self]);
		 
				/* add created form to self */
				$(self).append(form);
		 
				/* attach 3rd party plugin if requested */
				plugin.apply(form, [settings, self]);

				/* focus to first visible form element */
				$(':input:visible:enabled:first', form).focus();

				/* highlight input contents when requested */
				if (settings.select) {
					input.select();
				}
		
				/* discard changes if pressing esc */
				input.keydown(function(e) {
					if (e.keyCode == 27) {
						e.preventDefault();
						//self.reset();
						reset.apply(form, [settings, self]);
					}
				});

				/* discard, submit or nothing with changes when clicking outside */
				/* do nothing is usable when navigating with tab */
				var t;
				if ('cancel' == settings.onblur) {
					input.blur(function(e) {
						/* prevent canceling if submit was clicked */
						t = setTimeout(function() {
							reset.apply(form, [settings, self]);
						}, 500);
					});
				} else if ('submit' == settings.onblur) {
					input.blur(function(e) {
						/* prevent double submit if submit was clicked */
						t = setTimeout(function() {
							form.submit();
						}, 200);
					});
				} else if ($.isFunction(settings.onblur)) {
					input.blur(function(e) {
						settings.onblur.apply(self, [input.val(), settings]);
					});
				} else {
					input.blur(function(e) {
					  /* TODO: maybe something here */
					});
				}

				form.submit(function(e) {

					if (t) { 
						clearTimeout(t);
					}

					/* do no submit */
					e.preventDefault(); 
			
					/* call before submit hook. */
					/* if it returns false abort submitting */
					if (false !== onsubmit.apply(form, [settings, self])) { 
						/* custom inputs call before submit hook. */
						/* if it returns false abort submitting */
						if (false !== submit.apply(form, [settings, self])) { 

						  /* check if given target is function */
						  if ($.isFunction(settings.target)) {
							  var str = settings.target.apply(self, [input.val(), settings]);
							  $(self).html(str);
							  self.editing = false;
							  callback.apply(self, [self.innerHTML, settings]);
							  /* TODO: this is not dry */
							  if (!$.trim($(self).html())) {
								  $(self).html(settings.placeholder);
							  }
						  } else {
							  /* add edited content and id of edited element to POST */
							  var submitdata = {};
							  submitdata[settings.name] = input.val();
							  submitdata[settings.id] = self.id;
							  /* add extra data to be POST:ed */
							  if ($.isFunction(settings.submitdata)) {
								  $.extend(submitdata, settings.submitdata.apply(self, [self.revert, settings]));
							  } else {
								  $.extend(submitdata, settings.submitdata);
							  }

							  /* quick and dirty PUT support */
							  if ('PUT' == settings.method) {
								  submitdata['_method'] = 'put';
							  }

							  /* show the saving indicator */
							  $(self).html(settings.indicator);

								/* defaults for ajaxoptions */
								var ajaxoptions = {
									type	: 'POST',
									data	: submitdata,
									dataType: 'html',
									url	 : settings.target,
									success : function(result, status) {
										if (ajaxoptions.dataType == 'html') {
											$(self).html(result);
										}
										self.editing = false;
										callback.apply(self, [result, settings]);
										if (!$.trim($(self).html())) {
											$(self).html(settings.placeholder);
										}
									},
									error   : function(xhr, status, error) {
										onerror.apply(form, [settings, self, xhr]);
									}
								};

								/* override with what is given in settings.ajaxoptions */
								$.extend(ajaxoptions, settings.ajaxoptions);
								$.ajax(ajaxoptions);
							}
						}
					}

					/* show tooltip again */
					$(self).attr('title', settings.tooltip);

					return false;
				});
			});

			/* privileged methods */
			this.reset = function(form) {
				/* prevent calling reset twice when blurring */
				if (this.editing) {
					/* before reset hook, if it returns false abort reseting */
					if (false !== onreset.apply(form, [settings, self])) { 
						$(self).html(self.revert);
						self.editing   = false;
						if (!$.trim($(self).html())) {
							$(self).html(settings.placeholder);
						}
						/* show tooltip again */
						if (settings.tooltip) {
							$(self).attr('title', settings.tooltip);
						}
					}
				}
			};
		});
	};

	$.editable = {
		types: {
			defaults: {
				element : function(settings, original) {
					var input = $('<input type="hidden"></input>');
					$(this).append(input);
					return(input);
				},
				content : function(string, settings, original) {
					$(':input:first', this).val(string);
				},
				reset : function(settings, original) {
				  original.reset(this);
				},
				buttons : function(settings, original) {
					var form = this;
					if (settings.submit) {
						/* if given html string use that */
						if (settings.submit.match(/>$/)) {
							var submit = $(settings.submit).click(function() {
								if (submit.attr("type") != "submit") {
									form.submit();
								}
							});
						/* otherwise use button with given string as text */
						} else {
							var submit = $('<button type="submit" />');
							submit.html(settings.submit);
						}
						$(this).append(submit);
					}
					if (settings.cancel) {
						/* if given html string use that */
						if (settings.cancel.match(/>$/)) {
							var cancel = $(settings.cancel);
						/* otherwise use button with given string as text */
						} else {
							var cancel = $('<button type="cancel" />');
							cancel.html(settings.cancel);
						}
						$(this).append(cancel);

						$(cancel).click(function(event) {
							//original.reset();
							if ($.isFunction($.editable.types[settings.type].reset)) {
								var reset = $.editable.types[settings.type].reset;
							} else {
								var reset = $.editable.types['defaults'].reset;
							}
							reset.apply(form, [settings, original]);
							return false;
						});
					}
				}
			},
			text: {
				element : function(settings, original) {
					var input = $('<input />');
					if (settings.width  != 'none') { input.width(settings.width);  }
					if (settings.height != 'none') { input.height(settings.height); }
					/* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
					//input[0].setAttribute('autocomplete','off');
					input.attr('autocomplete','off');
					$(this).append(input);
					return(input);
				}
			},
			textarea: {
				element : function(settings, original) {
					var textarea = $('<textarea />');
					if (settings.rows) {
						textarea.attr('rows', settings.rows);
					} else if (settings.height != "none") {
						textarea.height(settings.height);
					}
					if (settings.cols) {
						textarea.attr('cols', settings.cols);
					} else if (settings.width != "none") {
						textarea.width(settings.width);
					}
					$(this).append(textarea);
					return(textarea);
				}
			},
			select: {
			   element : function(settings, original) {
					var select = $('<select />');
					$(this).append(select);
					return(select);
				},
				content : function(data, settings, original) {
					/* If it is string assume it is json. */
					if (String == data.constructor) {	  
						eval ('var json = ' + data);
					} else {
					/* Otherwise assume it is a hash already. */
						var json = data;
					}
					for (var key in json) {
						if (!json.hasOwnProperty(key)) {
							continue;
						}
						if ('selected' == key) {
							continue;
						} 
						var option = $('<option />').val(key).append(json[key]);
						$('select', this).append(option);	
					}					
					/* Loop option again to set selected. IE needed this... */ 
					$('select', this).children().each(function() {
						if ($(this).val() == json['selected'] || 
							$(this).text() == $.trim(original.revert)) {
								$(this).attr('selected', 'selected');
						}
					});
				}
			}
		},

		/* Add new input type */
		addInputType: function(name, input) {
			$.editable.types[name] = input;
		}
	};

	// publicly accessible defaults
	$.fn.editable.defaults = {
		name       : 'value',
		id         : 'id',
		type       : 'text',
		width      : 'auto',
		height     : 'auto',
		event      : 'click.editable',
		onblur     : 'cancel',
		loadtype   : 'GET',
		loadtext   : 'Loading...',
		placeholder: 'Click to edit',
		loaddata   : {},
		submitdata : {},
		ajaxoptions: {}
	};

})(jQuery);

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
	
	// Helper function used to load the proper applet (signed or unsigned).
	loadApplet: function() {
		var $ = this.jQuery;
		// For now, we just load the unsigned applet.
		// Someday, the loadApplet() function will examine
		// the Mambo session table to decide which applet to load.
		if ($('#signedapplet').val() == '1') {
			loadSignedApplet();
		} else {
			loadUnsignedApplet();
		}
	},

	addParam: function(obj,name,value) {
		var p = document.createElement("param");
		p.name = name;
		p.value = value;
		obj.appendChild(p);
	},
	
	loadApplet: function(jar, w, h, port, pass, connect_value, ro, msie) {
		var app = document.getElementById("theapp");
		var par = app.parentNode;
		par.removeChild(app);
		
		if (msie) {
			var newapp = document.createElement("applet");
			newapp.id = "theapp";
			newapp.code = "VncViewer.class";
			newapp.archive = jar;
			newapp.width = w;
			newapp.height = h;
			newapp.mayscript = "mayscript";

			HUB.Mw.addParam(newapp, "PORT", port);
			HUB.Mw.addParam(newapp, "ENCPASSWORD", pass);
			HUB.Mw.addParam(newapp, "CONNECT", connect_value);
			HUB.Mw.addParam(newapp, "ENCODING", "ZRLE");
			HUB.Mw.addParam(newapp, "View Only", ro);
			HUB.Mw.addParam(newapp, "trustAllVncCerts", "Yes");
			HUB.Mw.addParam(newapp, "Offer relogin", "Yes");
			HUB.Mw.addParam(newapp, "DisableSSL", "No");
			HUB.Mw.addParam(newapp, "Show controls", "No");

			if (jar.indexOf('Signed') >= 0) {
				HUB.Mw.addParam(newapp, "signed", "yes");
				HUB.Mw.addParam(newapp, "forceProxy", "yes");
			}
		} else {
			var appletDiv = document.createElement("div");
			var signed;
			if (jar.indexOf('Signed') >= 0) {
				signed = 'Yes';
			} else {
				signed = 'No';
			}
			appletDiv.innerHTML = '<applet id="theapp" code="VncViewer.class" archive="'+jar+'" width="'+w+'" height="'+h+'" MAYSCRIPT>' +
				'<param name="PORT" value="'+port+'"> ' +
				'<param name="ENCPASSWORD" value="'+pass+'"> ' +
				'<param name="CONNECT" value="'+connect_value+'"> ' +
				'<param name="View Only" value="'+ro+'"> ' +
				'<param name="trustAllVncCerts" value="Yes"> ' +
				'<param name="Offer relogin" value="Yes"> ' +
				'<param name="DisableSSL" value="No"> ' +
				'<param name="Show controls" value="No"> ' +
				'<param name="ENCODING" value="<?php echo $this->output->encoding; ?>"> ' +
				'<param name="signed" value="'+signed+'"> ' +
				'<param name="forceProxy" value="'+signed+'"> ' +
			'</applet>';
		}
		
		par.appendChild(newapp);
	},

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
		var $ = this.jQuery;
		HUB.Mw.clearTroubleshoot();
		HUB.Mw.cancelConnecting();

		var theapp = $('#theapp');
		if (theapp) {
			var par = theapp.parent();
			par.remove(theapp);
		}

		$('<div id="theapp"><p class="error">' +
				'It appears that the Java environment did not ' +
				'start properly.  Please make sure that you ' +
				'have Java installed and enabled for your web ' +
				'browser.  The version of the Java environment ' +
				'must be greater than or equal to 1.4.  ' +
				'(<a href="/kb/misc/java/">How do I do this?</a>)  ' +
				'Without Java support you will not be able to ' +
				'view any applications.' +
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
		var $ = this.jQuery;
		HUB.Mw.cancelConnecting();
		var trouble = $('#troubleshoot');
		if (!trouble) {
			return;
		}
		trouble.html(
			'<p class="error">' +
				'It appears that Java is either not installed or ' +
				'not enabled.  You will not be able to view tools ' +
				'until Java is enabled.<br />' +
				'(<a href="/kb/misc/java/">Learn how to enable Java</a>)  ' +
			'</p>'
		);
	},

	// Show a message explaining that there is a browser/Java bug.
	javaBug: function() {
		var $ = this.jQuery;
		HUB.Mw.cancelConnecting();
		var trouble = $('#troubleshoot');
		if (!trouble) {
			return;
		}
		trouble.html(
			'<p class="error">' +
				'There is a problem caused by the specific version ' +
				'of Java you are using with this browser. You will ' +
				'likely not be able to view tools. There are three ' +
				'things you can try:<br /> ' +
				'1) Restart your browser and disable Javascript ' +
				'before starting a tool the ' +
				'first time and re-enable Javascript once the first ' +
				'tool is running.<br />' +
				'2) Switch to a different version of Java. ' +
				'Version 1.6.0 Update 02 (and earlier) will work ' +
				'but 1.6.0 Update 03 and 04 do not.<br>' +
				'3) Use a browser other than Firefox.<br>' +
				'(<a href="/kb/tools/unable_to_connect_error_in_firefox/">More information</a>)  ' +
			'</p>'
		);
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
			document.open(action.slice(4), '_blank', 'width=600,height=600,toolbar=no,menubar=no,scrollbars=yes,resizable=yes');
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
			if (w < 100) { w = 100; }
			if (h < 100) { h = 100; }
			
			$('#app-content').css({
				'width': w.toString() + 'px',
				'height': h.toString() + 'px'
			});
			
			$('#app-size').html(w.toString()+' x '+h.toString());
			
			if ((document.all)&&(navigator.appVersion.indexOf("MSIE 7.")!=-1)) {
				if ($('#app-header')) {
					$('#app-header').css('width', w.toString() + 'px');
				}
				if ($('#app-footer')) {
					$('#app-footer').css('width', w.toString() + 'px');
				}
			}

			$('#theapp').css('width', w.toString() + 'px')
				.css('height', h.toString() + 'px')
				.attr('width', w.toString())
				.attr('height', h.toString());
		}
	},

	storageMonitor: function() {
		var $ = this.jQuery;
		//fetch.periodical(60000);
		var holdTheInterval = setInterval(function(){
			$.get('/index.php?option=com_tools&controller=storage&task=diskusage&no_html=1&msgs=0', {}, function(data) {
				$('#diskusage').html(data);
			});
		}, 60000); 
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

		if (w < 200) { w = 200; }
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
			$('<a class="resize" id="app-btn-resizehandle" alt="Popout" title="Popout"><span id="app-size">'+w.toString() + ' x ' + h.toString()+'</span></a>')
				.appendTo(li);
			li.appendTo(footermenu);
			
			// Init the resizing capabilities
			var wh = $('#app-content').height(),
				ah = $('#theapp').height(),
				os = wh - ah;

			$('#app-content').resizable({
				minHeight: 200,
				maxHeight: 3900,
				minWidth: 200,
				maxWidth: 3900,
				handles: 'se',
				resize: function(event, ui) {
					$('#app-size').html($('#app-content').width()+' x '+$('#app-content').height());
				},
				stop: function(event, ui) {
					if ($('#theapp')) {
						var w = parseFloat($('#app-content').width()),
							h = parseFloat($('#app-content').height());

						if ((document.all)&&(navigator.appVersion.indexOf("MSIE 7.")!=-1)) {
							if ($('#app-header')) {
								$('#app-header').css('width', w + 'px');
							}
							if ($('#app-footer')) {
								$('#app-footer').css('width', w + 'px');
							}
						}

						$('#app-size').html(w.toString()+' x '+h.toString());

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
		$('#session-title').editable('index.php?option=com_tools&controller=sessions&task=rename&no_html=1&id=' + $('#session-title').attr('rel'), {
			id   : 'title',
			name : 'name',
			width : '200px',
			submit : 'OK',
			cancel : 'cancel'
		});
		
		// Initiate the storage usage
		HUB.Mw.storageMonitor();
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

