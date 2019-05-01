/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(document).ready(function() {

	var cartRedirectUrl = $('#cartRedirectUrl').attr('href');
	console.log(cartRedirectUrl);

	setTimeout(function() {
		window.location = cartRedirectUrl;
	}, 2000);

});
