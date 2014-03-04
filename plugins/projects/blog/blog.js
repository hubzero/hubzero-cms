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
// Project Micro Blog JS
//----------------------------------------------------------

HUB.ProjectMicroblog = {

	initialize: function() {
		// Infofeed - Comments
		var default_comment = 'Write your comment';
		var default_blog = 'Got an update?';
		var addcomment = $$('.addcomment');
		var showc = $$('.showc');
		
		if(addcomment.length > 0) {
			addcomment.each(function(i) {
				if(!i.hasClass('hidden')) {	
					i.addClass('hidden');
				}	
			});
		}
		
		// Showing comment area
		if(showc.length > 0) {
			showc.each(function(item) {
				item.addEvent('click', function(e) {
					new Event(e).stop();
					var id = item.getProperty('id').replace('addc_','');
					var acid = 'commentform_' + id;
				
					if($(acid) && $(acid).hasClass('hidden')) {
						$(acid).removeClass('hidden');		
						var coord = $(acid).getCoordinates();			
						var myFx = new Fx.Scroll(window).scrollTo(coord['left'], (coord['top'] - 200));
					}
					else if($(acid) && !$(acid).hasClass('hidden')) {
						$(acid).addClass('hidden');
					}
				});	
			});
		}
		
		// Comment form
		var commentarea = $$('.commentarea');
		if(commentarea.length > 0) {			
			commentarea.each(function(item) {	
				$(item).addEvent('keyup', function(e) {					
					HUB.ProjectMicroblog.setCounter($(item) );
				});
					
				if(item.value=='') {
					item.value = default_comment;
					item.setStyle('color', '#999');
					item.setStyle('height', '20px');
					item.setStyle('font-size', '100%');
				}
				item.addEvent('focus', function(e) {
					// Clear default value
					if(item.value == default_comment)	 {
						item.value = '';
						item.setStyle('color', '#000');
						item.setStyle('height', '70px');
					}				
				});	
			});
		}
		
		// Blog entry form
		if($('blogentry')) {	
			if($('blogentry').value=='') {
				$('blogentry').value = default_blog;
				$('blogentry').setStyle('color', '#999');
				$('blogentry').setStyle('height', '20px');
				$('blogentry').setStyle('font-size', '100%');
				$('blog-submit').addClass('hidden');
				$('blog-submitarea').setStyle('height', '0');
			}
			
			$('blogentry').addEvent('focus', function(e) {
				// Clear default value
				if($('blogentry').value == default_blog)	 {
					$('blogentry').value = '';
					$('blogentry').setStyle('color', '#000');
					$('blogentry').setStyle('height', '60px');
					$('blog-submit').removeClass('hidden');
					$('blog-submitarea').setStyle('height', '20px');
				}										   
			});	
			
			$('blogentry').addEvent('keyup', function(e) {					
				HUB.ProjectMicroblog.setCounter($('blogentry'), $('counter_number_blog') );
			});	
			
			// On click outside
			if($('blog-submitarea')) {
				$('blog-submitarea').addEvent('click', function(e) {
					// Clear default value
					if($('blogentry').value == default_blog || $('blogentry').value == '')	 {
						new Event(e).stop();
						$('blogentry').value = default_blog;
						$('blogentry').setStyle('color', '#999');
						$('blogentry').setStyle('height', '20px');
						$('blog-submit').addClass('hidden');
						$('blog-submitarea').setStyle('height', '0');
					}
				});	
			}
		}
		
		// Do not allow to post default values		
		if($('blog-submit')) {	
			$('blog-submit').addEvent('click', function(e){		
				if($('blogentry').value == '' || $('blogentry').value == default_blog) {
					new Event(e).stop();
					$('blogentry').value = default_blog;
					$('blogentry').setStyle('color', '#999');
					$('blogentry').setStyle('height', '20px');
					$('blog-submit').addClass('hidden');
					$('blog-submitarea').setStyle('height', '0');
				}
			});	
		}
		if($$('.c-submit').length > 0) {	
			$$('.c-submit').each(function(item) {				
				item.addEvent('click', function(e){	
					cid = item.getProperty('id').replace('cs_','');
					caid = 'ca_' + cid;
					if($(caid)) {
						if($(caid).value == '' || $(caid).value == default_comment) {
							new Event(e).stop();
						}
					}					
				});
			});
		}

		// Confirm delete
		var delit = $$('.delit');
		if(delit.length > 0)
		{
			delit.each(function(i) {
				var link =   i.getElement('a');
				link.addEvent('click', function(e) {	
					new Event(e).stop();
					if(HUB.Projects)
					{
						HUB.Projects.addConfirm (link, 'Permanently delete this entry?', 'yes, delete', 'cancel');
						if($('confirm-box'))
						{
							$('confirm-box').setStyles({'margin-left': '-100px' });		
						}
					}		
				});
			});		
		}
		
		// Show more updates
		if($('more-updates') && $('pid')) {	
			$('more-updates').addEvent('click', function(e) {
				new Event(e).stop();
				var link = $('more-updates').getElement('a');
				var url = link.getProperty('href');
				url = url + '&no_html=1&ajax=1&action=update';
				new Ajax(url,{
						'method' : 'get',
						'update' : $('latest_activity'),
						onComplete: function(response) { 
						  HUB.ProjectMicroblog.initialize();
						}
				}).request();
				
			});	
		}
		
	},
	
	setCounter: function(el, numel ) {		
		var maxchars = 250;			
		var current_length = el.value.length;
		var remaining_chars = maxchars-current_length;
		if(remaining_chars < 0) {
			remaining_chars = 0;
		}
		
		if(numel) {
			if(remaining_chars <= 10){
				numel.innerHTML = remaining_chars + ' chars remaining';
				$(numel.parentNode).setStyle('color', '#ff0000');			
			} else {
				$(numel.parentNode).setStyle('color', '#999999');
				numel.innerHTML = '';
			}
		}
		
		if (remaining_chars == 0) {
			el.setProperty('value', el.getProperty('value').substr(0,maxchars));
		}			
	}
}
	
window.addEvent('domready', HUB.ProjectMicroblog.initialize);