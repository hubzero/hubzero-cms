Joomla.submitbutton = function(task, type = '') {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Joomla.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		$(document).trigger('editorSave');
		if (task == 'cancel' || task == 'cancelemail' || document.formvalidator.isValid(frm)) {
			Joomla.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

jQuery(document).ready(function($){
	$('#btn-batch-submit')
		.on('click', function (e){
			Joomla.submitbutton('user.batch');
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
