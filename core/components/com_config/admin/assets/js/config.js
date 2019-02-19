
Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frma = document.getElementById('application-form');

	if (frma) {
		if (task == 'application.cancel' || document.formvalidator.isValid(frma)) {
			Joomla.submitform(task, frma);
		} else {
			alert(frma.getAttribute('data-invalid-msg'));
		}
	}

	var frmc = document.getElementById('component-form');

	if (frmc) {
		if (task == 'cancel' || document.formvalidator.isValid(frmc)) {
			Joomla.submitform(task, frmc);
		} else {
			alert(frmc.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	var frmc = $('#component-form');

	if (frmc.length) {
		$('#btn-apply').on('click', function(e){
			Joomla.submitform('component.apply', frmc);
		});

		$('#btn-save').on('click', function(e){
			Joomla.submitform('component.save', frmc);
		});

		$('#btn-cancel').on('click', function(e){
			if ($(this).attr('data-refresh')) {
				window.parent.location.href = window.parent.location.href;
			}
			window.parent.$.fancybox.close();
		});
	}
});
