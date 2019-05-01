/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

var Fields = {
	initialise: function() {
		$('#field-event').on('change', function(){
			var ev = $(this).val().replace('::', '--');

			$('fieldset.eventparams').each(function(i, el) {
				$(el).css('display', 'none');
			});

			if ($('#params-' + ev)) {
				$('#params-' + ev).css('display', 'block');
			}
		});

		$('#field-recurrence').on('change', function(){
			var min = '*',
				hour = '*',
				day = '*',
				month = '*',
				dow = '*',
				recurrence = $(this).val();

			switch (recurrence)
			{
				case '0 0 1 1 *':
					min = '0';
					hour = '0';
					day = '1';
					month = '1';
				break;
				case '0 0 1 * *':
					min = '0';
					hour = '0';
					day = '1';
				break;
				case '0 0 * * 0':
					min = '0';
					hour = '0';
					dow = '0';
				break;
				case '0 0 * * *':
					min = '0';
					hour = '0';
				break;
				case '0 * * * *':
					min = '0';
				break;
			}

			if (recurrence == 'custom') {
				if ($('#custom').hasClass('hide')) {
					$('#custom').removeClass('hide');
				}
			} else {
				if (!$('#custom').hasClass('hide')) {
					$('#custom').addClass('hide');
				}
			}

			$('#field-minute-c').val(min);
			$('#field-minute-s').val(min);
			$('#field-hour-c').val(hour);
			$('#field-hour-s').val(hour);
			$('#field-day-c').val(day);
			$('#field-day-s').val(day);
			$('#field-month-c').val(month);
			$('#field-month-s').val(month);
			$('#field-dayofweek-c').val(dow);
			$('#field-dayofweek-s').val(dow);
		});

		$('#field-minute-s').on('change', function(){
			$('#field-minute-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-minute-c').on('change', function(){
			$('#field-minute-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-hour-s').on('change', function(){
			$('#field-hour-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-hour-c').on('change', function(){
			$('#field-hour-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-day-s').on('change', function(){
			$('#field-day-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-day-c').on('change', function(){
			$('#field-day-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-month-s').on('change', function(){
			$('#field-month-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-month-c').on('change', function(){
			$('#field-month-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-dayofweek-s').on('change', function(){
			$('#field-dayofweek-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-dayofweek-c').on('change', function(){
			$('#field-dayofweek-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});
	}
}

jQuery(document).ready(function($){
	Fields.initialise();
});
