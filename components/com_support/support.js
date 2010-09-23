/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
