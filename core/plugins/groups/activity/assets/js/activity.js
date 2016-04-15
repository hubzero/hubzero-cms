/**
 * @package     hubzero-cms
 * @file        plugins/groups/activity/activity.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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

jQuery(document).ready(function(jq){
	var $ = jq,
		container = $('.activity-feed');

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
});
