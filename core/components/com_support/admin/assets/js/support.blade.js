/**
 * Support — Blade-specific JavaScript
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Add aria-labels to dynamically created elements (WCAG)
(function() {
	function labelDynamic() {
		var inputs = document.querySelectorAll('.qq-upload-button input[type="file"]:not([aria-label])');
		for (var i = 0; i < inputs.length; i++) {
			inputs[i].setAttribute('aria-label', 'Upload file');
		}
		// Query condition builder selects
		var flds = document.querySelectorAll('select.fld:not([aria-label])');
		for (var i = 0; i < flds.length; i++) {
			flds[i].setAttribute('aria-label', 'Field');
		}
		var ops = document.querySelectorAll('select.op:not([aria-label])');
		for (var i = 0; i < ops.length; i++) {
			ops[i].setAttribute('aria-label', 'Operator');
		}
		var vals = document.querySelectorAll('select.val:not([aria-label])');
		for (var i = 0; i < vals.length; i++) {
			vals[i].setAttribute('aria-label', 'Value');
		}
	}
	var observer = new MutationObserver(labelDynamic);
	if (document.body) {
		observer.observe(document.body, { childList: true, subtree: true });
	} else {
		document.addEventListener('DOMContentLoaded', function() {
			observer.observe(document.body, { childList: true, subtree: true });
		});
	}
})();
