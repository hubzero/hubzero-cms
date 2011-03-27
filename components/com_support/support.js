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
		var iTTips = new MooTips($$('.fixedImgTip'), {
				showDelay: 500,			// Delay for 500 milliseconds
				maxTitleChars: 100,
				className: 'img',
				fixed: true,			// fixed in place; note tip mouseover does not hide tip
				offsets: {'x':20,'y':5} // offset by 100,100
			});
			
		if ($('messages')) {
			$('messages').addEvent('change', HUB.Support.getMessage);
		}
		
		if ($('toggle-details')) {
			$('toggle-details').onclick = function() {
				var tbody = $('ticket-details-body');
				if (tbody.hasClass('hide')) {
					tbody.removeClass('hide');
				} else {
					tbody.addClass('hide');
				}
				return false;
			}	
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
					es.disabled = false;
					$('commentform').removeClass('private');
				}
			}
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Support.initialize);

