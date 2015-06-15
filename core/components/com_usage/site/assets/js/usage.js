/**
 * @package     hubzero-cms
 * @file        components/com_usage/assets/js/usage.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.Usage = {
	cutup: function(p, h) {
		var pArray = p.split('.'),
			k = null;

		switch (h)
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
		var $ = jq,
			cLinks = document.getElementsByClassName(cls);

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
		var $ = jq;

		swfobject.embedSWF("/libraries/ofc/open-flash-chart.swf", "chart", "600", "350", "9.0.0", "expressInstall.swf", {
			"data-file":"/usage/chart/" + $('#period').val() + "/?no_html=1"
		});
	}
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	if (jQuery.ui && jQuery.ui.tooltip) {
		$('.fixedImgTip').tooltip({
			position: {
				my: 'center bottom',
				at: 'center top'
			},
			// When moving between hovering over many elements quickly, the tooltip will jump around
			// because it can't start animating the fade in of the new tip until the old tip is
			// done. Solution is to disable one of the animations.
			hide: false,
			create: function(event, ui) {
				var tip = $(this),
					tipText = tip.attr('title');

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip.attr('title', parts[1]);
				}
			},
			tooltipClass: 'tooltip'
		});
	}
});

