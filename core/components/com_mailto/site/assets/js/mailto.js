/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('mailtoForm');

	// do field validation
	if (frm.mailto.value == "" || form.from.value == "") {
		alert(frm.getAttribute('data-invalid-msg'));
		return false;
	}
	form.submit();
}

jQuery(document).ready(function($){
	$('#mailto_send').on('click', function(e) {
		e.preventDefault();

		return Hubzero.submitbutton('send');
	});

	$('.cancel').on('click', function(e) {
		e.preventDefault();

		window.close();
		return false;
	});
});
