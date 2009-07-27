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
// Answers Scripts
//----------------------------------------------------------
HUB.Answers = {
	initialize: function() {
		
		
		
		//------------------------
		// reply to review or comment
		//------------------------
		var add = $$('.addcomment');
		var show = $$('.showreplyform');
		if(add) {
	
			for (i = 0; i < add.length; i++) 
			{
				if(add[i].hasClass('hide') == true) {
				add[i].style.display = "none"; // hide form	
				}
				if(show) {	
					show[i].onclick=function() {
						p = $(this.parentNode.parentNode.parentNode).getElement('.addcomment');
						p.style.display = "inline";
						t = p.getElement('.commentarea');
						t.value = 'Enter your comments...';
					}
				}
			}
			
			if($$('.commentarea')) {	
			$$('.commentarea').each(function(item) {
					// clear the default text						 
					item.addEvent('focus', function() {	
						if(item.value =='Enter your comments...') {
							item.value = '';
						}
					}
				);
			});
			}
			
			if($$('.closeform')) {		
			$$('.closeform').each(function(item) {
					// clear the default text						 
					item.addEvent('click', function() {	
						$(item.parentNode.parentNode.parentNode).style.display = 'none';
					}
				);
			});
			}
			
		}
		
	},

	setZindex: function(el) {
		var LIs = el.parentNode.parentNode.parentNode.getElementsByTagName('li');

		if (LIs) {
			for (i = 0; i < LIs.length; i++) {
				LIs[i].style.zIndex = 1;
			}
		}
	}
}

window.addEvent('domready', HUB.Answers.initialize);
