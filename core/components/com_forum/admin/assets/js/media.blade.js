/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('a.deletefile').forEach(function(el) {
		el.addEventListener('click', function(e) {
			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});
});
