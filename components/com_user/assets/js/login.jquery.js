/**
 * @package     hubzero-cms
 * @file        /components/com_user/assets/js/login.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		var $ = this.jQuery;
		var login_button = $('#login-submit');
		var username     = $('#username');
		var password     = $('#password');
		var error        = $('#authentication .error');
		var hcredentials = $('#credentials-hub');

		$('input:checkbox').uniform();

		hcredentials.on('keyup', function(event) {
			if(error.html() != '' && event.keyCode != '13') {
				error.slideUp('fast', function(){});
				login_button.attr('disabled', false);
				login_button.fadeTo('fast', '1');
			}
			$(this).fadeTo('fast', '1');
		});

		login_button.on('click', function(event) {
			event.preventDefault();

			$(this).attr('disabled', true);
			$(this).fadeTo('fast', '.5');

			// Grab the form
			var form = $(this).parents("form");

			// Ajax request
			$.ajax({
				type: 'POST',
				url: form.attr("action")+"?no_html=1",
				data: form.serialize(),
				success: function(data, status, xhr)
				{
					// Parse the returned json data
					var response = jQuery.parseJSON(data);

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
						error.html(response.error.message);
						error.slideDown('fast', function(){});
					}
				},
				error: function(xhr, status, error)
				{
					console.log("An error occured while trying to login.");
				},
				complete: function(xhr, status) {}
			});
		});
	}
};

jQuery(document).ready(function($){
	HUB.User.initialize();
});