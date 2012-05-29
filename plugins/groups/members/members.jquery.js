/**
 * @package     hubzero-cms
 * @file        plugins/groups/members/members.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.GroupsMembers = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		$('.remove-role a').on('click', function(e) {
			e.preventDefault();
			var answer = confirm('Are you sure you want to delete this member role? It will also delete any associations members have with the role.');
			if (answer) { 
				window.location = $(this).attr('href');
			}
		});
		
			$('a.message').fancybox({
				type: 'ajax',
				width: 300,
				height: 405,
				autoSize: false,
				fitToView: false,
				titleShow: false,
				tpl: {
					wrap:'<div class="fancybox-wrap"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div>'
				},
				beforeLoad: function() {
					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);	
				},
				afterShow: function() {
					if ($('#message-ajax')) {
						$('#message-ajax').submit(function(e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function(returndata) {
								$.fancybox().close();
							});
						});
					}
				}
			});
			//end message members pop up
			
			$('a.assign-role').fancybox({
				type: 'ajax',
				width: 300,
				height: 150,
				autoSize: false,
				fitToView: false,
				titleShow: false,
				tpl: {
					wrap:'<div class="fancybox-wrap"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div>'
				},
				beforeLoad: function() {
					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);	
				},
				afterShow: function() {
					frm = $('#hubForm-ajax');
					uid = $('#uid').val();
					if (frm) {
						frm.on('submit', function(e) {
							e.preventDefault();
							if ($('#roles').val() == '') {
								alert('You must select a member role.');
								return false;
							}
							$.post($(this).attr('action'), $(this).serialize(), function(returndata) {
								role = $('#roles').options[$('#roles').selectedIndex].text();
								old = $('#roles-list-' + uid).html();
								if (old == '') {
									$('#roles-list-' + uid).html(role);
								} else {
									$('#roles-list-' + uid).html(old + ', ' + role);
								}
								$.fancybox().close();
							});
						});
					}
				}
			});
			//end assign role pop ups
		
	} //end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.GroupsMembers.initialize();
});
