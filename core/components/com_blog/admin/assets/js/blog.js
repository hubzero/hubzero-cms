
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

/*
document.addEventListener('DOMContentLoaded', function() {
	var frm = document.getElementById('item-form');

	if (frm) {
		frm.addEventListener('submit', function(event) {
			event.preventDefault();
			if (!document.formvalidator.isValid(frm)) {
				alert(frm.getAttribute('data-invalid-msg'));
				return false;
			}
			return true;
		});
	}
});
*/