/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

jQuery(document).ready(function($){
	$("#reset_hits,#reset_votes").on('click', function(e){
		e.preventDefault();

		if (confirm($(this).attr('data-confirm'))) {
			// The reset tasks read the article ID from "id" (the form only
			// carries "fields[id]"); the form already includes the CSRF token.
			var frm = document.getElementById('item-form');

			if (frm) {
				$('<input type="hidden" name="id" />')
					.val($('#field-id').val())
					.appendTo(frm);

				Hubzero.submitform(this.id == 'reset_hits' ? 'resethits' : 'resetvotes', frm);
			}
		}

		return false;
	});
});
