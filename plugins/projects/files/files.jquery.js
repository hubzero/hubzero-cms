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
	dir: 0,
	
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
		
		// Enable disk usage indicator
		HUB.ProjectFiles.diskUsage();
		
		// Enable file preview
		HUB.ProjectFiles.previewFiles();
		
		// Enable rename
		HUB.ProjectFiles.enableRename();
		
		// Check what's uploaded
		HUB.ProjectFiles.checkWhatsUploaded();
		
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
		HUB.ProjectFiles.loadSharedFiles();	
		
		// Show more/less version history
		HUB.ProjectFiles.showRevisions();			
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
		
	loadSharedFiles: function() 
	{
		var $ = this.jQuery;
		var sharing 	= $('#sharing').length ? $('#sharing').val() : 0;
		var sync 		= $('#sync').length ? $('#sync').val() : 0;
		var subdir	 	= $('#subdir').length ? $('#subdir').val() : '';

		if (sharing == 1 && sync == 1)
		{				
			var projectid = $('#projectid') ? $('#projectid').val() : 0;
			var url = '/projects/' + projectid + '/files/?sync=1';
			url = url + '&no_html=1&ajax=1';
			url = url + '&subdir=' + subdir;
			
			var keyupTimer = setTimeout((function() 
			{  	
				// Set loading msg
				var log = $('#status-msg').empty().addClass('ajax-loading');
				$('#status-msg').css({'opacity':100});
				$('#status-msg').html(HUB.ProjectFiles.loadingIma('Syncing with remote service... Please wait'));
				
				var ajax = $.get(url, {}, function(data) 
				{					
					if (data) 
					{
						$('#plg-content').html(data);
						$('#status-msg').empty().removeClass('ajax-loading');

						// Make sure everything works again
						HUB.Projects.initialize();
						HUB.ProjectFiles.initialize();
						HUB.ProjectFiles.preSelect();
						
						if ($('#sync-msg').length > 0)
						{
							$('#status-msg').html($('#sync-msg').html());
						}
					}
				});	
			}), 500);
		}		
	},
	
	preSelect: function() 
	{
		var $ = this.jQuery
			boxes = $('.checkasset');
		var idx = -1;
			
		if (HUB.ProjectFiles.bchecked > 0 && boxes.length > 0)
		{
			boxes.each(function(i, el) 
			{	
				if ($(el).hasClass('dir')) {
					var idx = HUB.ProjectFiles.bfolders.indexOf($(el).val());
				}
				else {
					var idx = HUB.ProjectFiles.bselected.indexOf($(el).val());
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
			HUB.ProjectFiles.submitViaAjax(frm, 'Uploading file(s)... Please wait');
			frm.submit();
			$('#confirm-box').remove();
		});
		
		$('#c-archive').on('click', function(e){
			e.preventDefault();
			HUB.ProjectFiles.submitViaAjax(frm, 'Uploading file(s)... Please wait');
			frm.submit();
			$('#confirm-box').remove();
		});
		
		// Move close to item
		var coord = $('#uploader').position();		
		$('#confirm-box').css('left', coord.left).css('top', coord.top + 200);	
	},
	
	submitViaAjax: function (frm, txt)
	{
		var $ = this.jQuery;	
		if (!frm)
		{
			return;
		}
		var log = $('#status-msg').empty().addClass('ajax-loading');
		$('#status-msg').css({'opacity':100});	
		
		if (!txt)
		{
			txt = 'Please wait while we are performing your request...';	
		}
		
		// Add element
		$('#status-msg').html(HUB.ProjectFiles.loadingIma(txt));
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
		
		var manage = $('#file-manage'),
			ops = $('.fmanage')
		
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
				if (!$(item).hasClass('connection-active'))
				{
					connected = false;
				}
			}
						
			// Not every action happens in a light box 
			if (aid != 'a-download' && aid != 'a-history' 
				&& aid != 'a-folder' && connected == true) 
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
					&& aid != 'a-folder' && connected == true) 
				{
					e.preventDefault();
				}

				if ($(item).hasClass('inactive')) {
					e.preventDefault();
				}
				else if (aid != 'a-folder') 
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
					var subdir = $('#subdir') ? $('#subdir').val() : '';
					var fcase   = $('#case') ? $('#case').val() : '';
					
					if (subdir) {
						$(item).attr('href', $(item).attr('href') +	'&subdir=' + subdir);
					}
					if (fcase) {
						$(item).attr('href', $(item).attr('href') +	'&case=' + fcase);
					}
					
					// make AJAX call
					if (aid != 'a-download' && aid != 'a-history' && connected == true)  
					{
						$.fancybox(this,{
							type: 'ajax',
							width: 600,
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
								
								// Close box if no file to upload
								if (aid == 'a-upload' && $('#f-upload').length > 0)
								{
									$('#f-upload').on('click', function(e) {
										if ($('#uploader').val() == '') 
										{
											$.fancybox.close();
											e.preventDefault();
											proceed = 0;
										}
									});
								}
								
								if (proceed == 1)
								{
									$('#hubForm-ajax').submit(function() {
									    $.fancybox.close();
										var txt = '';
										if (aid == 'a-delete')
										{
											txt = 'Deleting file(s)...';
										}
										HUB.ProjectFiles.submitViaAjax($('#hubForm-ajax'), txt);
									});
								}
							}
						});
					}												
				}
			});								  												  
		});
	},
			
	checkWhatsUploaded: function ()
	{
		var $ = this.jQuery;
		
		// Check what's uploaded
		var bu = $('#f-upload');
		if (bu) {
			bu.on('click', function(e) {
				e.preventDefault();
				if ($('#uploader').val() != '') 
				{
					// Check extension
					var re = /[^.]+$/;
				    var ext = $('#uploader').val().match(re);

					// Compressed file extensions
					var tar = {
					  'gz'  	: 1,
					  '7z'      : 1,
					  'zip' 	: 1,
					  'zipx' 	: 1,
					  'sit' 	: 1,
					  'sitx' 	: 1,
					  'rar' 	: 1
					};

					if (tar[ext]) 
					{
						// Confirm further action - extract files?
						HUB.ProjectFiles.addQuestion();
					}
					else if ($('#plg-form')) 
					{						
						if ($('#plg-form').hasClass('submit-ajax'))
						{
							HUB.ProjectFiles.submitViaAjax($('#plg-form'), 'Uploading file(s)... Please wait');
						}

						$('#plg-form').submit();
					}				
				}
			});
		}
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
								if (classes[i].indexOf("file:") >= 0)
								{
									file = classes[i].split(":")[1];
								}
								if (classes[i].indexOf("dir:") >= 0)
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
		
		// Add form
		el.parent().append('<label id="editv">' + 
			'<input type="hidden" name="action" value="renameit" />' +
			'<input type="hidden" name="rename" value="' + rename + '" />' +  
			'<input type="hidden" name="oldname" value="' + original + '" />' + 
			'<input type="text" name="newname" value="' + original + '" maxlength="100" class="vlabel" />' +
			'<input type="submit" value="rename" />' +
			'<input type="button" value="cancel" class="cancel" id="cancel-rename" />' +
		'</label>');
		
		$('#cancel-rename').on('click', function(e){
			e.preventDefault();
			$('#editv').remove();
			el.removeClass('hidden');
			link.removeClass('hidden');
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
				
		// Is item checked?
		if ($(el).attr('checked') == 'checked' || tog == 2) 
		{
			if ($(el).hasClass('publ')) {
				pub = pub + 1;
			}
			if ($(el).hasClass('remote')) {
				remote = remote + 1;
			}
			if ($(el).hasClass('dir')) 
			{	
				var idx = bfolders.indexOf($(el).val());

				if (idx == -1) 
				{
					dir = dir + 1;
					bfolders.push($(el).val());	
				}
			}
			else {
				var idx = bselected.indexOf($(el).val());
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
			if ($(el).hasClass('publ')) {
				pub = pub - 1;
			}
			if ($(el).hasClass('remote')) {
				remote = remote - 1;
			}
			if ($(el).hasClass('dir')) {
				dir = dir - 1;
				var idx = bfolders.indexOf($(el).val());
				if (idx!=-1) bfolders.splice(idx, 1);
			}
			else {
				var idx = bselected.indexOf($(el).val());
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
	},
	
	diskUsage: function ()
	{
		var $ = this.jQuery;
		
		// Disk usage indicator
		if ($('#indicator-area').length) {
			var percentage = $('#indicator-area').attr('class');
			percentage = percentage.replace('used:', '');
			if (isNaN(percentage)) {
				percentage = 0;
			}
			percentage = Math.round(percentage);
			var measurein = 'px';
			if ($('#disk-usage')) {
				var measurein = '%';
			}
			$('#indicator-area').css('width', percentage + measurein);				
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
	
	watchSelections: function () 
	{
		var $ = this.jQuery;
		var bchecked 	= this.bchecked;
		var bselected 	= this.bselected;
		var bfolders 	= this.bfolders;
		var pub 		= this.pub;
		var dir 		= this.dir;
		var remote 		= this.remote;
		var ops = $('.fmanage');
		
		// Is selected file remote?
		if (remote > 0 && $('#a-share').length > 0 && bchecked == 1)
		{
			$('#a-share').addClass('stop-sharing');
			$('#a-share').attr('title', 'Stop sharing');
		}
		else if ($('#a-share').length > 0)
		{
			$('#a-share').removeClass('stop-sharing');
			$('#a-share').attr('title', 'Share remotely');
		}
		
		if (bchecked == 0) {
			ops.each(function(i, w) {
				if (!$(w).hasClass('inactive') && $(w).attr('id') != 'a-folder' && $(w).attr('id') != 'a-upload') {	
					$(w).addClass('inactive');
				}	
			});
			
			if ($('#a-folder').length && $('#a-folder').hasClass('inactive')) {
				$('#a-folder').removeClass('inactive');
			}
		}
		else if (bchecked == 1) {
			if ($('#a-delete') && $('#a-delete').hasClass('inactive')) {
				$('#a-delete').removeClass('inactive');
			}
			if ($('#a-move') && $('#a-move').hasClass('inactive')) {
				$('#a-move').removeClass('inactive');
			}	
			if ($('#a-download') && $('#a-download').hasClass('inactive') && dir == 0) {
				$('#a-download').removeClass('inactive');
			}
			if ($('#a-history') && $('#a-history').hasClass('inactive') && dir == 0) {
				$('#a-history').removeClass('inactive');
			}
			if ($('#a-folder').length && !$('#a-folder').hasClass('inactive')) {
				$('#a-folder').addClass('inactive');
			}
			if ($('#a-share').length && $('#a-share').hasClass('inactive')) {
				$('#a-share').removeClass('inactive');
			}
		}
		else if (bchecked > 1) {
			if ($('#a-delete') && $('#a-delete').hasClass('inactive')) {
				$('#a-delete').removeClass('inactive');
			}
			if ($('#a-move') && $('#a-move').hasClass('inactive')) {
				$('#a-move').removeClass('inactive');
			}			
			if ($('#a-download') && $('#a-download').hasClass('inactive') && dir == 0) {
				$('#a-download').removeClass('inactive');
			}
			else if($('#a-download') && !$('#a-download').hasClass('inactive') && dir != 0)
			{
				$('#a-download').addClass('inactive');
			}
			if ($('#a-history').length && !$('#a-history').hasClass('inactive')) {
				$('#a-history').addClass('inactive');
			}
			if ($('#a-folder').length && !$('#a-folder').hasClass('inactive')) {
				$('#a-folder').addClass('inactive');
			}
			if ($('#a-share').length && !$('#a-share').hasClass('inactive')) {
				$('#a-share').addClass('inactive');
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectFiles.initialize();
});