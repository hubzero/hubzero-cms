/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
