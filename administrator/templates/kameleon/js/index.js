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

	$("select, input[type=file]").uniform();

	/*if ($('#item-form').length) {
		$('#item-form input').each(function(i, el){
			if ($(el).is(":focus")) {
			console.log(el);
				$(el).closest('div.input-wrap').addClass('focused');
			} else {
				$(el).closest('div.input-wrap').removeClass('focused');
			}
		});
	}*/

	var msg = $('#system-message-container');
	if (msg.length && msg.html().replace(/\s+/, '') != '') {
		msg
			.hide()
			.fancybox({
				type: 'html',
				modal: true,
				autoSize: true,
				titleShow: false,
				content: msg.html(),
				afterShow: function(){
					setTimeout(function(){
						$.fancybox.close();
					}, 1.5 * 1000);
				}
			})
			.eq(0)
			.trigger('click');
	}
});