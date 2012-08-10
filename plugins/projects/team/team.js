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
// Project Team JS
//----------------------------------------------------------

HUB.ProjectTeam = {

	initialize: function() 
	{				
		var manage = $('team-manage');
		var ops = $$('.manage');
		if(manage) {
			if(manage.hasClass('hidden')) {
				manage.removeClass('hidden');
			}
			var boxes = $$('.checkmember');
			var bchecked = 0;
			var bselected = new Array();
			var bgroups = new Array();
						
			// Edit role inline
			HUB.RoleEditing = {
				initialize: function() {
					new eipa($$('.frole'), 'index.php', {option: 'com_projects', task: 'view', active: 'team', action: 'assignrole', ajax: 1, no_html: 1});
				}
			}
			window.addEvent('domready', HUB.RoleEditing.initialize);
						
			// Toggle checkboxes
			var toggle = $('toggle');
			if(toggle) {
				toggle.addEvent('click', function(e) {
					if(boxes.length > 0) {
						boxes.each(function(item) {
						 	if(toggle.checked == true) {
								if(item.checked == false) { 
									item.checked = true; 
									bchecked = bchecked + 1;
									bselected.push(item.value);
								}
							 	else { 
									item.checked = true;
								}
							}
							else {
								if(item.checked == true) { 
									item.checked = false;
									bchecked = bchecked - 1;
								 	var idx = bselected.indexOf(item.value);
									if(idx!=-1) bselected.splice(idx, 1);
								}								
							}
						});
					}
					HUB.ProjectTeam.watchSelections(bchecked);
				});				
			}
			
			// Check boxes for team members
			if(boxes.length > 0) {
				var group = '';
				boxes.each(function(item) {	
					item.addEvent('click', function(e) {							
						// Is item checked?
						if(item.checked == false) {		
							bchecked = bchecked - 1;						
						 	var idx = bselected.indexOf(item.value);
							if(idx!=-1) bselected.splice(idx, 1);
						}
						else {
							bchecked = bchecked + 1;
							bselected.push(item.value);							
						}
						HUB.ProjectTeam.watchSelections(bchecked);
					});
					
					// Check whole group
					item.addEvent('click', function(e) {
						var classes = item.getProperty('class').split(" ");
						for (i=classes.length-1;i>=0;i--) {
							if (classes[i].contains('group:')) {
								var group = classes[i].split(":")[1];
							} 
						}
						var cls = 'group:'+ group;
						if(group) {
							if(item.checked == true) {
								bgroups.push(group);
							}
							else {
								var idx = bgroups.indexOf(group);
								if(idx!=-1) bgroups.splice(idx, 1);
							}
							
							if(boxes.length > 0) {
								boxes.each(function(i) {
									if(i.hasClass(cls)) {
								 		if(item.checked == true) {
											if(i.checked == false) { 
												i.checked = true; 
												bchecked = bchecked + 1;
												bselected.push(i.value);
											}
										 	else { 
												i.checked = true;
											}
										}
										else {
											if(i.checked == true) { 
												i.checked = false;
												bchecked = bchecked - 1;
											 	var idx = bselected.indexOf(i.value);
												if(idx!=-1) bselected.splice(idx, 1);
											}								
										}
									}
								});
							}
						}
						HUB.ProjectTeam.watchSelections(bchecked);
					});	
	  												  
				});
			}
			
			// Control options
			ops.each(function(item) {	
				// disable options until a box is checked
				item.addClass('inactive');
				item.addEvent('click', function(e) {
					new Event(e).stop();
					var aid = item.getProperty('id');
					
					if (item.hasClass('inactive')) {
						// do nothing
					}
					else {	
						// Clean up url
						var clean = item.href.split('&owner[]=', 1);
						item.href = clean;
					
						// Add our checked boxes variables
						if(bselected.length > 0) {
							for (var k = 0; k < bselected.length; k++) {
								item.href = item.href + '&owner[]=' + bselected[k];
							}
						}
						// Add our selected groups variables
						if(bgroups.length > 0) {
							for (var k = 0; k < bgroups.length; k++) {
								item.href = item.href + '&group[]=' + bgroups[k];
							}
						}
						item.href = item.href + '&no_html=1&ajax=1';
					
						// make AJAX call
						if (!SqueezeBoxHub) {
							SqueezeBoxHub.initialize({ size: {x: 400, y: 500} });
						}
					
						// Modal box for actions
						SqueezeBoxHub.fromElement(item,{
							handler: 'url', 
							size: {x: 400, y: 500}, 
							ajaxOptions: {
								method: 'get',
								onComplete: function() {
									if($('cancel-action')) {
										$('cancel-action').addEvent('click', function(e) {
											SqueezeBoxHub.close();
										});
									}
									/*
									frm = $('hubForm-ajax');
									if (frm) {
										frm.addEvent('submit', function(e) {
											new Event(e).stop();
											frm.send();
										});
									}*/
								}
							}
						});
					}
				});								  												  
			});

		} // end if manage
		
		// Autocompleter for group names
		var elg = $('newgroup');
		if (elg) {
			var completerGroups = new Autocompleter.Ajax.Json(elg, 'index.php?option=com_projects&no_html=1&task=autocomplete&which=group', {
				'minLength': 1, // We wait for at least one character
				'overflow': true, // Overflow for more entries
				'wrapSelectionsWithSpacesInQuotes': false,
				'multiple': true,
				'injectChoice': function(choice) {
					var el = new Element('li').setHTML(this.markQueryValue(choice[0]));
					el.inputValue = choice[1];
					this.addChoiceEvents(el).injectInside(this.choices);
				}
			});
		} 

		// Autocompleter for member names
		var elm = $('newmember');
		if (elm) {
			var completerMember = new Autocompleter.MultiSelectable.Ajax.Json(elm, 'index.php?option=com_projects&no_html=1&task=autocomplete&which=user', {
				'minLength': 1, // We wait for at least one character
				'overflow': true, // Overflow for more entries
				'wrapSelectionsWithSpacesInQuotes': false,
				'multiple': true,
				'tagger': null,
				'injectChoice': function(choice) {
					var el = new Element('li').setHTML(this.markQueryValue(choice[0]));
					el.inputValue = choice[1];
					this.addChoiceEvents(el).injectInside(this.choices);
				}
			});
		}
		
	}, // end initialize
	
	watchSelections: function (bchecked ) {
		var ops = $$('.manage');
		var num = $('n_members').value;
		if(bchecked == 0) {
			ops.each(function(i) {
				if(!i.hasClass('inactive')) {	
					i.addClass('inactive');
				}	
			});
		}
		else if(bchecked == num) { 
			// cannot delete all members
			if($('t-delete') && !$('t-delete').hasClass('inactive')) {
				$('t-delete').addClass('inactive');
			}
			if($('t-role') && $('t-role').hasClass('inactive')) {
				$('t-role').removeClass('inactive');
			}			
		}
		else if(bchecked < num) {
			if($('t-delete') && $('t-delete').hasClass('inactive')) {
				$('t-delete').removeClass('inactive');
			}
			if($('t-role') && $('t-role').hasClass('inactive')) {
				$('t-role').removeClass('inactive');
			}				
		}
	}
}

