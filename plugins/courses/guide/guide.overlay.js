/**
 * @package     hubzero-cms
 * @file        plugins/courses/guide/guide.overlay.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function(jq){
	var $ = jq;
	
	$.fancybox.open(
		[{
			href: '#guide-overlay'
		}],
		{
			type: 'inline',
			width: '100%',
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			closeBtn: false,
			closeClick: true,
			topRatio: 0,
			tpl: {
				wrap:'<div class="fancybox-wrap" id="guide-content"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>'
			},
			afterShow: function() {
				var guide = $('div.guide-instructions');
				if (guide.length > 0) {
					var m = guide.css('margin-top');
					console.log(m);
					if (m != '0px') {
						guide.css('margin-bottom', ($('#page_container').offset().top - 340) + 'px');
					}
				}
				$('div.fancybox-wrap').css('position', 'absolute');
			},
			beforeClose: function() {
				$.get($('#guide-overlay').attr('data-action').nohtml(), {}, function(response){
					// Nothing to see here
					//console.log(response);
				});
			}
		}
	);

	$(window).resize(function() {
		var guide = $('div.guide-instructions');
		if (guide.length > 0) {
			var m = guide.css('margin-top');
			if (m != '0px') {
				guide.css('margin-bottom', ($('#page_container').offset().top - 340) + 'px');
			}
		}
	});
});