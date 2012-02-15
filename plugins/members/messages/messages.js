/**
 * @package     hubzero-cms
 * @file        plugins/members/messages/messages.js
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
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.MembersMsg = {
	checkAll: function( ele, clsName ) {
		if (ele.checked) {
			var val = true;
		} else {
			var val = false;
		}
		
		$$('input.'+clsName).each(function(el) {
			if (el.checked) {
				el.checked = val;
			} else {
				el.checked = val;
			}
		});
	},
	
	initalize: function() 
	{
		// Modal boxes for presentations
		
		$$('a.message-link').each(function(el) {
			if (el.href.indexOf('?') == -1) {
				el.href = el.href + '?no_html=1';
			} else {
				el.href = el.href + '&no_html=1';
			}
			el.addEvent('click', function(e) {
				new Event(e).stop();

				SqueezeBoxHub.fromElement(el,{
					handler: 'url', 
					size: {x: 700, y: 400}, 
					ajaxOptions: {
						method: 'get',
						onComplete: function() {
							
						}
					}
				});
			});
		});
	
		//------
		
		$$('#message-toolbar a.new').each(function(el) {
			if (el.href.indexOf('?') == -1) {
				el.href = el.href + '?no_html=1';
			} else {
				el.href = el.href + '&no_html=1';
			}
			el.addEvent('click', function(e) {
				new Event(e).stop();

				SqueezeBoxHub.fromElement(el,{
					handler: 'url', 
					size: {x: 700, y: 418}, 
					ajaxOptions: {
						method: 'get',
						evalScripts: true,
						onComplete: function() {
							frm = $('hubForm-ajax');
							if (frm) {
								frm.addEvent('submit', function(e) {
									new Event(e).stop();
									members = $('members').getValue();
									message = $('msg-message').getValue();
									
									if(!members) {
										alert("Must select a message recipient.");
										return false;
									}
									
									if(!message) {
										alert("You must enter a message.");
										return false;
									} 
									
									frm.send({
										onComplete: function() {
											SqueezeBoxHub.close();
											Growl.Bezel({
												image: '/components/com_members/images/mail.png',
												title: 'Message Sent',
												text: 'Your Message was successfully sent to ' + to + '.',
												duration: 2
											});
										}
							        });
								});
							}
						}
					}
				});
			});
		});
		
		//-----
		
	}
}

//--------

window.addEvent("domready",HUB.MembersMsg.initalize);
