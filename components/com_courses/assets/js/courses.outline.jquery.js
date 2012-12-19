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
		HUB.CoursesOutline.setupErrorMessage();
	},

	toggleUnits: function()
	{
		var $ = this.jQuery;

		// Establish Variable
		var unit      = $('.unit-item');
		var title     = $('.unit-title-arrow');
		var assetlist = $('.asset-group-type-list');

		// Add the active class to the first unit (giving the expanded down arrow next to the title)
		title.first().addClass('unit-title-arrow-active');
		// Hide all of the units except for the first one
		assetlist.not(':first').hide();

		// On title click, toggle display of content
		$('.outline-main').on('click', '.unit-title-arrow', function(){
			if($(this).hasClass('unit-title-arrow-active')){
				$(this).siblings('.asset-group-type-list').slideUp(500);
				$(this).removeClass('unit-title-arrow-active');
			} else {
				$('.asset-group-type-list').slideUp(500);
				$('.unit-title-arrow').removeClass('unit-title-arrow-active');
				$(this).siblings('.asset-group-type-list').slideDown(500);

				// Toggle class for arrow (active gives down arrow indicating expanded list)
				$(this).addClass('unit-title-arrow-active');
			}
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
				high -= 4; // For borders - this is hacky

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
			revert: false,
			tolerance: 'pointer',
			opacity: '0.6',
			items: 'li:not(.add-new)',
			start: function(){
				// Style the placeholdwer based on the size of the item grabbed
				$(".placeholder").css({
					'height': $(event.target).parent('asset-group-item').outerHeight(),
					'margin': $(event.target).parent('asset-group-item').css('margin')
				});
			},
			update: function(){
				// Save new order to the database
				var sorted = $(this).sortable('serialize');

				// Update the asset group ordering
				$.ajax({
					url: '/api/courses/assetgroupreorder',
					data: sorted,
					dataType: "json",
					type: 'POST',
					cache: false,
					statusCode: {
						201: function(data){
							// Report a message?
						},
						401: function(data){
							// Display the error message
							HUB.CoursesOutline.errorMessage(data.responseText);
						},
						404: function(data){
							HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
						},
						500: function(data){
							// Display the error message
							HUB.CoursesOutline.errorMessage(data.responseText);
						}
					}
				});
			}
		});
	},

	makeTitlesEditable: function()
	{
		// Hide inputs and show plain text
		$('.toggle-editable').show();
		$('.title-edit').hide();

		// Turn div "titles" into editable fields
		$(".unit").on('click', ".toggle-editable", function(event){
			event.stopPropagation();
			event.preventDefault();
			var parent = $(this).parents('li:first');
			var width  = $(this).width();
			var title  = parent.find('.title-edit:first');

			// Show the form
			$(this).hide();
			title.show();

			// Set the width of the form text input
			title.find('.title-text').css("width", width);
		});

		// Turn editable fields back into divs on cancel
		$(".unit").on('click', ".title-reset", function(event){
			event.stopPropagation();
			event.preventDefault();

			var parent = $(this).parents('li:first');
			var toggle = parent.find('.toggle-editable:first');
			var title  = parent.find('.title-edit:first');

			// Hide inputs and show plain text
			toggle.show();
			title.hide();

			title.find('.title-text:first').val(toggle.html());
		});

		// Save editable fields on save
		$(".unit").on('submit', '.title-form', function(event){
			event.stopPropagation();
			event.preventDefault();

			var form   = $(this);
			var parent = $(this).parents('li:first');

			// Update the asset group in the database
			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				dataType: "json",
				type: 'POST',
				cache: false,
				statusCode: {
					201: function(data){
						parent.find('.toggle-editable:first').html(parent.find('.title-text:first').val());

						// Hide inputs and show plain text
						parent.find('.toggle-editable:first').show();
						parent.find('.title-edit:first').hide();
					},
					401: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					},
					404: function(data){
						HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
					},
					500: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					}
				}
			});
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
			var addNew    = $(this);
			var itemClass = $(this).attr('class').replace('add-new ', '');
			var form      = $(this).find('form');
			var text      = '';
			var key       = itemClass.replace(/-/g, '');

			//addNew.before(_.template(HUB.CoursesOutline.Templates.test, {name: 'sam'}));

			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				dataType: "json",
				type: 'POST',
				cache: false,
				statusCode: {
					201: function(data){
						if(itemClass == 'asset-group-item') {
							// Insert in our HTML (uses "underscore.js")
							text = _.template(HUB.CoursesOutline.Templates.assetgroupitem, data);
							addNew.before(text);

							// Create a variable pointing to the new item just inserted
							var newAssetGroupItem = addNew.parent('.asset-group').find('.asset-group-item:not(.add-new):last');

							// Make that item look/function like the rest of them
							newAssetGroupItem.find('.uniform').uniform();
							newAssetGroupItem.find('.toggle-editable').show();
							newAssetGroupItem.find('.title-edit').hide();

							// Set up file upload and update progress bar based on the recently added item
							HUB.CoursesOutline.setupFileUploader();
							HUB.CoursesOutline.showProgressIndicator();

							// Refresh the sortable list
							$('.sortable').sortable('refresh');

							// Finally, show the new item
							newAssetGroupItem.slideDown('fast', 'linear');
						}
						else if(itemClass == 'unit-item') {
							// Insert in our HTML (uses "underscore.js")
							text = _.template(HUB.CoursesOutline.Templates.unititem, data);
							addNew.before(text);

							// Create a variable pointing to the new item just inserted
							var newUnit = addNew.parent('.unit').find('.unit-item:not(.add-new):last');

							// Make that item look/function like the rest of them
							newUnit.find('.uniform').uniform();
							newUnit.find('.toggle-editable').show();
							newUnit.find('.title-edit').hide();

							// Set up file upload and update progress bar based on the recently added item
							HUB.CoursesOutline.setupFileUploader();
							HUB.CoursesOutline.showProgressIndicator();

							// Refresh the sortable list
							HUB.CoursesOutline.makeSortable();

							$('.asset-group-type-list').delay(500).slideUp(500, function(){
								$('.unit-title-arrow').removeClass('unit-title-arrow-active');
								newUnit.find('.asset-group-type-list').slideDown(500);

								// Toggle class for arrow (active gives down arrow indicating expanded list)
								newUnit.find('.unit-title-arrow').addClass('unit-title-arrow-active');
							});
						}
					},
					401: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					},
					404: function(data){
						HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
					},
					500: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					}
				}
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
			var form  = $(this).parents('form');
			var label = form.find('span.published-label-text');
			var item  = form.parent('.asset-item');

			// Create ajax call to change info in the database
			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				dataType: "json",
				type: 'POST',
				cache: false,
				statusCode: {
					201: function(data){
						if(label.html() == 'Published') {
							replacement = 'Mark as reviewed and publish?';
							item.removeClass('published').addClass('notpublished');
						} else {
							replacement = 'Published';
							item.removeClass('notpublished').addClass('published');
						}
						label.html(replacement);

						HUB.CoursesOutline.showProgressIndicator();
					},
					401: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					},
					404: function(data){
						HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
					},
					500: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);
					}
				}
			});
		});
	},

	setupFileUploader: function()
	{
		// Sam: Keep the linter from complaining about multi-line strings
		/*jshint multistr:true */

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
			var barBorder  = $(this).find('.bar-border');

			$(this).fileupload({
				dropZone: $(this),
				dataType: 'json',
				statusCode: {
					201: function(data){
						if(assetslist.find('li:first').hasClass('nofiles'))
						{
							assetslist.find('li:first').remove();
						}
						$.each(data.files, function (index, file) {
							var li = ' \
								<li class="asset-item asset ' + file.type + ' notpublished"> \
									' + file.filename + ' \
									(<a class="" href="' + file.url + '">preview</a>) \
									<form action="/api/courses/assettogglepublished" class="next-step-publish"> \
										<span class="next-step-publish"> \
										<label class="published-label" for="published"> \
											<span class="published-label-text">Mark as reviewed and publish?</span> \
											<input class="uniform published-checkbox" name="published" type="checkbox" /> \
											<input type="hidden" class="asset_id" name="asset_id" value="' + file.id + '" /> \
											<input type="hidden" name="course_id" value="' + file.course_id + '" /> \
										</label> \
										</span> \
									</form> \
								</li> \
							';

							assetslist.append(li);

							assetslist.find('.uniform:last').uniform();
							HUB.CoursesOutline.showProgressIndicator();
							HUB.CoursesOutline.resizeFileUploader();

							// Reset progress bar after 2 seconds
							setTimeout( function(){
								bar.css('width', '0');
								barBorder.css('border', 'none');
							},2000);
						});
					},
					401: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);

						// Reset progress bar
						bar.css('width', '0');
						barBorder.css('border', 'none');
					},
					404: function(data){
						HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');

						// Reset progress bar
						bar.css('width', '0');
						barBorder.css('border', 'none');
					},
					500: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);

						// Reset progress bar
						bar.css('width', '0');
						barBorder.css('border', 'none');
					}
				},
				drop: function(e) {
					$(this).find('.bar-border').css({
						'border': '1px solid #999'
					});
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

	setupErrorMessage: function()
	{
		var $ = this.jQuery;

		var errorBox   = $('.error-box');
		var errorClose = $('.error-close');

		errorClose.on('click', function(){
			errorBox.slideUp('fast');
		});
	},

	errorMessage: function(message)
	{
		var $ = this.jQuery;

		var errorBox = $('.error-box');
		var error    = $('.error-message');

		error.html(message);
		errorBox.slideDown('fast');

	},

	Templates: {
		assetgroupitem : [
			'<li class="asset-group-item" id="assetgroupitem_<%= assetgroup_id %>" style="<%= assetgroup_style %>">',
				'<div class="sortable-handle"></div>',
				'<div class="uploadfiles">',
					'<p>Drag files here to upload</p>',
					'<form action="/api/courses/assetnew" class="uploadfiles-form">',
						'<input type="file" name="files[]" class="fileupload" multiple />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
					'</form>',
					'<div class="uploadfiles-progress">',
						'<div class="bar-border"><div class="bar"></div></div>',
					'</div>',
				'</div>',
				'<div class="asset-group-item-container">',
					'<div class="asset-group-item-title title toggle-editable"><%= assetgroup_title %></div>',
					'<div class="title-edit">',
						'<form action="/api/courses/assetgroupsave" class="title-form">',
							'<input class="uniform title-text" name="title" type="text" value="<%= assetgroup_title %>" />',
							'<input class="uniform title-save" type="submit" value="Save" />',
							'<input class="uniform title-reset" type="reset" value="Cancel" />',
							'<input type="hidden" name="course_id" value="<%= course_id %>" />',
							'<input type="hidden" name="id" value="<%= assetgroup_id %>" />',
						'</form>',
					'</div>',
					'<ul class="assets-list">',
						'<li class="asset-item asset missing nofiles">',
							'No files',
							'<span class="next-step-upload">',
								'Upload files &rarr;',
							'</span>',
						'</li>',
					'</ul>',
				'</div>',
			'</li>',
			'<div class="clear"></div>'
		].join("\n"),

		unititem : [
			'<li class="unit-item">',
				'<div class="unit-title-arrow"></div>',
				'<div class="title unit-title toggle-editable"><%= unit_title %></div>',
				'<div class="title-edit">',
					'<form action="/api/courses/unitsave" class="title-form">',
						'<input class="uniform title-text" name="title" type="text" value="<%= unit_title %>" />',
						'<input class="uniform title-save" type="submit" value="Save" />',
						'<input class="uniform title-reset" type="reset" value="Cancel" />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="id" value="<%= unit_id %>" />',
					'</form>',
				'</div>',
				'<div class="progress-container">',
					'<div class="progress-indicator"></div>',
				'</div>',
				'<div class="clear"></div>',
				'<ul class="asset-group-type-list" style="display:none">',
					'<% _.each(assetgroups, function(assetgroup){ %>',
						'<li>',
							'<div class="asset-group-title title"><%= assetgroup.assetgroup_title %></div>',
							'<div class="clear"></div>',
							'<ul class="asset-group sortable">',
								// @FIXME: do we want to create some placeholder asset groups? (see next line)
								//'<% print(_.template(HUB.CoursesOutline.Templates.assetgroupitem, assetgroup)); %>',
								'<li class="add-new asset-group-item">',
									'Add a new <% print(assetgroup.assetgroup_title.toLowerCase().replace(/s$/, "")) %>',
									'<form action="/api/courses/assetgroupsave">',
										'<input type="hidden" name="course_id" value="<%= course_id %>" />',
										'<input type="hidden" name="unit_id" value="<%= unit_id %>" />',
										'<input type="hidden" name="parent" value="<%= assetgroup.assetgroup_id %>" />',
									'</form>',
								'</li>',
							'</ul>',
						'</li>',
					'<% }) %>',
				'</ul>',
			'</li>'
		].join("\n")
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.CoursesOutline.initialize();
});