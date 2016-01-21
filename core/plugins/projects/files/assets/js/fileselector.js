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

HUB.ProjectFilesFileSelect = {
	jQuery: jq,
	uploaded: 0,
	updated: 0,
	failed: 0,
	queued: 0,
	processed: 0,
	selections: new Array(),

	initialize: function() {
		var $ = this.jQuery;

		var isMSIE = /*@cc_on!@*/0;

		// Enable dir collapsing
		HUB.ProjectFilesFileSelect.collapse();

		// Enable selection
		HUB.ProjectFilesFileSelect.selector();

		// Enable save button
		HUB.ProjectFilesFileSelect.enableButton();
		HUB.ProjectFilesFileSelect.enableSave();

		// Enable searching
		HUB.ProjectFilesFileSelect.enableSearchFilter();

		// Enable file upload
		HUB.ProjectFilesFileSelect.enableUpload();

		// Make zebra coloring
		HUB.ProjectFilesFileSelect.showOddEven();
	},

	showOddEven: function ()
	{
		var $ = this.jQuery;

		var items = $('#file-selector li');
		if (!items.length)
		{
			return false;
		}

		c = 1;
		var css = 'odd';
		items.each(function(i, item)
		{
			c++;

			$(item).removeClass('odd');
			$(item).removeClass('even')

			if (!$(item).hasClass('hidden'))
			{
				$(item).addClass(css);
				css = css == 'odd' ? 'even' : 'odd';
			}

		});
	},

	enableSearchFilter: function()
	{
		var $ = this.jQuery;
		var searchbox = $('#item-search');
		var directorySelect = $('#directorySelect');
		var output = $('#content-selector');
		var keyupTimer = '';

		var url = $('#filterUrl').length ? $('#filterUrl').val() : '';

		$(directorySelect).on('change', function()
		{
				HUB.ProjectFilesFileSelect.refreshData(true);
				return true;
		});

		if (!searchbox.length || !output.length)
		{
			return false;
		}

		$(searchbox).on('keyup', function(e)
		{
			// Get filtered data
			keyupTimer = setTimeout((function() {

				HUB.ProjectFilesFileSelect.refreshData(true);

				clearTimeout(keyupTimer);

			}), 2000);
		});

		$(searchbox).on('keydown', function(e)
		{
			clearTimeout(keyupTimer);
		});
	},

	refreshData: function(useLoader)
	{
		var $ = this.jQuery;

		var searchbox = $('#item-search');
		var directory = $('#directorySelect');
		var output = $('#content-selector');

		var url = $('#filterUrl').length ? $('#filterUrl').val() : '';

		if (!searchbox.length || !output.length)
		{
			return false;
		}

		url = url + '&filter=' + encodeURI($(searchbox).val());

		if (directory.length)
		{	
			url = url + '&directory=' + encodeURI($(directory).val());
		}

		var prev = $(output).html();

		if (useLoader)
		{
			// Show nothing while search is performed
			var show = '';
			if (HUB.Projects)
			{
				show = '<p class="content-loader"> ' + HUB.Projects.loadingIma('') + '</p>';
			}
			$(output).html(show);
		}

		// Ajax call to get current status of a block
		$.post(url, {},
		function (response)
		{
			if (response)
			{
				$(output).html(response);
			}
			else
			{
				$(output).html(prev);
			}

			// Re-enable js
			jQuery(document).trigger('ajaxLoad');

		});
	},

	enableSave: function()
	{
		var $ = this.jQuery;
		var btn  = $('#b-filesave');
		var form = $('#select-form');

		if (!btn.length || !form.length)
		{
			return false;
		}

		// Send data
		btn.on('click', function(e)
		{
			e.preventDefault();

			if (!btn.hasClass('disabled'))
			{
				selections = HUB.ProjectFilesFileSelect.collectSelections(false);
				$('#selecteditems').val(selections);

				form.submit();
			}

		});
	},

	enableUpload: function()
	{
		var $ = this.jQuery;
		var btn  		= $('#upload-file');
		var form 		= $('#upload-form');
		var statusBox 	= $('#status-box');

		if (!form.length)
		{
			return false;
		}

		var ajaxUploader = 0;
		if ($("#ajax-uploader").length)
		{
			var ajaxUploader = 1;
			var btn  		 = $('#f-upload');
			var uploader = new qq.ButtonFileUploader({
				element: $("#ajax-uploader")[0],
				action: $("#ajax-uploader").attr("data-action"),
				params: {test: 1},
				multiple: true,
				debug: true,
				maxChunkSize: 10000000,
				template: '<div class="qq-uploader">' +
							'<div class="qq-upload-button"><span>Click or drop file</span></div>' +
							'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
							'<ul class="qq-upload-list"></ul>' +
						   '</div>',
				fileTemplate: '<li>' +
						'<span class="qq-upload-icon"></span>' +
		                '<span class="qq-upload-file"></span>' +
						'<span class="qq-upload-name"></span>' +
						'<span class="qq-upload-status"></span>' +
						'<a class="qq-upload-cancel" href="#">Cancel</a>' +
						'<span class="qq-upload-ext"></span>' +
		                '<span class="qq-upload-size"></span>' +
						'<span class="qq-upload-spinner"></span>' +
						'<span class="qq-upload-error"></span>' +
		            '</li>',
				button: null,

				onComplete: function(id, file, response) {

					// All files processed?
					HUB.ProjectFilesFileSelect.processed = HUB.ProjectFilesFileSelect.processed + 1;
					if (HUB.ProjectFilesFileSelect.processed == HUB.ProjectFilesFileSelect.queued)
					{
						HUB.ProjectFilesFileSelect.refreshData(false);
					}

				}
			});
		}

		if (!btn.length)
		{
			return false;
		}

		if (ajaxUploader)
		{
			$(btn).addClass('btnaction');
			$(btn).addClass('disabled');

			$(btn).on('click', function(e) {
				e.preventDefault();

				var queue = uploader.checkQueue();
				var files = uploader.checkFiles();

				// Record number of items in queue
				HUB.ProjectFilesFileSelect.queued = queue.length;

				if (queue.length == 0)
				{
					// do nothing
				}
				else
				{
					// Archive file present?
					var arch = uploader._checkArchive();

					if (arch && !$(btn).hasClass('started'))
					{
						var question  = 'Do you wish to expand selected archive file(s)?';
						var yesanswer = 'yes, expand';
						var noanswer  = 'no, upload as an archive';

						// Add confirmation
						$(btn).parent().after('<div class="confirmaction" id="confirm-box" style="display:block;">' +
							'<p>' + question + '</p>' +
							'<p>' +
								'<a href="#" class="confirm" id="confirm-yes">' + yesanswer + '</a>' +
								'<a href="#" class="confirm c-no" id="confirm-no">' + noanswer + '</a>' +
								'<a href="#" class="cancel" id="confirm-box-cancel">cancel</a>' +
							'</p>' +
						'</div>');

						$('#confirm-box-cancel').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();
						});

						$('#confirm-yes').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();

							// Start upload
							uploader.startUploads(1);
						});

						$('#confirm-no').on('click', function(e){
							e.preventDefault();
							$('#confirm-box').remove();

							// Start upload
							uploader.startUploads(0);
						});

						// Move close to item
						var coord = $('#f-upload').position();
						$('#confirm-box').css('left', coord.left - 50).css('top', coord.top + 100 );
					}
					else
					{
						// Start upload
						uploader.startUploads(0);
					}
				}

				return false;
			});
		}
		else
		{
			// Send data
			btn.on('click', function(e)
			{
				e.preventDefault();

				if (!btn.hasClass('disabled') && !$('#quick-upload').hasClass('disabled'))
				{
					form.submit();
				}
			});
		}

		// Upload
		$(form).submit(function()
		{
		    var url = form.attr('action');
			var formData = new FormData($(this)[0]);

			// Show loader
			statusBox.css('opacity', '1.0');
			statusBox.html('<p class="status-loading">' + HUB.Projects.loadingIma('Uploading...') + '</p>');
			$('#quick-upload').addClass('disabled');

		    $.ajax({
		           type: "POST",
		           url: url,
		           data: formData,
				   contentType: false,
				   processData: false,
		           success: function(response)
		           {
						var success = 0;
						var error   = 'There was a problem uploading file(s)';

						if (response)
						{
						    try {
						        response = $.parseJSON(response);
								if (response.error || response.error != false)
								{
									error = response.error;
								}
								else
								{
									success = 1;
								}

						    }
							catch (e)
							{
								success = 0;
						    }
						}

						// Success or error
						if (success)
						{
							HUB.ProjectFilesFileSelect.refreshData(false);
							statusBox.html('<p class="status-success">Upload successful</p>');
							HUB.ProjectFilesFileSelect.fadeMessage();
						}
						else
						{
							statusBox.html('<p class="status-error">' + error + '</p>');
							HUB.ProjectFilesFileSelect.fadeMessage();
						}

						$('#quick-upload').removeClass('disabled');
						$('#uploader').val('');
		           }
		         });

		    return false;
		});

	},

	fadeMessage: function()
	{
		var $ = this.jQuery;

		if (!$('#status-box').length)
		{
			return false;
		}
		$("#status-box").animate({opacity:0.0}, 2000, function() {
		    $('#status-box').html('');
			$("#status-box").css('opacity', '1.0');
		});
	},

	enableButton: function()
	{
		var $ = this.jQuery;
		var btn = $('#b-filesave');
		var selection = $('#selecteditems').length ? $('#selecteditems').val() : '' ;

		if (!btn.length)
		{
			return false;
		}

		success = HUB.ProjectFilesFileSelect.checkProgress();
		if (success == true)
		{
			if (btn.hasClass('disabled'))
			{
				btn.removeClass('disabled');
			}
			if ($('#req').length && !$('#req').hasClass('success'))
			{
				//$('#req').addClass('success');
			}
		}
		else
		{
			if (!btn.hasClass('disabled'))
			{
				btn.addClass('disabled');
			}
			if ($('#req').length && $('#req').hasClass('success'))
			{
				//$('#req').removeClass('success');
			}
		}
	},

	checkProgress: function ()
	{
		var $ = this.jQuery;
		var success = false;

		checker = $('.selectedfilter').length;

		// Check that we satisfy minimum/maximum requirements
		if (HUB.ProjectFilesFileSelect.checkMinimum(checker)
			&& HUB.ProjectFilesFileSelect.checkMaximum(checker))
		{
			success = true;
		}

		return success;
	},

	selector: function ()
	{
		var $ = this.jQuery;
		var max = $('#maxitems').length ? $('#maxitems').val() : 0;

		if (!$('#file-selector').length)
		{
			return false;
		}

		$('#file-selector').selectable({
			filter: ".allowed",
			cancel: ".collapsor",
		    selected: function (event, ui)
			{
				// Prevent going over maximum
				numSelected = $('.selectedfilter').length;
				if (max > 0 && numSelected > 1
					&& !$(ui.selected).hasClass('selectedfilter')
					&& !HUB.ProjectFilesFileSelect.checkMaximum(numSelected + 1))
				{
					// Remove filter from previously selected item(s)
					/*
					var popItem = $('.selectedfilter')[0];
					$(popItem).removeClass('selectedfilter');

					$(ui.selected).addClass('selectedfilter');
					*/

					// Nothing happens
					$(ui.selected).removeClass('selectedfilter');
				}
		        else if ($(ui.selected).hasClass('selectedfilter'))
				{
		            $(ui.selected).removeClass('selectedfilter');
		            // do unselected stuff
		        }
				else
				{
		            $(ui.selected).addClass('selectedfilter');
		        }

				HUB.ProjectFilesFileSelect.enableButton();
		    },
		    unselected: function (event, ui)
			{
		        $(ui.selected).removeClass('selectedfilter');
				HUB.ProjectFilesFileSelect.enableButton();
		    }
		});

	},

	checkMinimum: function(num)
	{
		var $ = this.jQuery;
		var min = $('#minitems').length ? $('#minitems').val() : 0;

		if (min > 0)
		{
			if (num >= min)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return true;
	},

	checkMaximum: function(num)
	{
		var $ = this.jQuery;
		var max = $('#maxitems').length ? $('#maxitems').val() : 0;

		if (max > 0)
		{
			if (num <= max)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return true;
	},

	collectSelections: function(getArray)
	{
		var $ = this.jQuery;
		var items = $('.selectedfilter');
		var selections 	= new Array();
		var selString = '';

		if (items.length > 0)
		{
			items.each(function(i, item)
			{
				var el = $(item).find('span.item-wrap')[0];

				if (el && $(el).length)
				{
					var id = $(el).attr('id');

					var idx = HUB.Projects.getArrayIndex(id, selections);

					// Add
					if (idx == -1)
					{
						selections.push(id);
						selString = selString + id + ',';
					}
				}
			});
		}

		return getArray ? selections : selString;
	},

	collapse: function ()
	{
		var $ = this.jQuery;

		var collapse = $('.collapsor');
		var folders = $('.type-folder');

		// Collapse/uncollapse folder contents
		if (collapse.length > 0)
		{
			collapse.each(function(i, item)
			{
				$(item).on('click', function(e)
				{
					var parent = $(item).parent().parent();
					var parentId = $(parent).attr('id').replace('dir-', '');

					var action =  $(parent).hasClass('collapsed') ? 'uncollapse' : 'collapse';
					HUB.ProjectFilesFileSelect.collapseElements(parentId, action);
				});
			});
		}

		// Collapse on load
		if (folders.length > 0)
		{
			folders.each(function(i, item)
			{
				var parentId = $(item).attr('id').replace('dir-', '');

				if (!$(item).hasClass('opened'))
				{
					HUB.ProjectFilesFileSelect.collapseElements(parentId, 'collapse');
				}

			});
		}
	},

	collapseElements: function (parentId, action)
	{
		var $ = this.jQuery;

		var items = $('.parent-' + parentId);
		var controller = $('#dir-' + parentId);

		if (!controller.length)
		{
			return false;
		}

		if (action == 'collapse')
		{
			$(controller).addClass('collapsed');
		}
		else
		{
			$(controller).removeClass('collapsed');
		}

		var subDirs = new Array();

		if (items.length)
		{
			items.each(function(i, item)
			{
				if (action == 'collapse')
				{
					// Collapsing
					$(item).addClass('hidden');
				}
				else
				{
					// Uncollapsing
					if ($(item).hasClass('hidden'))
					{
						// Sub folders remain collapsed
						if ($(item).hasClass('type-folder'))
						{
							var subId = $(item).attr('id').replace('dir-', '');
							subDirs.push(subId);

							if (!$(item).hasClass('collapsed'))
							{
								$(item).addClass('collapsed');
							}
						}

						$(item).removeClass('hidden');

						for (var i = 0, j = subDirs.length; i < j; i++)
						{
						   if ($(item).hasClass('parent-' + subDirs[i]))
							{
								$(item).addClass('hidden');
							}
						}

					}
				}
			});
		}

		HUB.ProjectFilesFileSelect.showOddEven();

	}
};

jQuery(document).ready(function($){
	HUB.ProjectFilesFileSelect.initialize();
});

// Register the event
jQuery(document).on('ajaxLoad', HUB.ProjectFilesFileSelect.initialize);
