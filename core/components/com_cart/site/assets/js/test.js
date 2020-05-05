/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(document).ready(function() {

	$('#ajax').click(function() {
		//var submitTo = '/cart/request/add';
		submitTo = '/api/courses/premisRegister';

		$.ajaxSetup({ cache: false });
		$.post(submitTo, $("#frm").serialize(), function(data) {
			if (data.status && data.status != 'error') {

			} else {

			}

		}, "json");

		return false;
	});

});
