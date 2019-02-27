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
	$('.jform-rightbtn').on('click', function(e){
		e.preventDefault();

		$('.chk-menulink').each(function(i, el) {
			el.checked = !el.checked;
		});
	});
});