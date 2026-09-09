/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

document.addEventListener('DOMContentLoaded', function() {
	// Toggle text and classes when clicking reply
	document.querySelectorAll('.below').forEach(function(container) {
		container.addEventListener('click', function(e) {
			var replyLink = e.target.closest('a.reply');
			if (replyLink) {
				e.preventDefault();

				var frmId = replyLink.getAttribute('rel');
				var frm = document.getElementById(frmId);
				if (!frm) return;

				if (frm.classList.contains('hide')) {
					frm.classList.remove('hide');
					replyLink.classList.add('active');
					replyLink.textContent = replyLink.getAttribute('data-txt-active');
				} else {
					frm.classList.add('hide');
					replyLink.classList.remove('active');
					replyLink.textContent = replyLink.getAttribute('data-txt-inactive');
				}
				return;
			}

			var deleteLink = e.target.closest('a.delete');
			if (deleteLink) {
				var res = confirm(deleteLink.getAttribute('data-confirm'));
				if (!res) {
					e.preventDefault();
				}
			}
		});
	});

	// Abuse report links - open in modal or navigate
	document.querySelectorAll('a.abuse').forEach(function(el) {
		el.addEventListener('click', function(e) {
			e.preventDefault();
			var href = this.getAttribute('href').nohtml();
			var self = this;

			fetch(href)
				.then(function(response) { return response.text(); })
				.then(function(html) {
					// Create a simple modal dialog
					var overlay = document.createElement('div');
					overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;'
						+ 'background:rgba(0,0,0,0.5);z-index:9999;display:flex;'
						+ 'align-items:center;justify-content:center;';

					var modal = document.createElement('div');
					modal.style.cssText = 'background:#fff;border-radius:8px;padding:20px;'
						+ 'max-width:500px;width:90%;max-height:80vh;overflow-y:auto;';
					modal.id = 'sbox-content';
					modal.innerHTML = html;

					overlay.appendChild(modal);
					document.body.appendChild(overlay);

					overlay.addEventListener('click', function(ev) {
						if (ev.target === overlay) {
							overlay.remove();
						}
					});

					var frm = modal.querySelector('#hubForm-ajax');
					if (frm) {
						frm.addEventListener('submit', function(ev) {
							ev.preventDefault();
							var formData = new FormData(frm);

							fetch(frm.getAttribute('action'), {
								method: 'POST',
								body: formData
							})
							.then(function(resp) { return resp.json(); })
							.then(function(response) {
								if (!response.success) {
									frm.insertAdjacentHTML(
										'afterbegin',
										'<p class="error">' + response.message + '</p>'
									);
									return;
								}
								modal.innerHTML = '<p class="passed">' + response.message + '</p>';

								var comment = document.getElementById('c' + response.id);
								if (comment) {
									var body = comment.querySelector('.comment-body');
									if (body) {
										body.innerHTML = '<p class="warning">'
											+ self.getAttribute('data-txt-flagged') + '</p>';
									}
								}

								setTimeout(function() {
									overlay.remove();
								}, 2000);
							});
						});
					}
				});
		});
	});

	// Note: datetimepicker removed - use native datetime-local inputs or flatpickr
});
