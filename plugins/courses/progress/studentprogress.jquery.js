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
		$('.unit-details').hide();

		$('.unit-overview').on('click', function(e) {
			$(this).siblings('.unit-details').slideToggle(function() {
				$(this).parent('.unit-entry').toggleClass('unit-entry-active');
			});
		});

		var marker = progress.find('.current:last').attr('class').match(/unit_([0-9]+)/);
		progress.find('.unit').removeClass('current');

		// Display the current unit indicator
		progress.find('.unit-inner').each(function(idx) {
			var element = $(this);
			var unitId  = element.parent('.unit').attr('class').match(/unit_([0-9]+)/);

			console.log(unitId[1]);
			console.log(marker[1]);

			if (unitId[1] <= marker[1]) {
				setTimeout(function() {
					progress.find('.unit').removeClass('current');

					element.parent('.unit').addClass('current');
				}, (idx + 1) * 250);
			}
		});
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.initialize();
});