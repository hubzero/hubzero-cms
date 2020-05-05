/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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