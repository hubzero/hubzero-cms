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
HUB.Wishlist = {
	initialize: function() {
		
		
		
		//------------------------
		// reply to review or comment
		//------------------------
		var add = $$('.addcomment');
		var show = $$('.showreplyform');
		if(add) {
	
			for (i = 0; i < add.length; i++) 
			{
				//if(add[i].hasClass('hide') == true) {
				//add[i].style.display = "none"; // hide form	
				//}
				
			}
			
			if($$('.showreplyform')) {		
			$$('.showreplyform').each(function(item) {
									 
					item.addEvent('click', function() {	
							
							var vnum = this.getProperty('id').replace('rep_','');

							$('comm_' + vnum).removeClass('hide');
							$('comm_' + vnum).style.display = "block";
							t = $('comm_' + vnum).getElement('.commentarea');
							t.value = 'Enter your comments...';
		
					}
				);
			});
			}
			
			// show hide comments area
			if($('section-comments')) { 
			
				$('part_com').addEvent('click', function(){
					
					if($('part_com').hasClass('collapse')) {
						$('part_com').removeClass('collapse');
						$('full_com').style.display = "none";
						$('part_com').addClass('expand');
					} else {
						$('part_com').removeClass('expand');
						$('full_com').style.display = "block";
						$('part_com').addClass('collapse');
					}
														   
					return false;
				
				});
			
			}
			
			// show/hide plan area
			if($('section-plan')) { 
			
				$('part_plan').addEvent('click', function(){
					
					if($('part_plan').hasClass('collapse')) {
						$('part_plan').removeClass('collapse');
						$('full_plan').style.display = "none";
						$('part_plan').addClass('expand');
					} else {
						$('part_plan').removeClass('expand');
						$('full_plan').style.display = "block";
						$('part_plan').addClass('collapse');
					}
														   
					return false;
				
				});
			
			}
			
			// due date
			if($('nodue')) { 
				var frm = document.getElementById('hubForm');
				$('nodue').addEvent('click', function(){
					
					frm.publish_up.value = '';
				
				});			
				
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
						var vnum = this.getProperty('id').replace('close_','');
						$('comm_' + vnum).addClass('hide');
						$('comm_' + vnum).style.display = "none";
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

window.addEvent('domready', HUB.Wishlist.initialize);
