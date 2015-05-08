/**
 * @package     hubzero-cms
 * @file        plugins/courses/progress/assessmentdetails.jquery.js
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

jQuery(document).ready(function($){
	HUB.Plugins.CoursesAssessmentDetails.initialize();
});

HUB.Plugins.CoursesAssessmentDetails = {
	jQuery: jq,

	initialize: function() {
		var $ = this.jQuery,
			b = $('.response-bar-inner');

		b.each(function ( idx, val ) {
			var v = $(val);
			v.animate({width : v.data('width')+'%'}, 500);
		});
	} // end initialize
};