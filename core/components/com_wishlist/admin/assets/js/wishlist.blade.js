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
	var ownerassignees = [];
	var ownerdata = document.getElementById('owner-data');

	if (ownerdata) {
		var cdata = JSON.parse(ownerdata.innerHTML);

		for (var i = 0; i < cdata.data.length; i++) {
			ownerassignees[i] = cdata.data[i];
		}
	}

	var wishlistSelect = document.getElementById('field-wishlist');

	if (wishlistSelect) {
		wishlistSelect.addEventListener('change', function(e) {
			changeDynaList(
				'fieldassigned',
				ownerassignees,
				this.options[this.selectedIndex].value,
				0,
				0
			);
		});
	}
});
