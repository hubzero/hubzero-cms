
Joomla.submitbutton = function(task) {
	if (task == 'resetclientsecret') {
		var afrm = document.getElementById('adminForm');

		if (confirm(afrm.getAttribute('data-confirmreset'))) {
			Joomla.submitform(task, afrm);
		}
		return;
	}

	if (task == 'removetokens') {
		var afrm = document.getElementById('adminForm');

		if (confirm(afrm.getAttribute('data-confirmrevoke'))) {
			Joomla.submitform(task, afrm);
		}
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}
