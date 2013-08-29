/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Project File Manager JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectFiles = {
	jQuery: jq,
	bchecked: 0,
	bselected: new Array(),
	bfolders: new Array(),
	pub: 0,
	remote: 0,
	service: '',
	sConflict: 0,
	dir: 0,
	syncTimer: '',
	
	initialize: function() 
	{
		var $ = this.jQuery
			boxes = $('.checkasset'),
			toggle = $('#toggle');
		
		var bchecked 	= 0;
		var bselected 	= new Array();
		var bfolders 	= new Array();
		var pub 		= 0;
		var dir 		= 0;
		var remote 		= 0;
														
		// Enable confirmations on actions
		HUB.ProjectFiles.addConfirms();
		
		// Enable file preview
		HUB.ProjectFiles.previewFiles();
		
		// Enable rename
		HUB.ProjectFiles.enableRename();
		
		// Extended upload		
		if (HUB.ProjectUploadFiles)
		{
			HUB.ProjectUploadFiles.initialize();
		}
							
		// File management
		if ($('#file-manage').length) 
		{
			// Show the controls (turned off without Javascript)				
			if ($('#file-manage').hasClass('hidden')) {
				$('#file-manage').removeClass('hidden');
			}
		}
				
		// Checkboxes behavior			
		if (boxes.length > 0) 
		{
			boxes.each(function(i, item) 
			{	
				$(item).on('click', function(e) {
					HUB.ProjectFiles.collectSelections(item, 0);
					HUB.ProjectFiles.watchSelections();
				});				  												  
			});						
		}
		
		// Toggle
		if (toggle.length > 0) {
			toggle.on('click', function(e) {								
				var tog = toggle.attr('checked') == 'checked' ? 2 : 3;
				HUB.ProjectFiles.remote = 0;
				boxes.each(function(i, item) 
				{	
					if (toggle.attr('checked') == 'checked') {
						$(item).attr('checked','checked');						
					}
					else
					{
						$(item).removeAttr("checked");
					}
					
					HUB.ProjectFiles.collectSelections(item, tog);
					HUB.ProjectFiles.watchSelections();							  												  
				});		
			});
		}
		
		// Activate file management buttons
		HUB.ProjectFiles.activateFileOptions();	
		
		// Load shared files info
		HUB.ProjectFiles.iniSync();	
		
		// Show more/less version history
		HUB.ProjectFiles.showRevisions();
		
		// Show diffs
		HUB.ProjectFiles.showDiffs();
		
		$('html').on('click', function(e) 
		{
			if ($('#more-options').length && !$('#more-options').hasClass('hidden'))
			{
				$('#more-options').addClass('hidden');
			}
		});
		
		// Disk space indicator
		if (HUB.ProjectFilesDiskSpace)
		{
			HUB.ProjectFilesDiskSpace.initialize();
		}
	},
	
	showDiffs: function() 
	{
		var $ = this.jQuery
			ndiff = $('.diff-new'),
			odiff = $('.diff-old');
			
		var form = $('#hubForm-ajax').length ? $('#hubForm-ajax') : $('#plg-form');
		
		if ($('#rundiff').length > 0 && ndiff.length > 0 && odiff.length > 0)
		{
			$('#rundiff').on('click', function(e) 
			{
				e.preventDefault();
				var selNew  = $(".diff-new:checked").val();
				var selOld  = $(".diff-old:checked").val();
				
				var selectedIndex = odiff.index(odiff.filter(':checked'));
				
				if (selNew == selOld && odiff.eq(selectedIndex + 1).length > 0)
				{
					$(".diff-old:checked").val(odiff.eq(selectedIndex + 1).val());
				}
				
				form.submit();

			});
		}
	},
		
	showRevisions: function() 
	{
		var $ = this.jQuery
			showmore = $('.showmore'),
			showless = $('.showless');
			
		if (showmore.length > 0)
		{
			showmore.each(function(i, item) 
			{	
				$(item).on('click', function(e) {
					var id = $(item).parent().attr('id').replace('short-', '');
					var longtxt = $('#long-' + id);
					var shorttxt = $('#short-' + id);
					
					if (longtxt.length > 0 && shorttxt.length > 0)
					{
						shorttxt.addClass('hidden');
						longtxt.removeClass('hidden');
					}
				});				  												  
			});
			
			showless.each(function(i, item) 
			{	
				$(item).on('click', function(e) {
					var id = $(item).parent().attr('id').replace('long-', '');
					var longtxt = $('#long-' + id);
					var shorttxt = $('#short-' + id);
					
					if (longtxt.length > 0 && shorttxt.length > 0)
					{
						longtxt.addClass('hidden');
						shorttxt.removeClass('hidden');
					}
				});				  												  
			});
		}		
	},
	
	checkSyncStatus: function(action)
	{		
		clearTimeout(HUB.ProjectFiles.typingTimer);
		var repeat = action == 'progress' ? 20 : 50000;
		
		if (action == 'progress' && !$('#sync-wrap').hasClass('syncing'))
		{
			$('#sync-wrap').addClass('syncing');
		}
		else if (action != 'progress' && $('#sync-wrap').hasClass('syncing'))
		{
			$('#sync-wrap').removeClass('syncing');
		}	
		
		var projectid = $('#projectid').length ? $('#projectid').val() : 0;
		var sortdir   = $('#sortdir').length ? $('#sortdir').val() : 0;
		var sortby    = $('#sortby').length ? $('#sortby').val() : 0;
		var subdir    = $('#subdir').length ? $('#subdir').val() : '';
		
		var statusUrl = '/projects/' + projectid + '/files/?action=sync_status&no_html=1&ajax=1&subdir=' + subdir;
		if (sortby)
		{
			statusUrl = statusUrl + '&sortby=' + sortby;
		}
	
		if (sortdir)
		{
			statusUrl = statusUrl + '&sortdir=' + sortdir;
		}
		
		HUB.ProjectFiles.typingTimer = setTimeout((function() 
		{  
			var status = $.get(statusUrl, {}, function(response) 
			{
				if (response)
				{
				    try {
				        response = $.parseJSON(response);
				    } 
					catch (e) 
					{
						return;
				    }
				}
				
				if (response.msg)
				{
					$('#sync-status').html(response.msg);
				}
				
				if (response.status == 'progress')
				{
					action = 'progress';
				}
				else
				{
					action = 'complete';
				}
				
				if (response.output)
				{
					$('#plg-content').html(response.output);

					// Make sure everything works again
					HUB.Projects.initialize();
					HUB.ProjectFiles.initialize();
					HUB.ProjectFiles.preSelect();
				}
				
				// Timed sync request
				if (response.auto)
				{
					HUB.ProjectFiles.loadSharedFiles(1, 0);
				}
								
				// Repeat call
				HUB.ProjectFiles.checkSyncStatus(action);	
			});
		}), repeat);
	},
	
	iniSync: function() 
	{
		var $ = this.jQuery;
		var sharing 	= $('#sharing').length ? $('#sharing').val() : 0;
		var sync 		= $('#sync').length ? $('#sync').val() : 0;
		
		// Check that we have connections
		if (sharing != 1 || !$('#a-sync').length)
		{
			return false;
		}
				
		// Check sync status every 5 minutes
		HUB.ProjectFiles.checkSyncStatus('check');
		
		$('#a-sync').unbind();
		
		// Initiate sync manually		
		$('#a-sync').on('click', function(e) 
		{
			e.preventDefault();
			HUB.ProjectFiles.loadSharedFiles(0, 0);
		});
		
		// Sync request on page load (usually after local change)
		if (sync == 1)
		{
			HUB.ProjectFiles.loadSharedFiles(1, 1);
		}
	},
		
	loadSharedFiles: function(auto, queue) 
	{
		var $ = this.jQuery;
			
		// Can't stop syncing
		if ($('#sync-wrap').hasClass('syncing'))
		{
			return false;
		}
			
		var projectid = $('#projectid').length ? $('#projectid').val() : 0;
		var sortdir   = $('#sortdir').length ? $('#sortdir').val() : '';
		var sortby    = $('#sortby').length ? $('#sortby').val() : '';
		var subdir    = $('#subdir').length ? $('#subdir').val() : '';
		var url = '/projects/' + projectid + '/files/?action=sync';
		url = url + '&no_html=1&ajax=1';
		url = url + '&subdir=' + subdir;
		
		if (sortby)
		{
			url = url + '&sortby=' + sortby;
		}
	
		if (sortdir)
		{
			url = url + '&sortdir=' + sortdir;
		}
		
		if (auto)
		{
			url = url + '&auto=1';
		}
		
		if (queue)
		{
			url = url + '&queue=1';
		}
		
		$('#sync_output').addClass('hidden');			
		
		HUB.ProjectFiles.checkSyncStatus('progress');
								
		var ajax = $.get(url, {}, function(response) 
		{									
			if (response)
			{
			    try {
			        response = $.parseJSON(response);
			    } 
				catch (e) 
				{
			        //alert(response);
					return;
			    }
			}
			else
			{
				$('#status-msg').css({'opacity':100});
				$('#status-msg').html('<p class="witherror">Oups! Unknown sync error, please try again!</p>');
				return false;
			}
				
			if (response.output)
			{
				$('#plg-content').html(response.output);

				// Make sure everything works again
				HUB.Projects.initialize();
				HUB.ProjectFiles.initialize();
				HUB.ProjectFiles.preSelect();
			}
			
			if (response.debug)
			{
				$('#sync_output').removeClass('hidden');
				$('#sync_output').html(response.debug);
			}
			
			if (response.error && !response.auto)
			{
				$('#status-msg').css({'opacity':100});
				$('#status-msg').html('<p class="witherror">' + response.error + '</p>');
			}
			else if (response.message && !response.auto)
			{
				$('#status-msg').css({'opacity':100});
				$('#status-msg').html('<p>' + response.message + '</p>');
			}				
		});
	},
	
	preSelect: function() 
	{
		var $ = this.jQuery;
		var boxes = $('.checkasset');
		var idx = -1;
			
		if (HUB.ProjectFiles.bchecked > 0 && boxes.length > 0)
		{
			HUB.ProjectFiles.remote = 0;
			HUB.ProjectFiles.service = '';
			
			boxes.each(function(i, el) 
			{	
				if ($(el).hasClass('dirr')) {
					//var idx = HUB.ProjectFiles.bfolders.indexOf($(el).val());
					var idx = HUB.Projects.getArrayIndex($(el).val(), HUB.ProjectFiles.bfolders);
				}
				else {
					//var idx = HUB.ProjectFiles.bselected.indexOf($(el).val());
					var idx = HUB.Projects.getArrayIndex($(el).val(), HUB.ProjectFiles.bselected);
				}
				
				if (idx != -1 && $(el).attr('checked') != 'checked')
				{
					$(el).attr('checked', 'checked');
					HUB.ProjectFiles.collectSelections(el, 0);
					HUB.ProjectFiles.watchSelections();	
				}		  												  
			});						
		}
	},
	
	addQuestion: function() 
	{		
		var $ = this.jQuery;	
		var expand = $('#expand_zip');
		if ($('#confirm-box').length > 0) {
			$('#confirm-box').remove();
		}
		
		var parentul = $('#c-filelist').length > 0 ? $('#c-filelist') : $('#filelist');
		var frm = $('#upload-form').length > 0 ? $('#upload-form') : $('#plg-form');
		
		// Add confirmation
		$(parentul).before('<div class="confirmaction" id="confirm-box" style="display:block;">' + 
			'<p>Do you wish to expand the file you are uploading?</p>' + 
			'<p>' + 
				'<span class="confirm" id="c-expand">yes, expand</span>' + 
				'<span class="confirm c-no" id="c-archive">no, upload as an archive</span>' + 
				'<a href="#" class="cancel" id="confirm-box-cancel">cancel</a>' + 
			'</p>' + 
		'</div>');
		
		$('#confirm-box-cancel').on('click', function(e){
			e.preventDefault();
			$('#confirm-box').remove();
		});
		
		$('#c-expand').on('click', function(e){
			e.preventDefault();
			expand.val(1);
			HUB.ProjectFiles.submitViaAjax('Uploading file(s)... Please wait');
			frm.submit();
			$('#confirm-box').remove();
		});
		
		$('#c-archive').on('click', function(e){
			e.preventDefault();
			HUB.ProjectFiles.submitViaAjax('Uploading file(s)... Please wait');
			frm.submit();
			$('#confirm-box').remove();
		});
		
		// Move close to item
		var coord = $('#uploader').position();		
		$('#confirm-box').css('left', coord.left).css('top', coord.top + 200);	
	},
	
	submitViaAjax: function (txt)
	{
		var $ = this.jQuery;	
		
		$('#status-msg').css({'opacity':100});	
		var log = $('#status-msg').empty().addClass('ajax-loading');
		
		if (!txt)
		{
			txt = 'Please wait while we are performing your request...';	
		}
		
		// Add element
		$('#status-msg').html(HUB.ProjectFiles.loadingIma(txt));
		$('#status-msg').css({'opacity':100});
	},
	
	loadingIma: function(txt)
	{
		var $ = this.jQuery;
		
		var html = '<span id="fbwrap">' + 
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ' +
			txt +
			'</span>' +
		'</span>';
		
		return html;
	},
	
	activateFileOptions: function ()
	{
		var $ = this.jQuery;
		var bchecked 	= this.bchecked;
		var bselected 	= this.bselected;
		var bfolders 	= this.bfolders;
		var pub 		= this.pub;
		var dir 		= this.dir;
		var remote 		= this.remote;
		
		var manage 		= $('#file-manage');
		var	ops 		= $('.fmanage');
			
		var bWidth 		= 600;
		
		// File options 
		ops.each(function(i, item) 
		{	
			// disable options until a box is checked
			var aid = $(item).attr('id');
			if (aid != 'a-folder' && aid != 'a-upload') 
			{
				$(item).addClass('inactive');
			}
			
			var connected = true;

			if (aid == 'a-share')
			{
				if ($(item).hasClass('service-google') && $('#service-google').length > 0 && $('#service-google').val() == '0' )
				{
					connected = false;
				}
			}
						
			// Not every action happens in a light box 
			if (aid != 'a-download' && aid != 'a-history' 
				 && connected == true) 
			{
				var href = $(item).attr('href');
				if (href.search('&no_html=1') == -1) {
					href = href + '&no_html=1';
				}
				if (href.search('&ajax=1') == -1) {
					href = href + '&ajax=1';
				}
				$(item).attr('href', href);
			}
			
			$(item).on('click', function(e) 
			{	
				var aid = $(item).attr('id');
				if (aid != 'a-download' && aid != 'a-history' 
					 && connected == true) 
				{
					e.preventDefault();
				}

				if ($(item).hasClass('inactive')) {
					e.preventDefault();
				}
				else
				{	
					// Clean up url
					var clean = $(item).attr('href').split('&asset[]=', 1);
					$(item).attr('href', clean);	
					
					var clean = $(item).attr('href').split('&folder[]=', 1);
					$(item).attr('href', clean);			

					// Add our checked boxes variables
					if (bselected.length > 0) {
						for (var k = 0; k < bselected.length; k++) {
							$(item).attr('href', $(item).attr('href') + '&asset[]=' + bselected[k]);
						}
					}
					
					// Add our selected folders variables
					if (bfolders.length > 0) {
						for (var k = 0; k < bfolders.length; k++) {
							$(item).attr('href', $(item).attr('href') + '&folder[]=' + bfolders[k]);
						}
					}
					
					// Add case and subdir
					var subdir  = $('#subdir') ? $('#subdir').val() : '';
					var fcase   = $('#case') ? $('#case').val() : '';
					
					if (subdir) {
						$(item).attr('href', $(item).attr('href') +	'&subdir=' + subdir);
					}
					if (fcase) {
						$(item).attr('href', $(item).attr('href') +	'&case=' + fcase);
					}
					
					if (aid == 'a-share')
					{
						converted = 2;
						if ($(item).hasClass('stop-sharing'))
						{
							converted = 1;
						}
												
						$(item).attr('href', $(item).attr('href') +	'&converted=' + converted);
					}
					
					if (aid == 'a-compile' || aid == 'a-upload')
					{
						bWidth = 800;
					}
					
					// Show more options
					if (aid == 'a-more')
					{
						HUB.ProjectFiles.showExtraOptions($(item).attr('href'));
						e.stopPropagation();
						return;
					}
										
					// make AJAX call
					if (aid != 'a-download' && aid != 'a-history' && connected == true)  
					{
						$.fancybox(this,{
							type: 'ajax',
							width: bWidth,
							height: 'auto',
							autoSize: false,
							fitToView: false,
							wrapCSS: 'sbp-window',
							afterShow: function() 
							{
								if ($('#cancel-action')) {
									$('#cancel-action').on('click', function(e) {
										$.fancybox.close();
									});
								}
								var proceed = 1;
																
								if (proceed == 1 && aid != 'a-upload')
								{
									$('#hubForm-ajax').submit(function() 
									{    
										var txt = '';
										if (aid == 'a-delete')
										{
											txt = 'Deleting file(s)...';
										}
										HUB.ProjectFiles.submitViaAjax(txt);
										$.fancybox.close();
									});
								}
							}
						});
					}												
				}
			});								  												  
		});
	},
		
	showExtraOptions: function (url)
	{
		var $ = this.jQuery;
		
	 	if (!$('#more-options').length)
		{
			return false;
		}
		
		if (!$('#more-options').hasClass('hidden'))
		{
			$('#more-options').addClass('hidden');
			$('#more-options').html('');
			return;
		}
		
		// Move close to item
		var coord = $('#a-more').position();			
		$('#more-options').css('left', coord.left);		
				
		$.get(url, {}, function(data) 
		{
			if (data && data != 'NA') 
			{
				if ($('#more-options').hasClass('hidden'))
				{
					$('#more-options').removeClass('hidden');
				}
				$('#more-options').html(data);
			}
		});
								
	},
	
	enableRename: function ()
	{
		var $ = this.jQuery;
		
		// Renaming
		var rename = $('.rename');
		if (rename.length > 0) {
			rename.each(function(i, item) {
				$(item).on('click', function(e) {
					e.preventDefault();
					var id = $(item).attr('id').replace('rename-c-', '');
					var edit = $('#edit-c-' + id);
					if (edit)
					{
							var classes = edit.attr('class').split(" ");
							var dir = '';
							var file = '';

							for ( i=classes.length-1; i>=0; i-- ) 
							{
								if (classes[i].search("file:") >= 0)
								{
									file = classes[i].split(":")[1];
								}
								if (classes[i].search("dir:") >= 0)
								{
									dir = classes[i].split(":")[1];
								}
							}
							file = unescape(file);
							file = unescape(file.replace(/\+/g, " "));
							dir = unescape(dir);
							dir = unescape(dir.replace(/\+/g, " "));
							HUB.ProjectFiles.addEditForm($(item), edit, file, dir);
					}
				});
			});				
		}
	},
	
	addEditForm: function (link, el, file, dir) 
	{		
		var $ = this.jQuery;	
		if ($("#editv").length > 0) {
		   return;
		}
		el.addClass('hidden');
		link.addClass('hidden');
			
		var original = '';
		var rename   = 'file';
		if (file)
		{
			original = file;
		}
		else
		{
			rename   = 'dir';
			original = dir;
		}	
		
		var fcase   = $('#case') ? $('#case').val() : '';
		var action  = fcase == 'files' ? 'action' : 'do';
		
		// Add form
		el.parent().append('<label id="editv">' + 
			'<input type="hidden" name="' + action + '" value="renameit" />' +
			'<input type="hidden" name="rename" value="' + rename + '" />' +  
			'<input type="hidden" name="oldname" value="' + original + '" />' + 
			'<input type="text" name="newname" value="' + original + '" maxlength="100" class="vlabel" />' +
			'<input type="submit" value="rename" id="submit-rename" />' +
			'<input type="button" value="cancel" class="cancel" id="cancel-rename" />' +
		'</label>');
		
		$('#cancel-rename').on('click', function(e){
			e.preventDefault();
			$('#editv').remove();
			el.removeClass('hidden');
			link.removeClass('hidden');
		});	
		
		$('#submit-rename').on('click', function(e){			
			HUB.ProjectFiles.submitViaAjax('Renaming selected item');
		});	
	},
	
	collectSelections: function (el, tog) 
	{
		var $ = this.jQuery;
		var bchecked 	= this.bchecked;
		var bselected 	= this.bselected;
		var bfolders 	= this.bfolders;
		var pub 		= this.pub;
		var dir 		= this.dir;
		var remote 		= this.remote;
		var service 	= this.service;
		var sConflict 	= this.sConflict;
				
		// Is item checked?
		if ($(el).attr('checked') == 'checked' || tog == 2) 
		{
			if ($(el).hasClass('publ')) {
				pub = pub + 1;
			}
			if ($(el).hasClass('remote')) {
				remote = remote + 1;
				
				// Service is determined by first selected remote item
				if ($(el).hasClass('service-google') && remote == 1) {
					service = 'google';
				}
				// TBD indicate conflict with other services
				if ($(el).hasClass('notconnected')) {
					sConflict = 1;
				}
			}
			if ($(el).hasClass('dirr')) 
			{	
				//var idx = bfolders.indexOf($(el).val());
				var idx = HUB.Projects.getArrayIndex($(el).val(), bfolders);

				if (idx == -1) 
				{
					dir = dir + 1;
					bfolders.push($(el).val());	
				}
			}
			else {
				//var idx = bselected.indexOf($(el).val());
				var idx = HUB.Projects.getArrayIndex($(el).val(), bselected);
				if (idx == -1) 
				{
					bselected.push($(el).val());
				}
			}
			// Add class to tr
			$(el).parent().parent().addClass("i-selected");
		}
		else 
		{		 	
			// Item unchecked
			if ($(el).hasClass('publ')) {
				pub = pub - 1;
			}
			if ($(el).hasClass('remote')) 
			{
				remote = remote - 1;
				
				// Clean up service
				if ($(el).hasClass('service-google') && remote == 0) {
					service = '';
				}
				if (remote == 0) {
					sConflict = 0;
				}
			}
			if ($(el).hasClass('dirr')) {
				dir = dir - 1;
				var idx = HUB.Projects.getArrayIndex($(el).val(), bfolders);
				//var idx = bfolders.indexOf($(el).val());
				if (idx!=-1) bfolders.splice(idx, 1);
			}
			else {
				//var idx = bselected.indexOf($(el).val());
				var idx = HUB.Projects.getArrayIndex($(el).val(), bselected);
				if (idx!=-1) bselected.splice(idx, 1);
			}
			
			// Remove class from tr
			$(el).parent().parent().removeClass("i-selected");
		}

		if (pub <= 0) {
			pub = 0;
		}
		if (dir <= 0) {
			dir = 0;
		}
		if (remote <= 0) {
			remote = 0;
		}
		
		// Record new values
		HUB.ProjectFiles.bchecked = bselected.length + bfolders.length;
		HUB.ProjectFiles.bselected = bselected;
		HUB.ProjectFiles.bfolders = bfolders;
		HUB.ProjectFiles.dir = dir;
		HUB.ProjectFiles.pub = pub;
		HUB.ProjectFiles.remote = remote;
		HUB.ProjectFiles.service = service;
		HUB.ProjectFiles.sConflict = sConflict;
	},
	
	addConfirms: function ()
	{
		var $ = this.jQuery;
		
		// Confirm directory deletion
		if ($('#delete-dir') && HUB.Projects) {
			$('#delete-dir').on('click', function(e) {
				e.preventDefault();			
				HUB.Projects.addConfirm($('#delete-dir'), 
				'Are you sure you want to delete this directory and all its contents?', 
				'Yes, delete', 'No, do not delete');
			});
		}
		
		// Confirm directory deletion
		if ($('#disconnect') && HUB.Projects) {
			$('#disconnect').on('click', function(e) {
				e.preventDefault();			
				HUB.Projects.addConfirm($('#disconnect'), 
				'Are you sure you want to disconnect project from this service?', 
				'Yes, disconnect and remove remote data', 'No, do not disconnect');
			});
		}
	},

	previewFiles: function ()
	{
		var $ = this.jQuery;
		
		// Preview files
		var preview = $('.preview');		
		var div = $('#preview-window');
		var keyupTimer2 = '';
		var preview_open = 0;
		var in_preview = 0;
		
		if ($('#plg-content').length > 0 && div.length > 0)
		{
			$('#plg-content').on('mouseout', function(e) {
				div.css('display', 'none');
				preview_open = 0;					
			});
		}
		
		var sharing 	= $('#sharing').length ? $('#sharing').val() : 0;
		var sync 		= $('#sync').length ? $('#sync').val() : 0;
		
		preview.each(function(i, item) 
		{
			$(item).on('click', function(e) 
			{
				if (sync == 1 && sharing == 1) 
				{
					// Can't download file while syncing with remote service
					e.preventDefault();
				}
			});
			
			$(item).on('mouseover', function(e) 
			{
				e.preventDefault();
				var coord = $(item).position();
				if (keyupTimer2) {
					clearTimeout(keyupTimer2);
				}
				
				if (div.length) {
					div.empty();
					
					if ($(item).parent().parent().hasClass('google-resource'))
					{
						div.append('<p class="ajax-loading">' + HUB.ProjectFiles.loadingIma('Fetching remote data') + '</p>');
						div.css('display', 'block');
					}
					
					in_preview = $(item).attr('id');
					var left = $(item).innerWidth() + coord.left; // safe margin
					var top = coord.top ;
					div.css({'width': '300px', 'top': top, 'left': left });
					
					var original = $(item).attr('href');
					var href = $(item).attr('href') + '&render=preview&no_html=1&ajax=1';
					preview_open = 1;
					
					$.get( href, {}, function(data) 
					{							
						if(data)
						{
							$(item).attr('href', original);
														
							if (preview_open == 1 && (in_preview == $(item).attr('id'))) 
							{
								div.html(data);
								div.css('display', 'block');
								keyupTimer2 = setTimeout((function() {  
									div.css('display', 'none');				
								}), 5000);
							}							
						}
					});
				}					
			});
			
			$(item).on('mouseout', function(e) {
				e.preventDefault();
				if (div) {
					div.css('display', 'none');
					preview_open = 0;
				}						
			});
		});
	},
	
	getFileExt: function(val)
	{
		var $ = this.jQuery;
		/*var re = /[^.]+$/;
	    var ext = val.match(re);*/
		var ext = val.split('.').pop().toLowerCase();
		return ext;	
	},
	
	getConvertable: function(ext)
	{
		var array = {
		  'doc'  	: 1,
		  'docx'    : 1,
		  'html' 	: 1,
		  'txt' 	: 1,
		  'rtf' 	: 1,
		  'xls' 	: 1,
		  'xlsx' 	: 1,
		  'ods'  	: 1,
		  'csv'     : 1,
		  'tsv' 	: 1,
		  'tab' 	: 1,
		  'ppt' 	: 1,
		  'pps' 	: 1,
		  'pptx' 	: 1,
		  'wmf' 	: 1,
//		  'jpg' 	: 1,
//		  'gif' 	: 1,
//		  'png' 	: 1,
		  'pdf' 	: 1,
		  'tex'		: 1
		};
	
		if (array[ext]) 
		{
			return true;
		}
		
		return false;
	},
	
	getPreviewable: function(ext)
	{
		var array = {
		  'txt'		: 1,
		  'sty'		: 1,
		  'cls'	    : 1,
		  'css'		: 1,
		  'xml'		: 1,
		  'jpg' 	: 1,
		  'jpeg'	: 1,
		  'gif' 	: 1,
		  'png' 	: 1,
		  'pdf' 	: 1,
		  'tex'		: 1
		};
	
		if (array[ext]) 
		{
			return true;
		}
		
		return false;
	},
	
	watchSelections: function () 
	{
		var $ = this.jQuery;
		var bchecked 	= this.bchecked;
		var bselected 	= this.bselected;
		var bfolders 	= this.bfolders;
		var pub 		= this.pub;
		var dir 		= this.dir;
		var remote 		= this.remote;
		var service 	= this.service;
		var sConflict 	= this.sConflict;
		var ops = $('.fmanage');
		
		// Is selected file remote?
		if (remote > 0 && $('#a-share').length > 0 && bchecked > 0 && remote == bchecked)
		{
			$('#a-share').addClass('stop-sharing');
			$('#a-share').attr('title', 'Stop collaborative editing');
		}
		else if ($('#a-share').length > 0)
		{
			$('#a-share').removeClass('stop-sharing');
			$('#a-share').attr('title', 'Start collaborative editing');
		}
		
		if (bchecked == 0) 
		{
			ops.each(function(i, w) 
			{
				if (!$(w).hasClass('inactive') && $(w).attr('id') != 'a-folder' && $(w).attr('id') != 'a-upload') {	
					$(w).addClass('inactive');
				}	
			});
			
			if ($('#a-folder').length && $('#a-folder').hasClass('inactive')) {
				$('#a-folder').removeClass('inactive');
			}
		}
		else if (bchecked == 1) 
		{
			if ($('#a-delete').length && $('#a-delete').hasClass('inactive')) {
				$('#a-delete').removeClass('inactive');
			}
			if ($('#a-move').length && $('#a-move').hasClass('inactive') && (remote == 0)) {
				$('#a-move').removeClass('inactive');
			}	
			if ($('#a-download').length && $('#a-download').hasClass('inactive') && dir == 0) {
				$('#a-download').removeClass('inactive');
			}
			if ($('#a-history').length && $('#a-history').hasClass('inactive') && dir == 0) {
				$('#a-history').removeClass('inactive');
			}
			if ($('#a-folder').length && !$('#a-folder').hasClass('inactive')) {
				$('#a-folder').addClass('inactive');
			}
						
			if ($('#a-more').length && $('#a-more').hasClass('inactive') && dir == 0) {
				$('#a-more').removeClass('inactive');
			}
			
			// Sharing
			if ($('#a-share').length && $('#a-share').hasClass('inactive') && dir == 0) 
			{
				var selected = bselected[0];
				
				var ext = HUB.ProjectFiles.getFileExt(selected);
				
				// Only allow for certain file types
				if ((ext && HUB.ProjectFiles.getConvertable(ext)) || remote > 0)
				{
					$('#a-share').removeClass('inactive');
				}
			}
			
			// Compile preview
			if ($('#a-compile').length && $('#a-compile').hasClass('inactive') && dir == 0) 
			{
				var selected = bselected[0];
				
				var ext = HUB.ProjectFiles.getFileExt(selected);
				
				// Only allow for certain file types
				if ((ext && HUB.ProjectFiles.getPreviewable(ext)) || remote > 0)
				{
					$('#a-compile').removeClass('inactive');
				}
			}
			
		}
		else if (bchecked > 1) 
		{
			if($('#a-more').length && !$('#a-more').hasClass('inactive') )
			{
				$('#a-more').addClass('inactive');
			}
			
			if ($('#a-delete').length && $('#a-delete').hasClass('inactive')) 
			{
				$('#a-delete').removeClass('inactive');
			}
			if ($('#a-move').length && $('#a-move').hasClass('inactive') && remote == 0) {
				$('#a-move').removeClass('inactive');
			}
			else if($('#a-move').length && !$('#a-move').hasClass('inactive') && (remote > 0))
			{
				$('#a-move').addClass('inactive');
			}			
			if ($('#a-download').length && $('#a-download').hasClass('inactive') && dir == 0 && remote == 0) {
				$('#a-download').removeClass('inactive');
			}
			else if($('#a-download').length && !$('#a-download').hasClass('inactive') && (dir != 0 || remote > 0))
			{
				$('#a-download').addClass('inactive');
			}
			if ($('#a-compile').length && !$('#a-compile').hasClass('inactive')) {
				$('#a-compile').addClass('inactive');
			}
			
			if ($('#a-history').length && !$('#a-history').hasClass('inactive')) {
				$('#a-history').addClass('inactive');
			}
			
			if ($('#a-folder').length && !$('#a-folder').hasClass('inactive')) {
				$('#a-folder').addClass('inactive');
			}
			
			// Sharing for collaborative editing is available for individually selected files only
			if($('#a-share').length && !$('#a-share').hasClass('inactive') )
			{
				$('#a-share').addClass('inactive');
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectFiles.initialize();
});