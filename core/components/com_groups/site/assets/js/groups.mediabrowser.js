/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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

function test() {
	alert('test');
}

HUB.GroupsMediaBrowser = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;

		// file tree
		HUB.GroupsMediaBrowser.folderTree();
		
		// file uploader
		HUB.GroupsMediaBrowser.uploader();
		
		// add folder
		$('.action-addfolder').on('click', function(event) {
			event.preventDefault();
			HUB.GroupsMediaBrowser.openLightbox( $(this).attr('href') );
		});
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
								// builld redirect url
								//var newHref = window.location.protocol + '//' + window.location.host + window.location.pathname;
								//newHref += '?tmpl=component&path=' + data;
								
								//redirect
								//window.location.href = newHref;
								window.location.reload();
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
	
	folderTree: function()
	{
		var $ = this.jQuery;
		
		$('.foldertree')
			
			// folder toggles
			.on('click', '.tree-folder-toggle', function (event) {
				event.preventDefault();
				
				// open next section
				// mark this as open
				$(this)
					.toggleClass('open')
					.parent('li').toggleClass('active').children('ul').first().toggle();
			})
			
			// clicking on a folder opening new folders
			// gets triggered programmatically too
			.on('click', '.tree-folder', function(event) {
				event.preventDefault();
			
				var parentLi = $(this).parent('li'),
					folder   = $(this).attr('data-folder');
			
				// toggle active class
				parentLi.addClass('active');
				
				//remove previous active folder & set new
				$('.tree-folder').removeClass('activelisted');
				$(this).addClass('activelisted');
			
				// get old source and create new source for filelist
				var filelistiframe = $('.upload-browser-filelist-iframe'),
					oldsource      = filelistiframe.attr('src'),
					newsource      = HUB.GroupsMediaBrowser._urlAddQueryParam(oldsource, 'path', folder);
			
				//update the file list with the folder we want to load
				filelistiframe.attr('src', newsource);
				
				// set the active folder
				$('.foldertree').attr('data-activefolder', folder);
			
				//set the select box dropdown
				$('.foldertree-list select').val( folder );
			});
		
		// if we have a folder tree lets auto-open to the active folder
		// also listen fro folder click notifications from inside file/folder list
		HUB.GroupsMediaBrowser.setFolderTreeOpenActive();
		
		// folder tree list change (used in small screens)
		$('.foldertree-list').on('change', 'select', function(event) {
			var folder = $(this).val();
			$('.foldertree').attr('data-activefolder', folder);
			HUB.GroupsMediaBrowser.folderTreeOpenActive();
		});
	},
	
	//-----
	
	setFolderTreeOpenActive: function( folder )
	{
		var $ = this.jQuery;

		if ($('.foldertree').length)
		{
			//open active from the start
			HUB.GroupsMediaBrowser.folderTreeOpenActive();

			$('.foldertree').attr('data-activefolder', folder);
			HUB.GroupsMediaBrowser.folderTreeOpenActive();
		}
	},

	//-----

	refreshAndOpenFolder: function( folder )
	{
		var url = HUB.GroupsMediaBrowser._urlAddQueryParam(window.location.href, 'path', folder);
		window.location.href = url;
	},

	//-----
	
	folderTreeOpenActive: function()
	{
		var $ = this.jQuery;
		
		// get active folder
		var activeFolder      = $('.foldertree').attr('data-activefolder').toLowerCase();
		
		// trigger clicking of active folder
		$('.foldertree .tree-folder').each(function(index, element){
			var $element = $(element);
			if (activeFolder == $element.attr('data-folder').toLowerCase())
			{
				$element
					// trigger clicking of folder - sets filelist frames source
					.trigger('click')
				
					// open filetree nodes to current location
					.parents('li').each(function(i, el) {
						var t = $(el).find('.tree-folder-toggle').first();
						t.addClass('open');
						t.parent('li').addClass('active');
						//.addClass('activelisted');
						t.parent('li').children('ul').first().show();
					});
			}
		});
	},
	
	//-----
	
	uploader: function()
	{
		var $ = this.jQuery,
			attach = $("#ajax-uploader");

		if (attach.length) {
			var totalFiles = 0;

			var uploader = new qq.FileUploader({
				element: attach[0],
				action: attach.attr("data-action"),
				multiple: true,
				//showDrop: true,
				template: '<div class="qq-uploader">' +
							'<div class="qq-upload-button"><span>' + attach.attr('data-instructions') + '</span></div>' + 
							'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
							'<ul class="qq-upload-list"></ul>' + 
						'</div>',
				onSubmit: function(id, file) {
					totalFiles++;

					// set folder params
					uploader.setParams({
						'folder' : $('.foldertree').attr('data-activefolder')
					});

					// add uploading indicator
					if (!$(".qq-uploading").length) {
						$(".upload-browser-col.right").append("<div class=\"qq-uploading\"><span>Uploading 1 file, 0 completed</span></div>");
					}
					// otherwise add 
					else {
						$('.qq-uploading span').text('Uploading ' + totalFiles + ' files, 0 completed');
					}
				},
				onComplete: function(id, file, response) {
					if (uploader._filesInProgress == 0) {
						$(".upload-browser-filelist-iframe").attr("src", $(".upload-browser-filelist-iframe").attr("src"));
						$(".qq-uploading").fadeOut("slow", function() {
							$(".qq-uploading").remove();
						});

						// tell the parent that images were just uploaded
						// group edit screen - picking group logo
						if (parent.HUB.Groups)
						{
							parent.HUB.Groups.imagesUploaded();
						}

						// reset count
						totalFiles = 0;
					} else {
						$('.qq-uploading span').text('Uploading ' + totalFiles + ' files, ' + (totalFiles - uploader._filesInProgress) + ' completed')
					}
				}
			});
		}
	},
	
	_urlAddQueryParam: function(url, param, value)
	{
		// Using a positive lookahead (?=\=) to find the
		// given parameter, preceded by a ? or &, and followed
		// by a = with a value after than (using a non-greedy selector)
		// and then followed by a & or the end of the string
		var val = new RegExp('(\\?|\\&)' + param + '=.*?(?=(&|$))'),
			parts = url.toString().split('#'),
			url = parts[0],
			hash = parts[1]
			qstring = /\?.+$/,
			newURL = url;
			
		// Check if the parameter exists
		if (val.test(url))
		{
			// if it does, replace it, using the captured group
			// to determine & or ? at the beginning
			newURL = url.replace(val, '$1' + param + '=' + value);
		}
		else if (qstring.test(url))
		{
			// otherwise, if there is a query string at all
			// add the param to the end of it
			newURL = url + '&' + param + '=' + value;
		}
		else
		{
			// if there's no query string, add one
			newURL = url + '?' + param + '=' + value;
		}
		
		if (hash)
		{
			newURL += '#' + hash;
		}
		
		return newURL;
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.GroupsMediaBrowser.initialize();
});