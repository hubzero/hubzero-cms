/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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