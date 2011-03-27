/**
 * @package     hubzero-cms
 * @file        components/com_xsearch/xsearch.js
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
// XSearch scripts
//----------------------------------------------------------
HUB.XSearch = {
	initialize: function() {
		$$('.category-header').each(function(hed) {
			var toks = hed.getAttribute('id').split('-');
			var pane = $(toks[1]);
			var fa = new Fx.Slider(pane, {duration: 800,onComplete: function(){}});
			hed.onclick = function() { 
					fa.toggle(); 
					if (this.hasClass('opened')) {
						this.removeClass('opened');
					} else {
						this.addClass('opened');
					}
				}
		});
	}
}

//----------------------------------------------------------

//window.addEvent('domready', HUB.XSearch.initialize);

