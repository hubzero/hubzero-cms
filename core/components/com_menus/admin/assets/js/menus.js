Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'items.setType' || task == 'items.setMenuType') {
			if (task == 'items.setType') {
				$('#item-form').find('input[name="fields[type]"]').val(type);
				$('#fieldtype').val('type');
			} else {
				$('#item-form').find('input[name="fields[menutype]"]').val(type);
			}
			Joomla.submitform('items.setType', frm);
		} else if (task == 'cancel' || task == 'items.cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			var invalids = $('#item-form .modal-value.invalid');

			if (invalids.length) {
				// special case for modal popups validation response
				$('#item-form .modal-value.invalid').each(function(i, field){
					var idReversed = field.id.split("").reverse().join("");
					var separatorLocation = idReversed.indexOf('_');
					var name = idReversed.substr(separatorLocation).split("").reverse().join("")+'name';
					$('#'+name).addClass('invalid');
				});
			} else {
				alert(frm.getAttribute('data-invalid-msg'));
			}
		}
	}
}

jQuery(document).ready(function($){
	$('#showmods').on('click', function(e) {
		$('.adminlist tr.nope').toggle();
	});
});
