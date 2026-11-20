/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
