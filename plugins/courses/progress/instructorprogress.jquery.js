/**
 * @package     hubzero-cms
 * @file        plugins/courses/progress/instructorprogress.jquery.js
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

		$('.instructor').on('click', '.student-name', function(e) {
			e.preventDefault();
			$(this).parents('.student').find('.student-details').slideToggle('slow', function() {
				$(this).parents('.student').toggleClass('active', 150, 'linear');
			});
		});

		$('.instructor').on('click', '.progress-container', function(e) {
			e.preventDefault();
			$(this).parents('.student').find('.student-details').slideToggle('slow', function() {
				$(this).parents('.student').toggleClass('active', 150, 'linear');
			});
		});

		// Add tooltips to units
		$('.instructor .unit-fill').tooltip({
			predelay: 500
		});
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.initialize();
});