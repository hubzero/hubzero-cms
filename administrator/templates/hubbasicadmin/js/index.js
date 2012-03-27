//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
if (!HUB){
	var HUB = {};
}

HUB.Base = {
	initialize: function() {
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
		
		//ipad and iphone fix
		if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
		    $$('.main-navigation li.node').each(function(el){
				el.addEvent('click', function(){
					
				});
		        //we just need to attach a click event listener to provoke iPhone/iPod/iPad's hover event
		    });
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Base.initialize);