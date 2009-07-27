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
// Usage stats
//----------------------------------------------------------
HUB.Usage = {
	initialize: function() {
		/*var r = false;
		var chLinks = document.getElementsByClassName('charthistory');
		if (chLinks) {
			for (var i=0; i < chLinks.length; i++)
			{
				chLinks[i].onclick = function() {
						document.getElementById('history').value = this.id;
						document.getElementById('largechart').src = this.href;
						UsageStats.deactivate('charthistory',0);
						UsageStats.deactivate('chart', 1);
						this.addClass('displaying');
						return false;
					}
			}
		}

		var cLinks = document.getElementsByClassName('chart');
		if (cLinks) {
			for (var k=0; k < cLinks.length; k++)
			{
				cLinks[k].onclick = function() {
						var h = $('history').value;
						var p = this.href;
						p = UsageStats.cutup(p,h);
						document.getElementById('largechart').src = p;
						UsageStats.deactivate('chart',0);
						this.addClass('displaying');
						return false;
					}
			}
		}*/
		// Init fixed position DOM: tooltips
		var iTTips = new MooTips($$('.fixedImgTip'), {
			showDelay: 500,			// Delay for 500 milliseconds
			maxTitleChars: 100,
			className: 'img',
			fixed: true,			// fixed in place; note tip mouseover does not hide tip
			offsets: {'x':20,'y':5} // offset by 100,100
		});
	},

	cutup: function(p, h) {
		var pArray = p.split('.');
		var k = null;
		switch(h)
		{
			case '1year': k = '-1'; break;
			case '2year': k = '-2'; break;
			case '0year': k = ''; break;
			default: k = ''; break;
		}
		var e = pArray.pop();
		pArray[pArray.length - 1] += k;
		pArray.push(e);
		return pArray.join('.');
	},

	deactivate: function(cls, pv) {
		var cLinks = document.getElementsByClassName(cls);
		if(cLinks) {
			for (var k=0; k < cLinks.length; k++)
			{
				if (cLinks[k].hasClass('displaying')) {
					cLinks[k].removeClass('displaying');
				}
				if(k == 0 && pv == 1) {
					if (!cLinks[k].hasClass('displaying')) {
						cLinks[k].addClass('displaying');
					}
				}
			}
		}
	},
	
	loadOFC: function() {
		p = $('period').value;
		swfobject.embedSWF("/plugins/xhub/xlibraries/ofc/open-flash-chart.swf", "chart", "600", "350", "9.0.0", "expressInstall.swf", {"data-file":"/usage/chart/"+p+"/?no_html=1"});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Usage.initialize);
