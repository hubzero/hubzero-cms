/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('component-form');

	if (frm) {
		$(document).trigger('editorSave');

		if (task == 'markscanned') {
			if (!confirm(frm.getAttribute('data-confirm'))) {
				return false;
			}
		}

		if (task == 'cancel' || frm.usernames.value != '') {
			Hubzero.submitform(task, frm);
			window.top.setTimeout("window.parent.location='" + frm.getAttribute('data-redirect') + "'", 700);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#btn-save").on('click', function(e){
		Hubzero.submitbutton('addusers');
	});

	$("#btn-cancel").on('click', function(e){
		window.parent.$.fancybox.close();
	});
});
