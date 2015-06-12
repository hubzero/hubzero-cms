(function($){
	$.fn.tabs = function() {
		this.each(function() {
			var el = $(this),
				current,
				dlHeight = el.height();

			el.addClass('enhance');

			var container = $('<div>').addClass('current');
			el.after(container);
			el.find('dd').hide().appendTo(container);

			var hash = location.hash;

			if (el.find('dt a[href="'+hash+'"]').length) {
				current = el.find('a[href="'+hash+'"]').parent().addClass('open');
			} else {
				current = el.find('dt:first').addClass('open');
			}

			var i = current.index();
			//console.log($(container.find('dd')[i]).height())
			$(container.find('dd')[i]).show();
			//var currentHeight = $(container.find('dd')[i]).show().height();
			//var currentHeight = current.next('dd').show().height();
			//el.css('height', dlHeight + currentHeight);
		});

		$('dl.enhance dt a').on('click', function(e){
			e.preventDefault();

			var parentsdl = $(this).parents('dl'),
				container = parentsdl.next();

			parentsdl.find('.open').removeClass('open'); //.next('dd').hide();

			var current = $(this).parent('dt').addClass('open'),
				i = current.index();
				//currentHeight = current.next('dd').show().height(),
				//dlHeight = parentsdl.removeAttr('style').height();

			$(container.find('dd')).hide();
			$(container.find('dd')[i]).show();

			//parentsdl.css('height', dlHeight + currentHeight);

			//location.hash = $(this).attr('href');

			return false;
		});
	}
})(jQuery);