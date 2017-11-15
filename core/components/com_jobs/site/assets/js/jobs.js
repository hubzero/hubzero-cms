/**
 * @package     hubzero-cms
 * @file        components/com_jobs/jobs.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Answers Scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Jobs = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		var default_tagline = 'Why should I be hired? (optional but highly recommended)',
			default_lookingfor = 'Briefly describe your career goals (optional but highly recommended)';

		// cancel confirm form	
		if ($('#showconfirm') && $('.confirmwrap')) {
			$('#showconfirm').on('click', function(e) {
				e.preventDefault();
				$('.confirmwrap').show();
			});
		}

		// Date picker
		if ($('#startdate').length > 0) {
			$('#startdate').datepicker({
				dateFormat: 'yy-mm-dd 00:00:00'
			});
		}

		if ($('#closedate').length > 0) {
			$('#closedate').datepicker({
				dateFormat: 'yy-mm-dd 00:00:00'
			});
		}

		if ($('#expiredate').length > 0) {
			$('#expiredate').datepicker({
				dateFormat: 'yy-mm-dd 00:00:00'
			});
		}

		// subscription form
		if ($('#subForm')) {
			var frm = $('#hubForm');

			// show details of selected option
			var services = $('.service');

			if (services.length) {
				var sel = 0;
				services.each(function(i, item) {
					if (item.attr('checked')) {
						$('#plan_'+ $(item).val()).show();
					}
					else {
						$('#plan_'+ $(item).val()).hide();
					}

					$('#units_' + $(item).val()).change(function() {
						var unitprice = $('#price_' + $(item).val()).val();
						var newprice = unitprice * $('#units_' + $(item).val()).val();
						$('#injecttotal_' + $(item).val()).html(newprice.toFixed(2));
					});	

					$(item).click(function(){
						HUB.Jobs.hideDetails();
						$('#plan_'+ $(item).val()).show();
					});
				});
			}
			
			// display total price calculation
			$('.totalprice').each(function(i, item) {
				$(item).show();
			});
		}

		// save to shortlist
		$(".favvit").each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();
				var oid = $($(item).parent()).attr('id').replace('o','');
				var profilebox = $($(item).parent().parent().parent());
				
				$.get('index.php?option=com_jobs&task=plugin&trigger=onMembersShortlist&no_html=1&oid='+oid,{}, function(data){
					$(profilebox).html(data);
				});
			});
		});

		// editing seeker info
		var tgmen = $('#tagline-men');
		if (tgmen.length) {
			HUB.Jobs.setCounter(tgmen, $('#counter_number_tagline'));

			if (tgmen.val() == '') {
				tgmen
					.val(default_tagline)
					.css('color', '#999');
			}

			tgmen
				.on('click', function(e) {
					// Clear default value
					if ($(this).val() == default_tagline) {
						$(this)
							.val('')
							.css('color', '#000');
					}
				})
				.on('keyup', function(e) {
					HUB.Jobs.setCounter(tgmen, $('#counter_number_tagline'));
				});
		}

		var lfmen = $('#lookingfor-men');
		if (lfmen.length) {
			HUB.Jobs.setCounter(lfmen, $('#counter_number_lookingfor') );

			if (lfmen.val() == '') {
				lfmen
					.val(default_lookingfor)
					.css('color', '#999');
			}

			lfmen.on('click', function(e) {
				// Clear default value
				if (lfmen.val() == default_lookingfor) {
					lfmen
						.val('')
						.css('color', '#000');
				}
			});	

			lfmen.on('keyup', function(e) {
				HUB.Jobs.setCounter(lfmen, $('#counter_number_lookingfor') );
			});
		}


		// submit form - cleanup default values
		if ($('#prefsForm').length) {
			$('#prefsForm').on('submit', function(){
				if (lfmen.val() == '' || lfmen.val() == default_lookingfor) {
					lfmen.val('');
				}
				if (tgmen.val() =='' || tgmen.val() == default_tagline) {
					tgmen.val('');
				}
			});	
		}

		// show chars counter
		$('.counter').each(function(i, elm) {
			$(elm).show();
		});
	},

	hideDetails: function() {
		var $ = this.jQuery;
		
		$('.subdetails').each(function(i, item) {
			$(item).hide();
		});
	},

	setCounter: function(el, numel) {
		var $ = this.jQuery;
		
		if (!$(el).val()) {
			return;
		}
		var maxchars = 140;
		var current_length = $(el).val().length;
		var remaining_chars = maxchars-current_length;
		if (remaining_chars < 0) {
			remaining_chars = 0;
		}
		$(numel).html(remaining_chars);
		
		if (remaining_chars <= 10){
			$(numel.parent()).css('color', '#CC0000');
		} else {
			$(numel.parent()).css('color', '#999999');
		}
		
		if (remaining_chars == 0) {
			$(el).val($(el).val().substr(0,maxchars));
		}
	}
}

jQuery(document).ready(function($){
	HUB.Jobs.initialize();
});
