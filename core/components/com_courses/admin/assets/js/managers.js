/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('.entry-role').on('click', function(e) {
		var task = document.getElementById('task');
		task.value = 'update';

		var form = document.getElementById('adminForm');
		form.submit();
	});
});
