/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$("a.deletefile").on('click', function(e){
		var res = confirm($(this).attr('data-confirm'));
		if (!res) {
			e.preventDefault();
		}
		return res;
	});
});
