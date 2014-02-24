/**
 * @package     hubzero-cms
 * @file        components/com_wishlist/wishlist.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// due date
	if ($('#nodue').length > 0) { 
		$('#nodue').on('click', function() {
			$('#hubForm').publish_up.val('');
		});
	}
	
	if ($('#publish_up').length > 0) {
		$('#publish_up').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: 0,
			maxDate: '+10Y'
		});
	}
	
	// show/hide plan area
	if ($('#section-plan').length && $('#part_plan').length) { 
		$('#part_plan').on('click', function() {
			if ($('#part_plan').hasClass('collapse')) {
				$('#part_plan').removeClass('collapse');
				$('#full_plan').css('display', "none");
				$('#part_plan').addClass('expand');
			} else {
				$('#part_plan').removeClass('expand');
				$('#full_plan').css('display', "block");
				$('#part_plan').addClass('collapse');
			}
			return false;
		});
	}

	$('a.reply').on('click', function (e) {
		e.preventDefault();

		var frm = $('#' + $(this).attr('data-rel'));

		if (frm.hasClass('hide')) {
			frm.removeClass('hide');
			$(this)
				.addClass('active')
				.text($(this).attr('data-txt-active'));
		} else {
			frm.addClass('hide');
			$(this)
				.removeClass('active')
				.text($(this).attr('data-txt-inactive'));
		}
	});
});

