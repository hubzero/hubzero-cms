/**
 * @package     hubzero-cms
 * @file        plugins/members/activity/assets/js/activity.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = false;

jQuery(document).ready(function(jq){
	var $ = jq,
		container = $('.activity-feed');

	_DEBUG = document.getElementById('system-debug') ? true : false;

	// Infinite scroll
	if (container.length) {
		container.infinitescroll({
				navSelector  : '.list-footer',    // selector for the paged navigation
				nextSelector : '.list-footer .next a',  // selector for the NEXT link (to page 2)
				itemSelector : '.activity-feed li.activity',     // selector for all items you'll retrieve
				loading: {
					finishedMsg: 'No more pages to load.'
				},
				path: function(index) {
					var path = $('.list-footer .next a').attr('href');
					limit = path.match(/limit[-=]([0-9]*)/).slice(1);
					start = path.match(/start[-=]([0-9]*)/).slice(1);
					console.log(path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit)));
					return path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit));
				},
				debug: true
			},
			// Trigger Masonry as a callback
			function(newElements) {
				// Hide new items while they are loading
				var $newElems = $(newElements).css({ opacity: 0 });

				// Show elems now they're ready
				$newElems.animate({ opacity: 1 });
			}
		);
	}

	container
		// Bookmark button
		.on('click', '.icon-starred', function (e){
			e.preventDefault();

			var bt = $(this),
				el = $('#' + bt.attr('data-id'));

			if (_DEBUG) {
				console.log('Calling: ' + bt.attr('href').nohtml());
			}

			$.getJSON(bt.attr('href').nohtml(), function (response){
				if (response.success) {
					el.toggleClass('starred');

					if (response.starred) {
						bt
							.attr('href', bt.attr('data-hrf-active'))
							.attr('title', bt.attr('data-txt-active'))
							.text(bt.attr('data-txt-active'));
					} else {
						bt
							.attr('href', bt.attr('data-hrf-inactive'))
							.attr('title', bt.attr('data-txt-inactive'))
							.text(bt.attr('data-txt-inactive'));
					}
				}
			});
		})
		// Delete button
		.on('click', '.icon-delete', function (e){
			e.preventDefault();

			var bt = $(this),
				res = confirm(bt.attr('data-txt-confirm'));

			if (!res) {
				return;
			}

			var el = $('#' + bt.attr('data-id'));

			el.addClass('processing'); //.fadeIn()

			if (_DEBUG) {
				console.log('Calling: ' + bt.attr('href').nohtml());
			}

			$.getJSON(bt.attr('href').nohtml(), function (response){
				if (response.success) {
					el.slideUp(250, function(){
						$(this).remove();
					});
				} else {
					el.removeClass('processing');
				}
			});
		})
		// Reply button
		.on('click', '.icon-reply', function (e) {
			e.preventDefault();

			var bt = $(this),
				frm = $('#' + bt.attr('rel'));

			if (frm.hasClass('hide')) {
				frm.removeClass('hide');
				bt
					.addClass('active')
					.attr('title', bt.attr('data-txt-active'))
					.text(bt.attr('data-txt-active'));
			} else {
				frm.addClass('hide');
				bt
					.removeClass('active')
					.attr('title', bt.attr('data-txt-inactive'))
					.text(bt.attr('data-txt-inactive'));
			}
		});
});
