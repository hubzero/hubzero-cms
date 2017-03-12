/**
 * @package     hubzero-cms
 * @file        components/com_members/assets/js/changepassword.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.MembersChangePassword = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		var passrule = $('#passrules');
		var password = $('#newpass');
		var passsave = $('#password-change-save');

		// Keep this disabled until API is more widely deployed
		password.on('keyup', function(){
			// Create an ajax call to check the potential password
			$.ajax({
				url: "/api/members/checkpass",
				type: "POST",
				data: {"password1": password.val()},
				dataType: "json",
				cache: false,
				success: function(json){
					if(json.html.length > 0 && password.val() !== '') {
						passrule.html(json.html);
					}
					else
					{
						// Probably deleted password, so reset classes
						passrule.find('li').switchClass('error passed', 'empty', 200);
					}
				}
			});
		});

		passsave.on('click', function(e){
			e.preventDefault();

			var form     = passsave.parents("form");
			var error    = $('#errors');
			var passrule = $('#passrules');

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
					var returned = jQuery.parseJSON(data);

					// If we successfully saved
					if(returned.success)
					{
						// Redirect if desired
						if(form.find('#pass_redirect').length) {
							window.location.href = returned.redirect;
						}

						// Remove errors and clear the fields
						error.removeClass('error').addClass('passed');
						error.html('Password save successful!');
						error.slideDown('fast');
						error.delay(2000).slideUp('fast', function(){
							$('#oldpass').val('');
							$('#newpass').val('');
							$('#newpass1').val('');
							$('#newpass2').val('');
							error.removeClass('passed').addClass('error');
							error.html('');
							passrule.find('li').switchClass('error passed', 'empty', 200);
						});

						window.location.reload();

					}
					else
					{
						// Add error message
						$('html, body').animate({'scrollTop':0}, 500);
						$('#oldpass').val('').focus();
						$('#newpass').val('');
						$('#newpass1').val('');
						$('#newpass2').val('');
						error.addClass('error');
						error.html(returned._missing.password);
						error.slideDown('fast');
					}
				},
				error: function(xhr, status, error)
				{
					console.log("An error occured while trying to save your password.");
					// Try reloading the page for good measure
					window.location.reload();
				}
			});
		});
	}
};

jQuery(document).ready(function($){
	HUB.MembersChangePassword.initialize();
});