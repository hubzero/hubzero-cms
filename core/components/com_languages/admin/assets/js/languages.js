Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$('#jform_searchstring').on('focus', function() {
		if (!Joomla.overrider.states.refreshed) {
			if ($(this).attr('data-cache_expired')) {
				Joomla.overrider.refreshCache();
				Joomla.overrider.states.refreshed = true;
			}
		}
		$(this).removeClass('invalid');
	});

	$('#searchstrings').on('click', function(e) {
		e.preventDefault();

		Joomla.overrider.searchStrings();
		return false;
	});
});
