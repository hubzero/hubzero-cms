/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
