/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var bdy = document.getElementById('preview-body');

	if (bdy) {
		var form = window.top.document.adminForm;
		var title = form.title.value;

		var alltext = form[bdy.getAttribute('data-parent-text')];

		var previewTitle = document.getElementById('preview-title');
		if (previewTitle) { previewTitle.value = title; }
		var previewText = document.getElementById('preview-text');
		if (previewText) { previewText.value = alltext; }
	}
});
