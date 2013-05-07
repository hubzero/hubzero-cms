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
		$('.instructor .unit-fill, .instructor .headers span').tooltip({
			predelay: 250
		});

		$('.instructor .grade-policy-inner').hide();

		$('.instructor .grade-policy-header').click(function() {
			$(this).siblings('.grade-policy-inner').slideToggle();
			$(this).find('span.details').fadeToggle();
		});

		var sliders = $('.slider');

		sliders.each(function() {
			var t = $(this);
			t.attr('readonly', true);

			var slider = $('<div class="slider"></div>').insertAfter(t).slider({
				min   : 0,
				max   : 100,
				value : $(this).val(),
				slide : function( event, ui ) {
					t.val(ui.value);
				}
			});
		});
	} // end initialize
};

jQuery(document).ready(function($){
	HUB.Plugins.CoursesProgress.initialize();
});