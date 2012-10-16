/**
 * @package     hubzero-cms
 * @file        plugins/courses/messages/messages.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.MembersMsg = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		$('#new-course-message').fancybox({
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
			afterShow: function() {
				if ($('#hubForm-ajax')) {
					$('#hubForm-ajax').submit(function(e) {
						e.preventDefault();      
						
						members = $('#msg-recipient').val();
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
	},
	
	checkAll: function( ele, clsName ) {
		if (ele.checked) {
			var val = true;
		} else {
			var val = false;
		}
		
		$('input.'+clsName).each(function(i, el) {
			if ($(el).attr('checked')) {
				$(el).attr('checked', val);
			} else {
				$(el).attr('checked', val);
			}
		});
	}
}


jQuery(document).ready(function($){
	HUB.MembersMsg.initialize();
});
