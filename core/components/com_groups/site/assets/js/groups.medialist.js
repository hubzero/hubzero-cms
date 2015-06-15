/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
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
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.GroupsMediaList = {
	jQuery: jq,
	
	initialize: function() 
	{
		// file list
		HUB.GroupsMediaList.fileList();
		
		// context menu for added actions
		HUB.GroupsMediaList.filelistContextMenu();
	},

	//-----
	
	callParent: function( folder )
	{
		// get the folder we want to open and a pointer to the filebrowser object
		var mediaBrowser = parent.HUB.GroupsMediaBrowser;

		// if we found the parent lets set the folder we want to open
		if (mediaBrowser)
		{
			mediaBrowser.setFolderTreeOpenActive( folder );
		}
	},

	//-----

	refreshParent: function( folder)
	{
		// get the folder we want to open and a pointer to the filebrowser object
		var mediaBrowser = parent.HUB.GroupsMediaBrowser;

		// if we found the parent lets set the folder we want to open
		if (mediaBrowser)
		{
			mediaBrowser.refreshAndOpenFolder( folder );
		}
	},
	
	//-----
	
	fileList: function()
	{
		var $ = this.jQuery;
		
		// if we have a filelist
		if ($('.upload-filelist').length)
		{
			// post notification when a folder is clicked in file/folder list
			$('.upload-filelist').on('click', '.folder', function(event) {
				event.preventDefault();
				HUB.GroupsMediaList.callParent( $(this).find('a').attr('data-folder') );
			});
			
			// breadcrumbs
			$('.upload-filelist-toolbar').on('click', '.path a', function(event) {
				event.preventDefault();
				HUB.GroupsMediaList.callParent( $(this).attr('data-folder') );
			});
			
			// open file detail pane
			$('.upload-filelist').on('click', '.file', function(event) {
				event.preventDefault();
				
				var $this      = $(this),
					detailView = $this.next('li');
				
				// open detail view and mark as opened
				$this.toggleClass('opened');
				detailView.toggle();
				
				// async load preview image
				var previewImage = detailView.find('.preview img');
				if (previewImage.length && previewImage.attr('data-src') != '')
				{
					$.get(previewImage.attr('data-src'), function() {
						previewImage.attr('src', previewImage.attr('data-src'));
						previewImage.attr('data-src', '');
					});
				}
			});
			
			// auto select file path when clicked
			$('.upload-filelist').on('click', '.path', function(event) {
				var path = $(this).find('span').selText();
			});
			
			// delete file
			$('.action-delete').on('click', function(event) {
				event.preventDefault();
				
				if (confirm("Delete the file: " + $(this).attr('data-file') + "?"))
				{
					$.ajax({
						url: $(this).attr('href'),
						type: 'get',
						success: function( data, status, jqXHR )
						{
							HUB.GroupsMediaList.callParent( data );
						}
					})
				}
			});
			
			// delete file
			$('.action-extract').on('click', function(event) {
				event.preventDefault();
				$.ajax({
					url: $(this).attr('href'),
					type: 'get',
					success: function( data, status, jqXHR )
					{
						// refresh parent to reload folder tree
						// needed to allow us to navigate newly extracted folder
						HUB.GroupsMediaList.refreshParent( data );
					}
				});
			});
			
			// move and rename
			$('.action-move, .action-rename').on('click', function(event) {
				event.preventDefault();
				HUB.GroupsMediaList.openLightbox( $(this).attr('href') );
			});
			
			// move and rename
			$('.action-raw').on('click', function(event) {
				event.preventDefault();
				HUB.GroupsMediaList.openLightbox( $(this).attr('href') );
			});
		}
	},
	
	openLightbox: function( url )
	{
		var $ = this.jQuery;

		$.fancybox({
			type: 'ajax',
			href: url,
			autoSize: false,
			autoHeight: true,
			width: 500,
			afterShow: function()
			{
				if ($('form'))
				{
					$('form').submit(function(event){
						event.preventDefault();
						$.ajax({
							url: $(this).attr('action'),
							type: 'post',
							data: $(this).serialize(),
							success: function( data, status, jqXHR )
							{
								HUB.GroupsMediaList.callParent( data );
								$.fancybox.close();
							},
							error: function( status, data, jqXHR )
							{
								alert(status.statusText);
							}
						});
					});
				}
			}
		});
	},
	
	//-----
	
	filelistContextMenu: function()
	{
		var $ = this.jQuery;

		// context menu for folders
		$.contextMenu({
			selector: '.upload-filelist .folder',
			callback: function( key, options) {
				HUB.GroupsMediaList.folderContextMenuAction( key, options.$trigger );
			},
			items: {
				'rename'  : { name: 'Rename Folder', icon: 'edit' },
				'move'    : { name: 'Move Folder', icon: 'move' },
				'sep1'    : "---------",
				'delete'  : { name: 'Delete Folder', icon: 'delete' },
			}
		});
		
		// context menu
		$.contextMenu({
			selector: '.upload-filelist .file:not(.file-zip):not(.file-tar):not(.file-gz)',
			callback: function( key, options) {
				HUB.GroupsMediaList.filelistContextMenuAction( key, options.$trigger );
			},
			items: {
				'download': { name: 'Download', icon: 'download' },
				'rename'  : { name: 'Rename File', icon: 'edit' },
				'move'    : { name: 'Move File', icon: 'move' },
				'sep1'    : "---------",
				'delete'  : { name: 'Delete File', icon: 'delete' },
			}
		});
		
		// context menu
		$.contextMenu({
			selector: '.file-zip, .file-tar, .file-gz',
			callback: function( key, options) {
				HUB.GroupsMediaList.filelistContextMenuAction( key, options.$trigger );
			},
			items: {
				'download': { name: 'Download', icon: 'download' },
				'rename'  : { name: 'Rename File', icon: 'edit' },
				'move'    : { name: 'Move File', icon: 'move' },
				'sep1'    : '----------',
				'extract' : { name: 'Extract', icon: 'extract' },
				'sep2'    : '----------',
				'delete'  : { name: 'Delete File', icon: 'delete' }
			}
		});
	},
	
	//-----
	
	folderContextMenuAction: function( action, element )
	{
		var $ = this.jQuery;

		var folder    = element.find('.name a').attr('data-folder'),
			actionUrl = element.find('.name a').attr('data-action-' + action);
		
		// confirm delete action
		if (action == 'delete')
		{
			if (confirm("Delete the folder: " + folder + "? This will also delete sub folders and files."))
			{
				$.ajax({
					url: actionUrl,
					type: 'get',
					success: function(data, status, jqXHR)
					{
						// refresh parent to reload folder tree
						HUB.GroupsMediaList.refreshParent( data );
					}
				})
			}
			return;
		}
		
		// open lightbox for new/edit folder, move folder
		HUB.GroupsMediaList.openLightbox( actionUrl );
	},
	
	//-----
	
	filelistContextMenuAction: function( action, element )
	{
		var $ = this.jQuery;

		//find button in file details which actually performs action
		var button = element.next('.file-details').find('.action-' + action);
		
		// if we found a button click it
		if (button.length)
		{
			if (button.data('events'))
			{
				if (button.data('events').click)
				{
					button.trigger('click');
				}
			}
			else
			{
				window.location.href = button.attr('href');
			}
		}
	},
	
	//-----
	
	ckeditorInsert: function( file, w )
	{
		var funcNum = HUB.GroupsMediaList._getUrlParam('CKEditorFuncNum');
		w.opener.CKEDITOR.tools.callFunction(funcNum, file, function() {
			w.close();
		});
		return false;
	},
	
	//-----
	
	_getUrlParam: function( paramName )
	{
		var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
		var match = window.location.search.match(reParam) ;
		return ( match && match.length > 1 ) ? match[ 1 ] : null ;
	},
};

jQuery.fn.selText = function() {
	var obj = this[0];
	if ($.browser.msie) 
	{
		var range = obj.offsetParent.createTextRange();
		range.moveToElementText(obj);
		range.select();
	}
	else if ($.browser.mozilla || $.browser.opera) 
	{
		var selection = obj.ownerDocument.defaultView.getSelection();
		var range = obj.ownerDocument.createRange();
		range.selectNodeContents(obj);
		selection.removeAllRanges();
		selection.addRange(range);
	}
	else if ($.browser.safari) 
	{
		var selection = obj.ownerDocument.defaultView.getSelection();
		selection.setBaseAndExtent(obj, 0, obj, 1);
	}
	return this;
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.GroupsMediaList.initialize();
});