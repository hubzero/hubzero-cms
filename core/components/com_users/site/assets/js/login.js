/**
 * @package     hubzero-cms
 * @file        components/com_user/site/assets/js/login.js
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
//  User scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.User = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery,
			login_button = $('.login-submit'),
			username     = $('.username'),
			password     = $('.passwd'),
			error        = $('.auth .input-error'),
			inputs       = $('.input-wrap'),
			loading      = $('.spinner'),
			attempts     = 0;

		$('input:checkbox').uniform();

		$('#username, #password').placeholder();

		$('.local').click(function ( e ) {

		});

		inputs.on('keyup', function(event) {
			if(error.html() !== '' && event.keyCode != '13') {
				$('.input-wrap').removeClass('input-wrap-error');
				error.slideUp('fast');
				login_button.attr('disabled', false);
				login_button.fadeTo('fast', '1');
				loading.hide();
			}
			$(this).fadeTo('fast', '1');
		});

		login_button.on('click', function(event) {
			event.preventDefault();

			$(this).attr('disabled', true);
			$(this).fadeTo('fast', '.5');
			loading.show();


			// Grab the form
			var form = $(this).parents("form");

			// Ajax request
			$.ajax({
				type: 'POST',
				url: form.attr("action")+"?no_html=1",
				data: form.serialize(),
				success: function(data, status, xhr)
				{
					var response = {};
					try {
						// Parse the returned json data
						response = jQuery.parseJSON(data);
					} catch (err) {
						console.log(err);
						password.val('');
						password.focus();
						error.html('Sorry. Something went wrong. Please try logging in again.');
						error.slideDown('fast');
						loading.hide();
						attempts++;

						if (attempts >= 3) {
							HUB.User.clearCookies();
						}
					}

					// If all went well
					if(response.success)
					{
						window.location.href = response.redirect;
					}
					// If there were errors
					else if(response.error)
					{
						password.val('');
						password.focus();
						$('.input-wrap').addClass('input-wrap-error');
						error.html(response.error);
						error.slideDown('fast');
						loading.hide();
					}
				},
				error: function(xhr, status, error)
				{
					console.log("An error occured while trying to login.");
					// Probably related to an expired session, reload the page to try and clear the problem up
					window.location.reload();
				},
				complete: function(xhr, status) {}
			});
		});
	},

	clearCookies: function() {
		var $       = this.jQuery;
		var cookies = document.cookie.split(";");

		for (i=0; i < cookies.length; i++) {
			var cookie = cookies[i];
			var eqPos  = cookie.indexOf("=");
			var name   = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
			if (name.length == 33) {
				document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
			}
		}

		window.location.reload();
	}
};

jQuery(document).ready(function($){
	HUB.User.initialize();
});
