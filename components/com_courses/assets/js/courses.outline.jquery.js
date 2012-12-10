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
		HUB.CoursesOutline.makeTitlesEditable();
		HUB.CoursesOutline.makeUniform();
		HUB.CoursesOutline.togglePublished();
		HUB.CoursesOutline.setupFileUploader();
		HUB.CoursesOutline.resizeFileUploader();
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

				if($(this).find('.asset-item').not('.nofiles, .notpublished').length >= 1){
					haveitems += 1;
				}

				// Calculate percentage of asset groups with assets
				percentage = (haveitems/count) * 100;
			});

			if((percentage >= 1) && (percentage <= 49)) {
				pclass = 'stop';
			} else if((percentage >= 50) && (percentage <= 99)) {
				pclass = 'yield';
			} else if(percentage == 100) {
				pclass = 'go';
			} else {
				percentage = 1;
				pclass     = 'stop';
			}

			$(this).find('.progress-indicator').removeClass('stop go yield').addClass(pclass);

			$(this).find('.progress-indicator').progressbar({
				value: percentage
			});

		});
	},

	resizeFileUploader: function()
	{
		var $ = this.jQuery;

		$('.asset-group-item').each(function(){
			var high = $(this).height();
				high -= $(this).children('.uploadfiles').css('margin-top').replace("px", "");
				high -= $(this).children('.uploadfiles').css('margin-bottom').replace("px", "");
				high -= $(this).css('padding-top').replace("px", "");
				high -= $(this).css('padding-bottom').replace("px", "");

			$(this).children('.uploadfiles').css('height', high);
		});
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
			start: function(){
				$(".placeholder").css('height', $(event.target).height());
			}
		});
	},

	makeTitlesEditable: function()
	{
		// Hide inputs and show plain text
		$('.editable').show();
		$('.asset-group-item-title-edit').hide();

		// Turn div "titles" into editable fields
		$(".sortable").on('click', ".editable", function(event){
			event.stopPropagation();
			event.preventDefault();
			var parent = $(this).parent();
			var width  = $(this).width();

			$(this).hide();
			parent.find('.asset-group-item-title-edit').show();

			parent.find('input[type="text"]:first').css("width", width);
		});

		// Turn editable fields back into divs on cancel
		$(".sortable").on('click', "input[type='reset']", function(event){
			event.stopPropagation();
			event.preventDefault();

			var parent = $(this).parents('.asset-group-item-container');

			// Hide inputs and show plain text
			parent.find('.editable').show();
			parent.find('.asset-group-item-title-edit').hide();

			parent.find('input[type="text"]:first').val(parent.find('.editable').html());
		});

		// Save editable fields on save
		$(".sortable").on('click', "input[type='submit']", function(event){
			event.stopPropagation();
			event.preventDefault();

			var parent = $(this).parents(".asset-group-item-container");

			parent.find('.editable').html(parent.find('input[type="text"]:first').val());

			// Hide inputs and show plain text
			parent.find('.editable').show();
			parent.find('.asset-group-item-title-edit').hide();
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
	},

	makeUniform: function()
	{
		var $ = this.jQuery;

		$('.uniform').uniform();
	},

	togglePublished: function()
	{
		var $ = this.jQuery;

		var replacement = '';

		// When clicking publish checkbox
		$('.unit').on('click', '.published-checkbox', function(){
			var label = $(this).parents('label').find('span.published-label-text');
			var item  = $(this).parents('.asset-item');
			var id    = item.find('.asset_id').val();

			// Create ajax call to change info in the database
			// @FIXME: remove 'nanotransistors'
			$.ajax({
				url: "/courses/nanotransistors/togglepublished",
				data: "asset_id="+id,
				dataType: "json",
				type: 'POST',
				cache: false,
				success: function(data){
					if(data.success) {
						if(label.html() == 'Published') {
							replacement = 'Mark as reviewed and publish?';
							item.removeClass('published').addClass('notpublished');
						} else {
							replacement = 'Published';
							item.removeClass('notpublished').addClass('published');
						}
						label.html(replacement);

						HUB.CoursesOutline.showProgressIndicator();
					} else {
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.error);
					}
				}
			});
		});
	},

	setupFileUploader: function()
	{
		var $ = this.jQuery;

		// Disable default browser drag and drop event
		$(document).bind('drop dragover', function (e) {
			e.preventDefault();
		});

		// Hide the file input
		$('.uploadfiles input').hide();

		// Set up file uploader on our file upload boxes
		$('.uploadfiles').each(function(){
			var assetslist = $(this).parent('.asset-group-item').find('.assets-list');
			var bar        = $(this).find('.bar');

			$(this).fileupload({
				dropZone: $(this),
				dataType: 'json',
				// @FIXME: remove 'nanotransistors'
				url: '/courses/nanotransistors/assetupload',
				done: function (e, data) {
					if(data.result.success) {
						if(assetslist.find('li:first').hasClass('nofiles'))
						{
							assetslist.find('li:first').remove();
						}
						$.each(data.result.files, function (index, file) {
							var li = '';
								li += '<li class="asset-item asset ' + file.type + ' notpublished">';
								li += file.filename;
								li += ' (<a class="" href="' + file.url + '">preview</a>)';
								li += '<span class="next-step-publish">';
								li += '<label class="published-label" for="published">';
								li += '<span class="published-label-text">Mark as reviewed and publish?</span>';
								li += '<input class="uniform published-checkbox" name="published" type="checkbox" />';
								li += '<input type="hidden" class="asset_id" name="' + file.id + '[id]" value="' + file.id + '" />';
								li += '</label>';
								li += '</span>';
								li += '</li>';

							assetslist.append(li);

							assetslist.find('.uniform:last').uniform();
							HUB.CoursesOutline.showProgressIndicator();

							// Reset progress bar after 2 seconds
							setTimeout( function(){
								bar.css('width', '0');
							},2000);
						});
					} else {
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.result.error);

						// Reset progress bar
						bar.css('width', '0');
					}
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$(this).find('.bar').css(
						'width',
						progress + '%'
					);
				}
			});
		});
	},

	errorMessage: function(message)
	{
		var $ = this.jQuery;

		var info = $('#info-message');
		var msg = '<p>' + message + '</p>';

		// Set dialog box message and title
		info.html(msg);
		info.attr('title','Error');
		info.dialog({
			modal : true
		});
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesOutline.initialize();
});