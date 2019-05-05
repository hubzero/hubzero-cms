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

function citeaddRow(id) {
	var tr    = $('#' + id).find('tbody tr:last');
	var clone = tr.clone(true);
	var cindex = $('#' + id).find('tbody tr').length;
	var inputs = clone.find('input,select');

	inputs.val('');
	inputs.each(function(i, el){
		$(el).attr('name', $(el).attr('name').replace(/\[\d+\]/, '[' + cindex + ']'));
	});
	tr.after(clone);
};

jQuery(document).ready(function($){
	$('#add_row').on('click', function(e){
		e.preventDefault();

		citeaddRow('assocs');
		return false;
	});

	var formatSelector = $('#format-selector'),
		formatBox = $('#format-string');

	if (formatSelector.length && formatBox.length) {
		//when we change format box
		formatSelector.on('change', function(event) {
			var value  = $(this).val(),
				format = $(this).find(':selected').attr('data-format');
			formatBox.val(format);
		});

		//when we customize the format
		formatBox.on('keyup', function(event) {
			var customOption = formatSelector.find('option[value=custom]');
			customOption.attr('data-format', formatBox.val());
		});

		$('#preformatted tr').on('click', function(e) {
			$('#format-string').val($('#format-string').val() + $(this).attr('id'));
			$('#format-string').focus();
		});
	}
});
