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

//-----------------------------------------------------------
//  Highlight table rows when clicking checkbox
//-----------------------------------------------------------
HUB.Groups = {
	checkRow: function(checkbox) {
		var tr = checkbox.parentNode.parentNode;
		if(checkbox.checked) {
			tr.addClass('selected');
		} else {
			tr.removeClass('selected');
		}
	},
	
	Asset_Browser: function () {
		var top_box = $('top_box');
		var bottom_box = $('bottom_box');
		var asset_browser = $('asset_browser');
		
		if(asset_browser && top_box && bottom_box) {
			var scroll_y = $(window).getSize().scroll.y;
			
			var top_box_top = top_box.getCoordinates().top;
			var bottom_box_bottom = bottom_box.getCoordinates().bottom;
			var asset_browser_height = asset_browser.getSize().size.y;
			
			var track_start = 7;
			var track_stop = bottom_box_bottom - top_box_top - asset_browser_height;
			
			if(scroll_y > track_stop) {
				margin = track_stop;
			} else if(scroll_y < track_start) {
				margin = track_start;
			} else {
				margin = scroll_y;
			}
			
			asset_browser.setStyles({
				'position':'absolute',
				'z-index':'999',
				'margin-top': margin
			});
		}
	
	},
	
	Toggle_Group_Description: function() {
		var toggler = $('toggle_description');
		var completes = 0;
		
		var private_desc = $('private');
		var public_desc = $('public');
		
		if(toggler) {
			toggler.removeClass('hide');
			
			toggler.addEvent('click', function(e) {
				e = new Event(e);
			
				if(public_desc.hasClass('hide')) {
					toggler.innerHTML = 'Show Private Description (-)';
					public_desc.removeClass('hide');
					private_desc.addClass('hide');
				} else {
					public_desc.addClass('hide');
					private_desc.removeClass('hide');
					toggler.innerHTML = 'Show Public Description (+)';
				}
			
				e.stop();
			});
		
		}
		
	},
	
	Toggle_Group_Controls: function() {
		if($('control_items')) {
			$('control_items').addClass('hide');
			$('toggle-controls').setStyle('display','block');
			
			$('toggle-controls').addEvent('click', function(e){
				new Event(e).stop();
				var status = '';
				
				if(this.innerHTML.test('Manager')) {
					status = 'Manager';
				} else if(this.innerHTML.test('Member')) {
					status = 'Member';
				} else if(this.innerHTML.test('Pending')) {
					status = 'Pending';
				}
				
				if(this.innerHTML.test('Hide')) {
					$('control_items').addClass('hide');
					this.innerHTML = 'Show ' + status + ' Controls';
					this.removeClass('active');
				} else { 
					$('control_items').removeClass('hide');
					this.innerHTML = 'Hide ' + status + ' Controls';
					this.addClass('active');
				}
				
				
			});
		}
	},
	
	Group_Cancel_Check: function() {
		$$('a.cancel_group_membership').addEvent('click', function(e) {
			new Event(e).stop();
			
			var answer = confirm('Are you sure you would like to cancel your group membership?')
			if(answer) { 
				window.location = this.href;
			}
		});
	},
	
	Pick_Logo: function() {
		if($('group_logo')) {
			var gid = $('group_logo').getProperty("rel");
			
			$('group_logo').addEvent('change', function() {
				var logo = this.value;
				
				if(logo == '') {
					final_logo = '/components/com_groups/assets/img/group_default_logo.png';
				} else {
					final_logo = logo;
				}
				
				$('logo_picked').innerHTML = '<img src="' + final_logo + '" />';
			});
		}
	},
	
	Pick_Content: function() {
		var overview_content = $('overview_content');
		if(overview_content) {
			var checked = $('group_overview_type_custom').getProperty('checked');
			if(!checked) {
				overview_content.addClass('hide');
			}
			
			$$('input[type=radio]').addEvent('click', function() {
				$$('p.side-by-side').removeClass('checked');
				var thisp = this.parentNode.parentNode;
				thisp.addClass('checked');
		
				if(this.value == 1) {
					overview_content.removeClass('hide');
				} else {
					overview_content.addClass('hide');
				}
			});
		}
	},
	
	initialize: function() {
		HUB.Groups.Toggle_Group_Description();
		HUB.Groups.Toggle_Group_Controls();
		HUB.Groups.Group_Cancel_Check();
		
		//customize
		HUB.Groups.Asset_Browser();
		HUB.Groups.Pick_Logo();
		HUB.Groups.Pick_Content();
		
		//function that hides all p tags without content
		$$('p').each(function(el) {
			var text = el.innerHTML;
			if(escape(text) == '%3Cbr%3E%0A') {
				el.addClass('hide');
			}
		});
		
		
		//group features screenshots
		$$('a.screenshot').addEvent('click', function(e) {
			new Event(e).stop();
			
			SqueezeBoxHub.fromElement(this,{
				handler: 'adopt',
				size: {x: 850 ,y: 446},
			});
			
		});
		
		
		$$('a.quick-view').addEvent('click', function(e) {
			new Event(e).stop();
			var id = this.getProperty('rel');
			var el = $(id);
			var patt = /page/;
			
			if(patt.test(id)) { 
				SqueezeBoxHub.fromElement(el,{
					handler: 'adopt',
				});
			} else {
				SqueezeBoxHub.fromElement(el,{
					handler: 'adopt',
					size: {x: 300 ,y: 250},
				});
			}
		});
		
		$$('.leave_area').addEvent('click', function(e) {
			new Event(e).stop();
			
			var question = this.getProperty('rel');
			var answer = confirm(question);
			if(answer) { 
				window.location = this.href;
			}
		});
		
		var tables = document.getElements('.dataset');
		tables.each(function(table) {
			var inputs = table.getElementsByTagName('input');
			for (var i=0; i<inputs.length; i++) {
				if (inputs[i].type == 'checkbox') {
					inputs[i].onclick = function() { HUB.Groups.checkRow(this); }
				}
			}
		});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Groups.initialize);
window.addEvent('scroll', HUB.Groups.Asset_Browser);