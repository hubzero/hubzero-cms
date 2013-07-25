/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/build.jquery.js
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

jQuery(document).ready(function($) {
	// Set defaults for ajax calls
	$.ajaxSetup({
		dataType   : "json",
		type       : 'POST',
		cache      : false,
		statusCode : {
			// 200 created
			200: function ( data, textStatus, jqXHR ) {
				// Do nothing
				// This should be overwritten per request
			},
			// 201 created
			201: function ( data, textStatus, jqXHR ) {
				// Do nothing
				// This should be overwritten per request
			},
			401: function ( data, textStatus, jqXHR ) {
				// Display the error message
				HUB.CoursesOutline.message.show(data.responseText);
			},
			404: function ( data, textStatus, jqXHR ) {
				HUB.CoursesOutline.message.show('Method not found. Ensure the the hub API has been configured');
			},
			500: function ( data, textStatus, jqXHR ) {
				// Display the error message
				HUB.CoursesOutline.message.show(data.responseText);
			}
		}
	});

	// Initialize Objects
	HUB.CoursesOutline.asset.init();
	HUB.CoursesOutline.assetgroup.init();
	HUB.CoursesOutline.unit.init();
	HUB.CoursesOutline.message.init();

	// Hack for ie iframe height issue
	if ($.browser.msie && parseInt($.browser.version, 10) < 9) {
		$('.content-box iframe').css({
			'height' : $('.content-box').height()
		});
	}
});

