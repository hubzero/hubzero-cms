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
// Project Component JS
//----------------------------------------------------------

HUB.Projects = {

	initialize: function() 
	{					
		// Fix up users with no JS
		HUB.Projects.fixJS();
		
		// Activate boxed content
		HUB.Projects.launchBox();
		
		// Add confirms
		HUB.Projects.addConfirms();
		
		// Reviewers
		HUB.Projects.addFiltering();
		
	},
	
	addFiltering: function()
	{
		// Browse projects - filtering
		var filterby = $$('.filterby');
		if(filterby.length > 0) 
		{
			filterby.each(function(item) 
			{
				item.addEvent('change', function(e) {
					if($('browseForm'))
					{
						$('browseForm').submit();
					}
				});
			});
		}
	},
	
	addConfirms: function()
	{
		// Confirm delete
		if($('suspend')) 
		{
			$('suspend').addEvent('click', function(e) 
			{
				new Event(e).stop();			
				HUB.Projects.addConfirm($('suspend'), 
				'Are you sure you want to suspend this project?', 
				'Yes, suspend', 'No, do not suspend');
			});
		}

		// Confirm revert
		if($('confirm-revert')) 
		{
			$('confirm-revert').addEvent('click', function(e) 
			{
				new Event(e).stop();			
				HUB.Projects.addConfirm($('confirm-revert'), 
				'Are you sure you want to revert this project to draft mode?', 
				'Yes, revert', 'No, keep as pending');
			});
		}		
	},
	
	// Refresh counts of project activities and resources
	refreshCount: function(what) 
	{	
		if($('projectid')) {
			var url = 'index.php?option=com_projects&task=showcount&no_html=1&pid=' + $('projectid').value;
			if(what=='publications' && $('c-publications') && $('c-publications-num')) {				
				new Ajax(url + '&what=publication', {
						'method' : 'get',
						'update' : $('c-publications-num')
				}).request();
			}
			if(what=='files' && $('c-files') && $('c-files-num')) {
				new Ajax(url + '&what=files', {
						'method' : 'get',
						'update' : $('c-files-num')
				}).request();
			}
			if(what=='team' && $('c-team') && $('c-team-num')) {
				new Ajax(url + '&what=team', {
						'method' : 'get',
						'update' : $('c-team-num')
				}).request();
			}
			if(what=='todo' && $('c-todo') && $('c-todo-num')) {
				new Ajax(url + '&what=todo', {
						'method' : 'get',
						'update' : $('c-todo-num')
				}).request();
			}
			if(what=='newactivity' && $('c-new') && $('c-new-num')) {
				new Ajax(url + '&what=newactivity', {
						'method' : 'get',
						'update' : $('c-new-num')
				}).request();
			}	
		}
	},
	
	// Launch SqueezeBox with Ajax actions	
	launchBox: function() 
	{	
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) 
			{
				SqueezeBoxHub.initialize({ size: {x: 600, y: 500} });
			}

			var inbox = $$('.showinbox');
			if(inbox.length > 0) 
			{
				inbox.each(function(item) 
				{	
					// Clean up
					item.removeEvents();				

					// Open box on click
					item.addEvent('click', function(e) {
						new Event(e).stop();

						var href = item.href;
						if(href.search('&ajax=1') == -1) {
							item.href = item.href + '&ajax=1';	
						}
						if(href.search('&no_html=1') == -1) {
							item.href = item.href + '&no_html=1';	
						}					

						if(!item.hasClass('inactive')) 
						{						
							// Modal box for actions
							SqueezeBoxHub.fromElement(item,{						
								size: {x: 600, y: 500}, 
								classWindow: 'sbp-window',
								classOverlay: 'sbp-overlay',
								handler: 'url', 
								ajaxOptions: {
									method: 'get',
									onComplete: function() 
									{	
										// Activate cancel button
										if($('cancel-action')) {
											$('cancel-action').addEvent('click', function(e) {
												SqueezeBoxHub.close();
											});
										}

										// Pass selections (publications)
										if($('ajax-selections') && $('section'))
										{
											if(HUB.ProjectPublications) 
											{
												var replacement = '';
												if($('section').value == 'gallery' || $('section').value == 'content') {
													replacement = 'clone-';	
												}
												else {
													replacement = 'clone-author::';		
												}
												var selections = HUB.ProjectPublications.gatherSelections(replacement);
												$('ajax-selections').value = selections;
											}
										}
										// Reviewers
										HUB.Projects.resetApproval();									
									}
								}
							});
						}			
					});						  												  
				});
			}
		}		
	},
	
	resetApproval: function() 
	{
		if($('grant_approval') && $('rejected') )
		{
			$('grant_approval').addEvent('keyup', function(e) 
			{
				if($('grant_approval').value != '')
				{
					$('rejected').checked = false;
				}
			});
			$('rejected').addEvent('click', function(e) 
			{
				if($('rejected').checked  == true)
				{
					$('grant_approval').value = '';
				}
			});
		}
	},
	
	setCounter: function(el, numel ) 
	{		
		var maxchars = 250;			
		var current_length = el.value.length;
		var remaining_chars = maxchars-current_length;
		if(remaining_chars < 0) 
		{
			remaining_chars = 0;
		}
		
		// Show remaining characters
		if(numel) 
		{
			if(remaining_chars <= 10)
			{
				numel.innerHTML = remaining_chars + ' chars remaining';
				$(numel.parentNode).setStyle('color', '#ff0000');			
			} else {
				$(numel.parentNode).setStyle('color', '#999999');
				numel.innerHTML = '';
			}
		}
		
		// Do not let more characters
		if (remaining_chars == 0) 
		{
			el.setProperty('value', el.getProperty('value').substr(0,maxchars));
		}			
	},
	
	cleanupText: function(text) 
	{
		// Clean up entered value
		var cleaned = text.toLowerCase();
		cleaned = cleaned.replace('_', '');
		cleaned = cleaned.replace('-', '');
		cleaned = cleaned.replace(' ', '');
		cleaned = cleaned.replace(/[|&;$%@"<>()+,#!?.~*^=-_]/g, '');
		return cleaned;
	},
	
	fixJS: function()
	{
		// Hide all no-js messages
		var nojs = $$('.nojs');
		if(nojs.length > 0) 
		{
			nojs.each(function(item) {
				item.style.display = 'none';
			});	
		}

		// Show all js-only options
		var js = $$('.js');
		if(js.length > 0) 
		{
			js.each(function(item) {
				item.removeClass('js');
			});	
		}
	},
	
	addConfirm: function (link, question, yesanswer, noanswer) 
	{			
		if($('confirm-box')) 
		{
			$('confirm-box').remove();	
		}

		// Add confirmation
		var confirm =  new Element('div', {
			'class': 'confirmaction'
		}).inject(link.parentNode.parentNode, 'before');
		confirm.setProperty('id', 'confirm-box');
		confirm.style.display = 'block';
		
		var href = link.href;

		var p = new Element('p');
		p.injectInside(confirm);
		p.innerHTML = question;

		var p2 = new Element('p');
		p2.injectInside(confirm);

		var a1 = new Element('a', {
			'href': link.href,
			'class': 'confirm'
		}).injectInside(p2);
		a1.innerHTML = yesanswer;

		var a2 = new Element('a', {
			'href': '#',
			'class': 'cancel',
			'events': {
				'click': function(evt){
					(new Event(evt)).stop();
					$('confirm-box').remove();
				}
			}
		}).injectInside(p2);
		a2.innerHTML = noanswer;
		
		// Move close to item
		var coord = link.parentNode.getCoordinates();	
		
		// Scroll
		var myFx = new Fx.Scroll(window).scrollTo(coord['left'], (coord['top'] - 200));
					
		$('confirm-box').setStyles({'left': coord['left'], 'top': coord['top'] });
	}
}		
	
window.addEvent('domready', HUB.Projects.initialize);
