/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel') {
			Hubzero.submitform(task, frm);
			return;
		}

		if (task == 'delete') {
			frm.admin_action.value = 'delete';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'suspend') {
			frm.admin_action.value = 'suspend';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'reinstate') {
			form.admin_action.value = 'reinstate';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	var password = $('#newpass'),
		passrule = $('#passrules');

	if (password.length > 0 && passrule.length > 0) {
		password.on('keyup', function(){
			// Create an ajax call to check the potential password
			$.ajax({
				url: password.attr('data-href'), //"/api/members/checkpass",
				type: "POST",
				data: "password1=" + password.val() + "&" + password.attr('data-values'),
				dataType: "json",
				cache: false,
				success: function(json) {
					if (json.html.length > 0 && password.val() !== '') {
						passrule.html(json.html);
					} else {
						// Probably deleted password, so reset classes
						passrule.find('li').switchClass('error passed', 'empty', 200);
					}
				}
			});
		});
	}

	$('#do-delete').on('click', function (e) {
		e.preventDefault();

		Hubzero.submitbutton('delete');
	});

	$('#do-unarchive').on('click', function (e) {
		e.preventDefault();

		Hubzero.submitbutton('unarchive');
	});

	$('#do-archive').on('click', function (e) {
		e.preventDefault();

		Hubzero.submitbutton('archive');
	});

	$('#do-reinstate').on('click', function (e) {
		e.preventDefault();

		Hubzero.submitbutton('reinstate');
	});

	$('#do-suspend').on('click', function (e) {
		e.preventDefault();

		Hubzero.submitbutton('suspend');
	});
});
