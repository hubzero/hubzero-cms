/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $        = jq,
		passrule = $('#passrules'),
		password = $('#newpass'),
		passsave = $('#password-change-save');

	password.on('keyup', function(){
		// Create an ajax call to check the potential password
		$.ajax({
			url: "/api/members/checkpass",
			type: "POST",
			data: "password1="+password.val(),
			dataType: "json",
			cache: false,
			success: function(json) {
				if(json.html.length > 0 && password.val() !== '') {
					passrule.html(json.html);
				} else {
					// Probably deleted password, so reset classes
					passrule.find('li').switchClass('error passed', 'empty', 200);
				}
			}
		});
	});

	passsave.on('click', function(e){
		e.preventDefault();

		var form  = passsave.parents("form");
		var error = $('.error-message');

		// Set form to post with no_html true
		form.find('#pass_no_html').val('1');

		// Do the actual password save
		$.ajax({
			type: 'POST',
			url: form.attr("action"),
			data: form.serialize(),
			cache: false,
			success: function(data, status, xhr)
			{
				// Parse the returned json data
				var returned = JSON.parse(data);

				// If we successfully saved
				if (returned.success)
				{
					error.removeClass('error').addClass('passed');
					error.html('Password save successful!');
					error.slideDown('fast');
					error.delay(2000).slideUp('fast', function() {
						// Redirect if desired
						if (returned.redirect.length)
						{
							window.location.href = returned.redirect;
						}
						else
						{
							window.location.reload();
						}
					});
				}
				else
				{
					// Add error message
					$('input[name="password1"]').val('').focus();
					$('input[name="password2"]').val('');
					error.addClass('error');
					error.html(returned.message);
					error.slideDown('fast');
				}
			},
			error: function(xhr, status, error)
			{
				console.log("An error occurred while trying to save your password.");
				// Try reloading the page for good measure
				window.location.reload();
			}
		});
	});
});
