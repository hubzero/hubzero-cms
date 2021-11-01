/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function validURL(str) {
	var pattern = new RegExp('^(https)(:\/\/)(.+).git$');
	return !!pattern.test(str);
}

function NoChars(str) {
	var pattern = new RegExp('^(?!_)[_A-z0-9]*$');
	return !!pattern.test(str);
}

function validBranch(str) {
	var pattern = new RegExp('^(origin)(\/)[_A-z0-9]*$');
	return !!pattern.test(str);
}

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel') {
			Hubzero.submitform(task, frm);
		}
		else if (task != 'cancel')
		{
			document.formvalidator.isValid(frm);

			if (!validURL(document.getElementById('field-url').value)) {
				alert('Please enter a valid HTTPS GIT URL with a ".git" at the end.');
				return;
			}

			if (!NoChars(document.getElementById('field-name').value)) {
				alert('Please enter a valid Extension Name. This can be used as a Title of the Custom Extension to help you find it in the list. NO characters or spaces expected for underscores but not at the beginning of the string.');
				return;
			}

			if (!NoChars(document.getElementById('field-alias').value)) {
				alert('Please enter a valid Alias.  NO characters or spaces expected for underscores but not at the beginning of the string.');
				return;
			}

			if (!document.getElementById('field-type').value) {
				alert('Please select an Extension Type.');
				return;
			}

			if (document.getElementById('field-git_branch').value) {
				if (!validBranch(document.getElementById('field-git_branch').value)) {
					alert('Please enter a valid GIT Branch in the form of "origin/branchname". NO characters allowed. If nothing is entered "origin/master" is used.');
					return;
				}
			}
			Hubzero.submitform(task, frm);
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
