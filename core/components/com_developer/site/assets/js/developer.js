/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('.confirm').on('click', function (e) {
		var res = confirm($(this).attr('data-txt-confirm'));
		if (!res) {
			e.preventDefault();
		}
		return res;
	});

	if ($('.toc').length)
	{
		// syntax highlight
		if (typeof(hljs) !== 'undefined')
		{
			hljs.configure({tabReplace: '    '});
			hljs.initHighlightingOnLoad();
		}

		// scroll toc with window scroll
		$(window).scroll(function() {
			var scrollTop = $(document).scrollTop(),
				threshold = 20,
				min = $('.docs .grid').first().offset().top,
				max = $('.docs .grid').first().offset().top + $('.docs .grid').first().outerHeight(true) - $('.toc').outerHeight(true);

			if (scrollTop > min && scrollTop < max)
			{
				$('.toc').css({
					top: scrollTop - min + threshold
				});
			}
		});

		// do we need to open a specific pane?
		var active = 0;
		var hash = window.location.hash.replace('#', '');
		if (hash)
		{
			var parts   = hash.split('-');
			var section = parts[0];
			var header  = $('.toc-header[data-section="'+section+'"');
			active      = parseInt(header.attr('data-index'))
		}

		// table of contents accordion
		$('.toc').accordion({
			header: '.toc-header:not(.label,.divider)',
			heightStyle: "content",
			collapsible: true,
			active: active
		});

		// smooth scroll toc links
		$('.toc a').on('click', function(event) {
			event.preventDefault();

			// get anchor
			var anchor  = $(this).attr('href');
			var element = $(anchor);
			if (!$(anchor).length)
			{
				var element = $('.endpoint[id="' + anchor.replace('#', '') + '"]');
			}

			// scroll to anchor point
			$('html, body').animate({
				scrollTop: element.offset().top - 20
			}, 500, function(){
				// update location hash
				window.location.hash = anchor;
			});
		});
	}
});
