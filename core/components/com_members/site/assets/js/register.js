/**
 * @package     hubzero-cms
 * @file        components/com_register/register.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.Register = {
	disableIndie: function() {
		var $ = jq;

		$('#type-indie').attr('checked', false);
		$('#username').attr('disabled', false);
		$('#passwd').attr('disabled', false);
	},

	disableDomains: function() {
		var $ = jq;

		$('#username').val('');
		$('#username').attr('disabled', true);
		$('#passwd').val('');
		$('#passwd').attr('disabled', true);
		$('#type-indie').attr('checked', true);
		$('.option').each(function(i, input) {
			var name = $(input).attr('name');
			var value = $(input).val();
			if (name == 'domain' && value != '') {
				if ($(input).attr('checked')) {
					$(input).attr('checked', false);
				}
			}
		});
	},

	checkLogin: function() {
		var $ = jq,
			submitTo = $('#base_uri').val() + '/members/register/checkusername?userlogin=' + $('#userlogin').val(),
			usernameStatus = $('#usernameStatus');

		$.getJSON(submitTo, function(data) {
			usernameStatus.html(data.message);
			usernameStatus.removeClass('ok');
			usernameStatus.removeClass('notok');
			if (data.status == 'ok') {
				usernameStatus.addClass('ok');
			} else {
				usernameStatus.addClass('notok');
			}
		});
	}
}

jQuery(document).ready(function($){
	var $ = jq,
		w = 760,
		h = 520;

	$('.com_register a.popup').each(function(i, trigger) {
		href = $(this).attr('href');
		if (href.indexOf('?') == -1) {
			href += '?tmpl=component';
		} else {
			href += '&tmpl=component';
		}
		$(this).attr('href', href);
	});

	// Look for the "type-linked" element
	var typeindie = $('#type-indie');
	if (typeindie.length) {
		// Found it - means we're on the initial registration
		// form where users choose a linked account or not
		$('#username').attr('disabled', true);
		$('#passwd').attr('disabled', true);
		$('.option').each(function(i, input) {
			var name = $(input).attr('name');
			var value = $(input).val();
			var checked = $(input).attr('checked');
			if (name == 'domain' && value != '') {
				$(input).on('click', HUB.Register.disableIndie);

				if (checked == 'checked') {
					$('#username').attr('disabled', false);
					$('#passwd').attr('disabled', false);
				}
			}
		});
		$(typeindie).on('click', HUB.Register.disableDomains);
	}

	var userlogin = $('#userlogin');
	var usernameStatusAfter = $('#userlogin');
	var passwd = $('#password');
	var passrule = $('#passrules');

	if (passwd.length > 0 && passrule.length > 0) {
		passwd.on('keyup', function(){
			// Create an ajax call to check the potential password
			$.ajax({
				url: "/api/members/checkpass",
				type: "POST",
				data: "password1="+passwd.val(),
				dataType: "json",
				cache: false,
				success: function(json) {
					if (json.html.length > 0 && passwd.val() !== '') {
						passrule.html(json.html);
					} else {
						// Probably deleted password, so reset classes
						passrule.find('li').switchClass('error passed', 'empty', 200);
					}
				}
			});
		});
	}

	if (userlogin.length > 0) {
		usernameStatusAfter.after('<p class="hint" id="usernameStatus">&nbsp;</p>');

		userlogin.focusout(function(obj) {
			var timer = setTimeout('HUB.Register.checkLogin()',200);
		});
	}
});
