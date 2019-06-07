/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('#sign-up-submit').on('click', function(event) {
		var email = $(this).parents('form').find('#email'),
			filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		//check to make sure we have an email address and its valid
		if (email.val() == '' || !filter.test(email.val())) {
			event.preventDefault();
			email.focus();
			alert(email.attr('data-invalid'));
		}
	});
});