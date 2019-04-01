/**
 * @package     hubzero-cms
 * @file        core/plugins/projects/files/assets/js/fileselector.js
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
	jQuery  : jq,
	fetched : [],

	initialize: function () {
		var $ = this.jQuery;

		var isMSIE = /*@cc_on!@*/0;

		// Enable dir collapsing
		HUB.ProjectFilesFileSelect.collapse();

		// Enable selection
		HUB.ProjectFilesFileSelect.selector();

		// Enable save button
		HUB.ProjectFilesFileSelect.enableButton();
		HUB.ProjectFilesFileSelect.enableSave();

		// Enable file upload
		HUB.ProjectFilesFileSelect.enableUpload();
	},

	fetchData: function ( target, parent )
	{
		target = $(target);

		var url  = $('#filterUrl').length ? $('#filterUrl').val() : '';
			url += "&parent=" + parent;

		if (target.data('path'))
		{
			url += "&directory=" + encodeURI(target.data('path'));
		}

		if (target.data('connection'))
		{
			url += "&cid=" + encodeURI(target.data('connection'));
		}

		target.after('<p class="content-loader-slim"> ' + HUB.Projects.loadingIma('') + '</p>');

		$.ajax({
			dataType : 'html',
			type     : 'GET',
			cache    : false,
			url      : url,
			success  : function ( data, textStatus, jqXHR ) {
				// Log our fetch
				HUB.ProjectFilesFileSelect.fetched.push(parent);

				// Insert new data
				target.next('.content-loader-slim').remove();
				target.after(data);
			},
			error    : function ( jqXHR, textStatus, errorThrown ) {
				// We're going to assume we get here because the user isn't authorized to the remote client
				// Open up a new window to handle the oauth transaction
				url += '&return=' + encodeURI(window.location.origin);
				var auth = window.open(url, "_blank", "toolbar=no, scrollbars=no, resizable=no, width=500, height=600");

				// Remove the loader and recollapse the folder
				target.next('.content-loader-slim').remove();
				target.find('.collapsor').first().trigger('click');

				// Listen for the oauth process to land back on the hub
				$(auth).load(function () {
					if (this.location.hostname == window.location.hostname) {
						// Close the oauth window and refetch the providers fields
						auth.close();
						target.find('.collapsor').first().trigger('click');
					}
				});
			}
		});
	},

	fetchRoot: function ()
	{
		var url  = $('#filterUrl').length ? $('#filterUrl').val() : '';
			url += "&directory=.";

		var sel = $('.file-selector');
		sel.html('<p class="content-loader-slim"> ' + HUB.Projects.loadingIma('') + '</p>');

		$.ajax({
			dataType : 'html',
			type     : 'GET',
			cache    : false,
			url      : url,
			success  : function ( data, textStatus, jqXHR ) {
				// Insert new data
				sel.html(data);
			},
		});
	},

	refetchData: function ( directory, connection )
	{
		var $ = this.jQuery;

		if (!directory)
		{
			// No directory, so start fresh
			HUB.ProjectFilesFileSelect.fetched = [];
			HUB.ProjectFilesFileSelect.fetchRoot();
		}
		else
		{
			var target = $('.type-folder[data-path="' + directory + '"]');

			if ($.type(connection) !== "undefined")
			{
				target = target.filter('.type-folder[data-connection="' + connection + '"]');
			}

			var parent = target.attr('id').replace('dir-', ''),
				action = target.hasClass('collapsed') ? 'uncollapse' : 'collapse';

			// If the dir has already been fetched, clear it out
			var loc = $.inArray(parent, HUB.ProjectFilesFileSelect.fetched);
			if (loc >= 0)
			{
				HUB.ProjectFilesFileSelect.fetched.splice(loc, 1);

				// If it's open, close it and clear out its immediate children
				if (action == 'collapse')
				{
					// Clear this item from fetched data (so we refetch)
					HUB.ProjectFilesFileSelect.collapseElements(parent, action, function ( )
					{
						HUB.ProjectFilesFileSelect.clearFolder(target);
						target.find('.collapsor').first().trigger('click');
					});
				}
				else
				{
					// It's closed, so silently clear out the children so we refresh next time
					HUB.ProjectFilesFileSelect.clearFolder(target);
				}
			}
		}
	},

	clearFolder: function ( folder )
	{
		var parent = folder.attr('id').replace('dir-', ''),
			items  = $('.type-folder.parent-' + parent);

		// Delete subfolders
		items.each(function ( )
		{
			HUB.ProjectFilesFileSelect.clearFolder($(this));
		});

		folder.siblings('.parent-' + parent).remove();
	},

	enableSave: function ( )
	{
		var $    = this.jQuery,
			btn  = $('#b-filesave'),
			form = $('#select-form');

		if (!btn.length || !form.length)
		{
			return false;
		}

		// Send data
		btn.on('click', function ( e )
		{
			e.preventDefault();

			if (!btn.hasClass('disabled'))
			{
				$('#selecteditems').val(HUB.ProjectFilesFileSelect.collectSelections());

				form.submit();
			}
		});
	},

	collectSelections: function ( )
	{
		var $          = this.jQuery,
			items      = $('.selectedfilter'),
			selections = [],
			selString  = '';

		if (items.length > 0)
		{
			items.each(function ( i, item )
			{
				var el = $(item).find('span.item-wrap').first();

				if (el)
				{
					var id  = el.attr('id');

					// Add
					if ($.inArray(id, selections) === -1)
					{
						selections.push(id);
						selString = selString + id + ',';
					}
				}
			});
		}

		return selString;
	},

	enableUpload: function ( )
	{
		var $         = this.jQuery,
			btn       = $('#upload-file'),
			form      = $('#upload-form'),
			statusBox = $('#status-box');

		if (!form.length)
		{
			return false;
		}

		// Send data
		btn.on('click', function(e)
		{
			e.preventDefault();

			if (!btn.hasClass('disabled') && !$('#quick-upload').hasClass('disabled'))
			{
				form.submit();
			}
		});

		// Upload
		$(form).submit(function ( )
		{
			var url      = form.attr('action'),
				formData = new FormData($(this)[0]);

			// Show loader
			statusBox.css('opacity', '1.0');
			statusBox.html('<p class="status-loading">' + HUB.Projects.loadingIma('Uploading...') + '</p>');
			$('#quick-upload').addClass('disabled');

			$.ajax({
				type        : "POST",
				url         : url,
				data        : formData,
				contentType : false,
				processData : false,
				success     : function ( response )
				{
					var success = 0,
						error   = 'There was a problem uploading file(s)';

					if (response)
					{
						try
						{
							response = $.parseJSON(response);

							if (response.error || response.error !== false)
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
						if ($('.type-folder[data-connection="-1"]').length)
						{
							var val = form.find('select[name="subdir"]').val();
							if (!val)
							{
								val = '.';
							}

							HUB.ProjectFilesFileSelect.refetchData(val, '-1');
						}
						else
						{
							HUB.ProjectFilesFileSelect.refetchData(form.find('select[name="subdir"]').val());
						}
						statusBox.html('<p class="status-success">Upload successful</p>');
						HUB.ProjectFilesFileSelect.fadeMessage();
						$('.file-selector').find('.collapsor').first().trigger('click');
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

	fadeMessage: function ( )
	{
		var $      = this.jQuery,
			status = $('#status-box');

		if (!status.length)
		{
			return false;
		}

		$("#status-box").animate({opacity:0.0}, 2000, function()
		{
			status.html('');
			status.css('opacity', '1.0');
		});
	},

	enableButton: function ( )
	{
		var $         = this.jQuery,
			btn       = $('#b-filesave'),
			selection = $('#selecteditems').length ? $('#selecteditems').val() : '' ;

		if (!btn.length)
		{
			return false;
		}

		var success = HUB.ProjectFilesFileSelect.checkProgress();

		if (success === true)
		{
			if (btn.hasClass('disabled'))
			{
				btn.removeClass('disabled');
			}
		}
		else
		{
			if (!btn.hasClass('disabled'))
			{
				btn.addClass('disabled');
			}
		}
	},

	checkProgress: function ( )
	{
		var $       = this.jQuery,
			success = false,
			checker = $('.selectedfilter').length;

		// Check that we satisfy minimum/maximum requirements
		if (HUB.ProjectFilesFileSelect.checkMinimum(checker) &&
			HUB.ProjectFilesFileSelect.checkMaximum(checker))
		{
			success = true;
		}

		return success;
	},

	selector: function ()
	{
		var $   = this.jQuery,
			max = $('#maxitems').length ? $('#maxitems').val() : 0,
			sel = $('#file-selector');

		if (!sel.length)
		{
			return false;
		}

		sel.selectable({
			filter: ".allowed",
			cancel: ".collapsor",
			selected: function ( event, ui )
			{
				// Prevent going over maximum
				numSelected = $('.selectedfilter').length;
				if (max > 0 && numSelected > 1 &&
					!$(ui.selected).hasClass('selectedfilter') &&
					!HUB.ProjectFilesFileSelect.checkMaximum(numSelected + 1))
				{
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
			unselected: function ( event, ui )
			{
				$(ui.selected).removeClass('selectedfilter');
				HUB.ProjectFilesFileSelect.enableButton();
			}
		});

	},

	checkMinimum: function ( num )
	{
		var $   = this.jQuery,
			min = $('#minitems').length ? $('#minitems').val() : 0;

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

	checkMaximum: function ( num )
	{
		var $   = this.jQuery,
			max = $('#maxitems').length ? $('#maxitems').val() : 0;

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

	collapse: function ( )
	{
		var $ = this.jQuery;

		$('.file-selector').on('click', '.collapsor', function ( e )
		{
			var parent   = $(this).parent(),
				parentId = $(parent).attr('id').replace('dir-', ''),
				action   = $(parent).hasClass('collapsed') ? 'uncollapse' : 'collapse';

			HUB.ProjectFilesFileSelect.collapseElements(parentId, action);
		});
	},

	collapseElements: function ( parentId, action, callback )
	{
		var $          = this.jQuery,
			items      = $('.parent-' + parentId),
			controller = $('#dir-' + parentId);

		if (!controller.length)
		{
			return false;
		}

		if (action == 'collapse')
		{
			$(controller).addClass('collapsed');

			// Hide children
			if (items.length)
			{
				items.addClass('hidden');
				items.each(function ( i, val )
				{
					var item = $(val);

					// Hide and collapse all sub-children as well
					if (item.hasClass('type-folder'))
					{
						HUB.ProjectFilesFileSelect.collapseElements(item.attr('id').replace('dir-', ''), action);
					}
				});
			}
		}
		else
		{
			// Don't refetch if we've already done so
			if ($.inArray(parentId, HUB.ProjectFilesFileSelect.fetched) === -1)
			{
				HUB.ProjectFilesFileSelect.fetchData(controller, parentId);
			}

			// Uncollapse and show children (no recursion required)
			$(controller).removeClass('collapsed');
			items.removeClass('hidden');
		}

		if ($.type(callback) === 'function')
		{
			callback();
		}
	}
};

jQuery(document).ready(function($){
	HUB.ProjectFilesFileSelect.initialize();
});

// Register the event
jQuery(document).on('ajaxLoad', HUB.ProjectFilesFileSelect.initialize);
