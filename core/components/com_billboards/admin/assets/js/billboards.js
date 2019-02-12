
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
	var styling_table  = $('#styling_table');

	if (styling_table.length) {
		var slider = styling_table.hide();

		$('#styling').on('click', function(e) {
			e.preventDefault();

			slider.slideToggle();
		});
	}
});