HUB.CoursesOutline = {
	jQuery: jq,

	/* ------------ */
	// ASSET OBJECT //
	/* ------------ */
	asset: {
		// Track unique counter for uploads to ensure progress bars stay separate
		counter   : 1,
		// Track whether or not the trash bin is open or closed
		trashOpen : false,

		/*
		 * Initialize assets.
		 * This is a one-time method.
		 * As individual assets are inserted into the page later,
		 * the refresh method should be triggered
		 */
		init: function() {
			var $  = HUB.CoursesOutline.jQuery;

			// Attach click events
			$('.unit')
				.on('click', '.asset-delete', this.remove)
				.on('click', '.asset-edit', this.edit)
				.on('click', '.published-checkbox', this.state)
				.on('click', '.asset-preview', this.preview)
				.on('click', '.aux-attachments a:not(.browse-files, .attach-wiki, .help-info)', this.auxAttachmentShow)
				.on('click', '.aux-attachments .help-info', this.auxAttachmentHelp)
				.on('click', '.aux-attachments .aux-attachments-cancel', this.auxAttachmentHide)
				.on('click', '.aux-attachments a.attach-wiki', this.editWiki)
				.on('click', '.aux-attachments .aux-attachments-submit', this.auxAttachmentSubmit)
				.on('click', '.browse-files', this.openFileBrowser)
				.on('click', '.asset-item-title.toggle-editable', this.showTitleQuickEdit)
				.on('click', '.asset-title-reset', this.resetTitleQuickEdit)
				.on('submit', '.asset-title-form', this.submitTitleQuickEdit);

			$('.delete-tray').on('click', '.restore', this.restore);
			$('.header .trash').on('click', this.toggleTrash);

			// Lastely, attach a custom listener to outline main, so we know when to refresh/update asset groups
			$('.outline-main')
				.on('assetUpdate', this.update)
				.on('assetCreate', this.refresh);

			// Disable default browser drag and drop event
			function pd ( e ) { e.preventDefault(); }
			$(document).bind('drop dragover', pd);

			// Trigger refresh (which just initializes all items that may need to be initialized more than once)
			$('.outline-main').trigger('assetCreate');
		},

		/*
		 * Setup per-instance items
		 * This should be called via the assetCreate trigger when new items are added to the page
		 * isFirst tracks whether or not the asset just entered was the first in the list,
		 * meaning, that sortable list should be initialized, rather than refreshed.
		 */
		refresh: function ( e, id, isFirst ) {
			var $          = HUB.CoursesOutline.jQuery,
			sortableAssets = '';

			// If undefined, assume true
			isFirst = ($.type(isFirst) === "undefined") ? true : isFirst;

			// Set a default selector
			if (!id) {
				selector       = '.asset-group-item';
				sortableAssets = $(selector).find('.sortable-assets');
			} else {
				selector       = "#" + id;
				sortableAssets = $(selector).parents('.assets-list');
			}

			// Refresh sortable list
			if (isFirst) {
				// Make assets sortable
				sortableAssets.sortable({
					placeholder          : "placeholder-assets",
					handle               : '.sortable-assets-handle',
					forcePlaceholderSize : true,
					revert               : false,
					tolerance            : 'intersect',
					opacity              : '0.6',
					items                : 'li',
					axis                 : 'y',
					update               : function() {
						// Save new order to the database
						var sorted = $(this).sortable('serialize'),
						scope_id   = $(this).parents('.asset-group-item').find('input[name="scope_id"]').val(),
						scope      = 'asset_group';

						// Update the asset group ordering
						$.ajax({
							url  : '/api/courses/asset/reorder',
							data : sorted+"&scope_id="+scope_id+"&scope="+scope
						});
					}
				});
			} else {
				// Refresh sortable list to include new items
				sortableAssets.sortable('refresh');
			}

			// Uniform checkboxes
			$(selector + " .next-step-publish .uniform").uniform();
		},

		/*
		 * Insert a new asset into the page
		 * This method is called directly
		 */
		insert: function ( data, assetslist, options ) {
			var $   = HUB.CoursesOutline.jQuery,
			isFirst = false;

			options = ($.type(options) === 'object') ? options : {};

			// If this is an empty asset group, remove the "no files" list item first
			if(assetslist.find('li:first').hasClass('nofiles'))
			{
				assetslist.find('li:first').remove();
				// Set isFirst to true
				isFirst = true;
			}

			// Loop through the uploaded files and add li for each of them
			$.each(data.assets, function ( index, asset ) {
				var callback = function() {
					// Insert in our HTML (uses "underscore.js")
					assetslist.append(_.template(HUB.CoursesOutline.asset.templates.item, asset));

					var newAsset = assetslist.find('.asset-item:last');

					// Trigger asset create and unit update
					$('.outline-main').trigger('assetCreate', [newAsset[0].id, isFirst]);
					$('.outline-main').trigger('unitCreate');
				};

				// Reset progress bar (if applicable), otherwise, just call our callback
				if (options.progressBarId) {
					HUB.CoursesOutline.asset.resetProgresBar(options.progressBarId, 1000, callback);
				} else {
					callback();
				}
			});
		},

		/*
		 * Update an asset in the page
		 */
		update: function ( e, obj, data ) {
			var $ = HUB.CoursesOutline.jQuery;

			if ($.type(data.asset_title) === 'string') {
				obj.find('.asset-item-title').html(data.asset_title);
				obj.find('.title-text').val(data.asset_title);
			}

			if ($.type(data.files) === 'array') {
				obj.find('.asset-preview').attr('href', data.files[0].asset_url);
			}

			if ($.type(data.asset_state) === 'number' && data.asset_state === 0) {
				obj.find('.published-label-text').html('Mark as reviewed and publish?');
				obj.removeClass('published').addClass('notpublished');
			} else if ($.type(data.asset_state) === 'number' && data.asset_state === 1) {
				obj.find('.published-label-text').html('Published');
				obj.removeClass('notpublished').addClass('published');
			}

			// If we're moving the asset to a new scope
			if ($.type(data.files) === 'array' &&
					data.files[0].scope_id !== "" &&
					obj.find('input[name="scope_id"]').val() != data.files[0].scope_id) {
				var clone      = obj.clone(),
				assetGroupItem = obj.parents('.asset-group-item');
				assetslist     = assetGroupItem.find('.assets-list');

				// Remove the original asset
				obj.remove();

				// If this is now an empty asset group, add the "no files" item
				if(assetslist.find('li').length === 0)
				{
					assetslist.append(_.template(HUB.CoursesOutline.asset.templates.empty));
				}

				clone.hide();
				clone.find('input[name="scope_id"]').val(data.files[0].scope_id);

				var newAssetsList = $('#assetgroupitem_'+data.files[0].scope_id+' .assets-list');

				// If this is an empty asset group, remove the "no files" list item first
				if(newAssetsList.find('li:first').hasClass('nofiles'))
				{
					newAssetsList.find('li:first').remove();
				}

				newAssetsList.append(clone);

				clone.show('slide', 'slow');
			}
		},

		/*
		 * Edit an asset
		 * This is the full asset edit (not just the quick title edit)
		 * Defaults to edit form in content box
		 */
		edit: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			form  = $(this).siblings('.next-step-publish'),
			src   = '/courses/'+form.find('input[name="course_id"]').val()+'/'+form.find('input[name="offering"]').val()+'/outline?action=build';
			src  += '&scope=<%scope%>&scope_id='+form.find('input[name="scope_id"]').val()+'&asset_id='+form.find('.asset_id').val()+'&tmpl=component';

			e.preventDefault();

			// Create ajax call to edit asset
			$.ajax({
				url: '/api/courses/asset/edit',
				data: form.serializeArray(),
				statusCode: {
					// 200 OK
					200: function ( data, textStatus, jqXHR ) {
						switch ( data.type ) {
							case 'js' :
								eval(data.value);
							break;

							default :
								if (data.options && data.options.scope) {
									src = src.replace(/<%scope%>/, data.options.scope);
								} else {
									src = src.replace(/<%scope%>/, 'asset');
								}

								$.contentBox({
									src         : src,
									title       : 'Edit Asset',
									onAfterLoad : function( content ) {
										content.find('.cancel').click(function () {
											// Close the content box
											$.contentBox('close');
										});

										content.find('.edit-form').submit(function ( e ) {
											e.preventDefault();

											// Create ajax call to change info in the database
											$.ajax({
												url: $(this).attr('action'),
												data: $(this).serializeArray(),
												statusCode: {
													// 200 OK
													200: function ( data, textStatus, jqXHR ) {
														// Close the content box
														$.contentBox('close');
														$('.outline-main').trigger('assetUpdate', [form.parent('li'), data]);
													},
													// @FIXME: this is wrong...this should be 200
													// This is because in the wiki edit, we're always posting to asset/new,
													// which always returns a 201
													201: function ( data, textStatus, jqXHR ) {
														// Close the content box
														$.contentBox('close');
														$('.outline-main').trigger('assetUpdate', [form.parent('li'), data.assets.assets]);
													}
												}
											});
										});
									}
								});
							break;
						}
					}
				}
			});
		},

		/*
		 * Edit an asset's state (i.e. published, unpublished)
		 * @FIXME: clean up
		 */
		state: function ( e ) {
			var $       = HUB.CoursesOutline.jQuery,
			t           = $(this),
			form        = t.parents('form'),
			item        = form.parent('.asset-item');

			// If this is an Exam, we also want to set deployment info
			if(item.hasClass('form') && item.hasClass('notpublished')){
				var assetA    = item.find('a.asset-preview');
				var assetHref = assetA.attr('href');

				// Create ajax call to get the form id
				$.ajax({
					url: '/api/courses/asset/getformid',
					data: [{'name':'id', 'value':form.find('.asset_id').val()}],
					statusCode: {
						200: function ( data ) {
							formId = data.form_id;

							$.fancybox({
								fitToView: false,
								autoResize: false,
								autoSize: false,
								closeBtn: false,
								modal: true,
								height: ($(window).height())*2/3,
								type: 'iframe',
								href: '/courses/form?task=deploy&formId='+formId+'&tmpl=component',
								afterShow: function() {
									var contents = $('.fancybox-iframe').contents();

									// Highjack the 'cancel' button to close the iframe
									contents.find('#cancel').bind('click', function( e ) {
										e.preventDefault();

										// Close fancybox
										$.fancybox.close();

										// Reset check box
										t.attr('checked', false);
										$.uniform.restore(t);
										t.uniform();
									});

									// Listen for deployment create call from iframe
									$('body').on('deploymentsave', function () {
										// Close fancybox
										$.fancybox.close();

										$.ajax({
											url: '/api/courses/asset/save',
											data: form.serialize(),
											statusCode: {
												200: function ( data ) {
													$('.outline-main').trigger('assetUpdate', [item, data.files[0]]);
												}
											}
										});
									});

									// Fallback...if for some reason the deploymentsave trigger isn't fired
									$('.fancybox-iframe').load(function () {
										var content = $(this).contents();
										content.find('#done').click(function () {
											// Close fancybox
											$.fancybox.close();

											$.ajax({
												url: '/api/courses/asset/save',
												data: form.serialize(),
												statusCode: {
													200: function ( data ) {
														$('.outline-main').trigger('assetUpdate', [item, data.files[0]]);
													}
												}
											});
										});
									});
								}
							});
						},
						204: function() {
							$.ajax({
								url: '/api/courses/asset/save',
								data: form.serialize(),
								statusCode: {
									200: function ( data ) {
										$('.outline-main').trigger('assetUpdate', [item, data.files[0]]);
									}
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
						200: function ( data ) {
							$('.outline-main').trigger('assetUpdate', [item, data]);
						}
					}
				});
			}
		},

		/*
		 * Preview an asset
		 * Defaults to asset preview in fancybox iframe
		 * Gives 5 seconds to load before timing out
		 */
		preview: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			loaded = false,
			form   = $(this).siblings('.next-step-publish'),
			t      = $(this);

			e.preventDefault();

			// Create ajax call to preview asset
			$.ajax({
				url: '/api/courses/asset/preview',
				data: form.serializeArray(),
				statusCode: {
					// 200 OK
					200: function ( data, textStatus, jqXHR ) {
						switch ( data.type ) {
							case 'js' :
								eval(data.value);
							break;

							case 'content':
								$.fancybox({
									type: 'ajax',
									content: data.value
								});
							break;

							default :
								$.fancybox({
									type: 'iframe',
									autoSize: false,
									width: ($(window).width())*5/6,
									height: ($(window).height())*5/6,
									href: t.attr('href'),
									beforeLoad: function() {
										setTimeout(function(){
											if (!loaded) {
												$.fancybox.close();
												var msg  = 'Oops, something went wrong trying to preview this asset. ';
													msg += 'It may be that preview isn\'t available for this asset, ';
													msg += 'or that trying again will solve the problem.';
												HUB.CoursesOutline.message.show(msg, 7500);
											}
										},5000);
									},
									afterLoad: function() {
										loaded = true;
									}
								});
							break;
						}
					}
				}
			});
		},

		/*
		 * Restore an asset from deleted assets bin
		 * @FIXME: use trigger here, not manual insert
		 */
		restore: function () {
			var $ = HUB.CoursesOutline.jQuery,
			form  = $(this).siblings('.next-step-publish'),
			li    = $(this).parents('li');

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

							html.show('slide', 'slow');
						});
					}
				}
			});
		},

		/*
		 * Remove (i.e. mark as deleted) asset
		 */
		remove: function ( e ) {
			var $      = HUB.CoursesOutline.jQuery,
			form       = $(this).siblings('.next-step-publish').serializeArray(),
			asset      = $(this).parent('li'),
			assetgroup = asset.parents('ul.assets-list');

			e.preventDefault();

			// Set the published value to 2 for deleted
			form.push({"name":"published", "value":"2"});

			$.ajax({
				url: '/api/courses/asset/save',
				data: form,
				statusCode: {
					200: function( data ) {
						// Report a message?
						asset.hide('transfer', {to:'.header .trash', className: "transfer-effect", easing: "easeOutCubic", duration: 1000}, function() {
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
								assetgroup.append(_.template(HUB.CoursesOutline.asset.templates.empty));
							}
						});
					}
				}
			});
		},

		/*
		 * Toggle deleted asset bin open/closed
		 */
		toggleTrash: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			unit   = $('.unit'),
			tray   = $('.delete-tray'),
			button = $('.header .trash');

			e.preventDefault();

			if(!HUB.CoursesOutline.asset.trashOpen) {
				unit.animate({'margin-right':301}, 500);
				button.addClass('active');
				tray.animate({'right':0}, 500, function() {
					HUB.CoursesOutline.asset.trashOpen = true;
				});
			} else {
				unit.animate({'margin-right':0}, 500);
				button.removeClass('active');
				tray.animate({'right':-300}, 500, function() {
					HUB.CoursesOutline.asset.trashOpen = false;
				});
			}
		},

		/* ------------------------------- */
		// Title quick/inline edit feature //
		/* ------------------------------- */

		/*
		 * Show quick edit
		 */
		showTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			parent = $(this).parents('li:first'),
			width  = $(this).width(),
			title  = parent.find('.title-edit:first');

			e.stopPropagation();
			e.preventDefault();

			// Show the form
			$(this).hide();
			title.show();
		},

		/*
		 * Reset quick edit
		 */
		resetTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			parent = $(this).parents('li:first'),
			toggle = parent.find('.toggle-editable:first'),
			title  = parent.find('.title-edit:first');

			e.stopPropagation();
			e.preventDefault();

			// Hide inputs and show plain text
			toggle.show();
			title.hide();

			title.find('.title-text:first').val(toggle.html());
		},

		/*
		 * Submit quick edit
		 */
		submitTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			form   = $(this),
			parent = $(this).parents('li:first');

			e.stopPropagation();
			e.preventDefault();

			// Update the asset group in the database
			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				statusCode: {
					200: function ( data, textStatus, jqXHR ) {
						parent.find('.toggle-editable:first').html(parent.find('.title-text:first').val());

						// Hide inputs and show plain text
						parent.find('.toggle-editable:first').show();
						parent.find('.title-edit:first').hide();
					}
				}
			});
		},

		/* ----------------------------------------------------------- */
		// Handle Asset creation | file upload | non-file based assets //
		/* ----------------------------------------------------------- */

		/*
		 * Setup file uploader on drop zone
		 */
		attach: function () {
			var $      = HUB.CoursesOutline.jQuery,
			assetslist = $(this).find('.assets-list'),
			form       = $(this).find('form'),
			fileupload = $(this),
			dialog     = $('#dialog-confirm'),
			message    = '',
			targetName = '',
			ulCount    = 0;

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
				progressInterval: 500,
				add: function ( e, data ) {
					// Get asset handlers for this file type
					$.ajax({
						url: '/api/courses/asset/handlers',
						data: 'name=' + data.files[0].name,
						success: function ( json ) {
							// Iterate counter (for uniqueness)
							HUB.CoursesOutline.asset.counter++;
							var counter = HUB.CoursesOutline.asset.counter;

							// Make sure the file isn't too large (this is checking against the minimum of PHP's post and max upload limit)
							if(json.max_upload < (data.files[0].size / 1000000)) {
								// Warn about file being too large
								HUB.CoursesOutline.message.show('Sorry, the file that you uploaded ("' + data.files[0].name + '") exceedes the upload limit of ' + json.max_upload + ' MB');
							// Make sure we know what to do with this file type
							} else if(!json.handlers.length) {
								HUB.CoursesOutline.message.show('Sorry, we don\'t know what to do with files of type "' + json.ext + '"');
							// Check to see if there are multiple ways of handling this file type
							} else if(json.handlers.length > 1) {
								// Handle multiple handlers for extension
								message += '<ul class="handlers-list">';
								message += '<p class="asset file">' + data.files[0].name + '</p>';
								$.each(json.handlers, function(index, value){
									message += '<li class="handler-item">';
									message += '<a id="handler-item-' + counter + '-' + value.classname + '" class="dialog-button">';
									message += value.message;
									message += '</a>';
									message += '</li>';
								});
								message += '</ul>';

								// Add the message to the dialog box
								dialog.html(message);

								// Bind click events to the message buttons
								$.each(json.handlers, function ( index, value ) {
									targetName = '#handler-item-' + counter + '-' + value.classname;
									dialog.on('click', targetName, function(){
										fileupload.fileupload(
											'option',
											'formData',
											function ( form ) {
												var formData = form.serializeArray();
												// Add an explicit handler to the submitted data
												formData.push({
													'name':'handler',
													'value': value.classname
												});

												return formData;
											}
										);

										HUB.CoursesOutline.asset.submit(fileupload, data, counter, assetslist, form);

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
							} else { // No errors and only one file handler, so simply proceed
								HUB.CoursesOutline.asset.submit(fileupload, data, counter, assetslist, form);
							}
						}
					});
				}
			});
		},

		/* 
		 * Submit file upload
		 */
		submit: function ( fileupload, data, counter, assetslist, form ) {
			var $         = HUB.CoursesOutline.jQuery,
			progressBarId = 'progress-bar-'+counter,
			filename      = data.files[0].name;

			// Setup the progress handler
			fileupload.on('fileuploadprogress', function ( e, data ) {
				// @FIXME: there's got to be a better way to do this
				if (data.files[0].name == filename) {
					HUB.CoursesOutline.asset.updateProgressBar(data, progressBarId);
				}
			});

			// Add the progress bar
			HUB.CoursesOutline.asset.createProgressBar(progressBarId, data.files[0].name, assetslist);

			// Attach a cancel button
			$("#"+progressBarId+' .cancel').click(function ( e ) {
				data.jqXHR.abort();
				HUB.CoursesOutline.asset.resetProgresBar(progressBarId);
			});

			// Submit the file upload request as normal
			data.submit();

			// 201 created - this is returned by the standard asset upload
			data.jqXHR.done(function ( data, textStatus, jqXHR ) {
				if(data.assets.js) {
					// If our asset handler returns JS, we'll run that
					eval(data.assets.js);
				} else {
					// Insert asset
					HUB.CoursesOutline.asset.insert(data, assetslist, {"progressBarId":progressBarId});
				}
			}).fail(function ( jqXHR, textStatus, errorThrown ) {
				// Reset progress bar
				HUB.CoursesOutline.asset.resetProgresBar(progressBarId, 1000);
			});
		},

		/*
		 * Insert an asset upload progress bar into the page
		 */
		createProgressBar: function ( id, filename, assetslist ) {
			var $ = HUB.CoursesOutline.jQuery,
			html  = '';
			html += '<div id="' + id + '" class="uploadfiles-progress">';
			html += '<div class="cancel"></div>';
			html += '<div class="bar-border">';
			html += '<span class="filename">' + filename + '</span>';
			html += '<div class="bar"></div>';
			html += '</div>';
			html += '</div>';

			assetslist.append(html);
		},

		/*
		 * Update the asset upload progress bar
		 */
		updateProgressBar: function ( data, progressBarId ) {
			var $    = HUB.CoursesOutline.jQuery,
			progress = parseInt(data.loaded / data.total * 100, 10);

			$('.unit').find("#" + progressBarId + " .bar").stop(true, true).animate({'width': progress + '%'}, 500);

			// If progress is 100% and extension is zip, let's add some explanation
			if(progress == 100) {
				var extension = data.files[0].name.split('.');

				if(extension[extension.length - 1] == 'zip') {
					$('.unit').find("#" + progressBarId + " .filename").html('unzipping...');
				} else {
					$('.unit').find("#" + progressBarId + " .filename").html('running virus scan and finalizing upload...');
				}
			}
		},

		/*
		 * Reset/remove the asset upload progress bar,
		 * either because the upload is complete, or because it failed
		 */
		resetProgresBar: function ( id, timeout, callback ) {
			var $    = HUB.CoursesOutline.jQuery;
			callback = ($.type(callback) === 'function') ? callback : function(){};
			timeout  = ($.type(timeout)  === 'number')   ? timeout  : 0;

			setTimeout(function(){
				$("#"+id).fadeOut('slow', function() {
					$("#"+id).remove();
					callback();
				});
			},timeout);
		},

		/*
		 * Open file browser (alternative to drag-n-drop feature)
		 */
		openFileBrowser: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery;

			e.preventDefault();
			$(this).parents('.uploadfiles').find('.fileupload').trigger('click');
		},

		/*
		 * Show the auxillary attachments form
		 */
		auxAttachmentShow: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			help  = '/help/courses/builder',
			className = e.currentTarget.className.replace(/attach-/, "");

			e.preventDefault();
			$(this).siblings('.aux-attachments-form').find('.aux-attachments-content-label').html(e.currentTarget.title);
			$(this).siblings('.aux-attachments-form').find('.input-type').val(className);
			$(this).siblings('.aux-attachments-form').find('.help-info').attr('href', help+'#'+className);
			$(this).siblings('.aux-attachments-form').removeClass('attach-link attach-object attach-wiki browse-files').addClass(e.currentTarget.className);
			$(this).siblings('.aux-attachments-form').fadeIn();
		},

		/*
		 * Open aux attachments help in a new popup window
		 */
		auxAttachmentHelp: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			specs = 'width=800,height=600,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=20,top=20';

			e.preventDefault();
			window.open($(this).attr('href'), '_blank', specs);
		},

		/*
		 * Hide the auxillary attachments form
		 */
		auxAttachmentHide: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery;

			e.preventDefault();
			$(this).parents('form').fadeOut();
			$(this).parents('form').find('.input-content').val('');
		},

		/*
		 * Submit the auxillary attachments form
		 */
		auxAttachmentSubmit: function ( e ) {
			var $      = HUB.CoursesOutline.jQuery,
			form       = $(this).parents('form'),
			assetslist = $(this).parents('.asset-group-item').find('.assets-list');

			e.preventDefault();

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
							HUB.CoursesOutline.asset.insert(data, assetslist);

							// Hide the form again
							form.fadeOut(function() {
								form.find('.input-content').val('');
							});
						}
					}
				}
			});
		},

		/*
		 * Create/Edit the wiki auxillary attachment type
		 */
		editWiki: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			form  = $(this).siblings('.aux-attachments-form'),
			src   = '/courses/'+form.find('input[name="course_id"]').val()+'/'+form.find('input[name="offering"]').val()+'/outline?action=build';
			src  += '&scope=wiki&scope_id='+form.find('input[name="scope_id"]').val()+'&tmpl=component';

			e.preventDefault();

			$.contentBox({
				src         : src,
				title       : 'Create a wiki page',
				onAfterLoad : function( content ) {
					var t = $(this);
					content.find('.cancel').click(function() {
						$.contentBox('close');
					});

					content.find('.edit-form').submit(function ( e ) {
						e.preventDefault();

						// Create ajax call to change info in the database
						$.ajax({
							url: $(this).attr('action'),
							data: $(this).serializeArray(),
							statusCode: {
								// 201 created - this is returned by the standard asset upload
								201: function ( data, textStatus, jqXHR ){
									// Close box
									$.contentBox('close');
									HUB.CoursesOutline.asset.insert(data, $('#assetgroupitem_'+data.assets.assets.scope_id+' .assets-list'));
								}
							}
						});
					});
				}
			});
		},

		/*
		 * Asset templates
		 */
		templates : {
			// Template for default asset item (with data)
			item : [
				'<li id="asset_<%= asset_id %>" class="asset-item asset <%= asset_type %> <%= asset_subtype %> notpublished">',
					'<div class="sortable-assets-handle"></div>',
					'<div class="asset-item-title title toggle-editable"><%= asset_title %></div>',
					'<div class="title-edit">',
						'<form action="/api/courses/asset/save" class="asset-title-form">',
							'<input class="title-text" name="title" type="text" value="<%= asset_title %>" />',
							'<input class="asset-title-save" type="submit" value="Save" />',
							'<input class="asset-title-reset" type="reset" value="Cancel" />',
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

			// Template for empty asset item (where asset group has no assets)
			empty : [
				'<li class="asset-item asset missing nofiles">',
					'No files',
					'<span class="next-step-upload">',
						'Upload files &rarr;',
					'</span>',
				'</li>'
			].join("\n")
		}
	},

	/* ------------------ */
	// ASSET GROUP OBJECT //
	/* ------------------ */
	assetgroup: {
		/*
		 * Initialize the asset groups on the page
		 */
		init: function () {
			var $ = HUB.CoursesOutline.jQuery;

			// Setup clicks and triggers
			$(".unit")
				.on('click', '.asset-group-title', this.toggleEditTitle)
				.on('click', '.asset-group-title-cancel', this.toggleEditTitle)
				.on('click', '.asset-group-title-save', this.saveTitle)
				.on('click', '.asset-group-edit', this.edit)
				.on('click', '.add-new.asset-group-item', this.create)
				.on('click', ".asset-group-item-title.toggle-editable", this.showTitleQuickEdit)
				.on('click', ".assetgroup-title-reset", this.resetTitleQuickEdit)
				.on('submit', '.assetgroup-title-form', this.submitTitleQuickEdit);
			$('.outline-main')
				.on('assetGroupUpdate', this.update)
				.on('assetGroupCreate', this.refresh)
				.trigger('assetGroupCreate');
		},

		/*
		 * Add a new asset group to the page
		 */
		create: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			addNew = $(this),
			form   = $(this).find('form');

			// Stop default event and propagation
			e.preventDefault();
			e.stopPropagation();

			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				statusCode: {
					201: function( data ) {
						// Insert in our HTML (uses "underscore.js")
						addNew.before(_.template(HUB.CoursesOutline.assetgroup.templates.item, data));

						// Create a variable pointing to the new item just inserted
						var newAssetGroupItem = addNew.parent('.asset-group').find('.asset-group-item:not(.add-new):last');

						// Trigger asset group update
						$('.outline-main').trigger('assetGroupCreate', [newAssetGroupItem[0].id]);
					}
				}
			});
		},

		/*
		 * Refresh the asset group 
		 * (i.e. anything that would happen every time a new asset group is added)
		 */
		refresh: function ( e, selector ) {
			var $   = HUB.CoursesOutline.jQuery,
			refresh = false;

			// Set default param if none given
			if (!selector) {
				selector = '.asset-group-item';
				refresh  = false;
			} else {
				selector = "#"+selector;
				refresh  = true;

				if ($(selector).parents('.asset-group').find('.asset-group-item:not(.add-new)').length <= 1) {
					// We just added our first asset group item, so we should not refresh
					refresh = false;
				}
			}

			if (refresh) {
				// Refresh sortable
				$(selector).parents('.asset-group').sortable('refresh');
			} else {
				// Make items sortable
				$(".sortable").sortable({
					placeholder: "placeholder",
					handle: '.sortable-handle',
					forcePlaceholderSize: true,
					revert: false,
					tolerance: 'pointer',
					opacity: '0.6',
					items: 'li:not(.add-new, .asset)',
					axis: 'y',
					start: function start ( e, ui ) {
						// Style the placeholdwer based on the size of the item grabbed
						$(".placeholder").css({
							'height': ui.item.outerHeight(),
							'margin': ui.item.css('margin')
						});
					},
					update: function update () {
						// Save new order to the database
						var sorted = $(this).sortable('serialize');

						// Update the asset group ordering
						$.ajax({
							url: '/api/courses/assetgroup/reorder',
							data: sorted
						});
					}
				});
			}

			// Show sortable handles, edit and delete on hover
			$(selector).hoverIntent({
				over: function () {
					$(this).find('.sortable-handle').show('slide', 250);
					$(this).find('.asset-group-edit').show('slide', 250);
					$(this).not('.add-new').animate({"padding-left":60}, 250);
					$(this).find('.sortable-assets-handle').show('slide', 250);
					$(this).find('.asset:not(.nofiles)').animate({"margin-left":30}, 250);
					$(this).find('.asset-delete, .asset-preview, .asset-edit').animate({"opacity":0.8}, 250);
				},
				out: function () {
					$(this).find('.sortable-handle').hide('slide', 250);
					$(this).find('.asset-group-edit').hide('slide', 250);
					$(this).not('.add-new').animate({"padding-left":10}, 250);
					$(this).find('.sortable-assets-handle').hide('slide', 250);
					$(this).find('.asset:not(.nofiles)').animate({"margin-left":10}, 250);
					$(this).find('.asset-delete, .asset-preview, .asset-edit').animate({"opacity":0}, 250);
				},
				timeout: 150,
				interval: 150
			});

			// Add tooltips to attachment buttons
			$(selector + ' .aux-attachments a:not(.help-info)').tooltip();

			// Set up file uploader on our file upload boxes
			$(selector).each(HUB.CoursesOutline.asset.attach);

			// Finally, show the new item
			$(selector).show('slide', 1000);
		},

		/*
		 * Update the asset group information
		 */
		update: function ( e, obj, options ) {
			var $ = HUB.CoursesOutline.jQuery;

			if ($.type(options.state) === 'number') {
				var state = (options.state == 1) ? 'published' : 'unpublished';
				obj.removeClass('published unpublished').addClass(state);
			}
			if ($.type(options.title) === 'string') {
				if (options.type == 'parent') {
					obj.find('.asset-group-title').html(options.title);
				} else {
					obj.find('.asset-group-item-title').html(options.title);
				}
			}
		},

		/*
		 * Edit asset group information
		 */
		edit: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			ag    = $(this).parent('.asset-group-item'),
			form  = $(this).siblings('.uploadfiles').find('.uploadfiles-form'),
			src   = '/courses/'+form.find('input[name="course_id"]').val()+'/'+form.find('input[name="offering"]').val()+'/outline?action=build';
			src  += '&scope=assetgroup&scope_id='+form.find('input[name="scope_id"]').val()+'&tmpl=component';

			e.preventDefault();

			$.contentBox({
				src         : src,
				title       : 'Edit Asset Group',
				onAfterLoad : function( content ) {
					content.find('.cancel').click(function() {
						// Close the content box
						$.contentBox('close');
					});

					content.find('.edit-form').submit(function (e) {
						e.preventDefault();

						// Create ajax call post save
						$.ajax({
							url: $(this).attr('action'),
							data: $(this).serializeArray(),
							statusCode: {
								// 200 OK
								200: function ( data, textStatus, jqXHR ) {
									// Close box
									$.contentBox('close');

									var options = {
										'state' : data.assetgroup_state,
										'title' : data.assetgroup_title
									};

									// Trigger asset group update
									$('.outline-main').trigger('assetGroupUpdate', [ag, options]);
								}
							}
						});
					});
				}
			});
		},

		/*
		 * Show/hide the asset group edit title form
		 */
		toggleEditTitle: function ( e ) {
			var $     = HUB.CoursesOutline.jQuery,
			t         = $(this).parents('.asset-group-title-container'),
			container = $(this).parents('.asset-group-type-item').find('.asset-group-container');

			e.preventDefault();

			t.find('form').slideToggle(500);
			container.toggleClass('active', 500);
			$(this).parents('.asset-group-type-item-container').toggleClass('active', 500);
		},

		/*
		 * Save asset group title
		 */
		saveTitle : function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			form  = $(this).parents('form');

			e.preventDefault();

			// Create ajax call post save
			$.ajax({
				url: form.attr('action'),
				data: form.serializeArray(),
				statusCode: {
					// 200 OK
					200: function ( data, textStatus, jqXHR ){
						var options = {
							title : data.assetgroup_title,
							state : data.assetgroup_state,
							type  : 'parent'
						},
						ag = form.parents('.asset-group-type-item');

						// Trigger asset group update
						$('.outline-main').trigger('assetGroupUpdate', [ag, options]);

						// Trigger click on title to close edit mode
						form.siblings('.asset-group-title').trigger('click');
					}
				}
			});
		},

		/* ------------------------------- */
		// Title quick/inline edit feature //
		/* ------------------------------- */

		/*
		 * Show quick edit
		 */
		showTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			parent = $(this).parents('li:first'),
			width  = $(this).width(),
			title  = parent.find('.title-edit:first');

			e.stopPropagation();
			e.preventDefault();

			// Show the form
			$(this).hide();
			title.show();
		},

		/*
		 * Reset quick edit
		 */
		resetTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			parent = $(this).parents('li:first'),
			toggle = parent.find('.toggle-editable:first'),
			title  = parent.find('.title-edit:first');

			e.stopPropagation();
			e.preventDefault();

			// Hide inputs and show plain text
			toggle.show();
			title.hide();

			title.find('.title-text:first').val(toggle.html());
		},

		/*
		 * Submit quick edit
		 */
		submitTitleQuickEdit: function ( e ) {
			var $  = HUB.CoursesOutline.jQuery,
			form   = $(this),
			parent = $(this).parents('li:first');

			e.stopPropagation();
			e.preventDefault();

			// Update the asset group in the database
			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				statusCode: {
					200: function ( data, textStatus, jqXHR ) {
						parent.find('.toggle-editable:first').html(parent.find('.title-text:first').val());

						// Hide inputs and show plain text
						parent.find('.toggle-editable:first').show();
						parent.find('.title-edit:first').hide();
					}
				}
			});
		},

		/*
		 * Asset group templates
		 */
		templates : {
			// Default asset group item template
			item : [
				'<li class="asset-group-item" id="assetgroupitem_<%= assetgroup_id %>" style="<%= assetgroup_style %>">',
					'<div class="sortable-handle"></div>',
					'<div class="asset-group-edit"></div>',
					'<div class="uploadfiles">',
						'<p>Drag files here to upload</p>',
						'<p>or</p>',
						'<div class="aux-attachments">',
							'<form action="/api/courses/asset/new" class="aux-attachments-form attach-link">',
								'<label for"content" class="aux-attachments-content-label">Attach a link:</label>',
								'<textarea class="input-content" name="content" placeholder="" rows="6"></textarea>',
								'<input class="input-type" type="hidden" name="type" value="link" />',
								'<input class="aux-attachments-submit" type="submit" value="Add" />',
								'<input class="aux-attachments-cancel" type="reset" value="Cancel" />',
								'<input type="hidden" name="course_id" value="<%= course_id %>" />',
								'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
								'<input type="hidden" name="scope_id" value="<%= assetgroup_id %>" />',
								'<a href="/help/courses/builder" target="_blank" class="help-info">help</a>',
							'</form>',
							'<a href="#" title="Attach a link" class="attach-link"></a>',
							'<a href="#" title="Embed a Kaltura or YouTube Video" class="attach-object"></a>',
							'<a href="#" title="Include a wiki page" class="attach-wiki"></a>',
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
							'<form action="/api/courses/assetgroup/save" class="assetgroup-title-form">',
								'<input class="title-text" name="title" type="text" value="<%= assetgroup_title %>" />',
								'<input class="assetgroup-title-save" type="submit" value="Save" />',
								'<input class="assetgroup-title-reset" type="reset" value="Cancel" />',
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
			].join("\n")
		}
	},

	/* ----------- */
	// UNIT OBJECT //
	/* ----------- */
	unit: {
		/*
		 * Initialize the units on the page
		 */
		init: function () {
			var $     = HUB.CoursesOutline.jQuery,
			unit      = $('.unit-item'),
			title     = $('.unit-title-arrow'),
			assetlist = $('.asset-group-type-list');

			// Add the active class to the first unit (giving the expanded down arrow next to the title)
			title.first().addClass('unit-title-arrow-active');
			// Hide all of the units except for the first one
			assetlist.not(':first').hide();

			// Add events
			$(".unit")
				.on('click', '.unit-title-arrow', this.toggleUnits)
				.on('click', ".add-new.unit-item", this.create)
				.on('click', '.unit-title', this.toggleEditForm)
				.on('click', '.unit-edit-reset', this.toggleEditForm)
				.on('click', '.unit-edit-save', this.edit);
			$('.outline-main')
				.on('unitCreate', this.refresh)
				.on('unitUpdate', this.update)
				.on('assetGroupCreate', this.refresh)
				.on('assetUpdate', this.refresh)
				.on('assetCreate', this.refresh)
				.trigger('unitCreate');
		},

		/*
		 * Toggle units open and closed
		 */
		toggleUnits: function () {
			var $ = HUB.CoursesOutline.jQuery;

			if ($(this).hasClass('unit-title-arrow-active')){
				$(this).siblings('.asset-group-type-list').slideUp(500, function() {
					$('html, body').animate({scrollTop: $(this).parents('.unit').offset().top - 10}, 1000);
				});
				$(this).removeClass('unit-title-arrow-active');
			} else {
				$('.asset-group-type-list').slideUp(500);
				$('.unit-title-arrow').removeClass('unit-title-arrow-active');
				$(this).siblings('.asset-group-type-list').slideDown(500, function() {
					$('html, body').animate({scrollTop: $(this).parents('li').offset().top - 10}, 1000);
				});

				// Toggle class for arrow (active gives down arrow indicating expanded list)
				$(this).addClass('unit-title-arrow-active');
			}
		},

		/*
		 * Refresh unit(s)
		 * Capture all per unit (repeatable) initialization
		 */
		refresh: function ( e, selector ) {
			var $ = HUB.CoursesOutline.jQuery;

			if (!selector) {
				selector = '.unit-item';
			} else {
				if ($.type(selector) === 'object') {
					selector = "#" + $("#" + selector[0].id).parents('.unit-item')[0].id;
				} else {
					selector = "#" + selector;
				}
			}

			$(selector).each(HUB.CoursesOutline.unit.resizeProgressBar);

			$(selector + ' .datepicker').datepicker({
				dateFormat: 'yy-mm-dd'
			});
		},

		/*
		 * Create a new unit
		 */
		create: function ( e ) {
			var $   = HUB.CoursesOutline.jQuery,
			addNew  = $(this),
			form    = $(this).find('form');

			// Stop default event and propagation
			e.preventDefault();
			e.stopPropagation();

			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				statusCode: {
					201: function( data ) {
						// Insert in our HTML (uses "underscore.js")
						addNew.before(_.template(HUB.CoursesOutline.unit.templates.item, data));

						// Create a variable pointing to the new item just inserted
						var newUnit = addNew.parent('.unit').find('.unit-item:not(.add-new):last');

						// Trigger unit create
						$('.outline-main').trigger('unitCreate', [newUnit[0].id]);

						// Show the unit and slide to it
						$('.asset-group-type-list').delay(500).slideUp(500, function () {
							$('.unit-title-arrow').removeClass('unit-title-arrow-active');
							newUnit.find('.unit-title-arrow').addClass('unit-title-arrow-active');
							newUnit.find('.asset-group-type-list').slideDown(500, function () {
								$('html, body').animate({scrollTop: newUnit.offset().top - 10}, 500);
							});
						});
					}
				}
			});
		},

		/*
		 * Update unit in page
		 */
		update: function ( e, id, data ) {
			var $ = HUB.CoursesOutline.jQuery;

			$("#"+id).find('.unit-title-value').html(data.unit_title);
		},

		/*
		 * Show/hide edit form
		 */
		toggleEditForm: function () {
			var $ = HUB.CoursesOutline.jQuery;

			var editContainer = $(this).parents('.unit-edit-container');
			editContainer.find('.unit-edit').slideToggle(500, function () {
				editContainer.toggleClass('active');
			});
		},

		/*
		 * Edit unit
		 */
		edit: function ( e ) {
			var $ = HUB.CoursesOutline.jQuery,
			form  = $(this).parent('form');

			e.preventDefault();

			// Create ajax call to edit unit
			$.ajax({
				url: form.attr('action'),
				data: form.serializeArray(),
				statusCode: {
					// 200 OK
					200: function ( data, textStatus, jqXHR ) {
						// Trigger update and click to close edit form
						$('.outline-main').trigger('unitUpdate', [form.parents('.unit-item')[0].id, data]);
						form.parent('.unit-edit').siblings('.unit-title').trigger('click');
					}
				}
			});
		},

		/*
		 * Set width of progress bar
		 * This is the unit progress bar (not the asset upload progress bar)
		 * This feature attempts to visual capture whether or not a unit has
		 * the required items.
		 */
		resizeProgressBar: function () {
			var $       = HUB.CoursesOutline.jQuery,
			progressbar = $('.progress-indicator'),
			count       = 0,
			haveitems   = 0,
			percentage  = 0,
			pclass      = 'stop';

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
		},

		/*
		 * Unit templates
		 */
		templates : {
			// Standard unit item template
			item : [
				'<li class="unit-item" id="unit_<%= unit_id %>">',
					'<div class="unit-title-arrow"></div>',
					'<div class="unit-edit-container">',
						'<div class="title unit-title">',
							'<div class="unit-title-value"><%= unit_title %></div>',
							'<div class="edit">edit</div>',
						'</div>',
						'<div class="clear"></div>',
						'<div class="unit-edit">',
							'<form action="/api/courses/unit/save" class="unit-edit-form">',
								'<label for="title">Title:</label>',
								'<input class="unit-edit-text" name="title" type="text" value="<%= unit_title %>" placeholder="title" />',
								'<label for="publish_up">Publish start date:</label>',
								'<input class="unit-edit-publish-up datepicker" name="publish_up" type="text" value="" placeholder="Publish start date" />',
								'<label for="publish_down">Publish end date:</label>',
								'<input class="unit-edit-publish-down datepicker" name="publish_down" type="text" value="" placeholder="Publish end date" />',
								'<input class="unit-edit-save" type="submit" value="Save" />',
								'<input class="unit-edit-reset" type="reset" value="Cancel" />',
								'<input type="hidden" name="course_id" value="<%= course_id %>" />',
								'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
								'<input type="hidden" name="section_id" value="<%= section_id %>" />',
								'<input type="hidden" name="id" value="<%= unit_id %>" />',
							'</form>',
						'</div>',
					'</div>',
					'<div class="progress-container">',
						'<div class="progress-indicator"></div>',
					'</div>',
					'<div class="clear"></div>',
					'<ul class="asset-group-type-list" style="display:none">',
						'<% _.each(assetgroups, function(assetgroup){ %>',
							'<li class="asset-group-type-item published">',
								'<div class="asset-group-type-item-container">',
									'<div class="asset-group-title-container">',
										'<div class="asset-group-title title">',
											'<div class="asset-group-title-edit edit">edit</div>',
											'<div class="title"><%= assetgroup.assetgroup_title %></div>',
										'</div>',
										'<form action="/api/courses/assetgroup/save">',
											'<div class="label-input-pair">',
												'<label for="title">Title:</label>',
												'<input class="" name="title" type="text" value="<%= assetgroup.assetgroup_title %>" />',
											'</div>',
											'<div class="label-input-pair">',
												'<label for="state">Published:</label>',
												'<select name="state">',
													'<option value="0">No</option>',
													'<option value="1" selected="selected">Yes</option>',
												'</select>',
											'</div>',
											'<input class="asset-group-title-save" type="submit" value="Save" />',
											'<input class="asset-group-title-cancel" type="reset" value="Cancel" />',
											'<input type="hidden" name="course_id" value="<%= course_id %>" />',
											'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
											'<input type="hidden" name="id" value="<%= assetgroup.assetgroup_id %>" />',
										'</form>',
									'</div>',
									'<div class="asset-group-container">',
										'<ul class="asset-group sortable">',
											'<li class="add-new asset-group-item">',
												'Add a new ',
												'<%',
													'if (assetgroup.assetgroup_title.slice(-3) === "ies") {',
														'print(assetgroup.assetgroup_title.toLowerCase().replace(/ies$/, "y"));',
													'} else {',
														'print(assetgroup.assetgroup_title.toLowerCase().replace(/s$/, ""));',
													'}',
												'%>',
												'<form action="/api/courses/assetgroup/save">',
													'<input type="hidden" name="course_id" value="<%= course_id %>" />',
													'<input type="hidden" name="offering" value="<%= offering_alias %>" />',
													'<input type="hidden" name="unit_id" value="<%= unit_id %>" />',
													'<input type="hidden" name="parent" value="<%= assetgroup.assetgroup_id %>" />',
												'</form>',
											'</li>',
										'</ul>',
									'</div>',
								'</div>',
							'</li>',
						'<% }) %>',
					'</ul>',
				'</li>'
			].join("\n")
		}
	},

	/* -------------- */
	// MESSAGE OBJECT //
	/* -------------- */
	message: {
		/*
		 * Initialize messaging on the page
		 */
		init: function () {
			var $      = HUB.CoursesOutline.jQuery,
			errorBox   = $('.error-box'),
			errorClose = $('.error-close');

			errorClose.on('click', this.hide);
		},

		/*
		 * Show the message bar
		 */
		show: function ( message, timeout ) {
			var $    = HUB.CoursesOutline.jQuery,
			errorBox = $('.error-box'),
			error    = $('.error-message');

			error.html(message);
			errorBox.slideDown('fast');

			if (timeout) {
				setTimeout(this.hide, timeout);
			}
		},

		/*
		 * Hide the message bar
		 */
		hide: function () {
			var $      = HUB.CoursesOutline.jQuery,
			errorBox   = $('.error-box'),
			errorClose = $('.error-close');

			errorBox.slideUp('fast');
		}
	}
};

