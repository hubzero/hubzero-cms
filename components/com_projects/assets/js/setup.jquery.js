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

if (!jq) {
	var jq = $;
}

HUB.ProjectSetup = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ 				= this.jQuery;
		var hubfrm 			= $('#hubForm');
		var sbjt 			= $('#verificationarea');
		var moveon 			= $('#moveon');
		var describe 		= $('#describe');
		var describearea 	= $('#describearea');
		var ptitle 			= $('#ptitle');
		var sbjt_t 			= $('#verificationarea_title');
		var next_desc		= $('#next_desc');
		var next_step		= $('#next_step');
		
		// Setup
		var rest  = $('.restricted-opt');
		var resta = $('.restricted-answer');
		
		if(rest.length > 0 && resta.length > 0)
		{
			HUB.ProjectSetup.enableButton();
			
			$('.restricted-opt').each(function(i, item) {
				$(item).off('click');
				HUB.ProjectSetup.showStopSigns(item);
				$(item).on('click', function(e) {
					$('.restricted-answer').each(function(ritem) {
						$(ritem).removeAttr("checked");
					});
					$('#restricted-yes').attr('checked', 'checked');
					HUB.ProjectSetup.showStopSigns(item);
					HUB.ProjectSetup.enableButton();
				});
			});

			$('#restricted-no').on('click', function(e) {
				$('.restricted-opt').each(function(i, item) {
					$(item).removeAttr("checked");
				});	
			});	
						
			// Check if can proceed
			if($('.option').length > 0)
			{
				$('.option').each(function(i, item) 
				{
					$(item).on('click', function(e) {
						HUB.ProjectSetup.enableButton();
					});
				});
			}				
		}		
	
		// Setup pre-screen
		if ($('#f-restricted-no').length && $('#f-restricted-explain'))
		{
			$('#f-restricted-no').on('click', function(e) {
				$('#f-restricted-explain').addClass('hidden');
			});
			if ($('#f-restricted-no').attr('checked') == 'checked')
			{
				$('#f-restricted-explain').addClass('hidden');
			}
		}

		if ($('#f-restricted-yes').length && $('#f-restricted-explain'))
		{
			$('#f-restricted-yes').on('click', function(e) {
				$('#f-restricted-explain').removeClass('hidden');
			});
		}	
				
		// Show/hide options to describe project
		if ($('#pid') && ($('#pid').val() == '' || $('#pid').val() == 0))  
		{
			// Show by default for those with JS enabled
			if ($('#moveon')) {
				$('#moveon').css('display', 'block');
			}
			// Hide by default for those with JS enabled
			if ($('#describearea')) {
				$('#describearea').css('display', 'none');
			}
			
			// Title verification
			if ($('#describe') && $('#ptitle')) {
				var keyupTimerA = '';
				$('#ptitle').on('keyup', function(e) {
					if (keyupTimerA) {
						clearTimeout(keyupTimerA);
					}

					 var keyupTimerA = setTimeout((function() { 
						if ($('#ptitle').val().length > 5) {
							if ($('#verificationarea_title')) {
								$('#verificationarea_title').html('<p class="verify_passed">Title looks good &rarr;</p>');
							}
						} else {
							if ($('#verificationarea_title')) {
								$('#verificationarea_title').html('<p class="verify_failed">Title too short &rarr;</p>');
							}
						}
						HUB.ProjectSetup.watchInput($('#verified').val(), $('#ptitle').val().length, $('#describe'), $('#moveon'));
					}), 500);
				});
			}
			
			// Show description fields
			if ($('#next_desc')) {
				$('#next_desc').on('click', function(e) {
					e.preventDefault();
					$('#extended').val(1);
					$('#save_stage').val(0);
					$(hubfrm).submit();
				});
			}
			
			// Go to next step
			if ($('#next_step')) {
				$('#next_step').on('click', function(e) {
					e.preventDefault();
					$('#extended').val(0);
					$('#save_stage').val(1);
					$(hubfrm).submit();
				});
			}
		}

		// Verifier for project alias
		if ($('#verificationarea')) {
			var keyupTimerB = '';
			$('#name').on('keyup', function(e) {
				if (keyupTimerB) {
					clearTimeout(keyupTimerB);
				}

				// Clean up entered value
				$('#name').val(HUB.Projects.cleanupText($('#name').val()));

				$('#verificationarea').empty();
				var keyupTimerB = setTimeout((function() {  
					$('#verificationarea').append('<p id="loading-section"></p>');

					$.get('index.php?option=com_projects&task=verify&no_html=1&ajax=1&name=' 
					+ $('#name').val() + '&pid=' + $('#pid').val(), {}, function(data) {
						if (data) {
							var bits = data.split('::');
							var out  = '<p class="' + bits[0] + '">' + bits[1] + ' &rarr;</p>';
							$('#verificationarea').html(out);
						}

						if (data.search("passed") >= 0) { 
							$('#verified').val(1);
						} else { 
							$('#verified').val(0);
						}

						if ($('#pid') && ($('#pid').val() == '' || $('#pid').val() == 0)) {
							HUB.ProjectSetup.watchInput($('#verified').val(), $('#ptitle').val().length, $('#describe'), $('#moveon'));
						}
					});
				}), 1000);
			});
		}
		
		// Activate provisioned project
		if ($('#verify-alias')) 
		{
			HUB.ProjectSetup.enableButtonActivate();
			var keyupTimer1 = '';
			$('#new-alias').on('keyup', function(e) {
				if (keyupTimer1) {
					clearTimeout(keyupTimer1);
				}
				
				$('#new-alias').val(HUB.Projects.cleanupText($('#new-alias').val()));
				$('#verify-alias').empty();
				
				var keyupTimer1 = setTimeout((function() {  
					$('#verify-alias').append('<p id="loading-section"></p>');

					$.get('index.php?option=com_projects&task=verify&no_html=1&ajax=1&name='
					+ $('#new-alias').val()+'&pid='+$('#projectid').val(), {}, function(data) {
						if (data) {
							var bits = data.split('::');
							var out  = '<p class="' + bits[0] + '">' + bits[1] + '</p>';
							$('#verify-alias').html(out);
						}

						if (data.search("passed") >= 0) { 
							$('#verified').val(1);
						} else { 
							$('#verified').val(0);
						}

						HUB.ProjectSetup.enableButtonActivate();
					});
				}), 1000);
			});

			if ($('#agree')) {
				$('#agree').off('click');
				$('#agree').on('click', function(e) {
					HUB.ProjectSetup.enableButtonActivate();
				});
			}
		}		
	},
	
	watchInput: function(verified, supplied, elshow, elhide) 
	{
		if (verified==1 && supplied > 5) {
			elhide.css('display', 'none');
			elshow.css('display', 'block');
		} else {
			elhide.css('display', 'block');
			elshow.css('display', 'none');
		}
	},
	
	enableButton: function() 
	{
		var $ = this.jQuery;
		var con = $('#btn-finalize');
		var passed = 1;
		
		if($('#export') && $('#export').attr('checked') == 'checked')
		{
			passed = 0;
		}
		if($('#hipaa') && $('#hipaa').attr('checked') == 'checked')
		{
			passed = 0;
		}
		if($('#irb') && $('#irb').attr('checked') == 'checked' && $('#agree_irb').attr('checked') != 'checked' )
		{
			passed = 0;
		}
		if($('#ferpa') && $('#ferpa').attr('checked') == 'checked' && $('#agree_ferpa').attr('checked') != 'checked' )
		{
			passed = 0;
		}

		if(passed == 1 && con.hasClass('disabled')) { 
			con.removeClass('disabled'); 
		}
		if(passed == 0 && !con.hasClass('disabled')) {
			con.addClass('disabled');
		}
		
		con.off('click');
		con.on('click', function(e) {
			e.preventDefault();
			if (!con.hasClass('disabled')) {
				if ($('#hubForm')) {
					$('#hubForm').submit();
				}
			}
		});
	},
	
	showStopSigns: function(el)
	{
		var $ = this.jQuery;
		
		var oid = $(el).attr('id');
		var obox = '#stop-' + oid;
		
		if($(el).attr('checked') == 'checked' && $(obox).hasClass('hidden'))
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
		var $ = this.jQuery;
		var con = $('#b-continue');
		
		if (con)
		{
			var passed = 1;

			if (($('#verified') && $('#verified').val() == 0) || ($('#new-alias') && $('#new-alias').val() == '')) {
				passed = 0;
			}
			
			if (passed == 1 && con.hasClass('disabled')) { 
				con.removeClass('disabled'); 
			}
			if (passed == 0 && !con.hasClass('disabled')) {
				con.addClass('disabled');
			}

			con.off('click');
			con.on('click', function(e) {
				e.preventDefault();
				if (!con.hasClass('disabled')) {
					if ($('#activate-form')) {
						$('#activate-form').submit();
					}
				}
			});
		}
	}
}
	
jQuery(document).ready(function($){
	HUB.ProjectSetup.initialize();
});