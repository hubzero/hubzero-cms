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
		var submenu = $('sub-menu');
		if (submenu) {
			$$('.tab').each(function(href) {
				href.onclick = function() { 
						var section = this.rel + '-section';
						$$('.main').each(function(sect) {
							if (sect.id == section && sect.hasClass('hide')) {
								sect.removeClass('hide');
								this.parentNode.addClass('active');
							} else {
								if (!sect.hasClass('hide')) {
									sect.addClass('hide');
								}
							}
						}, this);

						$$('.tab').each(function(h) {
							var hs = h.rel + '-section';
							if (hs != section && h.parentNode.hasClass('active')) {
								h.parentNode.removeClass('active');
							}
						});
						return false;
					}
			});
		}
	}
}

window.addEvent('domready', HUB.Members.initialize);
