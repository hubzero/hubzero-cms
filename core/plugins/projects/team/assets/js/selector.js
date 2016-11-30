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

HUB.ProjectTeamSelect = {
	jQuery: jq,
	selections: new Array(),

	initialize: function() {
		var $ = this.jQuery;
		var isMSIE = /*@cc_on!@*/0;

		// Enable selection
		HUB.ProjectTeamSelect.selector();

		// Enable adding new author
		HUB.ProjectTeamSelect.enableAdd();

		// Enable save button
		HUB.ProjectTeamSelect.enableButton();
		HUB.ProjectTeamSelect.enableSave();

		// Make zebra coloring
		HUB.ProjectTeamSelect.showOddEven();

		// Link to add author
		HUB.ProjectTeamSelect.newAuthor();
	},

	newAuthor: function()
	{
		var $ = this.jQuery;

		var link = $('#newauthor-question');
		var abox = $('#abox-content-wrap');

		if (!link.length)
		{
			return false;
		}

		var url = link.attr('href');
		var url = url + '&ajax=1&no_html=1';

		link.on('click', function(e) 
		{
			e.preventDefault();

			// Ajax call to get current status of a block
			$.post(url, {}, 
			function (response) 
			{
				if (response)
				{
					$(abox).html(response);
				}

				// Re-enable js
				jQuery(document).trigger('ajaxLoad');
			});
		});
	},

	enableAdd: function()
	{
		var $ = this.jQuery;
		var btn = $('#b-add');
		var form = $('#add-author');
		var statusBox = $('#status-box');

		if (!btn.length || !form.length)
		{
			return false;
		}

		// Send data
		btn.on('click', function(e) 
		{
			e.preventDefault();

			var passed = HUB.ProjectTeamSelect.checkRequired();

			if (passed == false)
			{
				statusBox.html('<p class="status-error">Please make sure all fields are filled</p>');
				HUB.ProjectTeamSelect.fadeMessage();
			}
			else
			{
				form.submit();
			}
		});
	},

	checkRequired: function ()
	{
		var $ = this.jQuery;
		var success = true;
		var fields = $('.inputrequired');

		if (fields.length == 0)
		{
			return true;
		}

		fields.each(function(i, item)
		{
			if (!$(item).val() || $(item).val() == '')
			{
				success = false;
			}
		});

		return success;
	},

	showOddEven: function ()
	{
		var $ = this.jQuery;
		var items = $('#team-selector li');
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

	enableSave: function()
	{
		var $ = this.jQuery;
		var btn  = $('#b-save');
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
				selections = HUB.ProjectTeamSelect.collectSelections(false);
				$('#selecteditems').val(selections);
				form.submit();
			}
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
		var btn = $('#b-save');

		if (!btn.length)
		{
			return false;
		}

		success = HUB.ProjectTeamSelect.checkProgress();
		if (success == true)
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

	checkProgress: function ()
	{
		var $ = this.jQuery;
		var success = false;

		checker = $('.selectedfilter').length;

		// Check that we satisfy minimum/maximum requirements
		if (checker > 0)
		{
			success = true;
		}

		return success;
	},

	selector: function ()
	{
		var $ = this.jQuery;

		if (!$('#team-selector').length)
		{
			return false;
		}

		$('#team-selector').selectable({
			filter: ".allowed",
			cancel: ".collapsor",
			selected: function (event, ui) 
			{
				if ($(ui.selected).hasClass('selectedfilter')) 
				{
					$(ui.selected).removeClass('selectedfilter');
					// do unselected stuff
				}
				else
				{
					$(ui.selected).addClass('selectedfilter');
				}

				HUB.ProjectTeamSelect.enableButton();
			},
			unselected: function (event, ui) 
			{
				$(ui.selected).removeClass('selectedfilter');
				HUB.ProjectTeamSelect.enableButton();
			}
		});
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
				var id = $(item).attr('id').replace('author-', '');

				var idx = HUB.Projects.getArrayIndex(id, selections);

				// Add 
				if (idx == -1) 
				{
					selections.push(id);
					selString = selString + id + ',';
				}
			});
		}

		return getArray ? selections : selString;
	}
};

jQuery(document).ready(function($){
	HUB.ProjectTeamSelect.initialize();
});

// Register the event
jQuery(document).on('ajaxLoad', HUB.ProjectTeamSelect.initialize);
