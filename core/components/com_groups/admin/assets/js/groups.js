Joomla.submitbutton = function(task) {
	var cfrm = document.getElementById('component-form');

	if (cfrm) {
		Joomla.submitform(task, cfrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		$(document).trigger('editorSave');

		if (task == 'markscanned') {
			if (!confirm(frm.getAttribute('data-confirm'))) {
				return false;
			}
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$("#toolbar-unblock a").on('click', function(e){
		e.preventDefault();

		if (document.adminForm.boxchecked.value==0){
			alert('Please first make a selection from the list');
		}else{
			var serialized = '';
			$('input[type=checkbox]').each(function() {
				if (this.checked) {
					serialized += '&'+this.name+'='+this.value;
				}
			});
			if (serialized) {
				$.fancybox({
					arrows: false,
					type: 'iframe',
					autoSize: false,
					width: 400,
					height: 400,
					fitToView: true,
					href: $(this).attr('href') + serialized
				});
			}
		}
	});

	$("#btn-save").on('click', function(e){
		Joomla.submitbutton('save');
	});

	$("#btn-cancel").on('click', function(e){
		Joomla.submitbutton('cancel');
	});
});
