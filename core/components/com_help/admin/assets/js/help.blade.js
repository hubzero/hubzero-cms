/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var back = document.getElementById('back');

	if (back && window.history.length > 1) {
		back.style.display = 'block';
		back.addEventListener('click', function (e) {
			e.preventDefault();
			window.history.back();
		});
	}
});
