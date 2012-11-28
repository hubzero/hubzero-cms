/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.outline.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Courses outline javascript
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.CoursesOutline = {
	jQuery: jq,
	
	initialize: function() 
	{
		HUB.CoursesOutline.toggleUnits();
		HUB.CoursesOutline.showProgressIndicator();
		HUB.CoursesOutline.makeSortable();
	},

	toggleUnits: function()
	{
		var $ = this.jQuery;

		// Establish Variable
		var unit      = $('.unit-item');
		var title     = $('.unit-title');
		var assetlist = $('.asset-group-type-list');

		// Add the active class to the first unit (giving the expanded down arrow next to the title)
		title.first().toggleClass('unit-title-active');
		// Hide all of the units except for the first one
		assetlist.not(':first').hide();

		// On title click, toggle display of content
		$('.outline-main').on('click', '.unit-title', function(){
			$(this).siblings('.asset-group-type-list').slideToggle(500);

			// Toggle class for arrow (active gives down arrow indicating expanded list)
			$(this).toggleClass('unit-title-active');
		});
	},

	showProgressIndicator: function()
	{
		var $ = this.jQuery;

		// Instantiate variables
		var progressbar = $('.progress-indicator');
		var unit        = $('.unit-item');

		unit.each(function(){
			var count       = 0;
			var haveitems   = 0;
			var percentage  = 0;
			var pclass      = 'stop';

			$(this).find('.asset-group-item').each(function(){
				count += 1;

				if($(this).find('.asset-item').length >= 1){
					haveitems += 1;
					$(this).addClass('hasitem');
				} else {
					$(this).addClass('noitem');
				}

				// Calculate percentage of asset groups with assets
				percentage = (haveitems/count) * 100;
			});

			if((percentage >= 1) && (percentage <= 33)) {
				pclass = 'stop';
			} else if((percentage >= 34) && (percentage <= 66)) {
				pclass = 'yield';
			} else if((percentage >= 67) && (percentage <= 100)) {
				pclass = 'go';
			} else {
				percentage = 1;
				pclass     = 'stop';
			}

			$(this).find('.progress-indicator').addClass(pclass);

			// Setup jquery ui progress bar
			$(this).find('.progress-indicator').progressbar({
				value: percentage
			});
		});

		HUB.CoursesOutline.toggleFileUploader();
	},

	toggleFileUploader: function()
	{
		var $ = this.jQuery;

		//$('.asset-group-item').not('.hasitem').find('.uploadfiles').show();
	},

	makeSortable: function()
	{
		var $ = this.jQuery;

		$(".sortable").sortable({
			placeholder: "placeholder",
			forcePlaceholderSize: true,
			revert: true,
			tolerance: 'pointer',
			opacity: '0.8',
			items: 'li:not(.add-new)',
			axis: 'y',
			start: function(){
				$(".placeholder").css('height', $(event.target).height());
			}
		});

		// Turn div "titles" into editable fields
		$(".sortable").on('click', "div.editable", function(event){
			event.stopPropagation();
			event.preventDefault();
			var parent = $(this).parent();
			var width  = $(this).width();
			var value  = $(this).html();

			$(this).replaceWith('<div><input type="text" value="' + value + '" />');

			var input = parent.find('input[type="text"]:first');
			input.css("width", width+20);
			input.after('<input type="submit" value="Save" /><input type="reset" value="Cancel" /></div>');

			// Store away the previous val in the div data
			div = parent.find("div:first");
			div.data('prevVal', value);
		});

		// Turn editable fields back into divs on cancel
		$(".sortable").on('click', "input[type='reset']", function(){
			event.preventDefault();
			var parent = $(this).parent("div");
			parent.replaceWith('<div class="asset-group-item-title editable title">' + parent.data('prevVal') + '</div>');
		});

		// Save editable fields on save
		$(".sortable").on('click', "input[type='submit']", function(event){
			event.preventDefault();
			var parent = $(this).parent("div");
			parent.replaceWith('<div class="asset-group-item-title editable title">' + parent.find("input[type='text']:first").val() + '</div>');
		});

		// Add a new list item when clicking 'add'
		$(".sortable").on('click', ".add-new", function(event){
			event.preventDefault();
			event.stopPropagation();

			var text  = '<li class="unit-item">';
				text += '<div class="title unit-title">New Unit</div>';
				text += '<div class="progress-container">';
				text += '<div class="progress-indicator"></div>';
				text += '</div>';
				text += '<div class="clear"></div>';
				text += '</li>';

			$(this).before(text);

			$(this).prev('li').find('.progress-indicator').addClass('stop');

			// Setup jquery ui progress bar
			$(this).prev('li').find('.progress-indicator').progressbar({
				value: 1
			});
		});
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesOutline.initialize();
});