if (typeof(Joomla) == 'undefined')
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
jQuery(document).ready(function($){
	var menu = $('#toolbar-box'),
		top = menu.offset().top - parseFloat(menu.css('margin-top').replace(/auto/, 0));

	$(window).on('scroll', function(event) {
		// what the y position of the scroll is
		var y = $(window).scrollTop();
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
	if ((navigator.userAgent.match(/iPhone/i))
	 || (navigator.userAgent.match(/iPod/i))
	 || (navigator.userAgent.match(/iPad/i))) {
	 	// we just need to attach a click event listener to provoke iPhone/iPod/iPad's hover event
		$('.main-navigation li.node').on('click', function(){ });
	}
});
