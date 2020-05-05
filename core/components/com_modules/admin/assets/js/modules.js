/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		$(document).trigger('editorSave');

		if (task == 'cancel' || task == 'module.cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
			if (self != top) {
				window.top.setTimeout('window.parent.$.fancybox().close()', 1000);
			}
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	if ($('#item-form').length) {
		validate();
		$('select').on('change', function(e){
			validate();
		});
	}

	$('#btn-batch-submit')
		.on('click', function (e){
			Hubzero.submitbutton('batch');
		});

	$('#btn-batch-clear')
		.on('click', function (e){
			e.preventDefault();
			$('#batch-position-id').val('');
			$('#batch-access').val('');
			$('#batch-language-id').val('');
		});

	if ($('#moduleorder').length) {
		data = $('#moduleorder');

		if (data.length) {
			modorders = JSON.parse(data.html());

			var html = '\n	<select name="' + modorders.name + '" id="' + modorders.id + '"' + modorders.attr + '>';
			var i = 0,
				key = modorders.originalPos,
				orig_key = modorders.originalPos,
				orig_val = modorders.originalOrder;
			for (x in modorders.orders) {
				if (modorders.orders[x][0] == key) {
					var selected = '';
					if ((orig_key == key && orig_val == modorders.orders[x][1])
					 || (i == 0 && orig_key != key)) {
						selected = 'selected="selected"';
					}
					html += '\n		<option value="' + modorders.orders[x][1] + '" ' + selected + '>' + modorders.orders[x][2] + '</option>';
				}
				i++;
			}
			html += '\n	</select>';

			$('#moduleorder').after(html);
		}
	}
});

function validate(){
	var value = $('#jform_assignment').val(),
		list  = $('#menu-assignment');

	if (value == '-' || value == '0') {
		$('.jform-assignments-button').each(function(i, el) {
			$(el).prop('disabled', true);
		});
		list.find('input').each(function(i, el){
			$(el).prop('disabled', true);
			if (value == '-'){
				$(el).prop('checked', false);
			} else {
				$(el).prop('checked', true);
			}
		});
	} else {
		$('.jform-assignments-button').each(function(i, el) {
			$(el).prop('disabled', false);
		});
		list.find('input').each(function(i, el){
			$(el).prop('disabled', false);
		});
	}
}
