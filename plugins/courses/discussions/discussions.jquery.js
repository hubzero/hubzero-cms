/**
 * @package     hubzero-cms
 * @file        plugins/courses/forum/forum.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

_DEBUG = false;

HUB.Plugins.CoursesForum = {
	jQuery: jq,
	
	updateComments: function(data, addto) {
		var $ = this.jQuery;
		if (!addto) {
			addto = 'prepend'
		}
		if (data.length > 0) {
			var last = $('#lastchange'),
				last_id = $('#lastid');

			for ( var i = 0; i< data.length; i++ ) 
			{
				var item = data[i];
				if (item.parent && $('#t'+item.parent).length)
				{
					if (item.created > last.val()) {
						last.val(item.created);
					}
					last_id.val(item.id);

					if ($('#c' + item.id).length) {
						if (_DEBUG) {
							window.console && console.log('Comment #' + item.id + ' already exists!');
						}
						// Comment already exists!
						continue;
					}

					if ($('#t'+item.parent).length) {
						if (addto == 'prepend') {
							$('#t'+item.parent).prepend($(item.html).hide().fadeIn());
						} else {
							$('#t'+item.parent).append($(item.html).hide().fadeIn());
						}
					}
				}
			}
			
			jQuery(document).trigger('ajaxLoad');
		}
	},
	
	initialize: function() {
		var $ = this.jQuery
			container = $('#comments-container');

		if (container.length <= 0) {
			return;
		}

		var plgn   = HUB.Plugins.CoursesForum,
			cfrm   = $('#commentform'),
			abtn   = container.find('a.add'),
			feed   = container.find('div.comment-threads'),
			thread = container.find('div.comment-thread'),
			header = container.find('div.comments-toolbar span.comments');

		// Do some voodoo to get AJAX file upload working
		if (cfrm.length > 0) {
			$('<iframe src="about:blank?nocache=' + Math.random() + '" id="upload_target" name="upload_target" style="display:none;"></iframe>')
				.on('load', function(){
					data = jQuery.parseJSON($(this).contents().text());
					if (data) {
						if (_DEBUG) {
							window.console && console.log(data);
						}
						// Deactivate previous items
						$('#' + feed.data('active')).removeClass('active');
						feed.data('active', '');

						// Deactivate the add comment button
						abtn.removeClass('active');

						// Hide the "add comment" form and reset the fields
						cfrm.hide();
						cfrm.find('#field_comment').val('');
						cfrm.find('input[type=file]').val('');

						// Set some data so we know when/where to start pulling new results from
						feed.data('thread_last_change', data.thread.lastchange);
						feed.data('thread', data.thread.lastid);
						if (_DEBUG) {
							window.console && console.log('thread_last_change: ' + feed.data('thread_last_change') + ', thread: ' + feed.data('thread'));
						}

						header.text(data.thread.total + ' comments');

						// Update discussions list
						//feed.html(data.threads.html);
						if (data.threads.posts.length > 0) {
							$('#threads_lastchange').val(data.threads.lastchange);

							//var list = feed.find('div.category-results ul.discussions');//last = $('#threads_lastchange');
							//list.empty();

							for (var i = 0; i< data.threads.posts.length; i++) 
							{
								var item = data.threads.posts[i];

								if ($('#thread' + item.id).length) {
									// Comment already exists!
									continue;
								}
								var list = $('#category' + item.category_id);
								if (!list.length) {
									list = $('#categorynew');
								}
								if (!list.length) {
									continue;
								}
								if (list.find('li.comments-none').length) {
									list.empty();
								}
								list.prepend($(item.html).hide().fadeIn());
								//$(list.parent().parent()).find('span.count').text();

								if (item.mine) {
									var mine = $('#categorymine');
									if (mine.find('li.comments-none').length) {
										mine.empty();
									}
									mine.prepend($(item.html).hide().fadeIn());
								}

								//list.prepend($(item.html).hide().fadeIn());
							}
						}

						// Append thread data and fade it in
						thread.html(data.thread.html).hide().fadeIn();

						// Apply plugins to loaded content
						jQuery(document).trigger('ajaxLoad');
					}
				})
				.appendTo(cfrm);

			cfrm
				.attr('target', 'upload_target')
				.on('submit', function(e) {
					if ($('#field-category_id').val() == 0 || $('#field-category_id').val() == '0' || !$('#field-category_id').val()) {
						$(this).find('fieldset').append($('<p class="error">Please select a category.</p>'));
						e.stopImmediatePropagation();
						return false;
					}
					$(this).attr('action', $(this).attr('action').nohtml());
					return true;
				});
		}

		container
			// Toggle text and classes when clicking reply
			.on('click', 'a.reply', function (e) {
				e.preventDefault();

				var frm = $('#' + $(this).attr('rel'));

				if (frm.hasClass('hide')) {
					frm.removeClass('hide');
					$(this)
						.addClass('active')
						.text($(this).attr('data-txt-active'));
				} else {
					frm.addClass('hide');
					$(this)
						.removeClass('active')
						.text($(this).attr('data-txt-inactive'));
				}
			})
			// Add confirm dialog to delete links
			.on('click', 'a.delete', function (e) {
				e.preventDefault();

				var res = confirm('Are you sure you wish to delete this item?');
				if (res) {
					var srch = container.find('input.search').val();

					if (_DEBUG) {
						window.console && console.log('called:' + $(this).attr('href').nohtml() + (srch ? '&search=' + srch : ''));
					}
					$.getJSON($(this).attr('href').nohtml() + (srch ? '&search=' + srch : ''), {}, function(data){

						header.text(data.thread.total + ' comments');

						// Append data and fade it in
						thread.html(data.thread.html).hide().fadeIn();
						// Apply plugins to loaded content
						jQuery(document).trigger('ajaxLoad');
					});
				}
			})
			.on('click', 'a.sticky-toggle', function (e) {
				e.preventDefault();

				var el = $(this),
					par = el.parent().parent();

				if (_DEBUG) {
					window.console && console.log('called:' + el.attr('href').nohtml());
				}
				$.getJSON(el.attr('href').nohtml(), {}, function(data){
					par.toggleClass('stuck');
					$('#thread' + par.attr('data-thread')).toggleClass('stuck');
					if ($('#mine' + par.attr('data-thread')).length) {
						$('#mine' + par.attr('data-thread')).toggleClass('stuck');
					}

					if (par.hasClass('stuck')) {
						el
							.attr('href', el.attr('data-unstick-href'))
							.text(el.attr('data-unstick-txt'));
					} else {
						el
							.attr('href', el.attr('data-stick-href'))
							.text(el.attr('data-stick-txt'));
					}
				});
			});

		thread
			// Attach a click event for Iframe file upload
			.on('click', 'input[type=submit]', function (e) {
				var frm = $($(this).closest('form')),
					id = frm.attr('id') + '-iframe';

				// Iframe method for handling AJAX-like file uploads
				$('<iframe src="about:blank?nocache=' + Math.random() + '" id="' + id + '" name="' + id + '" style="display:none;"></iframe>')
					.on('load', function(){
						data = jQuery.parseJSON($(this).contents().text());

						if (data) {
							if (_DEBUG) {
								window.console && console.log(data);
							}
							feed.data('thread_last_change', data.thread.lastchange);
							feed.data('thread', data.thread.lastid);
							if (_DEBUG) {
								window.console && console.log('thread_last_change: ' + feed.data('thread_last_change') + ', thread: ' + feed.data('thread'));
							}

							if (data.thread.posts) {
								plgn.updateComments(data.thread.posts, 'append');
							/*if (data.threads.posts.length > 0) {
								var last = $('#threads_lastchange');

								for (var i = 0; i< data.threads.posts.length; i++) 
								{
									item = data.threads.posts[i];

									if ($('#thread' + item.id).length) {
										// Comment already exists!
										continue;
									}

									if (item.created > last.val()) {
										last.val(item.created);
									}

									feed.find('ul.discussions').prepend($(item.html).hide().fadeIn());
								}
							}*/
							}
						}
					})
					.appendTo(frm.parent());

				// Adjust the target and action for the form
				frm.attr('target', id)
					.on('submit', function() {
						var b = $(frm.parent().parent()).find('a.reply');
							b.removeClass('active')
								.text(b.attr('data-txt-inactive'));

						$(frm.parent()).addClass('hide');
						var act = frm.attr('action').split("?")[0];
						frm.attr('action', act.nohtml() + '&thread=' + feed.data('thread') + '&start_at=' + feed.data('thread_last_change'));
						return true;
					});
			});

		// Show add comment form when clicking "add" button
		abtn
			.on('click', function(e) {
				e.preventDefault();

				if ($(this).hasClass('active')) {
					return;
				}
				// Add active class
				$(this).addClass('active');

				header.text('Start a discussion');

				// Deactivate anything in the discussions list
				$('#' + feed.data('active')).removeClass('active');
				feed.data('active', '');

				$('#comments-new').hide();

				// Hide any displayed threads
				container.find('ol.comments').hide();

				// Fade in the comment form
				cfrm.fadeIn();
			});

		// Load thread in panel when clicking a discussion list item
		feed
			.on('click', 'div.category-header, div.thread-header', function (e) {
				e.preventDefault();
				$($(this).parent()).toggleClass('closed');
			})
			.on('click', 'li.thread a', function(e) {
				e.preventDefault();
			})
			.on('click', 'li.thread', function(e) {
				// Deactivate previous item
				$('#' + feed.data('active')).removeClass('active');
				feed.data('active', $(this).attr('id'));
				if (_DEBUG) {
					window.console && console.log('thread_active: ' + feed.data('active'));
				}

				$('#comments-new').hide();

				// Deactivate the add comment button
				abtn.removeClass('active');

				// Hide the "add comment" form
				cfrm.hide();

				// Active this item
				$(this).addClass('active');

				// Get thread data (there should always be at least one post)
				var thrd = $(this).attr('data-thread'),
					srch = container.find('input.search').val();

				if (_DEBUG) {
					window.console && console.log('called: ' + cfrm.attr('action').nohtml() + '&action=thread&thread=' + thrd + (srch ? '&search=' + srch : ''));
				}
				$.getJSON(cfrm.attr('action').nohtml() + '&action=thread&thread=' + thrd + (srch ? '&search=' + srch : ''), {}, function(data){
					// Set some data so we know when/where to start pulling new results from
					feed.data('thread_last_change', data.thread.lastchange);
					feed.data('thread', thrd);
					if (_DEBUG) {
						console.log('thread_last_change: ' + feed.data('thread_last_change') + ', thread: ' + feed.data('thread'));
					}

					header.text(data.thread.total + ' comments');

					// Append data and fade it in
					thread.html(data.thread.html).hide().fadeIn();
					// Apply plugins to loaded content
					jQuery(document).trigger('ajaxLoad');
				});
			})
			.scroll(function(){
				var shadow = $('.comment-threads-shadow');
				if (shadow.length <= 0) {
					var shadow = $('<div class="comment-threads-shadow"></div>').insertAfter($(this));
				}
				if ($(this).scrollTop() > 0) {
					if (!shadow.hasClass('scrolled')) {
						shadow.addClass('scrolled');
					}
				} else {
					shadow.removeClass('scrolled');
				}
			});

		// Make column resizable
		$(feed.parent()).resizable({
			handles: 'e',
			minWidth : '250',
			maxWidth : '400',
			resize: function(){
				var divTwo = $(this).next();
				divTwo.css('left', $(this).outerWidth() + 'px');
			}
		});

		container
			// Find all search forms
			.find('form.comments-search')
				.on('submit', function (e) {
					e.preventDefault();

					var self = $(this),
						input = self.find('input.search');

					// Remove any old search results
					/*var srch = feed.find('ul.search-results');
					if (srch.length > 0) {
						srch.remove();
					}*/
					// Hide the default list
					feed.find('div.category').hide();

					var srch = feed.find('div.search-results');
					/*if (srch.length > 0) {
						srch.remove();
					}*/

					if (_DEBUG) {
						window.console && console.log('searched: ' + self.attr('action').nohtml() + '&action=search&search=' + input.val());
					}

					// Perform the search
					$.getJSON(self.attr('action').nohtml() + '&action=search&search=' + input.val(), {}, function(data){
						if (data.threads.html) {
							//feed.prepend($(data.threads.html)); //.addClass('search-results'));
							//console.log(srch.find('div.category-content'));
							srch.find('div.category-content').html(data.threads.html);
							srch.show();
						}
					});
				})
				// Attach a key event to see if anyting has been typed
				.find('input.search').on('keyup', function () {
					var input = $(this);
					if (input.val() != '') {
						var clear = $('span.clear-search');
						// Create the clear button if it doesn't already exist
						if (clear.length <= 0) {
							var close = $('<span class="clear-search">x</span>')
								.on('click', function (e) {
									// Set the input value to blank
									input.val('');

									// Hide the clear button
									$(this).hide();

									// Remove any search results
									/*var srch = feed.find('ul.search-results');
									if (srch.length > 0) {
										srch.remove();
									}*/
									feed.find('div.search-results').hide();

									// And, finally, re-show the default list
									//feed.find('ul.discussions').show();
									feed.find('div.category-results').show();
								});
							// Add the close ubtton to the form
							$(input.parent()).append(close);
						} else {
							// Show the button
							clear.show();
						}
					}
				});

		// Notifier for new comments
		$('<div></div>')
			.attr('id', 'comments-new')
			.addClass('comments-notification')
			.text('0 new comments.')
			.on('click', function (e) {
				var cnew = $(this);

				if (_DEBUG) {
					window.console && console.log('called:' + cfrm.attr('action').nohtml() + '&action=posts&thread=' + feed.data('thread') + '&start_at=' + feed.data('thread_last_change'));
				}

				$.getJSON(cfrm.attr('action').nohtml() + '&action=posts&thread=' + feed.data('thread') + '&start_at=' + feed.data('thread_last_change'), {}, function(data){
					// Hide notification
					cnew.hide();

					// Append data
					plgn.updateComments(data.thread.posts, 'append');

					//feed.data('thread_last_change', $('#lastchange').val());
					feed.data('thread_last_change', data.thread.lastchange);
					feed.data('thread', data.thread.lastid);
					if (_DEBUG) {
						console.log('thread_last_change: ' + feed.data('thread_last_change') + ', thread: ' + feed.data('thread'));
					}
				});
			})
			.hide()
			.prependTo('div.comments-panel');

		// Notifier for new discussions
		$('<div></div>')
			.attr('id', 'threads-new')
			.addClass('comments-notification')
			.text('0 new discussions.')
			.on('click', function (e) {
				var tnew = $(this);

				if (_DEBUG) {
					window.console && console.log('called:' + cfrm.attr('action').nohtml() + '&action=threads&threads_start=' + $('#threads_lastchange').val());
				}

				$.getJSON(cfrm.attr('action').nohtml() + '&action=threads&threads_start=' + $('#threads_lastchange').val(), {}, function(data){
					// Hide notification
					tnew.hide();

					// Append data
					if (data.threads.posts.length > 0) {
						var last = $('#threads_lastchange');

						for (var i = 0; i< data.threads.posts.length; i++) 
						{
							item = data.threads.posts[i];

							if ($('#thread' + item.id).length) {
								// Comment already exists!
								continue;
							}

							if (item.created > last.val()) {
								last.val(item.created);
							}

							//feed.find('ul.discussions').prepend($(item.html).hide().fadeIn());
							feed.find('ul.discussions').prepend($(item.html));
						}
					}
				});
			})
			.hide()
			.prependTo('div.comments-feed');

		var api = '/api/forum/thread/?find=count&scope=' + $('#field-scope').val() + '&scope_id=' + $('#field-scope_id').val();

		// Heartbeat for checking for new posts
		setInterval(function () {
			if (_DEBUG) {
				window.console && console.log('called:' + api + '&thread=' + feed.data('thread') + '&start_at=' + feed.data('thread_last_change') + '&threads_start=' + $('#threads_lastchange').val());
			}

			$.getJSON(api + '&thread=' + feed.data('thread') + '&start_at=' + feed.data('thread_last_change') + '&threads_start=' + $('#threads_lastchange').val(), {}, function(data){
				if (data.code == 0) {
					if (data.count > 0) {
						$('#comments-new').text(data.count + ' new comments. Click to load.').fadeIn();
					}
					if (data.threads > 0) {
						$('#threads-new').text(data.threads + ' new discussions. Click to load.').fadeIn();
					}
				}
			});
		}, 60 * 1000);
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.CoursesForum.initialize();
});