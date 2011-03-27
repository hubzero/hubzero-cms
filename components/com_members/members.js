/**
 * @package     hubzero-cms
 * @file        components/com_members/members.js
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
//  Members scripts
//----------------------------------------------------------

HUB.Members = {
	initialize: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 300, y: 375} });
			}
			
			// Modal boxes for presentations
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
						size: {x: 300, y: 375}, 
						ajaxOptions: {
							method: 'get',
							onComplete: function() {
								frm = $('hubForm-ajax');
								if (frm) {
									frm.addEvent('submit', function(e) {
										new Event(e).stop();
										frm.send({
											//update: $('sbox-content'),
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
		}
	}
}

window.addEvent('domready', HUB.Members.initialize);

