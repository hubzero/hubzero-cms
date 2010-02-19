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
HUB.Jobs = {
	initialize: function() {
		
		var default_tagline = 'Why should I be hired? (optional but highly recommended)';
		var default_lookingfor = 'Briefly describe your career goals (optional but highly recommended)';
		
		// cancel confirm form	
		if($('showconfirm') && $$('.confirmwrap')[0]) {				
			$('showconfirm').addEvent('click', function(e) {
				e = new Event(e).stop();
				$$('.confirmwrap')[0].style.display = 'block';					   
			});		   
		}
				
		// subscription form
		if($('subForm')) {	
			var frm = document.getElementById('hubForm');
			
			// show details of selected option
			var services = $$('.service');
			
			if(services) {
				var sel = 0;
				services.each(function(item) {													
					if (item.checked) {
						$('plan_'+ item.value).style.display = 'block';							
					}
					else {
						$('plan_'+ item.value).style.display = 'none';	
					}
					
					$('units_' + item.value).addEvent('change', function() {																	 
						var unitprice = $('price_' + item.value).value;
						var newprice = unitprice * $('units_' + item.value).value;
						$('injecttotal_' + item.value).innerHTML = 	newprice.toFixed(2);
					});	
					
					item.addEvent('click', function(){					
						HUB.Jobs.hideDetails();
						$('plan_'+ item.value).style.display = 'block';					
					});									
			   });								 			   
			}
						
			// display total price calculation
			$$('.totalprice').each(function(item) {	
				item.style.display = 'block';					
			});			
		}		
		
		// save to shortlist
		if($$('.favvit')) {		
			if ($$('.favvit').length > 0) {				
				$$('.favvit').each(function(item) {		
					item.addEvent('click', function(e) {	
						e = new Event(e).stop();
						var oid = $(item.parentNode).getProperty('id').replace('o','');						
						var profilebox = $(item.parentNode.parentNode.parentNode);
							
						new Ajax('index.php?option=com_jobs&task=plugin&trigger=onMembersShortlist&no_html=1&oid='+oid,{
							method : 'get',
							update : $(profilebox)
						}).request();						
					});						  												  
				});								  
			}
		}
			
		// editing seeker info
		if($('tagline-men')) {		
			HUB.Jobs.setCounter($('tagline-men'), $('counter_number_tagline') );
			
			if($('tagline-men').value=='') {
				$('tagline-men').value = default_tagline;
				$('tagline-men').setStyle('color', '#999');
			}
			
			$('tagline-men').addEvent('click', function(e) {
					// Clear default value
					if($('tagline-men').value == default_tagline)	 {
						$('tagline-men').value = '';
						$('tagline-men').setStyle('color', '#000');
					}										   
			});	
			
			$('tagline-men').addEvent('keyup', function(e) {					
					HUB.Jobs.setCounter($('tagline-men'), $('counter_number_tagline') );
			});	
		}
				
		if($('lookingfor-men')) {		
				
			HUB.Jobs.setCounter($('lookingfor-men'), $('counter_number_lookingfor') );
			
			if($('lookingfor-men').value=='') {
				$('lookingfor-men').value = default_lookingfor;
				$('lookingfor-men').setStyle('color', '#999');
			}
			
			$('lookingfor-men').addEvent('click', function(e) {
					// Clear default value
					if($('lookingfor-men').value == default_lookingfor)	 {
						$('lookingfor-men').value = '';
						$('lookingfor-men').setStyle('color', '#000');
					}										   
			});	
			
			$('lookingfor-men').addEvent('keyup', function(e) {					
					HUB.Jobs.setCounter($('lookingfor-men'), $('counter_number_lookingfor') );
			});												   
		}
		
		
		// submit form - cleanup default values
		
		if($('prefsForm')) {			
			$('prefsForm').addEvent('submit', function(){
				if($('lookingfor-men').value=='' || $('lookingfor-men').value==default_lookingfor) {
					$('lookingfor-men').value = '';
				}
				if( $('tagline-men').value =='' || $('tagline-men').value == default_tagline) {
					$('tagline-men').value = '';
				}
			});	
		}
			
		// show chars counter
		if($$('.counter')) {			
			for (i = 0; i < $$('.counter').length; i++) {
				$$('.counter')[i].style.display = "block";
			}
		}			
	},
	
	hideDetails: function() {
			$$('.subdetails').each(function(item) {											
				item.style.display = 'none';								
			});
	},
	
	setCounter: function(el, numel) {
		
			var maxchars = 140;
			
			var current_length = el.value.length;
			var remaining_chars = maxchars-current_length;
			if(remaining_chars < 0) {
				remaining_chars = 0;
			}
			numel.innerHTML = remaining_chars;
			
			if(remaining_chars <= 10){
			$(numel.parentNode).setStyle('color', '#CC0000');
			} else {
			$(numel.parentNode).setStyle('color', '#999999');
			}
			
			if (remaining_chars == 0) {
				el.setProperty('value', el.getProperty('value').substr(0,maxchars));
			}			
	}
}

window.addEvent('domready', HUB.Jobs.initialize);