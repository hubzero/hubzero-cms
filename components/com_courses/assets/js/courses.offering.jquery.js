/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.offering.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.CoursesOffering =
{
	jQuery: jq,

	initialize: function()
	{
		//HUB.CoursesOffering.setupMasonry();
		HUB.CoursesOffering.setupAccordian();
	},

	setupAccordian: function()
	{
		var $ = this.jQuery;

		// Hide all of the units except for the first one
		/*$('.details').not(':first').hide();
		$('.unit-content h3:first').addClass('unit-content-active');

		// Set the timeline height
		$('.timeline').css('height', $('#course-outline').height());
*/
		// On title click, toggle display of content
		$('.unit-content').on('click', 'h3', function(){
			//$('.timeline').css('height', 0);
			if ($(this).hasClass('unit-content-available')) {
				$(this).siblings('.unit-availability').find('.details').slideUp(500, function() { //
					//$('.timeline').animate({'height': $('#course-outline').height()}, 250);
					//$('html, body').animate({scrollTop: $(this).parents('#content').offset().top - 10}, 1000);
				});
				$(this).removeClass('unit-content-available');
			} else {
				//$('.details').slideUp(500);
				//$('.unit-content h3').removeClass('unit-content-active');
				$(this).siblings('.unit-availability').find('.details').slideDown(500, function() { //.siblings('.unit-availability')
					//$(this).masonry('reload');
					//$('.timeline').animate({'height': $('#course-outline').height()}, 250);
					//$('html, body').animate({scrollTop: $(this).parents('.unit-content').offset().top - 10}, 1000);
				});

				// Toggle class for arrow (active gives down arrow indicating expanded list)
				$(this).addClass('unit-content-available');
			}
		});
	},

	setupMasonry: function()
	{
		var $ = this.jQuery;

		$('.details').masonry({
			itemSelector: '.weeksection',
			columnWidth: function(container) {
				return container / 2;
			}
		});
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesOffering.initialize();
});