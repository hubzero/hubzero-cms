/**
 * @package     hubzero-cms
 * @file        plugins/courses/progress/studentprogress.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
//  Forum scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.CoursesProgress = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery;

		var progress = $('.progress-timeline');

		// Hide the grade details
		$('.grade-details').hide();
		$('.toggle-grade-details').removeClass('toggle-grade-details-open');

		$('.toggle-grade-details').on('click', function(e) {
			e.preventDefault();
			$('.grade-details').slideToggle();
			$(this).toggleClass('toggle-grade-details-open');
		});

		progress.find('.unit').removeClass('current');

		// Display the current unit indicator
		progress.find('.past').each(function(idx) {
			var element = $(this);

			setTimeout(function() {
				progress.find('.unit').removeClass('current');

				element.parent('.unit').addClass('current');
			}, (idx + 1) * 250);
		});
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.initialize();
});