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
	var ownerassignees = new Array,
		ownerdata = $('#owner-data');

	if (ownerdata.length) {
		var cdata = JSON.parse(ownerdata.html());

		for (var i = 0; i < cdata.data.length; i++)
		{
			ownerassignees[i] = cdata.data[i];
		}
	}

	$('#field-wishlist').on('change', function(e){
		changeDynaList(
			'fieldassigned',
			ownerassignees,
			document.getElementById('field-wishlist').options[document.getElementById('field-wishlist').selectedIndex].value,
			0,
			0
		);
	});
});
