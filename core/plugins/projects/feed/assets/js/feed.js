/**
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

//----------------------------------------------------------
// Project Micro Blog JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = false;

jQuery(document).ready(function($){
	_DEBUG = document.getElementById('system-debug') ? true : false;

	var container = $('#latest_activity');

	if (!container.length) {
		return;
	}

	container
		// Showing comment area
		.on('click', '.showc', function(e) {
			e.preventDefault();

			var id = $(this).attr('id').replace('addc_', '');
			var acid = $('#commentform_' + id);

			if (acid.length) {
				if (acid.hasClass('hidden')) {
					acid.removeClass('hidden');

					$(this).text($(this).attr('data-active'));

					/*
					var frm = acid.find('form');
					frm.addClass('focused');

					var txt = acid.find('.commentarea')[0];
					$(txt)
						.focus();*/
				} else {
					acid.addClass('hidden');

					$(this).text($(this).attr('data-inactive'));
				}
			}
		})
		// Show more
		.on('click', '.more-content', function(e) {
			e.preventDefault();

			var shortBody = $(this).parent().parent().find("div.body");
			var longBody  = $(this).parent().parent().find("div.fullbody");

			$(shortBody).addClass('hidden');
			$(longBody).removeClass('hidden');
		})
		// Confirm delete
		.on('click', '.delit', function(e) {
			e.preventDefault();

			if (HUB.Projects) {
				HUB.Projects.addConfirm($(link), 'Permanently delete this entry?', 'yes, delete', 'cancel');

				if ($('#confirm-box')) {
					$('#confirm-box').css('margin-left', '-100px');
				}
			}
		})
		// Have submit button reset the form
		.on('click', '.c-submit', function(e) {
			var cid = $(this).attr('id').replace('cs_', '');
			var caid = '#ca_' + cid;
			var acid = '#commentform_' + cid;

			if ($(caid).length) {
				if ($(caid).val() == '') {
					e.preventDefault();
					$(acid).addClass('hidden');
				}
			}
		})
		// Submit comments on enter
		.on('keypress', '.commentarea', function(e){
			if (e.keyCode == 13) {
				e.preventDefault();
			}
		})
		.on('keyup', '.commentarea', function(e){
			if (e.keyCode == 13) {
				// Submit if not empty
				if ($(this).val() != '') {
					$(this)
						.closest('form')
						.submit();
				}
			}
		})
		.on('focus', '.commentarea', function(e){
			$(this).closest('form').addClass('focused');
		})
		.on('blur', '.commentarea', function(e){
			if ($(this).val() == '') {
				$(this).closest('form').removeClass('focused');
			}
		});

	// Comment form
	$('.commentarea').each(function(i, item) {
		if ($(item).val() == '') {
			$(item).closest('form').removeClass('focused');
		}
	});

	// Blog entry form
	var blogentry = $('#blogForm');
	if (blogentry.length) {
		blogentry.removeClass('focused');

		var blogtxt = blogentry.find('textarea');

		blogtxt
			.on('blur', function(e) {
				if ($(this).val() == '') {
					blogentry.removeClass('focused');
				}
			})
			.on('focus', function(e) {
				blogentry.addClass('focused');
			});
	}

	// Show more updates
	if ($('#more-updates').length && $('#pid').length) {
		$('#more-updates').on('click', function(e) {
			e.preventDefault();

			var link = $('#more-updates').find("a");

			if (link.length) {
				var url = link.attr('href') + '&no_html=1&ajax=1&action=update';

				$.get(url, {}, function(data) {
					$('#latest_activity').html(data);

					$('.commentarea').each(function(i, item) {
						if ($(item).val() == '') {
							$(item).removeClass('focused');
						}
					});
				});
			}
		});
	}

	// New content URL
	var url = container.attr('data-base').nohtml() + '&ajax=1&action=update&recorded=';

	// Frequency to poll for new content (in seconds)
	// Default to 60 if nothing found
	var freq = container.attr('data-frequency');
	freq = (freq ? freq : 60);

	setInterval(function () {
		var first = container.find('.activity-item');
		if (first.length) {
			url += first.attr('data-recorded');
		}

		if (_DEBUG) {
			window.console && console.log('called:' + url);
		}

		$.getJSON(url, {}, function(data){
			if (data.activities.length <= 0) {
				if (_DEBUG) {
					window.console && console.log('No results found');
				}
				return;
			}

			for (var i = 0; i< data.activities.length; i++)
			{
				var item = data.activities[i];

				if (item.class = 'quote' && $('#comments_'+item.parent).length) {
					if ($('#c_' + item.eid).length) {
						if (_DEBUG) {
							window.console && console.log('Comment #' + item.eid + ' already exists!');
						}
						// Comment already exists!
						continue;
					}

					$('#comments_' + item.parent).append($(item.body).hide().fadeIn());

					if ($('#li_' + item.parent).length) {
						$('#li_' + item.parent).attr('data-recorded', item.activity.recorded);
					}

					continue;
				}

				if ($('#li_' + item.eid).length) {
					if (_DEBUG) {
						window.console && console.log('Activity #' + item.eid + ' already exists!');
					}
					// Activity already exists!
					continue;
				}

				$('#activity-feed').prepend($(item.body).hide().fadeIn());
			}

			setTimeout(function() {
				$('.newitem').removeClass('newitem');
			}, 5 * 1000);

			jQuery(document).trigger('ajaxLoad');
		});
	}, freq * 1000);
});