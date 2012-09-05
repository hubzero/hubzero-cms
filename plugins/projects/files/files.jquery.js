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
	dir: 0,
	
	initialize: function() 
	{
		var $ = this.jQuery
			boxes = $('.checkasset'),
			toggle = $('#toggle');
		
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bfolders = this.bfolders;
		var pub = this.pub;
		var dir = this.dir;
														
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
					HUB.ProjectFiles.collectSelections(item);
					HUB.ProjectFiles.watchSelections();
				});				  												  
			});						
		}
		
		if (toggle.length > 0) {
			toggle.on('click', function(e) {
				boxes.each(function(i, item) 
				{	
					if (toggle.attr('checked') == 'checked') {
						$(item).attr('checked','checked');
					}
					else
					{
						$(item).removeAttr("checked");
					}
					HUB.ProjectFiles.collectSelections(item);	
					HUB.ProjectFiles.watchSelections();		  												  
				});				
			});
		}
		
		// Activate file management buttons
		HUB.ProjectFiles.activateFileOptions();					
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
		
		if (!txt)
		{
			txt = 'Please wait while we are performing your request...';	
		}
		
		// Add element
		$('#status-msg').html('<span id="fbwrap">' + 
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ' +
			txt +
			'</span>' +
		'</span>');
	},
	
	activateFileOptions: function ()
	{
		var $ = this.jQuery;
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bfolders = this.bfolders;
		var pub = this.pub;
		var dir = this.dir;
		
		var manage = $('#file-manage'),
			ops = $('.fmanage')
		
		// File options 
		ops.each(function(i, item) 
		{	
			// disable options until a box is checked
			if ($(item).attr('id') != 'a-folder') {
				$(item).addClass('inactive');
			}
			
			// Everything other than download happens in a light box 
			if ($(item).attr('id') != 'a-download') 
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
			
			$(item).on('click', function(e) {
				var aid = $(item).attr('id');
				if (aid != 'a-download') 
				{
					e.preventDefault();
				}

				if ($(item).hasClass('inactive')) {
					// do nothing
				}
				else {	
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
					if (aid != 'a-download') 
					{
						$.fancybox(this,{
							type: 'ajax',
							width: 600,
							height: 'auto',
							autoSize: false,
							fitToView: false,
							wrapCSS: 'sbp-window',
							afterShow: function() {
								if ($('#cancel-action')) {
									$('#cancel-action').on('click', function(e) {
										$.fancybox.close();
									});
								}
								
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
				if ($('#uploader').val() != '') {
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
					else if ($('#plg-form')) {						
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

							for ( i=classes.length-1; i>=0; i-- ) {
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
	
	collectSelections: function (el) 
	{
		var $ = this.jQuery;
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bfolders = this.bfolders;
		var pub = this.pub;
		var dir = this.dir;
		
		// Is item checked?
		if ($(el).attr('checked') != 'checked') {
			bchecked = bchecked - 1;
		 	
			if ($(el).hasClass('publ')) {
				pub = pub - 1;
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
		}
		else {
			bchecked = bchecked + 1;
			if ($(el).hasClass('publ')) {
				pub = pub + 1;
			}
			if ($(el).hasClass('dir')) {
				dir = dir + 1;
				bfolders.push($(el).val());
			}
			else {
				bselected.push($(el).val());
			}
		}

		if (pub <= 0) {
			pub = 0;
		}
		if (dir <= 0) {
			dir = 0;
		}
		
		// Record new values
		HUB.ProjectFiles.bchecked = bchecked;
		HUB.ProjectFiles.bselected = bselected;
		HUB.ProjectFiles.bfolders = bfolders;
		HUB.ProjectFiles.dir = dir;
		HUB.ProjectFiles.pub = pub;
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
		if ($('#indicator-area')) {
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
		
		preview.each(function(i, item) 
		{
			$(item).on('mouseover', function(e) 
			{
				e.preventDefault();
				var coord = $(item).position();
				
				if (div) {
					div.empty();
					var left = $(item).innerWidth() + coord.left; // safe margin
					var top = coord.top ;
					div.css({'width': '300px', 'top': top, 'left': left });
					
					var original = $(item).attr('href');
					var href = $(item).attr('href') + '&render=preview&no_html=1&ajax=1';
					preview_open = 1;
					
					$.get( href, {}, function(data) {
						if(data)
						{
							if(keyupTimer2) {
								clearTimeout(keyupTimer2);
							}
							$(item).attr('href', original);
							div.html(data);
							
							if (preview_open == 1) 
							{
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
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bfolders = this.bfolders;
		var pub = this.pub;
		var dir = this.dir;	
		var ops = $('.fmanage');
		if (bchecked == 0) {
			ops.each(function(i, w) {
				if (!$(w).hasClass('inactive') && $(w).attr('id') != 'a-folder') {	
					$(w).addClass('inactive');
				}	
			});
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
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectFiles.initialize();
});