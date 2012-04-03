/**
 * @package     hubzero-cms
 * @file        components/com_jobs/jobs.js
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
// Answers Scripts
//----------------------------------------------------------
HUB.Jobs = {
	initialize: function() {
		var default_tagline = 'Why should I be hired? (optional but highly recommended)',
			default_lookingfor = 'Briefly describe your career goals (optional but highly recommended)';
		
		// cancel confirm form	
		if ($('#showconfirm') && $('.confirmwrap')) {				
			$('#showconfirm').click(function(e) {
				e.preventDefault();
				$('.confirmwrap').show();					   
			});		   
		}
				
		// subscription form
		if ($('#subForm')) {	
			var frm = $('hubForm');
			
			// show details of selected option
			var services = $('.service');
			
			if (services) {
				var sel = 0;
				services.each(function(i, item) {													
					if (item.attr('checked')) {
						$('#plan_'+ $(item).val()).show();							
					}
					else {
						$('#plan_'+ $(item).val()).hide();	
					}
					
					$('#units_' + $(item).val()).change(function() {																	 
						var unitprice = $('#price_' + $(item).val()).val();
						var newprice = unitprice * $('#units_' + $(item).val()).val();
						$('#injecttotal_' + $(item).val()).html(newprice.toFixed(2));
					});	
					
					$(item).click(function(){					
						HUB.Jobs.hideDetails();
						$('#plan_'+ $(item).val()).show();					
					});									
			   });								 			   
			}
						
			// display total price calculation
			$('.totalprice').each(function(i, item) {	
				$(item).show();					
			});			
		}		
		
		// save to shortlist
		//if ($$('.favvit')) {		
			//if ($$('.favvit').length > 0) {				
				$('.favvit').each(function(i, item) {		
					$(item).click(function(e) {	
						e.preventDefault();
						var oid = $($(item).parent()).attr('id').replace('o','');						
						var profilebox = $($(item).parent().parent().parent());
							
						$.get('index.php?option=com_jobs&task=plugin&trigger=onMembersShortlist&no_html=1&oid='+oid,{}, function(data){
							$(profilebox).html(data);
						});						
					});						  												  
				});								  
			//}
		//}
			
		// editing seeker info
		if ($('tagline-men')) {		
			HUB.Jobs.setCounter($('#tagline-men'), $('#counter_number_tagline') );
			
			if ($('#tagline-men').val() == '') {
				$('#tagline-men').val() = default_tagline;
				$('#tagline-men').setStyle('color', '#999');
			}
			
			$('#tagline-men').bind('click', function(e) {
					// Clear default value
					if ($('#tagline-men').val() == default_tagline)	 {
						$('#tagline-men').val('');
						$('#tagline-men').css('color', '#000');
					}										   
			});	
			
			$('#tagline-men').bind('keyup', function(e) {					
				HUB.Jobs.setCounter($('#tagline-men'), $('#counter_number_tagline') );
			});
		}
				
		if ($('#lookingfor-men')) {		
			HUB.Jobs.setCounter($('#lookingfor-men'), $('#counter_number_lookingfor') );
			
			if ($('#lookingfor-men').val() == '') {
				$('#lookingfor-men').val(default_lookingfor);
				$('#lookingfor-men').setStyle('color', '#999');
			}
			
			$('#lookingfor-men').bind('click', function(e) {
					// Clear default value
					if ($('#lookingfor-men').val() == default_lookingfor)	 {
						$('#lookingfor-men').val('');
						$('#lookingfor-men').css('color', '#000');
					}										   
			});	
			
			$('#lookingfor-men').bind('keyup', function(e) {					
					HUB.Jobs.setCounter($('#lookingfor-men'), $('#counter_number_lookingfor') );
			});												   
		}
		
		
		// submit form - cleanup default values
		
		if ($('#prefsForm')) {			
			$('#prefsForm').bind('submit', function(){
				if ($('#lookingfor-men').val() == '' || $('#lookingfor-men').val() == default_lookingfor) {
					$('#lookingfor-men').val('');
				}
				if ($('#tagline-men').val() =='' || $('#tagline-men').val() == default_tagline) {
					$('#tagline-men').val('');
				}
			});	
		}
			
		// show chars counter	
		$('.counter').each(function(i, elm) {
			$(elm).show();
		});			
	},
	
	hideDetails: function() {
		$('.subdetails').each(function(i, item) {											
			$(item).hide();								
		});
	},
	
	setCounter: function(el, numel) {
		if (!$(el).val()) {
			return;
		}
		var maxchars = 140;
		var current_length = $(el).val().length;
		var remaining_chars = maxchars-current_length;
		if (remaining_chars < 0) {
			remaining_chars = 0;
		}
		$(numel).html(remaining_chars);
			
		if (remaining_chars <= 10){
			$(numel.parent()).css('color', '#CC0000');
		} else {
			$(numel.parent()).css('color', '#999999');
		}
			
		if (remaining_chars == 0) {
			$(el).val($(el).val().substr(0,maxchars));
		}			
	}
}

jQuery(document).ready(function($){
	HUB.Jobs.initialize();
});
