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
// Project To-do JS
//----------------------------------------------------------

HUB.ProjectTodo = {

	initialize: function() {
		
		// Fix up users with no JS
		if(HUB.Projects)
		{
			HUB.Projects.fixJS();
		}
		
		var editid = 0;
		var edithtml = '';
		
		var frm = $('plg-form');
		var todoid = $('todoid');
		var pinboard = $('pinboard');
		var container = $('wrap');
		var defaultdue = 'mm/dd/yyyy';
		
		// Get IE version
		var IE = HUB.ProjectTodo.getInternetExplorerVersion();
				
		// Pick list
		if($('pinselector')) {
			var keyupTimer = '';
			$('pinselector').addEvent('click', function(e){
				new Event(e).stop();
				$('pinoptions').style.display = 'block';
				clearTimeout(keyupTimer);
				keyupTimer = setTimeout((function() {  
					$('pinoptions').style.display = 'none';			
				}), 3000);
			});
			$$('.listclicker').each(function(item) {
				item.addEvent('click', function(e) {	
					$('pinoptions').style.display = 'none';
					var css = item.value ? 'pin_' + item.value : 'pin_grey';
					frm.list.value = item.value;
					if(css && !$('pinselector').getElement('span').hasClass(css)) {
						$('pinselector').getElement('span').setProperty('class', css);
					}
				});
			});
		}
		
		// Confirm delete
		var confirm = $$('.confirm-it');
		if(confirm.length > 0 && $('td-item')) {
			confirm.each(function(item) {
				item.addEvent('click', function(e) {	
					new Event(e).stop();			
					HUB.ProjectTodo.addConfirm(item, $('td-item'));
				});
			});	
		}
		
		// Edit todo item	
		if((IE == -1 || IE > 7) && $$('.todo-content').length > 0 && !($('td-item'))) {	
			$$('.todo-content').each(function(item) {												
				item.addEvent('dblclick', function(e) {	
				
					if(editid != 0) {
						// Hide previously edited item
						var previtem = $('td-content-' + editid);
						if ($('td-content-' + editid) && previtem.parentNode.parentNode.hasClass('edited')) {
							previtem.innerHTML = edithtml;
							previtem.parentNode.parentNode.removeClass('edited');
							var prevop = $('td-options-'+ editid);
							if(prevop.hasClass('hidden')) {	
								prevop.removeClass('hidden');
							}
						}
					}
							
					var tdid = item.parentNode.id.replace('td-','');
					editid = tdid;
					
					// Store original data
					var original = item.innerHTML;
					edithtml = original;
				
					var due = $('td-due-'+ tdid);
					if(due) {
						var duedate = due.innerHTML;
						duedate = duedate.replace('due ','');
						duedate = duedate.replace(' (overdue)','');
					}
					else {
						var duedate = '';
					}
					var assigned = $('td-assigned-'+ tdid);
					if(assigned) {
						var assignedto = assigned.innerHTML;
					}
					else {
						var assignedto = '';
					}
					var index = $('idx-'+ tdid);
					var index = index ? index.value : 0;
					
					// Pass item id to form
					todoid.value = tdid;			
					
					// Hide options
					var op = $('td-options-'+ tdid);
					if(op && !op.hasClass('hidden')) {	
						op.addClass('hidden');
					}
					
					// Hide text
					item.innerHTML = '';
					
					// Add parent class
					item.parentNode.parentNode.addClass('edited');							
					
					// Append textarea for todo content
					var input = new Element('textarea', {
						'name': 'content'
					}).appendText(original).injectInside(item);
					
					// Give enough space
					var rows = Math.round(original.length / 18);
					var space = (rows * 20) + 'px';
					if(original.length < 10) {
						space = '22px';
					}
					input.setStyle('height', space);
					
					// Make sure item is not too long
					input.addEvent('keyup', function(e) {					
						var maxchars = 150;			
						var current_length = input.value.length;
						var rows = Math.round(input.value.length / 18);
						var space = (rows * 20) + 'px';
						if(input.value.length < 10) {
							space = '22px';
						}
						input.setStyle('height', space);
						var remaining_chars = maxchars - current_length;
						if(remaining_chars < 0) {
							remaining_chars = 0;
						}
						if (remaining_chars == 0) {
							input.setProperty('value', input.getProperty('value').substr(0,maxchars));
						}
					});
					
					// Add assigned selector
					var selector = $('td-selector');
					if(selector) {
						var copy = selector.clone().injectInside(item);
						copy.removeClass('hidden');
						var teamselect = copy.getElement('select');
						teamselect.selectedIndex = index;
					}
					
					// Add due date selector
					if(!item.parentNode.parentNode.hasClass('tdclosed')) {
						var label = new Element('label', {
						}).injectInside(item);
						label.innerHTML = 'Due: ';
					
						var due = new Element('input', {
							'type': 'text',
							'name': 'due',
							'id': 'dued',
							'class': 'duebox',
							'value': duedate
						}).injectInside(label);
					
						var myCal = new Calendar({ dued : 'm/d/Y' }, { classes: ['todocal'], direction: 1 });
						due.removeEvents();
						due.removeProperty('readonly');
						if(!duedate && due.value == '') {
							due.value = defaultdue;
							due.setStyle('color', '#999');
						}
						due.addEvent('click', function(e) {					
							if(due.value == defaultdue) {
								due.value = '';
								due.setStyle('color', '#666');
							}
						});
					}
					
					// Add submit div
					var submitdiv =  new Element('div', {
						'class': 'submittodo'
					}).injectInside(item);
					
					// Add a submit button
					new Element('input', {
						'type': 'submit',
						'value': 'save'
					}).injectInside(submitdiv);
					
					// Add a cancel button
					new Element('input', {
						'type': 'button',
						'value': 'cancel',
						'events': {
							'click': function(evt){
								(new Event(evt)).stop();
								item.empty();
								item.innerHTML = original;
								if(op && op.hasClass('hidden')) {	
									op.removeClass('hidden');
								}
								item.parentNode.parentNode.removeClass('edited');
							}
						}
					}).injectInside(submitdiv);										
				}); // end on event				
			}); // end for each			
		} // end if to-do items exist
		
		// Calendar (item page)
		if($('td-item') && $('dued')) {
			var myCal = new Calendar({ dued : 'm/d/Y' }, { classes: ['todocal'], direction: 1 });
			$('dued').removeEvents();
			$('dued').removeProperty('readonly');
			if($('dued').value == '') {
				$('dued').value = defaultdue;
				$('dued').setStyle('color', '#999');
			}
			$('dued').addEvent('click', function(e) {					
				if($('dued').value == defaultdue) {
					$('dued').value = '';
				}
			});
		}
		
		// Sort list
		if($$('.sortoption').length > 0) {	
			$$('.sortoption').each(function(item) {	
				item.addEvent('change', function(e) {
					var action = 'index.php?option=com_projects&id='+ frm.id.value + '&task=view&list='+frm.list.value + '&sortby=' + item.value + '&state=' + frm.state.value + '&active=todo&ajax=1&no_html=1';
					new Ajax(action, {
						method : 'get',
						update : $('plg-content'),
						onComplete: function(response) { 
							HUB.ProjectTodo.initialize();
						}
					}).request();
				});	
			});
		}
		
		// Show list delete confirmation
		if($$('.dellist').length > 0) {	
			$$('.dellist').each(function(item) {	
				item.addEvent('click', function(e) {
					e = new Event(e).stop();
					var color = item.getProperty('id').replace('del-','');
					var ops = $('confirm-' + color);
					if(ops) {
						ops.setStyle('display', 'block');
					}
					var cnl = $('cnl-' + color);
					if(cnl) {
						cnl.addEvent('click', function(ee) {
							ee = new Event(ee).stop();
							ops.setStyle('display', 'none');
						});	
					}
				});	
			});			
		}
		
		// Set dragging on pins
		if($$('.pin').length > 0) {	
			$ini = 0;
			$$('.pin').each(function(item) {
			 if(!$('td-item')) { // only main view	
				item.style.cursor = 'move';			
				item.addEvent('mousedown', function(e) {
					e = new Event(e).stop();
					
					// Get properties of selected item
					var todo = this.parentNode.parentNode;
					var parentid = todo.id;
					var oldid = parentid.replace('todo-','');
					var coord = $(todo).getCoordinates();
					
					// Set some vars for ajax redirection
					var assigned 	= '';
					var list 		= '';
					var mine 		= '';
					var state 		= '';
					
					// Clone selected todo
					var clone = todo.clone([true, false]).inject(todo, 'before');
					clone.setStyles(coord); // this returns an object with left/top/bottom/right, so its perfect
					clone.setStyles({'opacity': 0.5, 'position': 'absolute', 'z-index': '1001'});
					clone.setStyles({'margin-top': '-145px', 'margin-left': '-15px' });
									
					// Clean up cloned todo
					if(clone.hasClass('droptarget')) {
						clone.removeClass('droptarget');
					}
					clone.setProperty('id', 'clone-'+ parentid);
					
					// Get element classes
					var classes = todo.getProperty('class').split(" ");
					for (i=classes.length-1;i>=0;i--) {
						if (classes[i].contains('tdassigned:')) {
							var assigned = classes[i].split(":")[1];
						}
						/* 
						if (classes[i].contains('tdlist:')) {
							var list = classes[i].split(":")[1];
						}
						if (classes[i].contains('tdmine:')) {
							var mine = classes[i].split(":")[1];
						}
						if (classes[i].contains('tdstate:')) {
							var state = classes[i].split(":")[1];
						}*/
					}
					
					// Re-order
					for (i = 0; i < $$('.droptarget').length; i++) {	
						var ind = $$('.droptarget')[i].getProperty('id');
						$(ind).removeEvents();
						$(ind).addEvents({
							'drop': function() {
								clone.remove();	
								this.removeEvents();
								var dropid = this.getProperty('id');							
								var newid = dropid.replace('todo-','');
								if(dropid == 'todo-completed') {
									// completed
									var action = 'index.php?option=com_projects&id='+ frm.id.value + '&task=view&list='+frm.list.value + '&state=1&active=todo&ajax=1&action=changestate&no_html=1&todoid='+oldid;
								}
								else if(dropid == 'todo-mine') {
									var action = 'index.php?option=com_projects&id='+ frm.id.value + '&task=view&list='+frm.list.value + '&mine=1&active=todo&ajax=1&action=assign&no_html=1&todoid='+oldid;
								}							
								else {							
									var action = 'index.php?option=com_projects&id='+ frm.id.value + '&task=view&list='+frm.list.value + '&mine='+ frm.mine.value + '&state='+ frm.state.value + '&active=todo&ajax=1&action=reorder&no_html=1&newid='+newid +'&oldid='+oldid;
								}
								// Call AJAX
								if((newid && newid != oldid) || (dropid == 'todo-mine' && assigned != frm.id.value) || (dropid == 'todo-completed') ) {
									new Ajax(action, {
										method : 'get',
										update : $('plg-content'),
										onComplete: function(response) { 
											HUB.ProjectTodo.initialize();
										}
									}).request();
								}
							},
							'over': function() {
								var dropid = this.getProperty('id');	
								var newid = dropid.replace('todo-','');
								var oldid = parentid.replace('todo-','');
								if(dropid == 'todo-completed') {
									var dropFx = $('todo-completed').getElement('div').effect('background-color', {wait: false});
									dropFx.start('b6faa7');
								}
								else if(dropid == 'todo-mine') {
									var dropFx = $('todo-mine').getElement('div').effect('background-color', {wait: false});
									dropFx.start('fde7ae');
								}
								else if(newid != oldid) {
									var dropFx = this.effect('background-color', {wait: false});
									dropFx.start('fde7ae');
								}
							},
							'leave': function() {
								var dropid = this.getProperty('id');	
								var newid = dropid.replace('todo-','');
								var oldid = parentid.replace('todo-','');
								if(dropid == 'todo-completed') {
									var dropFx = $('todo-completed').getElement('div').effect('background-color', {wait: false});
									dropFx.start('e6fde1');
								}
								else if(dropid == 'todo-mine') {
									var dropFx = $('todo-mine').getElement('div').effect('background-color', {wait: false});
									dropFx.start('fffbf1');
								}
								else if(newid != oldid) {
									var dropFx = this.effect('background-color', {wait: false});
									dropFx.start('ffffff');
								}
							}
						});
					}
					
					var dropx = new Array();
					for (i = 0; i < $$('.droptarget').length; i++) {	
						var ind = $$('.droptarget')[i].getProperty('id');
						dropx.push(ind);				
					}					
					
					var drag = clone.makeDraggable({
						droppables: dropx
					}); // this returns the dragged element
					//container: container

					drag.start(e); // start the event manual
					
					clone.addEvent('emptydrop', function(){
						clone.remove();
					});
										
				}); // end on event
			  }
			}); // end for each			
		} // end if to-do items exist
		
		// Comment form
		var commentarea = $$('.commentarea');
		var default_comment = 'Write your comment';
		if(commentarea.length > 0) {			
			commentarea.each(function(item) {	
				$(item).addEvent('keyup', function(e) {					
					HUB.ProjectTodo.setCounter($(item) );
				});

				if(item.value=='') {
					item.value = default_comment;
					item.setStyle('color', '#999');
					item.setStyle('height', '20px');
				}
				item.addEvent('focus', function(e) {
					// Clear default value
					if(item.value == default_comment)	 {
						item.value = '';
						item.setStyle('color', '#000');
						item.setStyle('height', '60px');
					}				
				});	
			});
		}		
	},
	
	_setHandles: function(w) {
		var v = w ? 'move' : 'auto';
		var handles = document.getElementsByClassName('handle');
		if(handles.length > 0) {
			for (var i=0; i < handles.length; i++)
			{
				handles[i].style.cursor = v;
			}
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
	},
	
	addConfirm: function (link, puid) {		
		if($('confirm-box')) {
			$('confirm-box').remove();	
		}

			// Add confirmation
		var confirm =  new Element('div', {
			'class': 'confirmaction'
		}).inject(puid, 'before');
		confirm.setProperty('id', 'confirm-box');
		confirm.style.display = 'block';
		
		var href = link.href;
		var what =  href.contains('comment') ? ' comment' : ' todo item';

		var p = new Element('p');
		p.injectInside(confirm);
		p.innerHTML = 'Permanently delete this' + what + '?';

		var p2 = new Element('p');
		p2.injectInside(confirm);

		var a1 = new Element('a', {
			'href': link.href,
			'class': 'confirm'
		}).injectInside(p2);
		a1.innerHTML = 'delete';

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
		a2.innerHTML = 'cancel';
		
		// Move close to item
		var coord = link.getCoordinates();		
		var myFx = new Fx.Scroll(window).scrollTo(coord['left'], (coord['top'] - 200));
		$('confirm-box').setStyles({'left': coord['left'], 'top': coord['top'] });
	},
	
	getInternetExplorerVersion: function () {	
		// Returns the version of Internet Explorer or a -1
		// (indicating the use of another browser).

	  var rv = -1; // Return value assumes failure.
	  if (navigator.appName == 'Microsoft Internet Explorer')
	  {
	    var ua = navigator.userAgent;
	    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	    if (re.exec(ua) != null)
	      rv = parseFloat( RegExp.$1 );
	  }
	  return rv;
	}
}
	
window.addEvent('domready', HUB.ProjectTodo.initialize);