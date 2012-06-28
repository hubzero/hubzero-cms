/**
 * @package     hubzero-cms
 * @file        components/com_usage/usage.js
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
// Usage stats
//----------------------------------------------------------
HUB.Usage = {
	initialize: function() {
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
		swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart", "600", "350", "9.0.0", "expressInstall.swf", {"data-file":"/usage/chart/"+p+"/?no_html=1"});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Usage.initialize);

