/**
 * @package     hubzero-cms
 * @file        components/com_support/support.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Support
//----------------------------------------------------------

HUB.Support = {
	getMessage: function() {
		var id = $('messages');
		if (id.value != 'mc') {
			var hi = $(id.value).value;
			var co = $('comment');
			co.value = hi;
		} else {
			var co = $('comment');
			co.value = '';
		}
	},
	
	initialize: function() {
		HUB.Support.addDeleteQueryEvent();
		HUB.Support.addEditQueryEvent();

		if ($('messages')) {
			$('messages').addEvent('change', HUB.Support.getMessage);
		}

		if ($('make-private')) {
			$('make-private').onclick = function() {
				var es = $('email_submitter');
				if (this.checked == true) {
					if (es.checked == true) {
						es.checked = false;
						es.disabled = true;
					}
					$('commentform').addClass('private');
				} else {
					es.checked = true;
					es.disabled = false;
					$('commentform').removeClass('private');
				}
			}
		}
	},

	addEditQueryEvent: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub || !$('sbox-window')) {
				SqueezeBoxHub.initialize({ size: {x: 750, y: 500} });
			}
			
			$$('a.modal').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();

					w = 600;
					h = 550;
					if (this.className) {
						var sizeString = this.className.split(' ').pop();
						if (sizeString && sizeString != 'play') {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
					}

					SqueezeBoxHub.fromElement(el,{
						handler: 'url', 
						size: {x: w, y: h}, 
						ajaxOptions: {method: 'get'},
						onComplete: function() {
							Conditions.addqueryroot('.query', true);

							if ($('queryForm')) {
								$('queryForm').addEvent('submit', function(e) {
									new Event(e).stop();

									if (!$('field-title').value) {
										alert('Please provide a title.');
										return false;
									}

									var myAjax = new Ajax($(this).getProperty('action'), {
										method: 'post',
										update: $('custom-views'),
										evalScripts: false,
										onSuccess: function() {
											HUB.Support.addEditQueryEvent();
											SqueezeBoxHub.close();
										}
									}).request();

									/*$(this).send({
										//update: $('sbox-content'),
										onComplete: function() {
											SqueezeBoxHub.close();
										}
							        });*/
								});
							}
						}
					});
				});
			});
		}
	},

	addDeleteQueryEvent: function() {
		$$('.views .delete').each(function(el) {
			$(el).addEvent('click', function(e){
				new Event(e).stop();

				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					return false;
				}

				var href = $(this).href;
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}

				var myAjax = new Ajax(href, {
					method: 'get',
					update: $('custom-views'),
					evalScripts: false,
					onSuccess: function() {
						HUB.Support.addDeleteQueryEvent();
						HUB.Support.addEditQueryEvent();
					}
				}).request();

				return false;
			});
		});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Support.initialize);

