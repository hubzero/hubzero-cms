/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var roleEntries = document.querySelectorAll('.entry-role');
	for (var i = 0; i < roleEntries.length; i++) {
		roleEntries[i].addEventListener('click', function(e) {
			var task = document.getElementById('task');
			task.value = 'update';

			var form = document.getElementById('adminForm');
			form.submit();
		});
	}
});
