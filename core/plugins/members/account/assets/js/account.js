/**
 * @package     hubzero-cms
 * @file        plugins/members/account/account.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Members account
//----------------------------------------------------------
HUB.Plugins.MembersAccount = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		// Set variables
		var subsectioncontent = $('.sub-section-content');
		var subsectionheader  = $('.sub-section h4');
		var cancel            = $('.cancel');
		var passcancel        = $('#pass-cancel');
		var password1         = $('#password1');
		var newpass1          = $('#newpass1');
		var password2         = $('#password2');
		var passsave          = $('#password-change-save');


		// Expand the submit button on hover (not necessary, just fun...)
		if($.isFunction($().hoverIntent)){
			$('.auth .account.active').hoverIntent({
				over: function(){
					$(this).find('.account-id').slideDown('fast');
				},
				timeout: 500,
				interval: 300,
				out: function(){
					$(this).find('.account-id').slideUp('fast');
				}
			});
		} else {
			// Add hover to account group
			$('.auth .account.active').hover(function() {
				$(this).find('.account-id').show();
			}, function() {
				$(this).find('.account-id').hide();
			});
		}

		// Augment cancel button in password box
		passcancel.on('click', function(){
			$('#section-edit-errors').slideUp('fast');
			$('#passrules').find('li').switchClass('error passed', 'empty', 200);
		});

		// Set keyup event on password field to do validation
		password1.on('keyup', HUB.Plugins.MembersAccount.checkPass);
		newpass1.on('keyup', HUB.Plugins.MembersAccount.checkPass);

		// Set event to save password
		passsave.on('click', HUB.Plugins.MembersAccount.passSave);
	}, // end initialize

	checkPass: function() {
		var $ = HUB.Plugins.MembersAccount.jQuery;

		var passrule  = $('#passrules');
		var pass = $('#password1');
		if(pass.length == 0) {
			pass = $('#newpass1');
		}

		// Create an ajax call to check the potential password
		$.ajax({
			url: "index.php?option=com_members&task=myaccount&active=account&action=checkPass",
			type: "POST",
			data: "password1="+pass.val(),
			dataType: "html",
			cache: false,
			success: function(html){
				if(html.length > 0 && pass.val() != '') {
					passrule.html(html);
				}
				else
				{
					// Probably deleted password, so reset classes
					passrule.find('li').switchClass('error passed', 'empty', 200);
				}
			}
		});
	}, // end checkPass

	passSave: function(e) {
		e.preventDefault();
		var $ = HUB.Plugins.MembersAccount.jQuery;

		var passsave = $('#password-change-save');
		var form     = passsave.parents("form");
		var error    = $('#section-edit-errors');
		var passrule = $('#passrules');

		// Set form to post with no_html true
		form.find('#pass_no_html').val('1');

		// Do the actual password save
		$.ajax({
			type: 'POST',
			url: form.attr("action"),
			data: form.serialize(),
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
					error.delay(2000).slideUp('fast', function() {
					window.location.href = returned.redirect;
					});
				}
				else
				{
					// Add error message
					error.html(returned._missing.password);
					error.slideDown('fast');
				}
			},
			error: function(xhr, status, error)
			{
				console.log("An error occured while trying to save your password.");
			}
		});
	} // end passSave
}

jQuery(document).ready(function($){
	HUB.Plugins.MembersAccount.initialize();
});
