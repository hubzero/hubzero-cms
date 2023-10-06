/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task, type = '') {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		$(document).trigger('editorSave');
		if (task == 'cancel' || task == 'cancelemail' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$('#btn-batch-submit')
		.on('click', function (e){
			Hubzero.submitbutton('user.batch');
		});

	$('#btn-batch-clear')
		.on('click', function (e){
			e.preventDefault();
			$('#batch-group-id').val('');
		});

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

	$('#class_id').on('change', function (e) {
		//e.preventDefault();
		$.getJSON($(this).attr('data-href') + $(this).val(), {}, function (data) {
			$.each(data, function (key, val) {
				var item = $('#field-'+key);
				item.val(val);

				if (e.target.options[e.target.selectedIndex].text == 'custom') {
					item.prop("readonly", false);
				} else {
					item.prop("readonly", true);
				}
			});
		});
	});
});

// Organization Dropdown
$(function(){
	if ($(".rorApiAvailable")[0]){
		$("#profile_organization").autocomplete({
			source: function(req, resp){
				var rorURL = "index.php?option=com_members&controller=members&task=getOrganizations&term=";
				var terms = $("#profile_organization").val();

				if (terms.indexOf(" ")){
					rorURL = rorURL + terms.split(" ").join("+");
				} else {
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
	}
});