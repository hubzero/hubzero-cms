Joomla.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (!frm) {
		frm = document.getElementById('component-form');
	}

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

/**
 * Toggles the check state of a group of boxes
 *
 * Checkboxes must have an id attribute in the form cb0, cb1...
 * @param The number of box to 'check'
 * @param An alternative field name
 */
function checkAllOptions()
{
	var f = document.adminForm;
	var c = f.toggleOpt.checked;

	$('.chk').each(function(i, el){
		el.checked = c;
	});
}

jQuery(document).ready(function($){
	$('#toggleOpt').on('change', function(e){
		checkAllOptions();
	});

	$('#newacl').on('click', function(e){
		Joomla.submitbutton('save');
	});

	var col = $('#field-color');

	if (col.length) {
		col.colpick({
			layout: 'hex',
			colorScheme: 'dark',
			submit: 1,
			onSubmit: function(hsb,hex,rgb,el) {
				col.val(hex);
			}
		});
	}

	$('#btn-apply,#btn-save').on('click', function(e){
		Joomla.submitbutton($(this).attr('data-task'));
	});
	$('#btn-cancel').on('click', function(e){
		if ($(this).attr('data-refresh') == 'true') {
			window.parent.location.href = window.parent.location.href;
		}
		window.parent.$.fancybox.close();
	});
});
