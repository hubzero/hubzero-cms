/**
 * @package     hubzero-cms
 * @file        administrator/modules/mod_login/assets/js/login.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){
	$('.local a').on('click', function(e)
	{
		e.preventDefault();

		var def = $('.default'),
			hz  = $('.hz');

		hz.addClass('incoming');
		def.fadeOut();

		$('.com_login').animate({'height': hz.height()});
		hz.fadeIn(function (e) {
			$(this).removeClass('incoming');
		});

		$('.input-username').focus();
	});

	$('.multi-auth a').on('click', function(e)
	{
		e.preventDefault();

		var def = $('.default'),
			hz  = $('.hz');

		def.addClass('incoming');
		hz.fadeOut();

		$('.com_login').animate({'height': def.height()});
		def.fadeIn(function (e) {
			$(this).removeClass('incoming');
		});
	});
});