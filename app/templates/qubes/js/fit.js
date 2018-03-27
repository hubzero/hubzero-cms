$(window).load(function() {	
	var $container = $('.explore .inner');
	// initialize
	
	$container.masonry({
	  columnWidth: ".expoblock",
	  itemSelector: '.expoblock'
	});
	
	$('.expoblock').on('mouseenter', function(e) {
		$(this).addClass('over');
	});
	$('.expoblock').on('mouseleave', function(e) {
		$(this).removeClass('over');
	});
	
	$('body').removeClass('nop');
});