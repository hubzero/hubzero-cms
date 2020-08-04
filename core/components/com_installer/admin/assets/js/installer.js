/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function validURL(str) {
	var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
		'((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
		'(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
		'(\\#[-a-z\\d_]*)?$','i'); // fragment locator
	return !!pattern.test(str);
}

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			if (task != 'cancel') {
				if (document.getElementById('field-alias').value == 'hz-installer'
				 || document.getElementById('field-alias').value == 'packagist.org') {
					alert(frm.getAttribute('data-invalid-msg'));
					return;
				}

				if (!validURL(document.getElementById('field-url').value)) {
					alert(frm.getAttribute('data-invalid-msg'));
					return;
				}
			}

			Hubzero.submitform(task, frm);
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
		Hubzero.submitbutton('save');
	});

	$("#btn-cancel").on('click', function(e){
		Hubzero.submitbutton('cancel');
	});
});
