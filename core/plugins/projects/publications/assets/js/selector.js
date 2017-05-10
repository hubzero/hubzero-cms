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

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

HUB.ProjectPublicationsSelector = {
	jQuery: jq,
	selections: new Array(),

	initialize: function() {
		var $ = this.jQuery;

		var isMSIE = /*@cc_on!@*/0;

		var container = $('#pub-selector');

		if (container.length) {
			// Infinite scroll
			var opts = {
					navSelector  : '.list-footer',    // selector for the paged navigation
					nextSelector : '.list-footer .next a',  // selector for the NEXT link (to page 2)
					itemSelector : '#pub-selector li',     // selector for all items you'll retrieve
					binder: container,
					behavior: 'local',
					loading: {
						finishedMsg: 'No more records to load.'
					},
					path: function(index) {
						var path = $('.list-footer .next a').attr('href'),
							limit = $('#limit').val(),
							start = 0;
						if (path.match(/limit[-=]([0-9]*)/)) {
							limit = path.match(/limit[-=]([0-9]*)/).slice(1);
						}
						limit = limit ? limit : 25;
						start = path.match(/start[-=]([0-9]*)/).slice(1);
						return path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit));
					},
					debug: true
				};

			if (jQuery().infinitescroll) {
				container.infinitescroll(
					opts,
					function(newElements) {
						HUB.ProjectPublicationsSelector.showOddEven();
					}
				);
			}

			var typedelay = (function(){
				var timer = 0;
				return function(callback, ms){
					clearTimeout(timer);
					timer = setTimeout(callback, ms);
				};
			})();

			$('#pub-search')
				.on('keyup', function (e) {
					var input = $(this);

					// Ad a slight delay so search only happens after
					// typing appears to stop (or pause, at least)
					typedelay(function(){
						if (!input.length) {
							return;
						}

						// Perform the search
						$.get($('.item-add').attr('href') + '&search=' + input.val(), {}, function(data){
							var results = $(data).find('#pub-selector-results').html();

							if (results && jQuery().infinitescroll) {
								// Disable infinite scroll
								container.infinitescroll('destroy');
								container.data('infinitescroll', null);

								// Add the results to the page
								$('#pub-selector-results').html(results);
								// Re-enable infinite scroll
								// This is ugly but done this way as the search results
								// will have its own pagination list. So, we need to force
								// infinite scrll to start over every time we get new results
								container = $('#pub-selector');
								opts.binder = container;
								container.infinitescroll(
									opts,
									function(newElements) {
										HUB.ProjectPublicationsSelector.showOddEven();
									}
								);
								HUB.ProjectPublicationsSelector.selector();
							}
						});
					}, 1000);
				});
		}

		// Enable selection
		HUB.ProjectPublicationsSelector.selector();

		// Enable save button
		HUB.ProjectPublicationsSelector.enableButton();
		HUB.ProjectPublicationsSelector.enableSave();

		// Make zebra coloring
		HUB.ProjectPublicationsSelector.showOddEven();
	},

	showOddEven: function ()
	{
		var $ = this.jQuery;

		var items = $('#pub-selector li');
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
				selections = HUB.ProjectPublicationsSelector.collectSelections(false);
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
		var btn = $('#b-filesave');
		var selection = $('#selecteditems').length ? $('#selecteditems').val() : '' ;

		if (!btn.length)
		{
			return false;
		}

		success = HUB.ProjectPublicationsSelector.checkProgress();
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
		optional = $('.el-optional').length;

		// Check that we satisfy minimum/maximum requirements
		if (checker >= 1 || optional >= 1)
		{
			success = true;
		}

		return success;
	},

	selector: function ()
	{
		var $ = this.jQuery;
		var max = $('#maxitems').length ? $('#maxitems').val() : 1;

		if (!$('#pub-selector').length)
		{
			return false;
		}

		$('#pub-selector').selectable({
			filter: ".allowed",
			cancel: 'a',
			selected: function (event, ui)
			{
				// Prevent going over maximum
				numSelected = $('.selectedfilter').length;
				if (max == numSelected
					&& !$(ui.selected).hasClass('selectedfilter'))
				{
					// Remove filter from previously selected item(s)
					var popItem = $('.selectedfilter')[0];
					$(popItem).removeClass('selectedfilter');

					$(ui.selected).addClass('selectedfilter');

					// Nothing happens
					//$(ui.selected).removeClass('selectedfilter');
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

				HUB.ProjectPublicationsSelector.enableButton();
			},
			unselected: function (event, ui)
			{
				$(ui.selected).removeClass('selectedfilter');
				HUB.ProjectPublicationsSelector.enableButton();
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
				var id = $(item).attr('id').replace("choice-", "");
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
	HUB.ProjectPublicationsSelector.initialize();
});

// Register the event
jQuery(document).on('ajaxLoad', HUB.ProjectPublicationsSelector.initialize);
