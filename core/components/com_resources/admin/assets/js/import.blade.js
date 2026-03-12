/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {

	var ResourceImport = new function() {
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
			self.timer = setInterval(function() {
				var span = document.querySelector('.countdown span');
				if (self.timeout >= 0) {
					if (span) {
						span.textContent = self.timeout;
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
			var actions = document.querySelector('.countdown-actions');
			if (!actions) return;

			actions.addEventListener('click', function(event) {
				var target = event.target.closest('button');
				if (!target) return;

				if (target.classList.contains('stop')) {
					event.preventDefault();
					self.toggleCountdown();
					var html = (target.textContent === 'Stop Import') ? 'Resume Import' : 'Stop Import';
					target.textContent = html;
				}

				if (target.classList.contains('start')) {
					event.preventDefault();
					var span = document.querySelector('.countdown span');
					if (span) {
						span.textContent = '0';
					}
					self.endCountdown();
					self.startImport();
				}

				if (target.classList.contains('start-over')) {
					event.preventDefault();

					self.setProgress(0);
					var resultsEl = document.querySelector('.results');
					if (resultsEl) {
						var newDiv = document.createElement('div');
						newDiv.className = 'results';
						resultsEl.parentNode.replaceChild(newDiv, resultsEl);
					}
					var statsEl = document.querySelector('.results-stats');
					if (statsEl) {
						statsEl.textContent = '';
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
						var newDiv = document.createElement('div');
						newDiv.className = 'results';
						resultsEl.parentNode.replaceChild(newDiv, resultsEl);
					}
					var statsEl = document.querySelector('.results-stats');
					if (statsEl) {
						statsEl.textContent = '';
					}
					var dryrunMsg = document.querySelector('.dryrun-message');
					if (dryrunMsg) {
						dryrunMsg.style.display = 'none';
					}

					var form = target.closest('form');
					if (form) {
						var dryrunInput = form.querySelector('input[name=dryrun]');
						if (dryrunInput) {
							dryrunInput.value = '0';
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
				progressEl.style.position = 'relative';
				var bar = progressEl.querySelector('.progress-bar');
				if (!bar) {
					bar = document.createElement('div');
					bar.className = 'progress-bar';
					bar.style.height = '100%';
					bar.style.width = '0%';
					bar.style.transition = 'width 0.3s';
					progressEl.appendChild(bar);
				}
				bar.style.width = '0%';
			}
		};

		this.setProgress = function(newValue) {
			if (isNaN(newValue)) {
				newValue = 0;
			}
			var progressEl = document.querySelector('.progress');
			if (progressEl) {
				var bar = progressEl.querySelector('.progress-bar');
				if (bar) {
					bar.style.width = Math.round(newValue) + '%';
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
			if (!form) return;

			// disable buttons
			var buttons = document.querySelectorAll('.countdown-actions button');
			buttons.forEach(function(btn) {
				btn.setAttribute('disabled', 'disabled');
			});

			var formData = new FormData(form);
			var params = new URLSearchParams(formData);

			fetch(form.getAttribute('action'), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: params.toString()
			})
			.then(function(response) { return response.json(); })
			.then(function(data) {
				self.handleResults(data);
			})
			.catch(function() {})
			.finally(function() {
				self.detachLeaveHandler();
				self.stopImportProgressChecker();

				buttons.forEach(function(btn) {
					btn.removeAttribute('disabled');
				});
				var startBtn = document.querySelector('.countdown-actions button.start');
				if (startBtn) startBtn.style.display = 'none';
				var stopBtn = document.querySelector('.countdown-actions button.stop');
				if (stopBtn) stopBtn.style.display = 'none';
				var startOverBtn = document.querySelector('.countdown-actions button.start-over');
				if (startOverBtn) startOverBtn.style.display = '';
				var startRealBtn = document.querySelector('.countdown-actions button.start-real');
				if (startRealBtn) startRealBtn.style.display = '';
			});

			this.startImportProgressChecker();
		};

		this.startImportProgressChecker = function() {
			var self = this;
			var idInput = document.querySelector('input[name="id"]');
			var importId = idInput ? idInput.value : '';

			this.checker = setInterval(function() {
				fetch('index.php?option=com_resources&controller=import&task=progress&id=' + importId)
					.then(function(response) { return response.json(); })
					.then(function(data) {
						var percent = 0;
						if (data && typeof data.processed !== 'undefined' && typeof data.total !== 'undefined') {
							percent = (data.processed / data.total) * 100;
						}
						self.setProgress(percent);
					})
					.catch(function() {});
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
					statsEl.textContent = data.records.length + ' records - ' + data.time + ' seconds';
				}

				var results = '';
				var sourceEl = document.getElementById('resource-template');
				if (sourceEl && typeof Handlebars !== 'undefined') {
					var source = sourceEl.innerHTML;
					var template = Handlebars.compile(source);

					data.records.forEach(function(record) {
						results += template(record);
					});
				}

				var resultsEl = document.querySelector('.results');
				if (resultsEl) {
					resultsEl.innerHTML = results;
				}
			}

			// Simple collapsible behavior instead of jQuery UI accordion
			var resultsEl = document.querySelector('.results');
			if (resultsEl) {
				var headers = resultsEl.querySelectorAll('h3, .accordion-header');
				headers.forEach(function(header) {
					var content = header.nextElementSibling;
					if (content) {
						content.style.display = 'none';
					}
					header.addEventListener('click', function() {
						if (content) {
							content.style.display = (content.style.display === 'none') ? '' : 'none';
						}
					});
				});
			}
		};

		this.resultHelpers = function() {
			if (typeof Handlebars === 'undefined') return;

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
			Handlebars.registerHelper('resource_data', function(record, options) {
				var html = '<table>';

				html += '<tr><th width="20%">ID</th><td>' + record.resource.id;
				if (record.resource.id) {
					html += ' - <a target="_blank" href="/resources/' + record.resource.id + '">https://' + window.location.host + '/resources/' + record.resource.id + '</a>';
				}
				html += '</td></tr>';
				html += '<tr><th width="20%">Title</th><td>' + record.resource.title + '</td></tr>';
				html += '<tr><th width="20%">Type</th><td>' + record.type.type + '</td></tr>';
				html += '<tr><th width="20%">Logical Type</th><td>' + record.resource.logical_type + '</td></tr>';
				html += '<tr><th width="20%">Abstract</th><td>' + record.resource.introtext + '</td></tr>';
				html += '<tr><th width="20%">Footer Text</th><td>' + record.resource.footertext + '</td></tr>';
				html += '<tr><th width="20%">Created</th><td>' + record.resource.created + '</td></tr>';
				html += '<tr><th width="20%">Created By</th><td>' + record.resource.created_by + '</td></tr>';
				html += '<tr><th width="20%">Modified</th><td>' + record.resource.modified + '</td></tr>';
				html += '<tr><th width="20%">Modified By</th><td>' + record.resource.modified_by + '</td></tr>';
				html += '<tr><th width="20%">Published</th><td>' + record.resource.published + '</td></tr>';
				html += '<tr><th width="20%">Publish Up</th><td>' + record.resource.publish_up + '</td></tr>';
				html += '<tr><th width="20%">Publish Down</th><td>' + record.resource.publish_down + '</td></tr>';
				html += '<tr><th width="20%">Access</th><td>' + record.resource.access + '</td></tr>';
				html += '<tr><th width="20%">Hits</th><td>' + record.resource.hits + '</td></tr>';
				html += '<tr><th width="20%">Path</th><td>' + record.resource.path + '</td></tr>';
				html += '<tr><th width="20%">Standalone</th><td>' + record.resource.standalone + '</td></tr>';
				html += '<tr><th width="20%">Group</th><td>' + record.resource.group_owner + '</td></tr>';
				html += '<tr><th width="20%">Rating</th><td>' + record.resource.rating + '</td></tr>';
				html += '<tr><th width="20%">Times Rated</th><td>' + record.resource.times_rated + '</td></tr>';
				html += '<tr><th width="20%">Params</th><td><pre>' + Handlebars.helpers.print_json_data.call(this, record.resource.params) + '</pre></td></tr>';
				html += '<tr><th width="20%">Attribs</th><td><pre>' + Handlebars.helpers.print_json_data.call(this, record.resource.attribs) + '</pre></td></tr>';
				html += '<tr><th width="20%">Alias</th><td>' + record.resource.alias + '</td></tr>';
				html += '<tr><th width="20%">Ranking</th><td>' + record.resource.ranking + '</td></tr>';

				html += '</table>';
				return html;
			});

			// output child resource data
			Handlebars.registerHelper('child_resource_data', function(children, options) {
				var html = '<table>';

				children.forEach(function(item) {
					html += '<tr><th width="20%">Title</th><td>' + item.title + '</td></tr>';
					html += '<tr><th width="20%">Link</th><td class="child-link"><span>' + item.path + '</span></td></tr>';
					html += '<tr><th colspan="2">&nbsp;</th></tr>';
				});

				html += '</table>';
				return html;
			});
		};

		this.scrollToResults = function() {
			var resultsEl = document.querySelector('.results');
			if (resultsEl) {
				var pos = resultsEl.getBoundingClientRect().top + window.pageYOffset - 50;
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
			document.querySelectorAll('.hook-up').forEach(function(btn) {
				btn.addEventListener('click', function(event) {
					event.preventDefault();

					var wrap = btn.closest('.input-wrap');
					var select = wrap ? wrap.querySelector('select') : null;
					if (!select) {
						var td = btn.closest('td');
						select = td ? td.querySelector('select') : null;
					}
					if (!select) return;

					var selectedOption = select.options[select.selectedIndex];
					var selectedOptionIndex = select.selectedIndex;

					if (selectedOptionIndex > 0) {
						select.insertBefore(selectedOption, select.options[selectedOptionIndex - 1]);
					}
				});
			});

			document.querySelectorAll('.hook-down').forEach(function(btn) {
				btn.addEventListener('click', function(event) {
					event.preventDefault();

					var wrap = btn.closest('.input-wrap');
					var select = wrap ? wrap.querySelector('select') : null;
					if (!select) {
						var td = btn.closest('td');
						select = td ? td.querySelector('select') : null;
					}
					if (!select) return;

					var selectedOption = select.options[select.selectedIndex];
					var selectedOptionIndex = select.selectedIndex;

					if (selectedOptionIndex < select.options.length - 1) {
						var ref = select.options[selectedOptionIndex + 1];
						if (ref.nextSibling) {
							select.insertBefore(selectedOption, ref.nextSibling);
						} else {
							select.appendChild(selectedOption);
						}
					}
				});
			});
		};
	};

	ResourceImport.init();
});
