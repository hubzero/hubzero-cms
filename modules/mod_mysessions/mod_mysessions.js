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

//-------------------------------------------------------------
// My sessions module
//-------------------------------------------------------------

HUB.Mod_MySessions = {
	initialize: function() {
		HUB.Mod_MySessions.diskMonitor();
	},

	diskMonitor: function() {
		if ($('diskusage')) {
			function fetch(){			
				new Ajax('index.php?option=com_mw&task=diskusage&no_html=1&msgs=1',{
						 'method' : 'get',
						 'update' : $('diskusage')
						 }).request();
			}

			fetch.periodical(60000);
		}
	}
}

//-------------------------------------------------------------
// Add functions to load event
//-------------------------------------------------------------

window.addEvent('domready', HUB.Mod_MySessions.initialize);
