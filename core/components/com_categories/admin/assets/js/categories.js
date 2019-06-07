/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('adminForm');

	if (frm) {
		Hubzero.submitform(task, frm);
		return;
	}

	frm = document.getElementById('item-form');

	if (frm) {
		$(document).trigger('editorSave');

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function ($) {
	$('#btn-batch-submit')
		.on('click', function (e){
			return Hubzero.submitbutton('category.batch');
		});

	$('#btn-batch-clear')
		.on('click', function (e){
			e.preventDefault();
			$('#batch-category-id').val('');
			$('#batch-access').val('');
			$('#batch-language-id').val('');
		});
});
