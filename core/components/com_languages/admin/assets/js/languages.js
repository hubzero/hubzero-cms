/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$('#jform_searchstring').on('focus', function() {
		if (!Hubzero.overrider.states.refreshed) {
			if ($(this).attr('data-cache_expired')) {
				Hubzero.overrider.refreshCache();
				Hubzero.overrider.states.refreshed = true;
			}
		}
		$(this).removeClass('invalid');
	});

	$('#searchstrings').on('click', function(e) {
		e.preventDefault();

		Hubzero.overrider.searchStrings();
		return false;
	});
});
