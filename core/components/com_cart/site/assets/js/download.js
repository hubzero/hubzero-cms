/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(document).ready(function() {

	var cartRedirectUrl = $('#cartRedirectUrl').attr('href');
	console.log(cartRedirectUrl);

	setTimeout(function() {
		window.location = cartRedirectUrl;
	}, 2000);

});
