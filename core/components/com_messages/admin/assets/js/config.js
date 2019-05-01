/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('config-form');

	if (frm) {
		if (task == 'cancel' || task == 'config.cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#action_save").on('click', function(e){
		e.preventDefault();

		Hubzero.submitform('save', this.form);
		window.top.setTimeout('window.parent.$.fancybox.close()', 1400);
	});

	$("#action_cancel").on('click', function(e){
		e.preventDefault();

		window.parent.$.fancybox.close();
	});
});