//---------

var eipa = new Class({

	initialize: function(els, action, params, options) {
		// Handle array of elements or single element
		if ($type(els) == 'array') {
			els.each(function(el){
				this.prepForm(el);
			}.bind(this));
		} else if ($type(els) == 'element') {
			this.prepForm(els);
		} else {
			return;
		}

		// Store the action (path to file) and params
		this.action = action;
		this.params = params;

		// Default options
		this.options = Object.extend({
			overCl: 'over',
			hiddenCl: 'hidden',
			editableCl: 'editable',
			textareaCl: 'textarea'
		}, options || {} );
	},

	prepForm: function(el) {
		var obj = this;
		el.addEvents({
			'mouseover': function(){this.addClass(obj.options.overCl);},
			'mouseout': function(){this.removeClass(obj.options.overCl);},
			'click': function(){obj.showForm(this);}
		});

	},

	showForm: function(el) {
		// Get the name (target) and id from your element
		var classes = el.getProperty('class').split(" ");
		for (i=classes.length-1;i>=0;i--) {
			if (classes[i].contains('owner:')) {
				var owner = classes[i].split(":")[1];
			} else if (classes[i].contains('role:')) {
				var role = classes[i].split(":")[1];
			}
		}
			
		// Hide your target element
		el.addClass(this.options.hiddenCl);

		// If the form exists already, let's show that
		if (el.form) {
			el.form.removeClass(this.options.hiddenCl);
			return;
		}

		// Create new form
		var form = new Element('form', {
			'id': 'form_' + el.getProperty('id'),
			'action': this.action,
			'class': this.options.editableCl
		});

		// Store new form in the element
		el.form = form;
		
		var select = new Element('select',{
		         styles:{
		            position:'relative'
		         },
				'name': 'role'
		}).inject(form);
		
		var o_m = new Element('option', {
		            'value' : '1'
		}).injectInside(select);
		
		var o_c = new Element('option', {
		            'value' : '0'
		}).injectInside(select);
						
		o_m.innerHTML = 'manager';
		o_c.innerHTML = 'collaborator';
		
		if (role == 1) {
		    select.selectedIndex = 0;
		}
		else {
			select.selectedIndex = 1;
		}
		
		// Need this to pass to the buttons
		var obj = this;

		// Add a submit button
		new Element('input', {
			'type': 'submit',
			'value': 'save',
			'events': {
				'click': function(evt){
					(new Event(evt)).stop();
					el.empty();
					el.appendText('saving...');
					obj.hideForm(form, el);
					form.send({update: $('cbody'),
					onComplete: function(response) {
					    HUB.ProjectTeam.initialize();
					} 
					});
				}
			}
		}).injectInside(form);
		
		// Add a cancel button
		new Element('input', {
			'type': 'button',
			'value': 'x',
			'class': 'cancel',
			'events': {
				'click': function(form, el){
					obj.hideForm(form, el);
				}.pass([form, el])
			}
		}).injectInside(form);

		// For every param, add a hidden input
		for (param in this.params) {
			new Element('input', {
				'type': 'hidden',
				'name': param,
				'value': this.params[param]
			}).injectInside(form);
		}

		// Pass owner id
		new Element('input', {
			'type': 'hidden',
			'name': 'owner[]',
			'value': owner
		}).injectInside(form);
				
		// Add the form after the target element
		form.injectAfter(el);
	},

	hideForm: function(form, el) {
		form.addClass(this.options.hiddenCl);
		el.removeClass(this.options.hiddenCl);
	}
});
	
window.addEvent('domready', HUB.ProjectTeam.initialize);