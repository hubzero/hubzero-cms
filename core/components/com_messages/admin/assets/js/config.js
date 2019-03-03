Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('config-form');

	if (frm) {
		if (task == 'cancel' || task == 'config.cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#action_save").on('click', function(e){
		e.preventDefault();

		Joomla.submitform('save', this.form);
		window.top.setTimeout('window.parent.$.fancybox.close()', 1400);
	});

	$("#action_cancel").on('click', function(e){
		e.preventDefault();

		window.parent.$.fancybox.close();
	});
});
