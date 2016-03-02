/**
 * @package     hubzero-cms
 * @file        modules/mod_reportproblems/mod_reportproblems.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {
		Modules: {}
	};
} else if (!HUB.Modules) {
	HUB.Modules = {};
}

//----------------------------------------------------------
// Trouble Report form
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Modules.ReportProblems = {

	initialize: function(trigger) {
		var pane = $('#help-pane'),
			trigger = $(trigger);

		if (!pane.length || !trigger.length) {
			return;
		}

		var frm = $(pane.find('form')[0]);

		trigger.fancybox({
			type: 'inline',
			href: '#help-pane',
			//width: 600,
			//height: 400,
			autoSize: true,
			fitToView: true,
			titleShow: false,
			arrows: false,
			closeBtn: true,
			afterLoad: function() {
				if (frm) {
					var loader = $('#trSending');

					$('<iframe src="about:blank" id="upload_target" name="upload_target" style="width:0px;height:0px;border:0px solid #fff;"></iframe>').appendTo(pane);

					loader.hide();

					frm
						.show()
						.attr('target', 'upload_target')
						.on('submit', function (e) {
							//e.preventDefault();

							var name   = $('#trName'),
								email  = $('#trEmail'),
								prob   = $('#trProblem'),
								upload = $('#trUpload'),
								errors = [],
								whiteSpace = /^[\s]+$/;

							$(this).find('p.error').remove();

							if (prob.val() == '' || whiteSpace.test(prob.val()) ) {
								errors.push("You're trying to send an empty trouble report. Please type something and try again.");
								prob.focus();
							}

							if (name.val() == '' || whiteSpace.test(name.val()) ) {
								errors.push("The 'name' field is required. Please type something and try again.");
								name.focus();
							}

							if (email.val() == '' || HUB.Modules.ReportProblems.validateEmail(email.val()) === false) {
								errors.push("Please provide a valid email address.");
								email.focus();
							}

							if (upload.val()) {
								var validExt = false,
									file = upload.val();

								for (var j = 0; j < _validFileExtensions.length; j++) {
									var sCurExtension = _validFileExtensions[j];
									if (file.substr(file.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
										validExt = true;
										break;
									}
								}

								if (!validExt) {
									errors.push("Invalid file extension.");
								}
							}

							if (errors.length > 0) {
								$('<p class="warning">' + errors.join('<br />') + '</p>').hide().prependTo(frm).fadeIn();
								return false;
							}

							var h = frm.height();

							frm.hide();

							loader
								.height(h)
								.show();

							return true;
						});
				}
			}
		});
	},

	hideTimer: function() {
		$('#trSending').hide();
		$('#trSuccess')
			.html(document.getElementById('upload_target').contentWindow.document.getElementById('report-response').innerHTML)
			.show();
	},

	resetForm: function() {
		$('#trProblem').val('');
		$('#trUpload').parent().html($('#trUpload').parent().html());
		$('#trSuccess').hide();

		var frm = $($('#help-pane').find('form'));

		frm.find('p.error').remove();
		frm.show();
	},

	validateEmail: function(emailStr) {
		var emailReg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/, // not valid
			emailReg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/; // valid
		if (!(!emailReg1.test(emailStr) && emailReg2.test(emailStr))) {
			return false;
		}
		return true;
	}
};

/*jQuery(document).ready(function(jq) {
	HUB.Modules.ReportProblems.initialize('#tab');
});*/
