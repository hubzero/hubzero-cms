/**
 * @package     hubzero-cms
 * @file        components/com_feedback/assets/js/quotes.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.quote-long').css('display', 'none');
	$('.quote-short').css('display', 'inline-block');

	$('.show-more').on('click', function(e){
		e.preventDefault();

		$('#' + this.id + '-long').css('display', 'inline-block');
		$('#' + this.id + '-short').css('display', 'none');
	});

	$('.fancybox-inline').fancybox();

	if ($('#quoteid').length) {
		var q = $('#quote_id').val();

		$('body').animate({
			scrollTop: $('#' + q).offset().top
		}, 1000);
		$('#' + q + '-long').css('display', 'inline-block');
		$('#' + q + '-short').css('display', 'none');
	}
});
