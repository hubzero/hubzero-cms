//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
var HUB = {};

HUB.Base = {
	initialize: function() {
		/*$$('img').each(function(trigger) {
			if (trigger.getProperty('src') == 'images/tick.png' || trigger.getProperty('src') == 'images/publish_g.png') {
				$(trigger.parentNode).addClass('published');
			}
			if (trigger.getProperty('src') == 'images/publish_r.png') {
				$(trigger.parentNode).addClass('expired');
			}
			if (trigger.getProperty('src') == 'images/publish_y.png') {
				$(trigger.parentNode).addClass('pending');
			}
			if (trigger.getProperty('src') == 'images/publish_x.png') {
				$(trigger.parentNode).addClass('unpublished');
			}
			if (trigger.getProperty('src') == 'images/disabled.png') {
				$(trigger.parentNode).addClass('archived');
			}
			if (trigger.getProperty('src') == 'images/uparrow.png') {
				$(trigger.parentNode).addClass('order-up');
			}
			if (trigger.getProperty('src') == 'images/downarrow.png') {
				$(trigger.parentNode).addClass('order-down');
			}
		});*/
		var msie6 = false; //$.browser == 'msie' && $.browser.version < 7;

		if (!msie6) {
			var menu = $('toolbar-box');
			var top = menu.getTop() - parseFloat(menu.getStyle('margin-top').replace(/auto/, 0));
			$(window).addEvent('scroll', function(event) {
				// what the y position of the scroll is
				var y = $(window).getScrollTop();
				// whether that's below the form
				if (y >= top) {
					// if so, ad the fixed class
					if (!$('cloned-toolbar-box')) {
						var menu2 = menu.clone().setStyles({
							'position':'fixed',
							'top': '0px',
							'z-index': 500,
							'width': menu.getSize().size.x
						}).setProperty('id', 'cloned-toolbar-box');
						menu2.inject(menu, 'after');
					}
				} else {
					// otherwise remove it
					if ($('cloned-toolbar-box')) {
						var removed = $('cloned-toolbar-box').remove();
					}
				}
		    });
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Base.initialize);