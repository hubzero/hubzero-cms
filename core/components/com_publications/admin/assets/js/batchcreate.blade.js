/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var PublicationImport = new function() {
		this.timer     = null;
		this.checker   = null;
		this.output    = document.getElementById('results');
		this.inputFile = document.getElementById('field-file');
		this.submitBtn = document.getElementById('batch_submit');

		this.init = function() {
			this.attachEvents();
			if (this.output) {
				this.output.style.display = 'none';
			}
		};

		this.attachEvents = function() {
			this.iniUpload();
		};

		this.iniUpload = function() {
			var self = this;

			if (!this.submitBtn) {
				return false;
			}
			this.submitBtn.addEventListener('click', function(event) {
				event.preventDefault();
				self.sendData(1);
			});
		};

		this.handleResults = function(data) {
			var self = this;
			console.log(data);

			// Show output area
			self.output.style.display = '';

			if (data && typeof data === 'string') {
				data = JSON.parse(data);
			}
			if (data.result == 'success') {
				self.output.classList.remove('witherror');
				self.output.innerHTML = data.records;

				// Append submit
				var recordcount = document.getElementById('recordcount');
				if (recordcount) {
					recordcount.insertAdjacentHTML(
						'beforebegin',
						self.drawControls()
					);

					var dorun = document.getElementById('dorun');
					if (dorun) {
						dorun.addEventListener('click', function(event) {
							event.preventDefault();
							self.sendData(2);
						});
					}
				}
			} else {
				self.output.classList.add('witherror');
				var out = '<p class="general-error">' + data.error + '</p>';
				if (data.records) {
					out = out + data.records;
				}
				self.output.innerHTML = out;
			}

			var resultlist = document.getElementById('resultlist');
			if (resultlist) {
				// Simple accordion: toggle content visibility on header click
				var headers = resultlist.querySelectorAll(
					'.ui-accordion-header, h3, [data-accordion-header]'
				);
				headers.forEach(function(header) {
					var content = header.nextElementSibling;
					if (content) {
						content.style.display = 'none';
					}
					header.addEventListener('click', function() {
						if (content) {
							content.style.display =
								content.style.display === 'none' ? '' : 'none';
						}
					});
				});
			}
		};

		this.drawControls = function() {
			var html = '<p id="controls" class="controlarea">' +
				'<input type="button" class="btn" id="dorun"' +
				' value="Create record(s)" />' +
				'</p>';

			return html;
		};

		this.sendData = function(dryrun) {
			var self = this;
			var form = document.querySelector('form');
			if (!form) {
				return;
			}

			var dryrunField = document.getElementById('dryrun');
			if (dryrunField) {
				dryrunField.value = dryrun;
			}

			var url = form.getAttribute('action');
			var formData = new FormData(form);

			fetch(url, {
				method: 'POST',
				body: formData
			})
			.then(function(response) {
				return response.text();
			})
			.then(function(responseText) {
				self.handleResults(responseText);
			});
		};

		this.showProgress = function() {
		};
	};

	PublicationImport.init();
});
