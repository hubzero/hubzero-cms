Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#reset_hits,#reset_votes").on('click', function(e){
		e.preventDefault();

		if (confirm($(this).attr('data-confirm'))) {
			return Joomla.submitform(task, frm);
		}

		return false;
	});
});
