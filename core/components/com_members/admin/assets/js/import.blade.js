/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

	var RecordImport = new function() {
		this.timer     = null;
		this.checker   = null;
		this.timeout   = 0;
		this.isPaused  = false;

		var countdownEl = document.querySelector('.countdown');
		if (countdownEl) {
			this.timeout = parseInt(countdownEl.getAttribute('data-timeout'), 10) || 0;
		}

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
			var countdownSpan = document.querySelector('.countdown span');

			self.timer = setInterval(function() {
				if (self.timeout >= 0) {
					if (countdownSpan) {
						countdownSpan.textContent = self.timeout;
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
			if (!actionsEl) { return; }

			actionsEl.addEventListener('click', function(event) {
				var target = event.target.closest('button');
				if (!target) { return; }

				event.preventDefault();

				if (target.classList.contains('stop')) {
					self.toggleCountdown();
					var html = (target.textContent.trim() === 'Stop Import') ? 'Resume Import' : 'Stop Import';
					target.textContent = html;
				} else if (target.classList.contains('start')) {
					var cs = document.querySelector('.countdown span');
					if (cs) { cs.textContent = '0'; }
					self.endCountdown();
					self.startImport();
				} else if (target.classList.contains('start-over')) {
					self.setProgress(0);
					var results = document.querySelector('.results');
					if (results) {
						var newResults = document.createElement('div');
						newResults.className = 'results';
						results.parentNode.replaceChild(newResults, results);
					}
					var stats = document.querySelector('.results-stats');
					if (stats) { stats.textContent = ''; }

					setTimeout(function() {
						self.startImport();
					}, 1000);
				} else if (target.classList.contains('start-real')) {
					self.setProgress(0);
					var results2 = document.querySelector('.results');
					if (results2) {
						var newResults2 = document.createElement('div');
						newResults2.className = 'results';
						results2.parentNode.replaceChild(newResults2, results2);
					}
					var stats2 = document.querySelector('.results-stats');
					if (stats2) { stats2.textContent = ''; }
					var dryrun = document.querySelector('.dryrun-message');
					if (dryrun) { dryrun.style.display = 'none'; }

					var form = target.closest('form');
					if (form) {
						var dryrunInput = form.querySelector('input[name=dryrun]');
						if (dryrunInput) { dryrunInput.value = '0'; }
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
				progressEl.style.position = 'relative';
				var inner = progressEl.querySelector('.progress-inner');
				if (!inner) {
					inner = document.createElement('div');
					inner.className = 'progress-inner';
					inner.style.cssText = 'height:100%;width:0%;background:#5cb85c;transition:width 0.3s;';
					progressEl.appendChild(inner);
				}
				inner.style.width = '0%';
			}
		};

		this.setProgress = function(newValue) {
			var progressEl = document.querySelector('.progress');
			if (progressEl) {
				var inner = progressEl.querySelector('.progress-inner');
				if (inner) {
					inner.style.width = Math.round(newValue) + '%';
				}
			}
			var pctEl = document.querySelector('.progress-percentage');
			if (pctEl) {
				pctEl.textContent = Math.round(newValue) + '%';
			}
		};

		this.startImport = function() {
			var self = this;
			var form = document.querySelector('form');

			// disable buttons
			var buttons = document.querySelectorAll('.countdown-actions button');
			for (var b = 0; b < buttons.length; b++) {
				buttons[b].setAttribute('disabled', 'disabled');
			}

			// start processing
			var formData = new FormData(form);
			var params = new URLSearchParams(formData).toString();

			fetch(form.getAttribute('action'), {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: params
			})
			.then(function(response) { return response.json(); })
			.then(function(data) {
				self.handleResults(data);
			})
			.finally(function() {
				self.detachLeaveHandler();
				self.stopImportProgressChecker();

				var btns = document.querySelectorAll('.countdown-actions button');
				for (var i = 0; i < btns.length; i++) {
					btns[i].removeAttribute('disabled');
				}
				var startBtn = document.querySelector('.countdown-actions button.start');
				if (startBtn) { startBtn.style.display = 'none'; }
				var stopBtn = document.querySelector('.countdown-actions button.stop');
				if (stopBtn) { stopBtn.style.display = 'none'; }
				var overBtn = document.querySelector('.countdown-actions button.start-over');
				if (overBtn) { overBtn.style.display = ''; }
				var realBtn = document.querySelector('.countdown-actions button.start-real');
				if (realBtn) { realBtn.style.display = ''; }
			});

			this.startImportProgressChecker();
		};

		this.startImportProgressChecker = function() {
			var self = this;
			var actionsEl = document.querySelector('.countdown-actions');
			var progressUrl = actionsEl ? actionsEl.getAttribute('data-progress') : null;
			if (!progressUrl) { return; }

			this.checker = setInterval(function() {
				fetch(progressUrl)
					.then(function(response) { return response.json(); })
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

			if (data.import === 'success') {
				var statsEl = document.querySelector('.results-stats');
				if (statsEl) {
					statsEl.textContent = data.records.length + ' records - ' + data.time + ' seconds';
				}

				var results  = '';
				var sourceEl = document.getElementById('entry-template');
				if (sourceEl) {
					var source   = sourceEl.innerHTML;
					var template = Handlebars.compile(source);

					// apply template to records
					for (var i = 0; i < data.records.length; i++) {
						results += template(data.records[i]);
					}
				}

				var resultsEl = document.querySelector('.results');
				if (resultsEl) {
					resultsEl.innerHTML = results;

					// Simple collapsible accordion behavior
					var headers = resultsEl.querySelectorAll('h3, .accordion-header');
					for (var h = 0; h < headers.length; h++) {
						(function(header) {
							header.style.cursor = 'pointer';
							var content = header.nextElementSibling;
							if (content) { content.style.display = 'none'; }
							header.addEventListener('click', function() {
								if (content) {
									content.style.display = (content.style.display === 'none') ? '' : 'none';
								}
							});
						})(headers[h]);
					}
				}
			}
		};

		this.resultHelpers = function() {
			// print formatted data
			Handlebars.registerHelper('print_json_data', function(data) {
				if (typeof(data) === 'object') {
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

				html += '<tr><th>ID</th><td>' + record.entry.uidNumber;
				if (record.entry.uidNumber) {
					html += ' - <a target="_blank" href="' + window.location.host + '/members/' + record.entry.uidNumber + '">https://' + window.location.host + '/members/' + record.entry.uidNumber + '</a>';
				}
				html += '</td></tr>';
				html += '<tr><th>Name</th><td>' + record.entry.name + '</td></tr>';
				html += '<tr><th>Username</th><td>' + record.entry.username + '</td></tr>';
				html += '<tr><th>Email</th><td>' + record.entry.email + '</td></tr>';
				html += '<tr><th>Bio</th><td>' + record.entry.bio + '</td></tr>';
				html += '<tr><th>Registered</th><td>' + record.entry.registerDate + '</td></tr>';
				html += '<tr><th>Employment</th><td>' + record.entry.orgtype + '</td></tr>';
				html += '<tr><th>Organization</th><td>' + record.entry.organization + '</td></tr>';
				html += '<tr><th>Gender</th><td>' + record.entry.gender + '</td></tr>';
				html += '<tr><th>Country Resident</th><td>' + record.entry.countryresident + '</td></tr>';
				html += '<tr><th>Country Origin</th><td>' + record.entry.countryorigin + '</td></tr>';
				html += '<tr><th>URL</th><td>' + record.entry.url + '</td></tr>';
				html += '<tr><th>Mail Preference</th><td>' + record.entry.mailPreferenceOption + '</td></tr>';
				html += '<tr><th>Email Confirmed</th><td>' + record.entry.emailConfirmed + '</td></tr>';
				html += '<tr><th>Phone</th><td>' + record.entry.phone + '</td></tr>';
				html += '<tr><th>Public Profile</th><td>' + record.entry.public + '</td></tr>';
				html += '<tr><th>ORCID</th><td>' + record.entry.orcid + '</td></tr>';

				html += '</table>';
				return html;
			});
		};

		this.scrollToResults = function() {
			var resultsEl = document.querySelector('.results');
			if (!resultsEl) { return; }
			var rect = resultsEl.getBoundingClientRect();
			var pos = rect.top + window.pageYOffset - 50;
			setTimeout(function() {
				window.scrollTo({ top: pos, behavior: 'smooth' });
			}, 1000);
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
			var hookUps = document.querySelectorAll('.hook-up');
			for (var u = 0; u < hookUps.length; u++) {
				hookUps[u].addEventListener('click', function(event) {
					event.preventDefault();

					var td = this.closest('td');
					if (!td) { return; }
					var select = td.querySelector('select');
					if (!select) { return; }
					var selectedOption = select.options[select.selectedIndex];
					var selectedOptionIndex = select.selectedIndex;

					if (selectedOptionIndex > 0) {
						var prev = select.options[selectedOptionIndex - 1];
						select.insertBefore(selectedOption, prev);
					}
				});
			}

			var hookDowns = document.querySelectorAll('.hook-down');
			for (var d = 0; d < hookDowns.length; d++) {
				hookDowns[d].addEventListener('click', function(event) {
					event.preventDefault();

					var td = this.closest('td');
					if (!td) { return; }
					var select = td.querySelector('select');
					if (!select) { return; }
					var selectedOption = select.options[select.selectedIndex];
					var selectedOptionIndex = select.selectedIndex;

					if (selectedOptionIndex < select.options.length - 1) {
						var next = select.options[selectedOptionIndex + 1];
						select.insertBefore(next, selectedOption);
					}
				});
			}
		};
	};

	RecordImport.init();
});
