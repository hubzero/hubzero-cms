/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-------------------------------------------------------------
// Add an event to close the notice
//-------------------------------------------------------------
jQuery(document).ready(function($){

	$('body').addClass('has-eprivacy-warning');

	$('.mod_eprivacy-close').on('click', function(e) {
		e.preventDefault();

		var id = $($(this).parent().parent()).attr('id'),
			days = $(this).attr('data-duration');

		$($(this).parent().parent()).hide();

		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));

		document.cookie = id + '=acknowledged; expires=' + date.toGMTString() + ';';

		$('body').removeClass('has-eprivacy-warning');
	});

});