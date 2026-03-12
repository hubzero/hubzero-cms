/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var _DEBUG = document.getElementById('system-debug') ? true : false;

	var deleteUsersBtn = document.getElementById('deleteUsers');
	if (deleteUsersBtn) {
		deleteUsersBtn.addEventListener('click', function () {
			return Hubzero.submitbutton('deleteUsers');
		});
	}

	var exportGroupsBtn = document.getElementById('exportGroups');
	if (exportGroupsBtn) {
		exportGroupsBtn.addEventListener('click', function () {
			return Hubzero.submitbutton('exportGroups');
		});
	}

	var deleteGroupsBtn = document.getElementById('deleteGroups');
	if (deleteGroupsBtn) {
		deleteGroupsBtn.addEventListener('click', function () {
			return Hubzero.submitbutton('deleteGroups');
		});
	}

	var BatchRecords = new function () {
		this.timer     = null;
		this.checker   = null;
		this.start     = 0;
		this.processed = 0;

		this.init = function () {
			var btn = document.getElementById('exportUsers'),
				self = this;

			if (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();

					var delay = parseInt(btn.getAttribute('data-delay'));
					delay = delay ? delay : 3;

					self.process(btn.getAttribute('data-progress'));

					self.checker = setInterval(function () {
						self.process(btn.getAttribute('data-progress'));
					}, 1000 * delay);
				});

				this.progress();
			}
		};

		this.process = function (url) {
			var self = this;

			if (_DEBUG) {
				window.console && console.log('calling: ' + url + self.start);
			}

			fetch(url + self.start)
				.then(function (response) {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(function (data) {
					var percent = 0;

					if (_DEBUG) {
						window.console && console.log(data);
					}

					if (data && typeof data.processed !== 'undefined') {
						self.processed += parseInt(data.processed);
						percent = (self.processed / parseInt(data.total)) * 100;
						if (data.start !== 'undefined') {
							if (data.start == self.start) {
								self.stopImportProgressChecker();
							} else {
								self.start = data.start;
							}
						}

						self.setProgress(percent);
					} else {
						self.stopImportProgressChecker();
						self.showMessage('error', 'There was an error trying to process the records.');
					}

					if (typeof data.errors !== 'undefined' && data.errors.length > 0) {
						self.showMessage('error', data.errors.join('<br />'));
					}

					if (percent >= 100) {
						self.stopImportProgressChecker();
						self.showMessage('success', 'Records processed.');
					}
				})
				.catch(function () {
					self.stopImportProgressChecker();
					self.showMessage('error', 'There was an error trying to process the records.');
				});
		};

		this.progress = function () {
			var progressBar = document.querySelector('.progress');
			if (progressBar) {
				progressBar.style.width = '0%';
				progressBar.setAttribute('aria-valuenow', '0');
			}
		};

		this.setProgress = function (newValue) {
			if (_DEBUG) {
				window.console && console.log('setting progress: ' + Math.round(newValue));
			}
			var progressBar = document.querySelector('.progress');
			if (progressBar) {
				progressBar.style.width = Math.round(newValue) + '%';
				progressBar.setAttribute('aria-valuenow', Math.round(newValue));
			}
			var progressPct = document.querySelector('.progress-percentage');
			if (progressPct) {
				progressPct.textContent = Math.round(newValue) + '%';
			}
		};

		this.stopImportProgressChecker = function () {
			clearTimeout(this.checker);
			if (_DEBUG) {
				window.console && console.log('stopped progress');
			}
		};

		this.showMessage = function (type, message) {
			// Simple notification fallback — append a message to the page
			var container = document.getElementById('system-message-container');
			if (!container) {
				container = document.body;
			}
			var dl = document.createElement('dl');
			dl.id = 'system-message';
			dl.innerHTML = '<dt class="' + type + '">' +
				(type === 'error' ? 'Error' : 'Success') +
				'</dt><dd class="' + type + ' message">' + message + '</dd>';
			container.appendChild(dl);
		};
	};

	BatchRecords.init();
});
