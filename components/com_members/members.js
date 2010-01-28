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
