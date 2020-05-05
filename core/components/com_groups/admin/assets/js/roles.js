/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('component-form');

	if (!frm) {
		frm = document.getElementById('item-form');
	}

	if (frm) {
		$(document).trigger('editorSave');

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);

			var redirect = frm.getAttribute('data-redirect');
			if (redirect) {
				window.top.setTimeout("window.parent.location='" + redirect + "'", 700);
			}
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#btn-save").on('click', function(e){
		Hubzero.submitbutton('delegate');
	});

	$("#btn-cancel").on('click', function(e){
		window.parent.$.fancybox.close();
	});
});
