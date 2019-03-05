/**
 * @package     hubzero-cms
 * @file        components/com_wiki/assets/js/media.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('a.delete').on('click', function (e) {
		e.preventDefault();

		if (confirm($(this).attr('data-confirm'))) {
			return true;
		}

		return false;
	});
});

