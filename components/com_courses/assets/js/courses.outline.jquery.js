
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
		HUB.CoursesOutline.makeUnitsSortable();
		HUB.CoursesOutline.makeAssetsSortable();
		HUB.CoursesOutline.makeAssetsDeletable();
		HUB.CoursesOutline.makeTitlesEditable();
		HUB.CoursesOutline.addNewItem();
		HUB.CoursesOutline.makeUniform();
		HUB.CoursesOutline.togglePublished();
		HUB.CoursesOutline.setupUrlAttach();
		HUB.CoursesOutline.setupFileUploader();
		HUB.CoursesOutline.resizeFileUploader();
		HUB.CoursesOutline.setupErrorMessage();
		HUB.CoursesOutline.calendar();
		HUB.CoursesOutline.preview();
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

			$(this).children('.uploadfiles').css('min-height', high);
		});

		// Also increase the size of the deletion tray
		$('.delete-tray').css('height', $('.unit').height());
	},

	makeUnitsSortable: function()
	{
		var $ = this.jQuery;

		$(".sortable").sortable({
			placeholder: "placeholder",
			handle: '.sortable-handle',
			forcePlaceholderSize: true,
			revert: false,
			tolerance: 'pointer',
			opacity: '0.6',
			items: 'li:not(.add-new, .asset)',
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

	makeAssetsSortable: function()
	{
		var $ = this.jQuery;

		$(".sortable-assets").sortable({
			placeholder: "placeholder-assets",
			handle: '.sortable-assets-handle',
			forcePlaceholderSize: true,
			revert: false,
			tolerance: 'pointer',
			opacity: '0.6',
			items: 'li',
			connectWith: '.assets-deleted',
			zIndex: 10000,
			remove: function(event, ui){
				// Get count of assets remaining
				var assetCount = $(this).find('li').length;

				if(assetCount === 0) {
					// Add an empty asset back
					$(this).append(_.template(HUB.CoursesOutline.Templates.emptyasset));
				}

				// @FIXME: we should only resize the one we're currently changing
				HUB.CoursesOutline.resizeFileUploader();

				// Get the form data (we're just stealing the info from another form)
				var form = ui.item.find('.title-form').serializeArray();

				// Set state equal to 2 for deleted
				form.push({'name':'state', 'value':2});

				// Now actually mark the asset as deleted
				$.ajax({
					url: '/api/courses/assetsave',
					data: form,
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
			},
			update: function(){
				// Save new order to the database
				var sorted   = $(this).sortable('serialize');
				var scope_id = $(this).parents('.asset-group-item').find('input[name="scope_id"]').val();
				var scope    = 'asset_group';

				// Update the asset group ordering
				$.ajax({
					url: '/api/courses/assetsreorder',
					data: sorted+"&scope_id="+scope_id+"&scope="+scope,
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

	makeAssetsDeletable: function()
	{
		var $ = this.jQuery;

		// Var to hold whether the delete tray has been locked open
		var locked = false;

		// Expand the submit button on hover (not necessary, just fun...)
		if($.isFunction($().hoverIntent)){
			$('.delete-tray').hoverIntent({
				over: function(){
					if(!locked) {
						$('.unit').animate({'margin-left':315}, 500);
						$('.delete-tray').animate({'margin-left':0}, 500, function() {
							$('.delete-tray').removeClass('closed').addClass('open');
						});
					}
				},
				out: function(){
					if(!locked) {
						$('.unit').animate({'margin-left':30}, 500);
						$('.delete-tray').animate({'margin-left':-285}, 500, function() {
							$('.delete-tray').addClass('closed').removeClass('open');
						});
					}
				},
				timeout: 1000,
				interval: 250
			});
		}

		// Make this a sortable list (even though we won't actually sort the trash), so that we can connect with the assets list
		$('.assets-deleted').sortable({
			'handle': '.sortable-assets-handle',
			over: function(){
				// @FIXME: we should only resize the one we're currently changing
				HUB.CoursesOutline.resizeFileUploader();
			}
		});

		// Toggle the delete tray lock
		$('.delete-tray .lock').on('click', function() {
			$(this).toggleClass('locked').toggleClass('unlocked');
			locked = (!locked) ? true : false;
		});

		// Restore an asset when clicking restore
		$('.delete-tray').on('click', '.restore', function() {
			var form = $(this).siblings('.next-step-publish');
			var li   = $(this).parents('li');

			// Now actually mark the asset as deleted
			$.ajax({
				url: '/api/courses/assettogglepublished',
				data: form.serializeArray(),
				dataType: "json",
				type: 'POST',
				cache: false,
				statusCode: {
					201: function(data){
						// Report a message?
						li.fadeOut('fast', function() {
							var html  = li.clone();
							var scope = li.find('input[name="scope_id"]').val();

							li.remove();

							var assetslist = $('#assetgroupitem_'+scope+' .assets-list');

							assetslist.append(html);

							if(assetslist.find('li:first').hasClass('nofiles'))
							{
								assetslist.find('li:first').remove();
							}

							html.find('span.published-label-text').html('Mark as reviewed and publish?');
							html.removeClass('published').addClass('notpublished');
							html.find('input.uniform').attr('checked', false);
							$.uniform.restore(html.find('.uniform'));
							html.find('.uniform').uniform();

							html.slideDown('fast', 'linear', function(){
								HUB.CoursesOutline.resizeFileUploader();
							});
						});
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

	makeTitlesEditable: function()
	{
		var $ = this.jQuery;

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
			title.find('.title-text').css("width", width+20);
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
							newAssetGroupItem.slideDown('fast', 'linear', function() {
								HUB.CoursesOutline.resizeFileUploader();
							});
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
							HUB.CoursesOutline.makeUnitsSortable();

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
			var distLink        = false;
			var form            = $(this).parents('form');
			var label           = form.find('span.published-label-text');
			var item            = form.parent('.asset-item');

			// If this is an Exam, we also want to set deployment info
			// @FIXME: only do this if it isn't already deployed
			// @FIXME: handle this with our new asset handlers
			if(item.hasClass('exam') && item.hasClass('notpublished')){
				// @FIXME: add a better method for getting form id (mainly because this one doesn't work once you publish the form)
				var assetA    = item.find('.asset-preview a');
				var assetHref = assetA.attr('href');
				var formId    = assetHref.match(/\/courses\/form\/layout\/([0-9]+)/);

				$.fancybox({
					fitToView: false,
					autoResize: false,
					autoSize: false,
					height: ($(window).height())*2/3,
					type: 'iframe',
					href: '/courses/form?task=deploy&formId='+formId[1]+'&tmpl=component',
					afterShow: function() {
						$('.fancybox-iframe').load(function() {
							var iframeTask = $(this)[0].contentWindow.location.pathname.match(/\/courses\/form\/([a-zA-Z]+)/);
							if(iframeTask[1] == 'showDeployment')
							{
								$.fancybox.close();
							}
						});
					},
					beforeClose: function() {
						// Grab the distribution link
						distLink = $('.fancybox-iframe').contents().find('.distribution-link a').attr('href').match(/\.org(\/.*)/);
					},
					afterClose: function() {
						if(distLink[1] && distLink[1].length){
							// Update URL for asset to have proper link
							// @FIXME: combine this call with the toggle publish call
							$.ajax({
								url: '/api/courses/assetsave',
								data: form.serialize()+'&url='+encodeURIComponent(distLink[1]),
								dataType: "json",
								type: 'POST',
								cache: false,
								statusCode: {
									201: function(data){
										// Update the link
										assetA.attr('href', distLink[1]);
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

							// And toggle the published state
							toggleState();
						} else {
							// @FIXME: uncheck box?
						}
					}
				});
			} else {
				toggleState();
			}

			function toggleState() {
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
			}
		});
	},

	setupUrlAttach: function() {
		var $ = this.jQuery;

		// Add a click to show URL attach form
		$('.unit').on('click', '.attach-a-link a', function(e) {
			e.preventDefault();
			$(this).siblings('.url').fadeToggle();
		});

		$('.unit').on('click', '.attach-a-link .attach-link-cancel', function(e) {
			e.preventDefault();
			$(this).parents('form').fadeOut();
		});
	},

	setupFileUploader: function()
	{
		var $ = this.jQuery;

		// Disable default browser drag and drop event
		$(document).bind('drop dragover', function(e) {
			e.preventDefault();
		});

		// Hide the file input
		$('.uploadfiles .fileupload').hide();

		// Set up file uploader on our file upload boxes
		$('.uploadfiles').each(function(){
			// Initialize a few variables
			var assetslist      = $(this).parent('.asset-group-item').find('.assets-list');
			var form            = $(this).find('form');
			var fileupload      = $(this);
			var message         = '';
			var dialog          = $("#dialog-confirm");
			var targetName      = '';
			var ulCount         = 0;
			var counter         = 0;

			// Setup dialog message box
			dialog.dialog({
				resizable: false,
				width: 450,
				modal: true,
				autoOpen: false,
				title: 'How do you want to make these files available?',
				buttons: {
					Cancel: function() {
						// Close the dialog box
						$(this).dialog("close");
					}
				}
			});

			$(this).fileupload({
				dropZone: $(this),
				dataType: 'json',
				statusCode: {
					// 201 created - this is returned by the standard asset upload
					201: function(data, textStatus, jqXHR){
						if(data.assets.js) {
							// If our asset handler returns JS, we'll run that
							eval(data.assets.js);
						} else {
							// If this is an empty asset group, remove the "no files" list item first
							if(assetslist.find('li:first').hasClass('nofiles'))
							{
								assetslist.find('li:first').remove();
							}

							// Loop through the uploaded files and add li for them
							$.each(data.assets, function (index, asset) {
								// Insert in our HTML (uses "underscore.js")
								var li = _.template(HUB.CoursesOutline.Templates.asset, asset);
								assetslist.append(li);

								// Get a pointer to our new asset item
								var newAsset = assetslist.find('.asset-item:last');

								// Update a few things after adding the new asset (reset the progress bar, make them sortable, etc...)
								newAsset.find('.uniform').uniform();
								newAsset.find('.toggle-editable').show();
								newAsset.find('.title-edit').hide();
								HUB.CoursesOutline.showProgressIndicator();
								HUB.CoursesOutline.resizeFileUploader();
								HUB.CoursesOutline.makeAssetsSortable();

								// Reset progress bar after 2 seconds
								HUB.CoursesOutline.resetProgresBar(asset.asset_title+'.'+asset.asset_ext, 2000);
							});
						}

					},
					401: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);

						// Reset progress bar
						// @FIXME: need to return filename even with errors!
						HUB.CoursesOutline.resetProgresBar(data.files[0].name, 0);
					},
					// 404 - this could come from a method not found, or the api not being configured at all
					404: function(data){
						HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');

						// Reset progress bar
						// @FIXME: need to return filename even with errors!
						HUB.CoursesOutline.resetProgresBar(data.files[0].name, 0);
					},
					500: function(data){
						// Display the error message
						HUB.CoursesOutline.errorMessage(data.responseText);

						// Reset progress bar
						// @FIXME: need to return filename even with errors!
						HUB.CoursesOutline.resetProgresBar(data.files[0].name, 0);
					}
				},
				add: function(e, data) {
					// Get asset handlers for this file type
					$.ajax({
						url: '/api/courses/assethandlers',
						data: 'name=' + data.files[0].name,
						dataType: "json",
						type: 'POST',
						cache: false,
						success: function(json) {
							// Make sure the file isn't too large (this is checking against the minimum of PHP's post and max upload limit)
							if(json.max_upload < (data.files[0].size / 1000000)) {
								// Warn about file being too large
								HUB.CoursesOutline.errorMessage('Sorry, the file that you uploaded ("' + data.files[0].name + '") exceedes the upload limit of ' + json.max_upload + ' MB');
							// Make sure we know what to do with this file type
							} else if(!json.handlers.length) {
								HUB.CoursesOutline.errorMessage('Sorry, we don\'t know what to do with files of type "' + json.ext + '"');
							// Check to see if there are multiple ways of handling this file type
							} else if(json.handlers.length > 1) {
								// Iterate counter (for uniqueness - in case someone uploads two files with the same name)
								counter += 1;

								// Handle multiple handlers for extension
								message += '<ul class="handlers-list">';
								message += '<p class="asset file">' + data.files[0].name + '</p>';
								$.each(json.handlers, function(index, value){
									message += '<li class="handler-item">';
									message += '<a id="' + (data.files[0].name + '_' + value.classname + counter).replace(/[. ]/g, '_') + '" class="dialog-button">';
									message += value.message;
									message += '</a>';
									message += '</li>';
								});
								message += '</ul>';

								// Add the message to the dialog box
								dialog.html(message);

								// Bind click events to the message buttons
								$.each(json.handlers, function(index, value){
									targetName = '#'+(data.files[0].name+'_'+value.classname + counter).replace(/[. ]/g, '_');
									dialog.on('click', targetName, function(){
										fileupload.fileupload(
											'option',
											'formData',
											function (form) {
												var formData = form.serializeArray();
												// Add an explicit handler to the submitted data
												formData.push({
													'name':'handler',
													'value': value.classname
												});

												return formData;
											});
										data.submit();
										// @FIXME: impliment cancel button
										HUB.CoursesOutline.assetProgressBar(data.files[0].name, fileupload);

										// Remove the ul for this file
										$(this).parents('ul').remove();

										ulCount = dialog.find('ul').length;

										if(ulCount === 0) {
											// Close the dialog box
											dialog.dialog("close");
										}
									});
								});

								// Add close event to clear message box text
								dialog.on('dialogclose', function() {
									// Clear the message
									message = '';
									dialog.html('');
								});

								// Open the dialog box (if it isn't already)
								if(!dialog.dialog("isOpen")) {
									dialog.dialog("open");
								}
							} else {
								// Submit the file upload request as normal
								data.submit();
								// @FIXME: impliment cancel button
								HUB.CoursesOutline.assetProgressBar(data.files[0].name, fileupload);
							}
						}
					});
				},
				progress: function (e, data) {
					var id = data.files[0].name.replace(/[. ]/g, '_') + '_progressbar';

					// Show progress bars for all pending uploads
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$(this).find("#"+id + " .bar").css(
						'width',
						progress + '%'
					);

					// If progress is 100% and extension is zip, let's add some explanation
					if(progress == 100) {
						var extension = data.files[0].name.split('.');

						if(extension[extension.length - 1] == 'zip') {
							$(this).find("#"+id + " .filename").html('unzipping...');
						}
					}
				}
			});
		});
	},

	assetProgressBar: function(filename, fileupload)
	{
		var $ = this.jQuery;

		// @FIXME: come up with a better way of tracking the this div through the upload process
		var id = filename.replace(/[. ]/g, '_') + '_progressbar';
		var html = '';
			html += '<div id="' + id + '" class="uploadfiles-progress">';
			html += '<div class="bar-border">';
			html += '<span class="filename">' + filename + '</span>';
			html += '<div class="bar"></div>';
			html += '</div>';
			html += '</div>';

		fileupload.append(html);
	},

	resetProgresBar: function(filename, timeout)
	{
		var $ = this.jQuery;

		var id = filename.replace(/[. ]/g, '_') + '_progressbar';

		setTimeout(function(){
			$("#"+id).remove();
		},timeout);
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

	calendar: function()
	{
		var $ = this.jQuery;

		$('.unit').on('click', '.calendar', function(){
			var form = $(this).find('form');

			$.fancybox({
				type: 'ajax',
				autoSize: false,
				width: '305',
				height: '190',
				href: form.attr('action')+'?'+form.serialize()+'&tmpl=component',
				afterShow: function() {
					$('.datepicker').datepicker({
						dateFormat: 'yy-mm-dd'
					});

					var detailsForm = $('.unit-details-form');
					detailsForm.submit(function(e){
						e.preventDefault();

						$.ajax({
							url: detailsForm.attr('action'),
							data: detailsForm.serialize(),
							dataType: "json",
							type: 'POST',
							cache: false,
							statusCode: {
								201: function(data){
									$.fancybox.close();
								},
								401: function(data){
									// Display the error message
									HUB.CoursesOutline.errorMessage(data.responseText);
									return false;
								},
								404: function(data){
									HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
									return false;
								},
								500: function(data){
									// Display the error message
									HUB.CoursesOutline.errorMessage(data.responseText);
									return false;
								}
							}
						});
					});
				}
			});
		});
	},

	preview: function()
	{
		var $ = this.jQuery;

		// Setup preview links to open in lightbox
		$('.unit').on('click', '.asset-preview a', function(e){
			e.preventDefault();

			$.fancybox({
				type: 'iframe',
				autoSize: true,
				width: ($(window).width())*5/6,
				href: $(this).attr('href')
			});
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
		asset : [
			'<li id="asset_<%= asset_id %>" class="asset-item asset <%= asset_type %> notpublished">',
				'<div class="sortable-assets-handle"></div>',
				'<div class="asset-item-title title toggle-editable"><%= asset_title %></div>',
				'<div class="title-edit">',
					'<form action="/api/courses/assetsave" class="title-form">',
						'<input class="uniform title-text" name="title" type="text" value="<%= asset_title %>" />',
						'<input class="uniform title-save" type="submit" value="Save" />',
						'<input class="uniform title-reset" type="reset" value="Cancel" />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
						'<input type="hidden" name="id" value="<%= asset_id %>" />',
					'</form>',
				'</div>',
				'<div class="asset-preview">',
					'(<a class="" href="<%= asset_url %>">preview</a>)',
				'</div>',
				'<form action="/api/courses/assettogglepublished" class="next-step-publish">',
					'<span class="next-step-publish">',
					'<label class="published-label" for="published">',
						'<span class="published-label-text">Mark as reviewed and publish?</span>',
						'<input class="uniform published-checkbox" name="published" type="checkbox" />',
						'<input type="hidden" class="asset_id" name="id" value="<%= asset_id %>" />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="scope_id" value="<%= scope_id %>" />',
						'<input type="hidden" name="scope" value="asset_group" />',
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
					'</label>',
					'</span>',
				'</form>',
				'<div class="restore">',
					'<button>Restore</button>',
				'</div>',
			'</li>'
		].join("\n"),

		emptyasset : [
			'<li class="asset-item asset missing nofiles">',
				'No files',
				'<span class="next-step-upload">',
					'Upload files &rarr;',
				'</span>',
			'</li>'
		].join("\n"),

		assetgroupitem : [
			'<li class="asset-group-item" id="assetgroupitem_<%= assetgroup_id %>" style="<%= assetgroup_style %>">',
				'<div class="sortable-handle"></div>',
				'<div class="uploadfiles">',
					'<p>Drag files here to upload</p>',
					'<p>or</p>',
					'<div class="attach-a-link">',
						'<form action="/api/courses/assetnew" class="url">',
							'<input class="uniform input-url" type="text" name="url" placeholder="URL" />',
							'<input class="uniform attach-link-submit" type="submit" value="Add" />',
							'<input class="uniform attach-link-cancel" type="reset" value="Cancel" />',
							'<input type="hidden" name="course_id" value="<%= course_id %>" />',
							'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
							'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
						'</form>',
						'<a href="#" class="">Attach a link</a>',
					'</div>',
					'<form action="/api/courses/assetnew" class="uploadfiles-form">',
						'<input type="file" name="files[]" class="fileupload" multiple />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
						'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
					'</form>',
				'</div>',
				'<div class="asset-group-item-container">',
					'<div class="asset-group-item-title title toggle-editable"><%= assetgroup_title %></div>',
					'<div class="title-edit">',
						'<form action="/api/courses/assetgroupsave" class="title-form">',
							'<input class="uniform title-text" name="title" type="text" value="<%= assetgroup_title %>" />',
							'<input class="uniform title-save" type="submit" value="Save" />',
							'<input class="uniform title-reset" type="reset" value="Cancel" />',
							'<input type="hidden" name="course_id" value="<%= course_id %>" />',
							'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
							'<input type="hidden" name="id" value="<%= assetgroup_id %>" />',
						'</form>',
					'</div>',
					'<ul class="assets-list sortable-assets">',
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
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
						'<input type="hidden" name="id" value="<%= unit_id %>" />',
					'</form>',
				'</div>',
				'<div class="calendar">',
					'<form action="/courses/<%= course_alias %>/manage/<%= offering_alias %>" class="calendar-form">',
						'<input type="hidden" name="scope" value="unit" />',
						'<input type="hidden" name="scope_id" value="<%= unit_id %>" />',
					'</form>',
				'</div>',
				'<div class="progress-container">',
					'<div class="progress-indicator"></div>',
				'</div>',
				'<div class="clear"></div>',
				'<ul class="asset-group-type-list" style="display:none">',
					'<% _.each(assetgroups, function(assetgroup){ %>',
						'<li class="asset-group-type-item">',
							'<div class="asset-group-title title"><%= assetgroup.assetgroup_title %></div>',
							'<div class="clear"></div>',
							'<ul class="asset-group sortable">',
								// @FIXME: do we want to create some placeholder asset groups? (see next line)
								//'<% print(_.template(HUB.CoursesOutline.Templates.assetgroupitem, assetgroup)); %>',
								'<li class="add-new asset-group-item">',
									'Add a new <% print(assetgroup.assetgroup_title.toLowerCase().replace(/s$/, "")) %>',
									'<form action="/api/courses/assetgroupsave">',
										'<input type="hidden" name="course_id" value="<%= course_id %>" />',
										'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
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