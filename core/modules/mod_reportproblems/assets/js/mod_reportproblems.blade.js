/**
 * mod_reportproblems — Blade view support form launcher
 *
 * Opens the support form in a native <dialog> with an iframe.
 * Reads data-trigger and data-form from the #help-pane element.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('DOMContentLoaded', function () {
	var pane = document.getElementById('help-pane');
	if (!pane) {
		return;
	}

	var formUrl  = pane.getAttribute('data-form');
	var selector = pane.getAttribute('data-trigger') || '#tab';

	if (!formUrl) {
		return;
	}

	var trigger = document.querySelector(selector);
	if (!trigger) {
		return;
	}

	// Build a reusable dialog
	var dialog = document.createElement('dialog');
	dialog.className = 'modal';
	dialog.innerHTML =
		'<div class="modal-box w-11/12 max-w-2xl p-0 overflow-hidden">'
		+ '<form method="dialog" class="absolute top-2 right-2 z-10">'
		+ '<button class="btn btn-sm btn-circle btn-ghost" aria-label="Close">'
		+ '&times;</button></form>'
		+ '<iframe class="w-full border-0" style="height:70vh"></iframe>'
		+ '</div>'
		+ '<form method="dialog" class="modal-backdrop"><button>close</button></form>';
	document.body.appendChild(dialog);

	var iframe = dialog.querySelector('iframe');

	trigger.addEventListener('click', function (e) {
		e.preventDefault();
		iframe.src = formUrl;
		dialog.showModal();
	});

	dialog.addEventListener('close', function () {
		iframe.src = 'about:blank';
	});
});
