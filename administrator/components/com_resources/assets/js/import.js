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
	
	var ResourceImport = new function() {
		this.timer     = null;
		this.checker   = null;
		this.timeout   = $('.countdown').attr('data-timeout');
		this.isPaused  = false;
	
		this.init = function() {
			if ($('.countdown').length)
			{
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
				if (self.timeout >= 0)
				{
					$('.countdown span').html(self.timeout);
					self.timeout--;
				}
				else
				{
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
			if (this.isPaused)
			{
				this.countdown();
			}
			else
			{
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
				$.getJSON('index.php?option=com_resources&controller=import&task=progress&id=' + $('input[name="id"]').val(), function(data) {
					var percent = (data.processed / data.total) * 100;
					self.setProgress( percent );
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

			if (data.import == 'success')
			{
				$('.results-stats').html(data.records.length + ' records - ' + data.time + ' seconds');

				var results  = '';
				var source   = $("#resource-template").html();
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
			Handlebars.registerHelper('resource_data', function(record, options) {
				var html = '<table>';

				html += '<tr><th width="20%">ID</th><td>' + record.resource.id;
				if (record.resource.id)
				{
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
				html += '<tr><th width="20%">Attribs</th><td><pre>' +  Handlebars.helpers.print_json_data.call(this, record.resource.attribs) + '</pre></td></tr>';
				html += '<tr><th width="20%">Alias</th><td>' + record.resource.alias + '</td></tr>';
				html += '<tr><th width="20%">Ranking</th><td>' + record.resource.ranking + '</td></tr>';
				
				html += '</table>';
				return html;
			});
			
			// output resource data
			Handlebars.registerHelper('child_resource_data', function(children, options) {
				var html = '<table>';

				$.each(children, function(index, item) {
					html += '<tr><th width="20%">Title</th><td>' + item.title + '</td></tr>';
					html += '<tr><th width="20%">Link</th><td class="child-link"><span>' + item.path + '</span></td></tr>';
					html += '<tr><th colspan="2">&nbsp;</th></tr>';
				});

				html += '</table>';
				return html;
			});
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
	
	ResourceImport.init();
});