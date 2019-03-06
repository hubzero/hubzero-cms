
Joomla.submitbutton = function(task) {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		if (task == 'resetclientsecret') {
			if (confirm(afrm.getAttribute('data-confirmreset'))) {
				Joomla.submitform(task, afrm);
			}
			return;
		}

		if (task == 'removetokens') {
			if (confirm(afrm.getAttribute('data-confirmrevoke'))) {
				Joomla.submitform(task, afrm);
			}
			return;
		}

		Joomla.submitform(task, afrm);
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
