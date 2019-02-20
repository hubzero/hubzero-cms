
Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('hubForm');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$('#btn-save').on('click', function (e){
		Joomla.submitbutton('save');
	});
	$('#btn-cancel').on('click', function (e){
		Joomla.submitbutton('cancel');
	});
});
