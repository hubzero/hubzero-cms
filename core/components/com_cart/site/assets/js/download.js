/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(document).ready(function() {

	var cartRedirectUrl = $('#cartRedirectUrl').attr('href');
	console.log(cartRedirectUrl);

	setTimeout(function() {
		window.location = cartRedirectUrl;
	}, 2000);

});
