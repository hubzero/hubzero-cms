
Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}
