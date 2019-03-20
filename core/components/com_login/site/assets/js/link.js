/**
 * @package     hubzero-cms
 * @file        components/com_user/assets/js/link.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		n = $('.next'),
		p = $('.previous');

	window.onpopstate = function (e) {
		if (e.state && e.state.step) {
			var pn = $('.prompt'+e.state.step);

			pn.addClass('incoming');
			$('.prompt-container').fadeOut();
			$('.prompt-wrap').animate({'height': pn.height()});
			pn.fadeIn(function (e) {
				$(this).removeClass('incoming');
			});
		} else {
			history.pushState({'step':1}, 'Account Setup', window.location.href);
		}
	};

	n.click(function (e) {
		e.preventDefault();

		var step = $(this).data('step'),
			next = step + 1,
			pc   = $('.prompt'+step),
			pn   = $('.prompt'+next);

		pn.addClass('incoming');
		pc.fadeOut();
		$('.prompt-wrap').animate({'height': pn.height()});
		pn.fadeIn(function (e) {
			$(this).removeClass('incoming');
		});

		history.pushState({'step':next}, 'Account Setup', $(this).parent().attr('href'));
	});

	p.click(function (e) {
		e.preventDefault();

		var step = $(this).data('step'),
			next = step - 1,
			pc   = $('.prompt'+step),
			pn   = $('.prompt'+next);

		pn.addClass('incoming');
		pc.fadeOut();
		$('.prompt-wrap').animate({'height': pn.height()});
		pn.fadeIn(function (e) {
			$(this).removeClass('incoming');
		});

		history.pushState({'step':next}, 'Account Setup', $(this).parent().attr('href'));
	});
});