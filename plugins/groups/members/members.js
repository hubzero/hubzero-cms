/**
 * @package     hubzero-cms
 * @file        plugins/groups/members/members.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.GroupsMembers = {
	initialize: function() {
		
		$$('.remove-role a').addEvent('click', function(e) {
			new Event(e).stop();
			var answer = confirm('Are you sure you want to delete this member role? It will also delete any associations members have with the role.');
			if(answer) { 
				window.location = this.href;
			}
		});
		
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 300, y: 375} });
			}
		
			
			$$('a.message').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBoxHub.fromElement(el,{
						handler: 'url', 
						size: {x: 300, y: 405}, 
						ajaxOptions: {
							method: 'get',
							onComplete: function() {
								frm = $('message-ajax');
								if (frm) {
									frm.addEvent('submit', function(e) {
										new Event(e).stop();
										frm.send({
											onComplete: function() {
												SqueezeBoxHub.close();
											}
								        });
									});
								}
							}
						}
					});
				});
			});
			//end message members pop up
			
			$$('a.assign-role').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBoxHub.fromElement(el,{
						handler: 'url', 
						size: {x: 300, y: 150}, 
						ajaxOptions: {
							method: 'get',
							onComplete: function() {
								frm = $('hubForm-ajax');
								uid = $('uid').value;
								if (frm) {
									frm.addEvent('submit', function(e) {
										new Event(e).stop();
										if($('role').value == '') {
											alert('You must select a member role.');
											return false;
										}
										frm.send({
											onComplete: function() {
												role = $('role').options[$('role').selectedIndex].text;
												old = $('roles-list-' + uid).innerHTML;
												if(old == '') {
													$('roles-list-' + uid).innerHTML = role;
												} else {
													$('roles-list-' + uid).innerHTML = old + ', ' + role;
												}
												SqueezeBoxHub.close();
											}
								        });
									});
								}
							}
						}
					});
				});
			});
			//end assign role pop ups
		}
		
	} //end initialize
}
//-----------
window.addEvent('domready', HUB.Plugins.GroupsMembers.initialize);
