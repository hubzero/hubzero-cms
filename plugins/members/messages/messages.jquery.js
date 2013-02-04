/**
 * @package     hubzero-cms
 * @file        plugins/members/messages/messages.js
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

HUB.MembersMsg = {
	jQuery: jq,
	
	checkAll: function( ele, clsName ) {
		var $ = this.jQuery;
		
		if (ele.checked) {
			var val = true;
		} else {
			var val = false;
		}
		
		$('input.'+clsName).each(function(i, el) {
			if (val && !$(el).attr('checked')) {
				$(el).attr('checked', 'checked');
			} else if (!val && $(el).attr('checked')) {
				$(el).removeAttr('checked');
			}
		});
	},
	
	initialize: function()
	{
		var $ = this.jQuery;
		
		$('a.message-link').fancybox({
			type: 'ajax',
			width: 700,
			height: 'auto',
			autoSize: false,
			fitToView: false,  
			titleShow: false,
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
			}
		}); 
		
		/////////////////////////////
		
		$('#message-toolbar a.new').fancybox({
			type: 'ajax',
			width: 700,
			height: 'auto',
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
			afterLoad: function(upcomingObject, currentObject) {
				var dom = $(upcomingObject.content);
				dom.filter('script').each(function() {
					$.globalEval(this.text || this.textContent || this.innerHTML || '');
				});
			},
			afterShow: function() {
				if ($('#hubForm-ajax')) {
					$('#hubForm-ajax').submit(function(e) {
						e.preventDefault();      
						
						members = $('#members').val();
						message = $('#msg-message').val();
						
						if(!members) {
							alert("Must select a message recipient.");
							return false;
						}
						
						if(!message) {
							alert("You must enter a message.");
							return false;
						}
						
						$.post($(this).attr('action'),$(this).serialize(), function(data) {
							$.fancybox().close();  
						});
					});
				}
			}
		});
	}
}

//--------

jQuery(document).ready(function($){
	HUB.MembersMsg.initialize();
});
