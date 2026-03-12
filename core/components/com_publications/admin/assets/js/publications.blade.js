/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'resetrating') {
			if (confirm(frm.getAttribute('data-confirmreset'))) {
				Hubzero.submitform(task, frm);
				return;
			} else {
				return;
			}
		}

		var admin_action = document.getElementById('admin_action');

		if (task == 'saveorder') {
			Hubzero.submitform('saveauthororder', frm);
			return;
		}

		if (task == 'publish') {
			admin_action.value = 'publish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'revert') {
			admin_action.value = 'revert';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'message') {
			admin_action.value = 'message';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'unpublish') {
			admin_action.value = 'unpublish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'republish') {
			admin_action.value = 'republish';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

HUB.Publications = {

	saveOrder: function() {
		var items = document.querySelectorAll('.pick');
		var selections = '';

		items.forEach(function(item) {
			var id = item.getAttribute('id');
			id = id.replace('author_', '');

			if (id != '' && id != ' ') {
				selections = selections + id + '-';
			}
		});

		HUB.Publications.displayOrdering();

		var neworder = document.getElementById('neworder');
		if (neworder) {
			neworder.value = selections;
		}
	},

	displayOrdering: function() {
		var nums = document.querySelectorAll('.ordernum');
		var o = 1;

		nums.forEach(function(item) {
			item.innerHTML = o;
			o++;
		});
	}
};

document.addEventListener('DOMContentLoaded', function() {
	var resetRating = document.getElementById('reset_rating');
	if (resetRating) {
		resetRating.addEventListener('click', function(e) {
			Hubzero.submitbutton('resetrating');
		});
	}

	var resetRanking = document.getElementById('reset_ranking');
	if (resetRanking) {
		resetRanking.addEventListener('click', function(e) {
			Hubzero.submitbutton('resetranking');
		});
	}

	var doMessage = document.getElementById('do-message');
	if (doMessage) {
		doMessage.addEventListener('click', function(e) {
			Hubzero.submitbutton('message');
		});
	}

	var doUnpublish = document.getElementById('do-unpublish');
	if (doUnpublish) {
		doUnpublish.addEventListener('click', function(e) {
			Hubzero.submitbutton('unpublish');
		});
	}

	var doRepublish = document.getElementById('do-republish');
	if (doRepublish) {
		doRepublish.addEventListener('click', function(e) {
			Hubzero.submitbutton('republish');
		});
	}

	var doPublish = document.getElementById('do-publish');
	if (doPublish) {
		doPublish.addEventListener('click', function(e) {
			Hubzero.submitbutton('publish');
		});
	}

	var doRevert = document.getElementById('do-revert');
	if (doRevert) {
		doRevert.addEventListener('click', function(e) {
			Hubzero.submitbutton('revert');
		});
	}

	var fieldPublished = document.getElementById('field-published');
	if (fieldPublished) {
		fieldPublished.addEventListener('change', function() {
			var unPubReason = document.getElementById('field-unPubReason');
			if (unPubReason) {
				unPubReason.disabled = (this.value != "0");
			}
		});
	}

	var fieldUnPubReason = document.getElementById('field-unPubReason');
	if (fieldUnPubReason) {
		fieldUnPubReason.addEventListener('change', function() {
			var reason = document.getElementById('reason');
			if (reason) {
				if (this.value == "0") {
					reason.disabled = false;
				} else {
					reason.value = '';
					reason.disabled = true;
				}
			}
		});
	}
});
