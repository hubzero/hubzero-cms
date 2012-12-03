/**
 * @package     hubzero-cms
 * @file        plugins/groups/blog/blog.js
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
HUB.Plugins.GroupsBlog = {
	initialize: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 500, y: 375} });
			}

			// Create a "login" button
			el = new Element('a', {
				href: '/login',
				title: 'Login',
				id: 'login-button'
			}).appendText('Login').addClass('pane-prev').injectTop($('hubForm'));
				
			// Add the event
			el.addEvent('click', function(e) {
				new Event(e).stop();

				SqueezeBoxHub.fromElement('login-btn',{
					handler: 'url', 
					size: {x: 500, y: 375}, 
					ajaxOptions: {
						method: 'get',
						onComplete: function() {
							frm = $('hubForm-ajax');
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
				}); // end SqueezeBoxHub
			}); // end addEvent
		} // end if
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.GroupsBlog.initialize);
