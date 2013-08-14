if (!Joomla)
{
	Joomla = {};
}
Joomla.submitbutton = function(pressbutton)
{
	return submitbutton(pressbutton);
}
Joomla.submitform = function(pressbutton)
{
	return submitform(pressbutton);
}

//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
var HUB = HUB || {};

window.addEvent('domready', function(){
	var menu = $('toolbar-box'),
		top = menu.getTop() - parseFloat(menu.getStyle('margin-top').replace(/auto/, 0));

	$(window).addEvent('scroll', function(event) {
		// what the y position of the scroll is
		var y = $(window).getScrollTop();
		// whether that's below the form
		if (y >= top) {
			// if so, add the fixed class
			menu.addClass('fixed');
		} else {
			// otherwise remove it
			menu.removeClass('fixed');
		}
	});

	// ipad and iphone fix
	if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
		$$('.main-navigation li.node').each(function(el){
			el.addEvent('click', function(){ });  // we just need to attach a click event listener to provoke iPhone/iPod/iPad's hover event
		});
	}
});