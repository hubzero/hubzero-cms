/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var searchField = document.getElementById('jform_searchstring');

	if (searchField) {
		searchField.addEventListener('focus', function() {
			if (!Hubzero.overrider.states.refreshed) {
				if (this.getAttribute('data-cache_expired')) {
					Hubzero.overrider.refreshCache();
					Hubzero.overrider.states.refreshed = true;
				}
			}
			this.classList.remove('invalid');
		});
	}

	var searchBtn = document.getElementById('searchstrings');

	if (searchBtn) {
		searchBtn.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.overrider.searchStrings();
			return false;
		});
	}
});
