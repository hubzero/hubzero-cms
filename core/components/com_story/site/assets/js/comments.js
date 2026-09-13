/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Moderating without losing your place
 *
 * A moderator works down a long discussion. Reloading the page after every
 * decision costs them the scroll position and their sense of where they were,
 * which is how a chore becomes a chore nobody finishes. So the request goes
 * out in the background and only the one comment changes.
 *
 * The form underneath is a real form and posts perfectly well on its own.
 * This is an improvement on that, not a replacement for it — with no script
 * the select and its submit button still work.
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var forms = document.querySelectorAll('form.story-moderate');

		Array.prototype.forEach.call(forms, function (form) {
			var select = form.querySelector('select');

			if (!select) {
				return;
			}

			// Choosing a reason is the decision. Asking for a second click on
			// a submit button adds nothing.
			select.addEventListener('change', function () {
				if (!select.value) {
					return;
				}

				send(form, select);
			});
		});
	});

	/**
	 * Post one moderation and fold the answer back into the page
	 */
	function send(form, select) {
		var body = new FormData(form);

		body.append('no_html', '1');

		select.disabled = true;

		fetch(form.getAttribute('action'), {
			method: 'POST',
			body: body,
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (answer) {
				apply(form, select, answer);
			})
			.catch(function () {
				// The background request failed, so fall back to the thing
				// that always works: submit the form for real.
				select.disabled = false;
				form.submit();
			});
	}

	/**
	 * Show what happened, on the comment it happened to
	 */
	function apply(form, select, answer) {
		var article = form.closest('.story-comment');
		var note    = document.createElement('span');

		note.className = 'story-moderate-note' + (answer.success ? ' is-done' : ' is-refused');
		note.textContent = answer.message || '';

		var existing = form.parentNode.querySelector('.story-moderate-note');

		if (existing) {
			existing.parentNode.removeChild(existing);
		}

		form.parentNode.insertBefore(note, form.nextSibling);

		if (!answer.success) {
			// A refusal is a thing to learn from, so the control stays usable
			// and the reason stays on screen.
			select.disabled = false;
			select.value = '';
			return;
		}

		// It is done, and it cannot be done twice.
		form.parentNode.removeChild(form);

		if (article && answer.score !== null && answer.score !== undefined) {
			var score = article.querySelector('.story-comment-score');

			if (score) {
				score.textContent = score.textContent.replace(/-?\d+(\.\d+)?/, answer.score);
			}
		}
	}
})();
