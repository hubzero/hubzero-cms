/**
 * @package     hubzero-cms
 * @file        components/com_feedback/assets/js/quotes.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$.each($('.quote-long'), function(index, value){
		$(value).css('display', 'none');
	});

	$.each($('.quote-short'), function(index, value){
		$(value).css('display', 'inline-block');
	});

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
		$('#' + q + '-long')[0].style.display = 'inline-block';
		$('#' + q + '-short')[0].style.display = 'none';
	}
});
