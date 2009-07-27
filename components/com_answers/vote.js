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
// Thumbs voting
//----------------------------------------------------------
HUB.Vote = {
	initialize: function() {
				
		//------------------------
		// review ratings
		//------------------------
		var vhints 		=  $$('.votinghints');
		var thumbsvote  =  $$('.thumbsvote');
		if(vhints) {
			
			
			for (i = 0; i < thumbsvote.length; i++) 
			{
				thumbsvote[i].onmouseover=function() {
					
					var el = this.getLast();
					var el = el.getLast();
					el.style.display = "inline";
					
				}
				thumbsvote[i].onmouseout=function() {
					var el = this.getLast();
					var el = el.getLast();
					el.style.display = "none";
				}
			}
		}
		
		//--------------
		var vote = $$('.revvote');
		if(vote) {
	
			for (i = 0; i < vote.length; i++) 
			{
				vote[i].onclick=function() {
					pn = $(this.parentNode.parentNode.parentNode);
					if($(this.parentNode).hasClass('gooditem')) {
						var s = 'yes';
					}
					else {
						var s = 'no';
					}
								
					var cat = $(this.parentNode.parentNode.parentNode).getProperty('class');
					if(!cat) { cat = 'com_answers'; }
					var itemlabel = cat.replace('com_','');
					
					var id = $(this.parentNode.parentNode.parentNode).getProperty('id').replace(itemlabel+'_','');
					
					var myAjax1 = new Ajax('index2.php?option='+cat+'&no_html=1&task=rateitem&refid='+id+'&ajax=1&vote='+s,{update:pn}).request();
									
				}
				
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

window.addEvent('domready', HUB.Vote.initialize);
