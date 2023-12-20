/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
			frm.admin_action.value = 'reinstate';
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

$(function(){
	$("#param-grant_agency").autocomplete({	
		open: function() {
			$("ul.ui-menu").width( $(this).innerWidth() );
		},
		
		source: function(req, resp){
			var rorURL = "index.php?option=com_projects&controller=projects&task=getGrantAgency&term=";
			
			var terms = $("#param-grant_agency").val();
			
			if (terms.indexOf(" "))
			{
				rorURL = rorURL + terms.split(" ").join("+");
			}
			else
			{
				rorURL = rorURL + terms;
			}
			
			$.ajax({
				url: rorURL,
				data: null,
				dataType: "json",
				success:function(result){
					resp(result);
				},
				error:function(jqXHR, textStatus, errorThrown){
					console.log(textStatus);
					console.log(errorThrown);
					console.log(jqXHR.responseText);
				}
			});
		}
	});
});
