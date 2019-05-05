/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ($) {
	$('a.delete-file')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});

	$('a.delete-folder')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			var numFiles = parseInt($(this).attr('data-files'));
			if (numFiles > 0) {
				e.preventDefault();
				alert($(this).attr('data-notempty'));
				return false;
			}
			return res;
		});
});
