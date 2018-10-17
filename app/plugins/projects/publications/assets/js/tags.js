jQuery(function($) {
	$('.js-only').css('display', 'inline');
	var nested_fa = $('.fa .fa');
	if (!nested_fa) {
		return;
	}
	nested_fa.css('display', 'none');
	var fas = $('.fa');

	var change = function(evt) {
		nested_fa.css('display', 'none');
		fas.each(function(idx, div) {
			if ($(div.firstChild).attr('checked')) {
				$(div).children().each(function(idx, el) {
					if ($(el).hasClass('fa')) {
						$(el).css('display', 'block');
					}
				});
			}
		});
		return;
	};
	fas.click(change);
	change();

	$('.suggested-tag').click(function(evt) {
		evt.stopPropagation();
		var text = $(evt.target).text();
		$('#actags').tokenInput('add', {id: text, name: text});
		return false;
	});
});
