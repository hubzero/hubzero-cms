Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('component-form');

	if (frm) {
		if (task == 'markscanned') {
			if (!confirm(frm.getAttribute('data-confirm'))) {
				return false;
			}
		}

		if (task == 'cancel' || frm.usernames.value != '') {
			Joomla.submitform(task, frm);
			window.top.setTimeout("window.parent.location='" + frm.getAttribute('data-redirect') + "'", 700);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#btn-save").on('click', function(e){
		Joomla.submitbutton('addusers');
	});

	$("#btn-cancel").on('click', function(e){
		window.parent.$.fancybox.close();
	});
});
