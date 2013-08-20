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

HUB.ProjectFiles = {

	initialize: function() {
		
			var manage = $('file-manage');
			var ops = $$('.fmanage');
			var boxes = $$('.checkasset');
			var toggle = $('toggle');
			var div = $('preview-window');
			var preview_open = 0;
									
			// Disk usage indicator
			if($('indicator-area')) {
				var percentage = $('indicator-area').getProperty('class');
				percentage = percentage.replace('used:', '');
				if(isNaN(percentage)) {
					percentage = 0;
				}
				percentage = Math.round(percentage);
				var measurein = 'px';
				if($('disk-usage')) {
					var measurein = '%';
				}
				$('indicator-area').setStyle('width', percentage + measurein);				
			}
			
			// Confirm directory deletion
			if($('delete-dir') && HUB.Projects) {
				$('delete-dir').addEvent('click', function(e) {
					new Event(e).stop();			
					HUB.Projects.addConfirm($('delete-dir'), 'Are you sure you want to delete this directory and all its contents?', 'Yes, delete', 'No, do not delete');
				});
			}	
										
			// File management
			if($('file-manage')) {				
				if($('file-manage').hasClass('hidden')) {
					$('file-manage').removeClass('hidden');
				}
				
				var bchecked = 0;
				var bselected = new Array();
				var bfolders = new Array();
				var pub = 0;
				var dir = 0;
				
				// Toggle checkboxes			
				if(toggle) {
					toggle.addEvent('click', function(e) {
						if(boxes.length > 0) {
							boxes.each(function(item) {
							 	if(toggle.checked == true) {
									if(item.checked == false) { 
										item.checked = true; 
										bchecked = bchecked + 1;

										if(item.hasClass('publ')) {
											pub = pub + 1;
										}
										if(item.hasClass('dir')) {
											dir = dir + 1;
											bfolders.push(item.value);
										}
										else {
											bselected.push(item.value);
										}
									}
								 	else { 
										item.checked = true;
									}
								}
								else {
									if(item.checked == true) { 
										item.checked = false;
										bchecked = bchecked - 1;
									 
										if(item.hasClass('publ')) {
											pub = pub - 1;
										}
										if(item.hasClass('dir')) {
											dir = dir - 1;
											var idx = bfolders.indexOf(item.value);
											if(idx!=-1) bfolders.splice(idx, 1);
										}
										else {
											var idx = bselected.indexOf(item.value);
											if(idx!=-1) bselected.splice(idx, 1);
										}
									}								
								}
								
							});
						}
						HUB.ProjectFiles.watchSelections(bchecked, pub, dir);
					});				
				}	
				
				// Checkboxes behavior			
				if(boxes.length > 0) {
					boxes.each(function(item) {	
						item.addEvent('click', function(e) {
							// Is item checked?
							if(item.checked == false) {
								bchecked = bchecked - 1;
							 	
								if(item.hasClass('publ')) {
									pub = pub - 1;
								}
								if(item.hasClass('dir')) {
									dir = dir - 1;
									var idx = bfolders.indexOf(item.value);
									if(idx!=-1) bfolders.splice(idx, 1);
								}
								else {
									var idx = bselected.indexOf(item.value);
									if(idx!=-1) bselected.splice(idx, 1);
								}
							}
							else {
								bchecked = bchecked + 1;
								if(item.hasClass('publ')) {
									pub = pub + 1;
								}
								if(item.hasClass('dir')) {
									dir = dir + 1;
									bfolders.push(item.value);
								}
								else {
									bselected.push(item.value);
								}
							}

							if(pub <= 0) {
								pub = 0;
							}
							if(dir <= 0) {
								dir = 0;
							}
							HUB.ProjectFiles.watchSelections(bchecked, pub, dir);
						});	
					  												  
					});						
				}
				
				// File options 
				ops.each(function(item) 
				{	
					// disable options until a box is checked
					if (item.getProperty('id') != 'a-folder') {
						item.addClass('inactive');
					}
					item.addEvent('click', function(e) {
						var aid = item.getProperty('id');
						if(aid != 'a-download') 
						{
							new Event(e).stop();
						}

						if (item.hasClass('inactive')) {
							// do nothing
						}
						else {	
							// Clean up url
							var clean = item.href.split('&asset[]=', 1);
							item.href = clean;							
							item.href = item.href.split('&folder[]=', 1);
							
							// Everything other than download happens in a light box 
							if(aid != 'a-download') 
							{
								if(item.href.search('&ajax=1') == -1) {
									item.href = item.href + '&ajax=1';	
								}
								if(item.href.search('&no_html=1') == -1) {
									item.href = item.href + '&no_html=1';	
								}
							}						

							// Add our checked boxes variables
							if(bselected.length > 0) {
								for (var k = 0; k < bselected.length; k++) {
									item.href = item.href + '&asset[]=' + bselected[k];
								}
							}
							
							// Add our selected folders variables
							if(bfolders.length > 0) {
								for (var k = 0; k < bfolders.length; k++) {
									item.href = item.href + '&folder[]=' + bfolders[k];
								}
							}
							
							// Add case and subdir
							var subdir = $('subdir') ? $('subdir').value : '';
							var fcase   = $('case') ? $('case').value : '';
							
							if(subdir) {
								item.href = item.href +	'&subdir=' + subdir;
							}
							if(fcase) {
								item.href = item.href +	'&case=' + fcase;
							}
							
							// make AJAX call
							if(aid != 'a-download') 
							{
								if (!SqueezeBoxHub) {
									SqueezeBoxHub.initialize({ size: {x: 400, y: 500} });
								}

								// Modal box for actions
								SqueezeBoxHub.fromElement(item,{
									handler: 'url', 
									size: {x: 400, y: 500}, 
									ajaxOptions: {
										method: 'get',
										onComplete: function(aid) 
										{
											if($('cancel-action')) {
												$('cancel-action').addEvent('click', function(e) {
													SqueezeBoxHub.close();
												});
											}
											$('hubForm-ajax').addEvent('submit', function(e) {
												new Event(e).stop();
												SqueezeBoxHub.close();
												var txt = '';
												if (aid == 'a-delete')
												{
													txt = 'Deleting file(s)...';
												}
												HUB.ProjectFiles.submitViaAjax($('hubForm-ajax'), txt);
											});
										}
									}
								});
							}												
						}
					});								  												  
				});
			}	
			
			// Preview files
			var preview = $$('.preview');
			var keyupTimer2 = '';
			if(preview.length > 0) {
				preview.each(function(item) {
					item.addEvent('mouseover', function(e) {
						new Event(e).stop();
						var coord = item.getCoordinates();
						
						if(div) {
							div.empty();
							var left = coord['width'].toInt() + coord['left'].toInt() ; // safe margin
							var top = coord['top'].toInt() - 170 ;
							div.setStyles({'width': '300px', 'top': top, 'left': left });
							
							var original = item.href;
							var href = item.href + '&render=preview&no_html=1&ajax=1';
							preview_open = 1;
							
							new Ajax(href, {
								method : 'get',
								update: div,
								onComplete: function() { 
									item.href = original;
									if(keyupTimer2) {
										clearTimeout(keyupTimer2);
									}
									
									if(preview_open == 1) {
										div.style.display = 'block';
										keyupTimer2 = setTimeout((function() {  
											div.style.display = 'none';				
										}), 5000);
									}
								}
							}).request();
						}						
					});
					item.addEvent('mouseout', function(e) {
						new Event(e).stop();
						if(div) {
							div.style.display = 'none';
							preview_open = 0;
						}						
					});
				});
			}
			
			// Check what's uploaded
			var bu = $('f-upload');
			if(bu) {
				bu.addEvent('click', function(e) {
					new Event(e).stop();
					if($('uploader').value != '') {
						// Check extension
						var re = /[^.]+$/;
					    var ext = $('uploader').value.match(re);

						// Compressed file extensions
						var tar = {
						  'gz'  	: 1,
						  '7z'      : 1,
						  'zip' 	: 1,
						  'zipx' 	: 1,
						  'sit' 	: 1,
						  'sitx' 	: 1,
						  'rar' 	: 1,
						  'tar' 	: 1
						};

						if (tar[ext]) {
							// Confirm further action - extract files?
							HUB.ProjectFiles.addQuestion();
						}
						else if($('plg-form')) 
						{	
							if ($('plg-form').hasClass('submit-ajax'))
							{
								HUB.ProjectFiles.submitViaAjax($('plg-form'), 'Uploading file(s)... Please wait');
							}
							else
							{
								$('plg-form').submit();
							}
						}				
					}
				});
			}
			
			// Renaming
			var rename = $$('.rename');
			if(rename.length > 0) {
				rename.each(function(item) {
					item.addEvent('click', function(e) {
						new Event(e).stop();
						var id = item.getProperty('id').replace('rename-c-', '');
						var edit = $('edit-c-' + id);
						if(edit)
						{
								var classes = edit.getProperty('class').split(" ");
								var dir = '';
								var file = '';

								for ( i=classes.length-1; i>=0; i-- ) {
									if (classes[i].contains('file:')) {
										file = classes[i].split(":")[1];
									}
									if (classes[i].contains('dir:')) {
										dir = classes[i].split(":")[1];
									}
								}
								file = unescape(file);
								file = unescape(file.replace(/\+/g, " "));
								dir = unescape(dir);
								dir = unescape(dir.replace(/\+/g, " "));
								HUB.ProjectFiles.addEditForm(item, edit, file, dir);
						}
					});
				});				
			}			
	},
	
	addQuestion: function() 
	{		
		var expand = $('expand_zip');
		if($('confirm-box')) {
			$('confirm-box').remove();	
		}
		var parentul = $('c-filelist') ? $('c-filelist') : $('filelist');
		var frm = $('upload-form') ? $('upload-form') : $('plg-form');
		
		// Add confirmation
		var confirm =  new Element('div', {
			'class': 'confirmaction'
		}).inject(parentul, 'before');
		confirm.setProperty('id', 'confirm-box');
		confirm.style.display = 'block';

		var p = new Element('p');
		p.injectInside(confirm);
		p.innerHTML = 'Do you wish to expand the file you are uploading?';

		var p2 = new Element('p');
		p2.injectInside(confirm);

		var a1 = new Element('span', {
			'class': 'confirm'
		}).injectInside(p2);
		a1.innerHTML = 'yes, expand';
		
		a1.addEvent('click', function(e) {
			new Event(e).stop();
			expand.value = 1;
			if(frm) {						
				HUB.ProjectFiles.submitViaAjax(frm, 'Uploading file(s)... Please wait');
				
				if($('confirm-box')) {
					$('confirm-box').remove();	
				}
			}
		});
		
		var a2 = new Element('span', {
			'class': 'confirm c-no'
		}).injectInside(p2);
		a2.innerHTML = 'no, upload as an archive';
		a2.addEvent('click', function(e) {
			new Event(e).stop();
			if(frm) 
			{	
				HUB.ProjectFiles.submitViaAjax(frm, 'Uploading file(s)... Please wait');
													
				if($('confirm-box')) {
					$('confirm-box').remove();	
				}
			}
		});

		var a3 = new Element('a', {
			'href': '#',
			'class': 'cancel c-no',
			'events': {
				'click': function(evt){
					(new Event(evt)).stop();
					$('confirm-box').remove();
					$('uploader').value = '';
				}
			}
		}).injectInside(p2);
		a3.innerHTML = 'cancel';
		
		// Move close to item
		var coord = $('uploader').getCoordinates();		
		$('confirm-box').setStyles({'left': coord['left'], 'top': coord['top'] });	
	},
	
	submitViaAjax: function (frm, txt)
	{
		if (!frm)
		{
			return;
		}
		var log = $('status-msg').empty().addClass('ajax-loading');	
		
		if (!txt)
		{
			txt = 'Please wait while we are performing your request...';	
		}
		
		var a1 = new Element('span', {
			'id': 'fbwrap'
		}).injectInside(log);
		
		var facebookG = new Element('span', {
			'id': 'facebookG'
		}).injectInside(a1);
		
		var block_1 = new Element('span', {
			'class' : 'facebook_blockG',
			'id': 'blockG_1'
		}).injectInside(facebookG);
		
		var block_2 = new Element('span', {
			'class' : 'facebook_blockG',
				'id': 'blockG_2'
		}).injectInside(facebookG);
		
		var block_3 = new Element('span', {
			'class' : 'facebook_blockG',
			'id': 'blockG_3'
		}).injectInside(facebookG);
				
		var a2 = new Element('span', {
		}).injectInside(a1);
		a2.innerHTML = txt;
				
		frm.submit( {
			onSuccess: function() {
				$('status-msg').removeClass('ajax-loading');
			}
		}); 		
	},
	
	addEditForm: function (link, el, file, dir) 
	{		
		if($('editv')) {
			return;
		}
		el.addClass('hidden');
		link.addClass('hidden');		
		
		var vwrap = new Element('label', {
			'id': 'editv'
		}).injectInside(el.parentNode);
		
		var original = '';
		var rename   = 'file';
		if(file)
		{
			original = file;
		}
		else
		{
			rename   = 'dir';
			original = dir;
		}
		
		// Hidden fields	
		var vinput = new Element('input', {
			'type': 'hidden',
			'name': 'action',
			'value': 'renameit'
		}).injectInside(vwrap);
		
		var vinput = new Element('input', {
			'type': 'hidden',
			'name': 'rename',
			'value': rename
		}).injectInside(vwrap);
		
		var vinput = new Element('input', {
			'type': 'hidden',
			'name': 'oldname',
			'value': original
		}).injectInside(vwrap);
		
		// Input
		var vinput = new Element('input', {
			'type': 'text',
			'name': 'newname',
			'class': 'vlabel',
			'maxlength': 100,
			'value': original
		}).injectInside(vwrap);
		
		// Add a submit button
		var vsubmit = new Element('input', {
			'type': 'submit',
			'value': 'rename'
		}).injectInside(vwrap);
		
		// Add a cancel button
		var vcancel = new Element('input', {
			'type': 'button',
			'value': 'x',
			'class': 'cancel',
			'events': {
				'click': function() {
					vwrap.remove();
					el.removeClass('hidden');	
					link.removeClass('hidden');											
				}
			}
		}).injectInside(vwrap);
	},
	
	watchSelections: function (bchecked, pub, dir ) {
		var ops = $$('.fmanage');
		if(bchecked == 0) {
			ops.each(function(i) {
				if(!i.hasClass('inactive') && i.getProperty('id') != 'a-folder') {	
					i.addClass('inactive');
				}	
			});
		}
		else if(bchecked == 1) {
			if($('a-delete') && $('a-delete').hasClass('inactive')) {
				$('a-delete').removeClass('inactive');
			}
			if($('a-move') && $('a-move').hasClass('inactive')) {
				$('a-move').removeClass('inactive');
			}
				
			if($('a-download') && $('a-download').hasClass('inactive') && dir == 0) {
				$('a-download').removeClass('inactive');
			} 
			/*				
			if($('a-download') && $('a-download').hasClass('inactive')) {
				$('a-download').removeClass('inactive');
			} */
		}
		else if(bchecked > 1) {
			if($('a-delete') && $('a-delete').hasClass('inactive')) {
				$('a-delete').removeClass('inactive');
			}
			/*
			else if(pub != 0) {
				$('a-delete').addClass('inactive');
			}*/
			
			if($('a-move') && $('a-move').hasClass('inactive')) {
				$('a-move').removeClass('inactive');
			}
			/*
			else if(pub != 0) {
				$('a-move').addClass('inactive');
			}*/
			
			if($('a-download') && $('a-download').hasClass('inactive') && dir == 0) {
				$('a-download').removeClass('inactive');
			}
			else if($('a-download') && !$('a-download').hasClass('inactive') && dir != 0)
			{
				$('a-download').addClass('inactive');
			}
			/*
			if($('a-download') && $('a-download').hasClass('inactive')) {
				$('a-download').removeClass('inactive');
			}
			*/
		}
	}
}
	
window.addEvent('domready', HUB.ProjectFiles.initialize);