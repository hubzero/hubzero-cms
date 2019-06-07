/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

				html += '<tr><th>ID</th><td>' + record.entry.gidNumber;
				if (record.entry.gidNumber)
				{
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

			// output member data
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