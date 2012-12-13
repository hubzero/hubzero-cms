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
		HUB.CoursesOutline.addNewItem();
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

			$(this).find('.asset-group-item:not(.add-new)').each(function(){
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

		$('.asset-group-item:not(.add-new)').each(function(){
			var high = $(this).height();
				high -= $(this).children('.uploadfiles').css('margin-top').replace("px", "");
				high -= $(this).children('.uploadfiles').css('margin-bottom').replace("px", "");
				high -= $(this).children('.uploadfiles').css('padding-bottom').replace("px", "");
				high -= $(this).children('.uploadfiles').css('padding-top').replace("px", "");
				high -= 4; // For borders?

			$(this).children('.uploadfiles').css('height', high);
		});
	},

	makeSortable: function()
	{
		var $ = this.jQuery;

		$(".sortable").sortable({
			placeholder: "placeholder",
			handle: '.sortable-handle',
			axis: "y",
			forcePlaceholderSize: true,
			revert: true,
			tolerance: 'pointer',
			opacity: '0.6',
			items: 'li:not(.add-new)',
			start: function(){
				$(".placeholder").css({'height': $(event.target).parent('asset-group-item').outerHeight(), 'margin': $(event.target).parent('asset-group-item').css('margin')});
			},
			update: function(){
				// @TODO: save new order to the database
			}
		});
	},

	makeTitlesEditable: function()
	{
		// Hide inputs and show plain text
		$('.editable').show();
		$('.asset-group-item-title-edit').hide();

		// Turn div "titles" into editable fields
		$(".unit").on('click', ".editable", function(event){
			event.stopPropagation();
			event.preventDefault();
			var parent = $(this).parent();
			var width  = $(this).width();

			$(this).hide();
			parent.find('.asset-group-item-title-edit').show();

			parent.find('input[type="text"]:first').css("width", width);
		});

		// Turn editable fields back into divs on cancel
		$(".unit").on('click', "input[type='reset']", function(event){
			event.stopPropagation();
			event.preventDefault();

			var parent = $(this).parents('.asset-group-item-container');

			// Hide inputs and show plain text
			parent.find('.editable').show();
			parent.find('.asset-group-item-title-edit').hide();

			parent.find('input[type="text"]:first').val(parent.find('.editable').html());
		});

		// Save editable fields on save
		$(".unit").on('click', "input[type='submit']", function(event){
			event.stopPropagation();
			event.preventDefault();

			var parent = $(this).parents(".asset-group-item-container");

			parent.find('.editable').html(parent.find('input[type="text"]:first').val());

			// Hide inputs and show plain text
			parent.find('.editable').show();
			parent.find('.asset-group-item-title-edit').hide();
		});
	},

	// Add a new item to the page
	addNewItem: function()
	{
		var $ = this.jQuery;

		// Add a new list item when clicking 'add'
		$(".unit").on('click', ".add-new", function(event){
			// Stop default event and propagation
			event.preventDefault();
			event.stopPropagation();

			// Get our class and grab HTML based on that
			var itemClass = $(this).attr('class').replace('add-new ', '');
			var text      = HUB.CoursesOutline.renderHtml(itemClass);

			// Insert in our HTML
			$(this).before(text);

			// Create a variable pointing to the new item just inserted
			var newAssetGroupItem = $(this).parent('.asset-group').find('.asset-group-item:not(.add-new):last');

			// Make that item look/function like the rest of them
			newAssetGroupItem.find('.uniform').uniform();
			newAssetGroupItem.find('.editable').show();
			newAssetGroupItem.find('.asset-group-item-title-edit').hide();

			// Set up file upload and update progress bar based on the recently added item
			HUB.CoursesOutline.setupFileUploader();
			HUB.CoursesOutline.showProgressIndicator();
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
							HUB.CoursesOutline.resizeFileUploader();

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
	},

	renderHtml: function(key)
	{
		// Sam: Keep the linter from complaining about multi-line strings
		/*jshint multistr:true */

		var $ = this.jQuery;

		key = key.replace(/-/g, '');

		var html = [];

		html['assetslist'] = ' \
			<ul class="assets-list"> \
				<li class="asset-item asset missing nofiles"> \
					No files \
					<span class="next-step-upload"> \
						Upload files &rarr; \
					</span> \
				</li> \
			</ul> \
		';

		// @FIXME: we need to get course_id and scope_id here
		html['assetgroupitem'] = ' \
			<li class="asset-group-item"> \
				<div class="sortable-handle"></div> \
				<div class="uploadfiles"> \
					<p>Drag files here to upload</p> \
					<form action="" class="uploadfiles-form"> \
						<input type="file" name="files[]" class="fileupload" multiple /> \
						<input type="hidden" name="course_id" value="" /> \
						<input type="hidden" name="scope_id" value="" /> \
					</form> \
					<div class="uploadfiles-progress"> \
						<div class="bar" style="width: 0%;"></div> \
					</div> \
				</div> \
				<div class="asset-group-item-container"> \
					<div class="asset-group-item-title editable title">New asset group</div> \
					<div class="asset-group-item-title-edit"> \
						<input class="uniform" type="text" value="New asset group" /> \
						<input class="uniform" type="submit" value="Save" /> \
						<input class="uniform" type="reset" value="Cancel" /> \
					</div> \
					' + html['assetslist'] + ' \
				</div> \
			</li> \
			<div class="clear"></div> \
		';

		return html[key];
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesOutline.initialize();
});