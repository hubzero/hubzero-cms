
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
	counter: 1,

	initialize: function()
	{
		HUB.CoursesOutline.setDefaults();
		HUB.CoursesOutline.toggleUnits();
		HUB.CoursesOutline.showProgressIndicator();
		HUB.CoursesOutline.makeAssetGroupsSortable();
		HUB.CoursesOutline.makeAssetsSortable();
		HUB.CoursesOutline.makeAssetsDeletable();
		HUB.CoursesOutline.makeTitlesEditable();
		HUB.CoursesOutline.addNewItem();
		HUB.CoursesOutline.makeUniform();
		HUB.CoursesOutline.togglePublished();
		HUB.CoursesOutline.setupAssetEdit();
		HUB.CoursesOutline.setupAuxAttach();
		HUB.CoursesOutline.setupFileUploader();
		HUB.CoursesOutline.resizeFileUploader(null, HUB.CoursesOutline.resizeDeleteTray());
		HUB.CoursesOutline.setupErrorMessage();
		HUB.CoursesOutline.calendar();
		HUB.CoursesOutline.preview();
	},

	setDefaults: function()
	{
		var $ = this.jQuery;

		$.ajaxSetup({
			dataType: "json",
			type: 'POST',
			cache: false,
			statusCode: {
				// 200 created
				200: function (data, textStatus, jqXHR){
					// Do nothing
				},
				// 201 created
				201: function (data, textStatus, jqXHR){
					// Do nothing
				},
				401: function (data, textStatus, jqXHR){
					// Display the error message
					HUB.CoursesOutline.errorMessage(data.responseText);
				},
				404: function (data, textStatus, jqXHR){
					HUB.CoursesOutline.errorMessage('Method not found. Ensure the the hub API has been configured');
				},
				500: function (data, textStatus, jqXHR){
					// Display the error message
					HUB.CoursesOutline.errorMessage(data.responseText);
				}
			}
		});
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
				$(this).siblings('.asset-group-type-list').slideUp(500, function() {
					HUB.CoursesOutline.resizeDeleteTray();
					$('html, body').animate({scrollTop: $(this).parents('.unit').offset().top - 10}, 1000);
				});
				$(this).removeClass('unit-title-arrow-active');
			} else {
				$('.asset-group-type-list').slideUp(500);
				$('.unit-title-arrow').removeClass('unit-title-arrow-active');
				$(this).siblings('.asset-group-type-list').slideDown(500, function() {
					HUB.CoursesOutline.resizeFileUploader();
					HUB.CoursesOutline.resizeDeleteTray();
					$('html, body').animate({scrollTop: $(this).parents('li').offset().top - 10}, 1000);
				});

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

	resizeFileUploader: function(selector, callback)
	{
		var $ = this.jQuery;

		// Set default
		selector = (selector) ? selector : '.asset-group-item:not(.add-new)';
		callback = (callback) ? callback : function(){};

		$(selector).each(function(){
			var high = $(this).height();
				high -= $(this).children('.uploadfiles').css('margin-top').replace("px", "");
				high -= $(this).children('.uploadfiles').css('margin-bottom').replace("px", "");
				high -= $(this).children('.uploadfiles').css('padding-bottom').replace("px", "");
				high -= $(this).children('.uploadfiles').css('padding-top').replace("px", "");
				//high -= 4; // For borders - this is hacky

			$(this).children('.uploadfiles').css('min-height', high);
		});

		callback();
	},

	resizeDeleteTray: function()
	{
		var $ = this.jQuery;

		$('.delete-tray').animate({'min-height': $('.unit').height()}, 0);
	},

	makeAssetGroupsSortable: function()
	{
		var $ = this.jQuery;

		// Show handles and delete on hover
		$('.asset-group-item').hoverIntent({
			over: function(){
				$(this).find('.sortable-handle').show('slide', 250);
				$(this).not('.add-new').animate({"padding-left":60}, 250);
				$(this).find('.sortable-assets-handle').show('slide', 250);
				$(this).find('.asset:not(.nofiles)').animate({"margin-left":30}, 250);
				$(this).find('.asset-delete, .asset-preview, .asset-edit').animate({"opacity":0.8}, 250);
			},
			out: function(){
				$(this).find('.sortable-handle').hide('slide', 250);
				$(this).not('.add-new').animate({"padding-left":10}, 250);
				$(this).find('.sortable-assets-handle').hide('slide', 250);
				$(this).find('.asset:not(.nofiles)').animate({"margin-left":10}, 250);
				$(this).find('.asset-delete, .asset-preview, .asset-edit').animate({"opacity":0}, 250);
			},
			timeout: 150,
			interval: 150
		});

		$(".sortable").sortable({
			placeholder: "placeholder",
			handle: '.sortable-handle',
			forcePlaceholderSize: true,
			revert: false,
			tolerance: 'pointer',
			opacity: '0.6',
			items: 'li:not(.add-new, .asset)',
			start: function(e, ui){
				// Style the placeholdwer based on the size of the item grabbed
				$(".placeholder").css({
					'height': ui.item.outerHeight(),
					'margin': ui.item.css('margin')
				});
			},
			update: function(){
				// Save new order to the database
				var sorted = $(this).sortable('serialize');

				// Update the asset group ordering
				$.ajax({
					url: '/api/courses/assetgroup/reorder',
					data: sorted
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

				// Update a few styles
				ui.item.find('.sortable-assets-handle').css("display", "none");
				ui.item.css("margin-left", 0);

				// Get the form data (we're just stealing the info from another form)
				var form = ui.item.find('.title-form').serializeArray();

				// Set state equal to 2 for deleted
				form.push({'name':'state', 'value':2});

				// Now actually mark the asset as deleted
				$.ajax({
					url: '/api/courses/asset/save',
					data: form
				});
			},
			update: function(){
				// Save new order to the database
				var sorted   = $(this).sortable('serialize');
				var scope_id = $(this).parents('.asset-group-item').find('input[name="scope_id"]').val();
				var scope    = 'asset_group';

				// Update the asset group ordering
				$.ajax({
					url: '/api/courses/asset/reorder',
					data: sorted+"&scope_id="+scope_id+"&scope="+scope
				});
			}
		});
	},

	makeAssetsDeletable: function()
	{
		var $ = this.jQuery;

		// Delete icon/button
		$('.unit').on('click', '.asset-delete', function(e) {
			e.preventDefault();

			var form       = $(this).siblings('.next-step-publish').serializeArray();
			var asset      = $(this).parent('li');
			var assetgroup = asset.parents('ul.assets-list');

			// Set the published value to 2 for deleted
			form.push({"name":"published", "value":"2"});

			$.ajax({
				url: '/api/courses/asset/save',
				data: form,
				statusCode: {
					200: function(data){
						// Report a message?
						asset.hide('transfer', {to:'.delete-tray h4', className: "transfer-effect", easing: "easeOutCubic", duration: 750}, function() {
							// Clone the asset for insertion to the deleted list
							var html  = asset.clone();

							// Now remove the active asset
							asset.remove();

							// Add the asset to the deleted list and fade it in
							$('ul.assets-deleted').append(html);
							html.find('.sortable-assets-handle').css("display", "none");
							html.css("margin-left", 0);
							html.fadeIn('fast');

							// Get count of assets remaining
							var assetCount = assetgroup.find('li').length;

							if(assetCount === 0) {
								// Add an empty asset back
								assetgroup.append(_.template(HUB.CoursesOutline.Templates.emptyasset));
							}

							// @FIXME: we should only resize the one we're currently changing
							HUB.CoursesOutline.resizeFileUploader();
						});
					}
				}
			});
		});

		// Var to hold whether the delete tray has been locked open
		var locked = false;

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
				url: '/api/courses/asset/togglepublished',
				data: form.serializeArray(),
				statusCode: {
					200: function(data){
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
				statusCode: {
					200: function (data, textStatus, jqXHR){
						parent.find('.toggle-editable:first').html(parent.find('.title-text:first').val());

						// Hide inputs and show plain text
						parent.find('.toggle-editable:first').show();
						parent.find('.title-edit:first').hide();
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

			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
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
							HUB.CoursesOutline.attachFileUploader('#'+newAssetGroupItem.attr('id')+' .uploadfiles');
							HUB.CoursesOutline.showProgressIndicator();

							// Refresh the sortable list
							$('.sortable').sortable('refresh');

							// Finally, show the new item
							newAssetGroupItem.slideDown('fast', 'linear', function() {
								HUB.CoursesOutline.resizeFileUploader(newAssetGroupItem);
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
							HUB.CoursesOutline.showProgressIndicator();

							// Refresh the sortable list
							HUB.CoursesOutline.makeAssetGroupsSortable();

							$('.asset-group-type-list').delay(500).slideUp(500, function(){
								$('.unit-title-arrow').removeClass('unit-title-arrow-active');
								newUnit.find('.asset-group-type-list').slideDown(500);

								// Toggle class for arrow (active gives down arrow indicating expanded list)
								newUnit.find('.unit-title-arrow').addClass('unit-title-arrow-active');
							});
						}
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
			if(item.hasClass('exam') && item.hasClass('notpublished')){
				var assetA    = item.find('a.asset-preview');
				var assetHref = assetA.attr('href');

				// Create ajax call to get the form id
				$.ajax({
					url: '/api/courses/asset/getformid',
					data: [{'name':'id', 'value':form.find('.asset_id').val()}],
					statusCode: {
						200: function(data){
							formId = data.form_id;

							$.fancybox({
								fitToView: false,
								autoResize: false,
								autoSize: false,
								height: ($(window).height())*2/3,
								type: 'iframe',
								href: '/courses/form?task=deploy&formId='+formId+'&tmpl=component',
								afterClose: function() {
									$.ajax({
										url: '/api/courses/asset/save',
										data: form.serialize(),
										statusCode: {
											200: function(data){
												// Update the link
												assetA.attr('href', data.files[0].asset_url);
												toggle();
											}
										}
									});
								}
							});
						}
					}
				});
			} else {
				$.ajax({
					url: form.attr('action'),
					data: form.serialize(),
					statusCode: {
						200: function(data){
							toggle();
						}
					}
				});
			}

			function toggle (){
				if(label.html() == 'Published') {
					replacement = 'Mark as reviewed and publish?';
					item.removeClass('published').addClass('notpublished');
				} else {
					replacement = 'Published';
					item.removeClass('notpublished').addClass('published');
				}
				label.html(replacement);

				HUB.CoursesOutline.showProgressIndicator();
			}
		});
	},

	setupAssetEdit: function()
	{
		var $ = this.jQuery;
		var contentBox = $('.content-box');

		function contentBoxClose() {
			contentBox.hide('slide', {'direction':'down'}, 500, function() {
				$('.content-box-overlay').fadeOut(100);
			});
			contentBox.find('iframe').attr('src', '');
		}

		// Add close on escape
		$(document).bind('keydown', function (e) {
			if(e.which == 27) {
				contentBoxClose();
			}
		});

		// Add close on click of close button
		$('.content-box-close').on('click', function() {
			contentBoxClose();
		});

		$('.unit').on('click', '.asset-edit', function (e) {
			e.preventDefault();

			var form  = $(this).siblings('.next-step-publish');
			var src   = '/courses/'+form.find('input[name="course_id"]').val()+'/manage/'+form.find('input[name="offering"]').val();
				src  += '?scope=asset&scope_id='+form.find('input[name="scope_id"]').val()+'&asset_id='+form.find('.asset_id').val()+'&tmpl=component';

			$('.content-box-header span').html('Edit Asset');

			contentBox.show('slide', {'direction':'down'}, 500, function () {
				$(this).siblings('.content-box-overlay').fadeIn(100);
				$(this).find('iframe').attr('src', src);
			});
		});

		// Attach submit and cancel buttons
		contentBox.find('iframe').load(function() {
			var content = $(this).contents();
			content.find('.cancel').click(function() {
				contentBoxClose();
			});

			content.find('.edit-form').submit(function (e) {
				e.preventDefault();

				// Create ajax call to change info in the database
				$.ajax({
					url: $(this).attr('action'),
					data: $(this).serializeArray(),
					statusCode: {
						// 200 OK
						200: function (data, textStatus, jqXHR){
							// Close box
							contentBoxClose();
							HUB.CoursesOutline.updateAssetInPage(data);
						}
					}
				});
			});
		});
	},

	setupAuxAttach: function()
	{
		var $ = this.jQuery;
		var contentBox = $('.content-box');

		// Add tooltips to attachment buttons
		$('.aux-attachments a').tooltip();

		// Add a click to show URL attach form
		$('.unit').on('click', '.aux-attachments a:not(.browse-files, .attach-note)', function (e) {
			e.preventDefault();
			$(this).siblings('.aux-attachments-form').find('.aux-attachments-content-label').html(e.originalEvent.target.title);
			$(this).siblings('.aux-attachments-form').find('.input-type').val(e.originalEvent.target.className.replace(/attach-/, ""));
			$(this).siblings('.aux-attachments-form').removeClass('attach-link attach-object attach-note browse-files').addClass(e.originalEvent.target.className);
			$(this).siblings('.aux-attachments-form').fadeIn();
		});

		// Hide for on cancel click
		$('.unit').on('click', '.aux-attachments .aux-attachments-cancel', function (e) {
			e.preventDefault();
			$(this).parents('form').fadeOut();
			$(this).parents('form').find('.input-content').val('');
		});

		function contentBoxClose() {
			contentBox.hide('slide', {'direction':'down'}, 500, function() {
				$('.content-box-overlay').fadeOut(100);
			});
			contentBox.find('iframe').attr('src', '');
		}

		// Add close on escape
		$(document).bind('keydown', function (e) {
			if(e.which == 27) {
				contentBoxClose();
			}
		});

		// Add close on click of close button
		$('.content-box-close').on('click', function() {
			contentBoxClose();
		});

		$('.unit').on('click', '.aux-attachments a.attach-note', function (e) {
			e.preventDefault();

			var form       = $(this).siblings('.aux-attachments-form');
			var src        = '/courses/'+form.find('input[name="course_id"]').val()+'/manage/'+form.find('input[name="offering"]').val();
				src       += '?scope=wiki&scope_id='+form.find('input[name="scope_id"]').val()+'&tmpl=component';

			$('.content-box-header span').html('Create a Note');

			contentBox.show('slide', {'direction':'down'}, 500, function () {
				$(this).siblings('.content-box-overlay').fadeIn(100);
				$(this).find('iframe').attr('src', src);
			});
		});

		// Attach submit and cancel buttons
		contentBox.find('iframe').load(function() {
			var content = $(this).contents();
			content.find('.wiki-cancel').click(function() {
				contentBoxClose();
			});

			content.find('.wiki-edit-form').submit(function (e) {
				e.preventDefault();

				// Create ajax call to change info in the database
				$.ajax({
					url: $(this).attr('action'),
					data: $(this).serializeArray(),
					statusCode: {
						// 201 created - this is returned by the standard asset upload
						201: function (data, textStatus, jqXHR){
							// Close box
							contentBoxClose();
							HUB.CoursesOutline.insertAssetInPage(data, $('#assetgroupitem_'+data.assets.assets.scope_id+' .assets-list'));
						}
					}
				});
			});
		});

		// Submit form
		$('.unit').on('click', '.aux-attachments .aux-attachments-submit', function(e) {
			e.preventDefault();

			var form       = $(this).parents('form');
			var assetslist = $(this).parents('.asset-group-item').find('.assets-list');

			// Create ajax call to change info in the database
			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				statusCode: {
					// 201 created - this is returned by the standard asset upload
					201: function(data, textStatus, jqXHR){
						if(data.assets.js) {
							// If our asset handler returns JS, we'll run that
							eval(data.assets.js);
						} else {
							HUB.CoursesOutline.insertAssetInPage(data, assetslist);

							// Hide the form again
							form.fadeOut(function() {
								form.find('.input-content').val('');
							});
						}
					}
				}
			});
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

		// Trigger file browser on click of browse files button
		$('.unit').on('click', '.browse-files', function(e) {
			e.preventDefault();
			$(this).parents('.uploadfiles').find('.fileupload').trigger('click');
		});

		HUB.CoursesOutline.attachFileUploader();
	},

	attachFileUploader: function(selector)
	{
		var $ = this.jQuery;

		// Set a default selector
		selector = (selector) ? selector : '.uploadfiles';

		// Hide the file input
		$(selector + ' .fileupload').hide();

		// Set up file uploader on our file upload boxes
		$(selector).each(function(){
			// Initialize a few variables
			var assetslist      = $(this).parent('.asset-group-item').find('.assets-list');
			var form            = $(this).find('form');
			var fileupload      = $(this);
			var message         = '';
			var dialog          = $("#dialog-confirm");
			var targetName      = '';
			var ulCount         = 0;

			// Setup dialog message box
			dialog.dialog({
				resizable: false,
				width: 450,
				modal: true,
				autoOpen: false,
				title: 'What do you want to do with these files?',
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
				progressInterval: 200,
				add: function(e, data) {
					// Get asset handlers for this file type
					$.ajax({
						url: '/api/courses/asset/handlers',
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
								// Iterate counter (for uniqueness)
								HUB.CoursesOutline.counter++;

								// Handle multiple handlers for extension
								message += '<ul class="handlers-list">';
								message += '<p class="asset file">' + data.files[0].name + '</p>';
								$.each(json.handlers, function(index, value){
									message += '<li class="handler-item">';
									message += '<a id="handler-item-' + HUB.CoursesOutline.counter + '-' + value.classname + '" class="dialog-button">';
									message += value.message;
									message += '</a>';
									message += '</li>';
								});
								message += '</ul>';

								// Add the message to the dialog box
								dialog.html(message);

								// Bind click events to the message buttons
								$.each(json.handlers, function (index, value){
									targetName = '#handler-item-' + HUB.CoursesOutline.counter + '-' + value.classname;
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
											}
										);

										fileSubmit(data);

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
								HUB.CoursesOutline.counter++;
								fileSubmit(data);
							}

							// Shared function for submitting a fileupload request (and setting appropriate callbacks)
							function fileSubmit(data) {
								var progressBarId = 'progress-bar-'+HUB.CoursesOutline.counter;

								// Setup the progress handler
								fileupload.on('fileuploadprogress', function (e, data) {
									HUB.CoursesOutline.assetProgress(data, progressBarId);
								});

								// Add the progress bar
								HUB.CoursesOutline.assetProgressBar(progressBarId, data.files[0].name, assetslist);

								// Attach a cancel button
								$("#"+progressBarId+' .cancel').click(function (e) {
									data.jqXHR.abort();
									HUB.CoursesOutline.resetProgresBar(progressBarId);
								});

								// Submit the file upload request as normal
								data.submit();

								// 201 created - this is returned by the standard asset upload
								data.jqXHR.done(function (data, textStatus, jqXHR) {
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
											var callback = function() {
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
											};

											// Reset progress bar
											HUB.CoursesOutline.resetProgresBar(progressBarId, 1000, callback);
										});
									}
								}).fail(function (jqXHR, textStatus, errorThrown) {
									// Reset progress bar
									HUB.CoursesOutline.resetProgresBar(progressBarId, 1000);
								});
							}
						}
					});
				}
			});
		});
	},

	assetProgress: function(data, progressBarId)
	{
		// Show progress bars for all pending uploads
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('.unit').find("#" + progressBarId + " .bar").animate({'width': progress + '%'}, 100);

		// If progress is 100% and extension is zip, let's add some explanation
		if(progress == 100) {
			var extension = data.files[0].name.split('.');

			if(extension[extension.length - 1] == 'zip') {
				$('.unit').find("#" + progressBarId + " .filename").html('unzipping...');
			}
		}
	},

	assetProgressBar: function(id, filename, assetslist)
	{
		var $ = this.jQuery;

		var html = '';
			html += '<div id="' + id + '" class="uploadfiles-progress">';
			html += '<div class="cancel"></div>';
			html += '<div class="bar-border">';
			html += '<span class="filename">' + filename + '</span>';
			html += '<div class="bar"></div>';
			html += '</div>';
			html += '</div>';

		assetslist.append(html);
	},

	resetProgresBar: function(id, timeout, callback)
	{
		var $ = this.jQuery;

		// Set defaults
		callback = ($.type(callback) === 'function') ? callback : function(){};
		timeout  = ($.type(timeout)  === 'number')   ? timeout  : 0;

		setTimeout(function(){
			$("#"+id).fadeOut('slow', function() {
				$("#"+id).remove();
				callback();
			});
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
							statusCode: {
								200: function(data){
									$.fancybox.close();
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
		$('.outline-main').on('click', 'a.asset-preview', function(e){
			e.preventDefault();

			$.fancybox({
				type: 'iframe',
				autoSize: false,
				width: ($(window).width())*5/6,
				height: ($(window).height())*5/6,
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

	insertAssetInPage: function(data, assetslist)
	{
		var $ = this.jQuery;

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
		});
	},

	updateAssetInPage: function(data)
	{
		var $ = this.jQuery;

		var asset = $('#asset_'+data.asset_id);

		asset.find('.asset-item-title').html(data.asset_title);
		asset.find('.title-text').val(data.asset_title);

		// If we're moving the asset to a new scope
		if (data.files[0].scope_id.length && asset.find('input[name="scope_id"]').val() != data.files[0].scope_id) {
			var clone = asset.clone();

			asset.remove();

			clone.hide();
			clone.find('input[name="scope_id"]').val(data.files[0].scope_id);

			$('#assetgroupitem_'+data.files[0].scope_id+' .assets-list').append(clone);

			clone.slideDown('fast');
		}
	},

	Templates: {
		asset : [
			'<li id="asset_<%= asset_id %>" class="asset-item asset <%= asset_type %> notpublished">',
				'<div class="sortable-assets-handle"></div>',
				'<div class="asset-item-title title toggle-editable"><%= asset_title %></div>',
				'<div class="title-edit">',
					'<form action="/api/courses/asset/save" class="title-form">',
						'<input class="uniform title-text" name="title" type="text" value="<%= asset_title %>" />',
						'<input class="uniform title-save" type="submit" value="Save" />',
						'<input class="uniform title-reset" type="reset" value="Cancel" />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
						'<input type="hidden" name="id" value="<%= asset_id %>" />',
					'</form>',
				'</div>',
				'<a class="asset-preview" href="<%= asset_url %>" title="preview"></a>',
				'<a class="asset-edit" href="#" title="edit"></a>',
				'<a class="asset-delete" href="#" title="delete"></a>',
				'<form action="/api/courses/asset/togglepublished" class="next-step-publish">',
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
					'<div class="aux-attachments">',
						'<form action="/api/courses/asset/new" class="aux-attachments-form attach-link">',
							'<label for"content" class="aux-attachments-content-label">Attach a link:</label>',
							'<textarea class="uniform input-content" name="content" placeholder="" rows="6"></textarea>',
							'<input class="input-type" type="hidden" name="type" value="link" />',
							'<input class="uniform aux-attachments-submit" type="submit" value="Add" />',
							'<input class="uniform aux-attachments-cancel" type="reset" value="Cancel" />',
							'<input type="hidden" name="course_id" value="<%= course_id %>" />',
							'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
							'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
						'</form>',
						'<a href="#" title="Attach a link" class="attach-link"></a>',
						'<a href="#" title="Embed a Kaltura or YouTube Object" class="attach-object"></a>',
						'<a href="#" title="Include a note" class="attach-note"></a>',
						'<a href="#" title="Browse for files" class="browse-files"></a>',
					'</div>',
					'<form action="/api/courses/asset/new" class="uploadfiles-form">',
						'<input type="file" name="files[]" class="fileupload" multiple />',
						'<input type="hidden" name="course_id" value="<%= course_id %>" />',
						'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
						'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
					'</form>',
				'</div>',
				'<div class="asset-group-item-container">',
					'<div class="asset-group-item-title title toggle-editable"><%= assetgroup_title %></div>',
					'<div class="title-edit">',
						'<form action="/api/courses/assetgroup/save" class="title-form">',
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
					'<form action="/api/courses/unit/save" class="title-form">',
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
									'<form action="/api/courses/assetgroup/save">',
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