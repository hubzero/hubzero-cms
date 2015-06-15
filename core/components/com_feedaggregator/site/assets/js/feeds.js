/**
 * @package     hubzero-cms
 * @file        components/com_feedaggregator/assets/js/feeds.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('.required-field').on('blur', function(){
		if ($(this).val() == '') {
			$(this).attr('style', 'background-color: #FFD6CC;');
		} else {
			$(this).attr('style', 'background-color: #FFFF;');
		}
	});

	$('#submitBtn').on('click', function (e) {
		e.preventDefault();

		var submitToken = 0,
			numItems = $('.required-field').length;

		$('.required-field').each(function() {
			if ($(this).val() == '') {
				$(this).attr('style', 'background-color: #FFD6CC;');
			} else if ($(this).val() != '') {
				submitToken = submitToken + 1;
			}
		});

		if (submitToken == numItems) {
			$('#hubForm').submit();
		} else {
			alert("Please check all required fields.");
		}
	});
}); 
