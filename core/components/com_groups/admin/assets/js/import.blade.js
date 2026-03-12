/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {

	var RecordImport = new function() {
		var countdownEl = document.querySelector('.countdown');

		this.timer     = null;
		this.checker   = null;
		this.timeout   = countdownEl ? countdownEl.getAttribute('data-timeout') : 0;
		this.isPaused  = false;

		this.init = function() {
			if (countdownEl) {
				this.countdown();
				this.countdownButtons();
				this.progress();
				this.attachLeaveHandler();
			}

			this.hooks();
		};

		this.countdown = function() {
			var self = this;
			this.isPaused = false;
			self.timer = setInterval(function() {
				var span = document.querySelector('.countdown span');
				if (self.timeout >= 0) {
					if (span) {
						span.innerHTML = self.timeout;
					}
					self.timeout--;
				} else {
					self.endCountdown();
					self.startImport();
				}
			}, 1000);
		};

		this.countdownButtons = function() {
			var self = this;
			var actionsEl = document.querySelector('.countdown-actions');

			if (!actionsEl) {
				return;
			}

			actionsEl.addEventListener('click', function(event) {
				var target = event.target;

				if (target.classList.contains('stop')) {
					event.preventDefault();
					self.toggleCountdown();
					var html = (target.innerHTML == 'Stop Import') ? 'Resume Import' : 'Stop Import';
					target.innerHTML = html;
				}

				if (target.classList.contains('start')) {
					event.preventDefault();
					var span = document.querySelector('.countdown span');
					if (span) {
						span.innerHTML = 0;
					}
					self.endCountdown();
					self.startImport();
				}

				if (target.classList.contains('start-over')) {
					event.preventDefault();

					self.setProgress(0);
					var resultsEl = document.querySelector('.results');
					if (resultsEl) {
						var newResults = document.createElement('div');
						newResults.className = 'results';
						resultsEl.parentNode.replaceChild(newResults, resultsEl);
					}
					var statsEl = document.querySelector('.results-stats');
					if (statsEl) {
						statsEl.innerHTML = '';
					}

					setTimeout(function() {
						self.startImport();
					}, 1000);
				}

				if (target.classList.contains('start-real')) {
					event.preventDefault();

					self.setProgress(0);
					var resultsEl = document.querySelector('.results');
					if (resultsEl) {
						var newResults = document.createElement('div');
						newResults.className = 'results';
						resultsEl.parentNode.replaceChild(newResults, resultsEl);
					}
					var statsEl = document.querySelector('.results-stats');
					if (statsEl) {
						statsEl.innerHTML = '';
					}
					var dryrunMsg = document.querySelector('.dryrun-message');
					if (dryrunMsg) {
						dryrunMsg.style.display = 'none';
					}

					var form = target.closest('form');
					if (form) {
						var dryrunInput = form.querySelector('input[name=dryrun]');
						if (dryrunInput) {
							dryrunInput.value = 0;
						}
					}

					setTimeout(function() {
						self.startImport();
					}, 1000);
				}
			});
		};

		this.toggleCountdown = function() {
			if (this.isPaused) {
				this.countdown();
			} else {
				this.isPaused = true;
				this.endCountdown();
			}
		};

		this.endCountdown = function() {
			clearInterval(this.timer);
			this.timer = null;
		};

		this.progress = function() {
			var progressEl = document.querySelector('.progress');
			if (progressEl) {
				progressEl.style.width = '0.01%';
				progressEl.setAttribute('data-value', 0.01);
			}
		};

		this.setProgress = function(newValue) {
			var progressEl = document.querySelector('.progress');
			if (progressEl) {
				progressEl.style.width = newValue + '%';
				progressEl.setAttribute('data-value', newValue);
			}
			var percentEl = document.querySelector('.progress-percentage');
			if (percentEl) {
				percentEl.innerHTML = Math.round(newValue) + '%';
			}
		};

		this.startImport = function() {
			var self = this;
			var form = document.querySelector('form');

			// disable buttons
			var buttons = document.querySelectorAll('.countdown-actions button');
			for (var i = 0; i < buttons.length; i++) {
				buttons[i].setAttribute('disabled', 'disabled');
			}

			// start processing
			var formData = new FormData(form);

			fetch(form.getAttribute('action'), {
				method: 'POST',
				body: formData
			})
			.then(function(response) {
				return response.json();
			})
			.then(function(data) {
				self.handleResults(data);
			})
			.finally(function() {
				self.detachLeaveHandler();
				self.stopImportProgressChecker();

				var buttons = document.querySelectorAll('.countdown-actions button');
				for (var i = 0; i < buttons.length; i++) {
					buttons[i].removeAttribute('disabled');
				}
				var startBtn = document.querySelector('.countdown-actions button.start');
				if (startBtn) {
					startBtn.style.display = 'none';
				}
				var stopBtn = document.querySelector('.countdown-actions button.stop');
				if (stopBtn) {
					stopBtn.style.display = 'none';
				}
				var startOverBtn = document.querySelector('.countdown-actions button.start-over');
				if (startOverBtn) {
					startOverBtn.style.display = '';
				}
				var startRealBtn = document.querySelector('.countdown-actions button.start-real');
				if (startRealBtn) {
					startRealBtn.style.display = '';
				}
			});

			this.startImportProgressChecker();
		};

		this.startImportProgressChecker = function() {
			var self = this;
			var actionsEl = document.querySelector('.countdown-actions');
			var progressUrl = actionsEl ? actionsEl.getAttribute('data-progress') : null;

			if (!progressUrl) {
				return;
			}

			this.checker = setInterval(function() {
				fetch(progressUrl)
					.then(function(response) {
						return response.json();
					})
					.then(function(data) {
						var percent = 0;
						if (data && typeof data.processed !== 'undefined' && typeof data.total !== 'undefined') {
							percent = (data.processed / data.total) * 100;
						}
						self.setProgress(percent);
					});
			}, 100);
		};

		this.stopImportProgressChecker = function() {
			clearTimeout(this.checker);
		};

		this.handleResults = function(data) {
			this.scrollToResults();
			this.resultHelpers();

			// make sure progress says 100
			this.setProgress(100);

			if (data.import == 'success') {
				var statsEl = document.querySelector('.results-stats');
				if (statsEl) {
					statsEl.innerHTML = data.records.length + ' records - ' + data.time + ' seconds';
				}

				var results  = '';
				var sourceEl = document.getElementById('entry-template');
				if (sourceEl && typeof Handlebars !== 'undefined') {
					var source   = sourceEl.innerHTML;
					var template = Handlebars.compile(source);

					for (var i = 0; i < data.records.length; i++) {
						results += template(data.records[i]);
					}
				}

				var resultsEl = document.querySelector('.results');
				if (resultsEl) {
					resultsEl.innerHTML = results;
				}
			}
		};

		this.resultHelpers = function() {
			if (typeof Handlebars === 'undefined') {
				return;
			}

			// print formatted data
			Handlebars.registerHelper('print_json_data', function(data) {
				if (typeof(data) == 'object') {
					return JSON.stringify(data, null, 4);
				}
				return data;
			});

			// Capitalize first char
			Handlebars.registerHelper('ucfirst', function(data) {
				return data.charAt(0).toUpperCase() + data.slice(1);
			});

			// output resource data
			Handlebars.registerHelper('entry_data', function(record, options) {
				var html = '<table>';

				html += '<tr><th>ID</th><td>' + record.entry.gidNumber;
				if (record.entry.gidNumber) {
					html += ' - <a target="_blank" href="' + window.location.host + '/groups/' + record.entry.gidNumber + '">https://' + window.location.host + '/groups/' + record.entry.gidNumber + '</a>';
				}
				html += '</td></tr>';
				html += '<tr><th>Description</th><td>' + record.entry.description + '</td></tr>';
				html += '<tr><th>CN</th><td>' + record.entry.cn + '</td></tr>';
				html += '<tr><th>Published</th><td>' + record.entry.published + '</td></tr>';
				html += '<tr><th>Approved</th><td>' + record.entry.approved + '</td></tr>';
				html += '<tr><th>Join Policy</th><td>' + record.entry.join_policy + '</td></tr>';
				html += '<tr><th>Restrict Message</th><td>' + record.entry.restrict_msg + '</td></tr>';
				html += '<tr><th>Discoverability</th><td>' + record.entry.discoverability + '</td></tr>';
				html += '<tr><th>Public Text</th><td>' + record.entry.public_desc + '</td></tr>';
				html += '<tr><th>Private Text</th><td>' + record.entry.private_desc + '</td></tr>';

				html += '</table>';
				return html;
			});
		};

		this.scrollToResults = function() {
			var resultsEl = document.querySelector('.results');
			if (resultsEl) {
				var pos = resultsEl.getBoundingClientRect().top + window.pageYOffset;
				pos -= 50;
				setTimeout(function() {
					window.scrollTo({
						top: pos,
						behavior: 'smooth'
					});
				}, 1000);
			}
		};

		this.attachLeaveHandler = function() {
			window.onbeforeunload = function() {
				return "Are you sure you want to leave?";
			};
		};

		this.detachLeaveHandler = function() {
			window.onbeforeunload = null;
		};

		this.hooks = function() {
			var hookUpBtns = document.querySelectorAll('.hook-up');
			for (var i = 0; i < hookUpBtns.length; i++) {
				hookUpBtns[i].addEventListener('click', function(event) {
					event.preventDefault();

					var td = this.closest('td');
					var select = td ? td.querySelector('select') : null;
					if (!select) {
						return;
					}
					var selectedIndex = select.selectedIndex;

					if (selectedIndex > 0) {
						var selectedOption = select.options[selectedIndex];
						var prevOption = select.options[selectedIndex - 1];
						select.insertBefore(selectedOption, prevOption);
					}
				});
			}

			var hookDownBtns = document.querySelectorAll('.hook-down');
			for (var i = 0; i < hookDownBtns.length; i++) {
				hookDownBtns[i].addEventListener('click', function(event) {
					event.preventDefault();

					var td = this.closest('td');
					var select = td ? td.querySelector('select') : null;
					if (!select) {
						return;
					}
					var selectedIndex = select.selectedIndex;

					if (selectedIndex < select.options.length - 1) {
						var selectedOption = select.options[selectedIndex];
						var nextOption = select.options[selectedIndex + 1];
						select.insertBefore(nextOption, selectedOption);
					}
				});
			}
		};
	};

	RecordImport.init();
});
