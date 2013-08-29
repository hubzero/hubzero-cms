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
// Project Setup JS
//----------------------------------------------------------

HUB.ProjectSetup = {

	initialize: function() 
	{
		var hubfrm 			= $('hubForm');
		var sbjt 			= $('verificationarea');
		var moveon 			= $('moveon');
		var describe 		= $('describe');
		var describearea 	= $('describearea');
		var ptitle 			= $('ptitle');
		var sbjt_t 			= $('verificationarea_title');
		var next_desc		= $('next_desc');
		var next_step		= $('next_step');
		
		// Setup
		var rest  = $$('.restricted-opt');
		var resta = $$('.restricted-answer');
		if(rest.length > 0 && resta.length > 0)
		{
			HUB.ProjectSetup.enableButton();
			if($('restricted-yes'))
			{
				rest.each(function(item) 
				{	
					item.removeEvents();
					HUB.ProjectSetup.showStopSigns(item);			
					item.addEvent('click', function(e) {
						resta.each(function(ritem) 
						{
							ritem.checked = false;
						});
						$('restricted-yes').checked = true;
						
						HUB.ProjectSetup.showStopSigns(item);
						HUB.ProjectSetup.enableButton();
					});
				});
			}

			if($('restricted-no'))
			{
				$('restricted-no').addEvent('click', function(e) {
					rest.each(function(item) 
					{
						item.checked = false;
						HUB.ProjectSetup.showStopSigns(item);
						HUB.ProjectSetup.enableButton();
					});	
				});	
			}
			
			// Check if can proceed
			if($$('.option').length > 0)
			{
				$$('.option').each(function(item) 
				{
					item.addEvent('click', function(e) {
						HUB.ProjectSetup.enableButton();
					});
				});
			}
		}
		
		// Setup pre-screen
		if ($('f-restricted-no') && $('f-restricted-explain'))
		{
			$('f-restricted-no').addEvent('click', function(e) {
				$('f-restricted-explain').addClass('hidden');
			});
			if ($('f-restricted-no').checked == true)
			{
				$('f-restricted-explain').addClass('hidden');
			}
		}
		
		if ($('f-restricted-yes') && $('f-restricted-explain'))
		{
			$('f-restricted-yes').addEvent('click', function(e) {
				$('f-restricted-explain').removeClass('hidden');
			});
		}
	
		// Show/hide options to describe project		
		if($('pid') && ($('pid').value == '' || $('pid').value == 0)) 
		{
			// Show by default for those with JS enabled
			if($('moveon')) 
			{
				$('moveon').style.display = 'block';	
			}
			// Hide by default for those with JS enabled
			if($('describearea')) 
			{
				$('describearea').style.display = 'none';	
			}
			
			// Title verification
			if($('describe') && $('ptitle')) 
			{
				var keyupTimerA = '';
				$('ptitle').addEvent('keyup', function(e) 
				{
					 if(keyupTimerA) 
					 {
						clearTimeout(keyupTimerA);	
					 }

					 var keyupTimerA = setTimeout((function() 
					 {  
						if($('ptitle').value.length > 5) {
							if($('verificationarea_title'))	{
								$('verificationarea_title').innerHTML = '<p class="verify_passed">Title looks good &rarr;</p>';
							}
						}
						else {
							if($('verificationarea_title'))	{
								$('verificationarea_title').innerHTML = '<p class="verify_failed">Title too short &rarr;</p>';
							}
						}
						HUB.ProjectSetup.watchInput(hubfrm.verified.value, $('ptitle').value.length, $('describe'), $('moveon'));					
					}), 500);
				});
			}
			
			// Show description fields
			if($('next_desc')) 
			{
				$('next_desc').addEvent('click', function(e) 
				{
						var e = new Event(e).stop();
						$(hubfrm).extended.value = 1;
						$(hubfrm).save_stage.value = 0;
						$(hubfrm).submit();
				});
			}
			
			// Go to next step		
			if($('next_step')) 
			{
				$('next_step').addEvent('click', function(e) 
				{
						var e = new Event(e).stop();
						$(hubfrm).extended.value = 0;
						$(hubfrm).save_stage.value = 1;
						$(hubfrm).submit();
				});
			}
		}

		// Verifier for project alias
		if ($('verificationarea')) 
		{
			var keyupTimerB = '';
			$('name').addEvent('keyup', function(e) 
			{
				if(keyupTimerB) {
					clearTimeout(keyupTimerB);		
				}

				// Clean up entered value
				$('name').value = HUB.Projects.cleanupText($('name').value);

				$('verificationarea').empty();
				var keyupTimerB = setTimeout((function() {  
					var p = new Element('p', {'id':'loading-section'});				
					p.injectInside($('verificationarea'));
					var response = '';
					new Ajax('index.php?option=com_projects&task=verify&no_html=1&ajax=1&name=' + hubfrm.name.value + '&pid=' + hubfrm.id.value,{
							'method' : 'get',
							onComplete: function(response) { 
								if(response) {
									var bits = response.split('::');
									var out  = '<p class="' + bits[0] + '">' + bits[1] + ' &rarr;</p>';
									$('verificationarea').innerHTML = out;
								}

								if(response.contains('passed')) { hubfrm.verified.value = 1; } 
								else { hubfrm.verified.value = 0; }

								if($('pid') && ($('pid').value == '' || $('pid').value == 0)) {
									HUB.ProjectSetup.watchInput(hubfrm.verified.value, $('ptitle').value.length, $('describe'), $('moveon'));
								}
							}
					}).request();
				}), 1000);				
			});
		}
		
		// Activate provisioned project
		if($('verify-alias')) {
			HUB.ProjectSetup.enableButtonActivate();
			var keyupTimer1 = '';
			$('new-alias').addEvent('keyup', function(e) {
				if(keyupTimer1) {
					clearTimeout(keyupTimer1);		
				}
				$('new-alias').value = HUB.Projects.cleanupText($('new-alias').value);
				$('verify-alias').empty();
				var keyupTimer1 = setTimeout((function() {  
					var p = new Element('p', {'id':'loading-section'});				
					p.injectInside($('verify-alias'));
					var response = '';
					new Ajax('index.php?option=com_projects&task=verify&no_html=1&ajax=1&name='+$('new-alias').value+'&pid='+$('projectid').value,{
							'method' : 'get',
							onComplete: function(response) { 
								if(response) {
									var bits = response.split('::');
									var out  = '<p class="' + bits[0] + '">' + bits[1] + '</p>';
									$('verify-alias').innerHTML = out;
									if(response.contains('passed')) { $('verified').value = 1; } 
									else { $('verified').value = 0; }
									HUB.ProjectSetup.enableButtonActivate();
								}
							}
					}).request();
				}), 1000);
			});

			if($('agree'))
			{
				$('agree').removeEvents();
				$('agree').addEvent('click', function(e) {
					HUB.ProjectSetup.enableButtonActivate();
				});
			}
		}		
	},
	
	watchInput: function(verified, supplied, elshow, elhide) {
		if(verified==1 && supplied > 5) {
			elhide.style.display = 'none';
			elshow.style.display = 'block';
		}
		else {
			elhide.style.display = 'block';
			elshow.style.display = 'none';
		}
	},
	
	showStopSigns: function(el)
	{
		var oid = el.getProperty('id');
		var obox = 'stop-' + oid;
		if(el.checked == true && $(obox).hasClass('hidden'))
		{
			$(obox).removeClass('hidden');
		}
		else
		{
			$(obox).addClass('hidden');
		}
	},
	
	enableButtonActivate: function() 
	{
		var con = $('b-continue');
		var passed = 1;
		
		if (con)
		{
			if($('verified') && $('verified').value == 0 || $('new-alias').value == '')
			{
				passed = 0;
			}
			if($('agree') && $('agree') && $('agree').checked == false)
			{
				passed = 0;
			}

			if(passed == 1 && con.hasClass('disabled')) { 
				con.removeClass('disabled'); 
			}
			if(passed == 0 && !con.hasClass('disabled')) {
				con.addClass('disabled');
			}

			con.removeEvents();
			con.addEvent('click', function(e) {
				new Event(e).stop();
				if(!con.hasClass('disabled')) {
					if($('activate-form')) {					
						$('activate-form').submit();
					}
				}
			});
		}
	},
	
	enableButton: function() 
	{
		var con = $('btn-finalize');
		var passed = 1;
		
		if($('export') && $('export').checked == true)
		{
			passed = 0;
		}
		if($('hipaa') && $('hipaa').checked == true)
		{
			passed = 0;
		}
		if($('irb') && $('irb').checked == true && $('agree_irb').checked == false )
		{
			passed = 0;
		}
		if($('ferpa') && $('ferpa').checked == true && $('agree_ferpa').checked == false )
		{
			passed = 0;
		}

		if(passed == 1 && con.hasClass('disabled')) { 
			con.removeClass('disabled'); 
		}
		if(passed == 0 && !con.hasClass('disabled')) {
			con.addClass('disabled');
		}
		
		con.removeEvents();
		con.addEvent('click', function(e) {
			new Event(e).stop();
			if(!con.hasClass('disabled')) {
				if($('hubForm')) {					
					$('hubForm').submit();
				}
			}
		});
	}
}
	
window.addEvent('domready', HUB.ProjectSetup.initialize);