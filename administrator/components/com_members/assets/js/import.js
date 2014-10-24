/**
 * @package     hubzero-cms
 * @file        administrator/components/com_resources/resources.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	var RecordImport = new function() {
		this.timer     = null;
		this.checker   = null;
		this.timeout   = $('.countdown').attr('data-timeout');
		this.isPaused  = false;

		this.init = function() {
			if ($('.countdown').length) {
				this.countdown();
				this.countdownButtons();
				this.progress();
				this.attachLeaveHandler();
			}

			this.hooks();
		}

		this.countdown = function() {
			var self = this;
			this.isPaused = false;
			self.timer = setInterval(function() {
				if (self.timeout >= 0) {
					$('.countdown span').html(self.timeout);
					self.timeout--;
				} else {
					self.endCountdown();
					self.startImport();
				}
			}, 1000);
		};

		this.countdownButtons = function() {
			var self = this;

			$('.countdown-actions')
				.on('click', '.stop', function(event) {
					event.preventDefault();
					self.toggleCountdown();
					var html = ($(this).html() == 'Stop Import') ? 'Resume Import' : 'Stop Import';
					$(this).html(html);
				})
				.on('click', '.start', function(event) {
					event.preventDefault();
					$('.countdown span').html(0);
					self.endCountdown();
					self.startImport();
				})
				.on('click', '.start-over', function(event) {
					event.preventDefault();

					self.setProgress(0);
					$('.results').replaceWith('<div class="results"></div>');
					$('.results-stats').html('');

					setTimeout(function() {
						self.startImport();
					}, 1000);
				})
				.on('click', '.start-real', function(event) {
					event.preventDefault();

					self.setProgress(0);
					$('.results').replaceWith('<div class="results"></div>');
					$('.results-stats').html('');
					$('.dryrun-message').hide();

					$(this).parents('form').find('input[name=dryrun]').val(0);

					setTimeout(function() {
						self.startImport();
					}, 1000);
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
			$('.progress').progressbar({
				value: 0.01
			});
		};

		this.setProgress = function( newValue ) {
			$('.progress').progressbar("value", newValue);
			$('.progress-percentage').html(Math.round(newValue) + '%');
		}

		this.startImport = function() {
			var self = this;

			// disable buttons
			$('.countdown-actions button').attr('disabled', 'disabled');

			// start processing
			$.ajax({
				type: 'post',
				data : $('form').serialize(),
				dataType: 'json',
				url:  $('form').attr('action'),
				complete: function(data) {
					self.detachLeaveHandler();
					self.stopImportProgressChecker();

					$('.countdown-actions button').removeAttr('disabled');
					$('.countdown-actions button.start').hide();
					$('.countdown-actions button.stop').hide();
					$('.countdown-actions button.start-over').show();
					$('.countdown-actions button.start-real').show();
				},
				success: function(data) {
					self.handleResults(data);
				}
			});

			this.startImportProgressChecker();
		};

		this.startImportProgressChecker = function() {
			var self = this;
			this.checker = setInterval(function(){
				$.getJSON($('.countdown-actions').attr('data-progress'), function(data) {
					var percent = (data.processed / data.total) * 100;
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
			$('.progress').progressbar("value", 100);

			if (data.import == 'success') {
				$('.results-stats').html(data.records.length + ' records - ' + data.time + ' seconds');

				var results  = '';
				var source   = $("#entry-template").html();
				var template = Handlebars.compile(source);

				//apply template to uploads
				$.each(data.records, function(i, e) {
					results += template( e );
				});

				$('.results').html(results);
			}

			$('.results').accordion({
				'active' : false,
				'heightStyle' : 'content',
				'collapsible' : true
			});
		};

		this.resultHelpers = function() {
			// print formatted data
			Handlebars.registerHelper('print_json_data', function(data) {
				if (typeof(data) == 'object')
				{
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
				if (record.entry.uidNumber)
				{
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

			// output resource data
			/*Handlebars.registerHelper('group_data', function(children, options) {
				var html = '<table>';

				$.each(children, function(index, item) {
					html += '<tr><th>Title</th><td>' + item.title + '</td></tr>';
					html += '<tr><th>Link</th><td class="child-link"><span>' + item.path + '</span></td></tr>';
					html += '<tr><th colspan="2">&nbsp;</th></tr>';
				});

				html += '</table>';
				return html;
			});*/
		};

		this.scrollToResults = function() {
			var pos = $('.results').position().top;
			pos -= 50;
			$("html, body").delay(1000).animate({ scrollTop: pos + "px" }, 2000);
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
			$('.hook-up').on('click', function(event) {
				event.preventDefault();

				var select = $(this).parents('td').find('select'),
					selectedOption = select.find('option:selected'),
					selectedOptionIndex = selectedOption.index();

				if (selectedOptionIndex > 0)
				{
					select.find('option').eq(selectedOptionIndex - 1).before(selectedOption);
				}
			});

			$('.hook-down').on('click', function(event) {
				event.preventDefault();

				var select = $(this).parents('td').find('select'),
					selectedOption = select.find('option:selected'),
					selectedOptionIndex = selectedOption.index();

				if (selectedOptionIndex < select.find('option').length)
				{
					select.find('option').eq(selectedOptionIndex + 1).after(selectedOption);
				}
			});
		}
	};

	RecordImport.init();
});