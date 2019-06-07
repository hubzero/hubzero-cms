/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
