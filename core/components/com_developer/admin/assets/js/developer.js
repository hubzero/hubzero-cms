/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		if (task == 'resetclientsecret') {
			if (confirm(afrm.getAttribute('data-confirmreset'))) {
				Hubzero.submitform(task, afrm);
			}
			return;
		}

		if (task == 'removetokens') {
			if (confirm(afrm.getAttribute('data-confirmrevoke'))) {
				Hubzero.submitform(task, afrm);
			}
			return;
		}

		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}