/* ------------------ */
// Content box plugin //
/* ------------------ */

(function( $ ) {

	var settings = {},
		methods  = {
			init : function ( options ) {
				// Create some defaults, extending them with any options that were provided
				settings = $.extend( {
					element     : $('.content-box'),
					title       : 'Edit Item',
					src         : '/courses',
					onAfterLoad : function ( content ) {}
				}, options);

				// Add close on escape
				$(document).bind('keydown', function ( e ) {
					if(e.which == 27) {
						methods.close();
					}
				});

				// Add close on click of close button
				settings.element.find('.content-box-close').on('click', function () {
					methods.close();
				});

				// Finally, execute show
				methods.show();
			},
			show : function () {
				$('.content-box-header span').html(settings.title);
				settings.element.find('.loading-bar').show();
				settings.element.show('slide', {'direction':'down'}, 500, function () {
					$(this).siblings('.content-box-overlay').fadeIn(100);
					$(this).find('.content-box-inner').append('<iframe src="'+settings.src+'"></iframe>');

					// Execute after load function
					settings.element.find('iframe').load(function () {
						var content = $(this).contents();

						// Add close on escape within iframe as well
						content.bind('keydown', function ( e ) {
							if(e.which == 27) {
								methods.close();
							}
						});

						settings.element.find('.loading-bar').hide();
						settings.onAfterLoad( content );
					});
				});
			},
			close : function () {
				settings.element.find('iframe').remove();
				settings.element.hide('slide', {'direction':'down'}, 500, function () {
					$('.content-box-overlay').fadeOut(100);
				});
			}
	};

	$.contentBox = function ( method ) {
		// Method calling logic
		if ( methods[ method ] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' + method + ' does not exist on jQuery.contentBox' );
		}
	};
})( jQuery );