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
if (!jq) {
	var jq = $;
}

HUB.Usage = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;
		
		$('.fixedImgTip').tooltip({
			position:'TOP RIGHT',
			//offset: [10,-20],
			onBeforeShow: function(event, position) {
				var tip = this.getTip(),
					tipText = tip[0].innerHTML;

				if (tipText.indexOf(" :: ") != -1) {
					var parts = tipText.split(" :: ");
					tip[0].innerHTML = "<span class=\"tooltip-title\">"+parts[0]+"</span><span>"+parts[1]+"</span>";
				}
			}
		}).dynamic({ bottom: { direction: 'down' }, right: { direction: 'left' } });
	},

	cutup: function(p, h) {
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
		var cLinks = document.getElementsByClassName(cls);
		if (cLinks) {
			for (var k=0; k < cLinks.length; k++)
			{
				if ($(cLinks[k]).hasClass('displaying')) {
					$(cLinks[k]).removeClass('displaying');
				}
				if (k == 0 && pv == 1) {
					if (!$(cLinks[k]).hasClass('displaying')) {
						$(cLinks[k]).addClass('displaying');
					}
				}
			}
		}
	},
	
	loadOFC: function() {
		var $ = this.jQuery;
		
		p = $('#period').val();
		swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart", "600", "350", "9.0.0", "expressInstall.swf", {"data-file":"/usage/chart/"+p+"/?no_html=1"});
	}
}

jQuery(document).ready(function($){
	HUB.Usage.initialize();
});

