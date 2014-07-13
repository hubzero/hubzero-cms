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
// Project Publication Manager JS (NEW)
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectPublicationsDraft = {
	jQuery: jq,
	timer: '',
	doneTypingInterval: 1000,
	completeness: 0,
	minChars: 3,

	initialize: function()
	{
		var $ = this.jQuery;

		// Check block status on each element change
		HUB.ProjectPublicationsDraft.onElementChange();

		// Enable block actions
		HUB.ProjectPublicationsDraft.enableBlock();

		// Enable idnividual element controls
		HUB.ProjectPublicationsDraft.enableElementNav();

		// Set initial completeness
		HUB.ProjectPublicationsDraft.completeness = $('#complete').length ? $('#complete').val() : 0;
		HUB.ProjectPublicationsDraft.checkBlockCompleteness();

		// Show 'more' link for extensive side text
		HUB.ProjectPublicationsDraft.showMoreText();

		// Saving publication version params
		HUB.ProjectPublicationsDraft.saveParam();

		// Confirm item deletion
		HUB.ProjectPublicationsDraft.confirmDelete();
		HUB.ProjectPublicationsDraft.confirmStatusChange();

		// Enable disputes
		HUB.ProjectPublicationsDraft.allowDispute();

	},

	// Allow to edit notices
	allowDispute: function()
	{
		var $ = this.jQuery;

		// Allow editing of notices
		var edits = $('.disputeit a');
		if (edits.length)
		{
			edits.each(function(i, item)
			{
				$(item).on('click', function(e)
				{
					e.preventDefault();

					var element = $(item).parent();
					HUB.ProjectPublicationsDraft.drawDisputeBox(element);

				});
			});
		}

		// Allow removal of notices
		var dels = $('.remove-notice a');
		if (dels.length)
		{
			dels.each(function(i, item)
			{
				var element = $(item).parent();
				var props   = $(element).attr('id');

				var form 	= $('#notice-form');
				var url 	= form.attr('action');
				var href	= $(item).attr('href');
				url = url + '?version=' + $('#version').val();
				url = url + '&action=undispute&p=' + props + href;

				$(item).attr('href', url);

				$(item).on('click', function(e)
				{
					e.preventDefault();

					if (HUB.Projects)
					{
						HUB.Projects.addConfirm($(item), 'Remove dispute message?', 'yes, remove', 'cancel');
						if ($('#confirm-box')) {
							$('#confirm-box').css('margin-left', '-100px');
							$('#confirm-box').css('font-size', '100%');
						}
					}
				});
			});
		}
	},

	drawDisputeBox: function (element)
	{
		var $ 		= this.jQuery;
		var review 	= $('#notice-review');
		var submit 	= $('#notice-submit');
		var form 	= $('#notice-form');

		if (!review.length || !$('#addnotice').length || !element.length)
		{
			return false;
		}

		// Reload prop value
		$('#props').val('');
		var value = $(element).attr('id');
		$('#props').val(value);

		// Open form in fancybox
		$.fancybox( [$('#addnotice')] );

		$(form).unbind();

		// Submit only if reason is entered
		$(submit).on('click', function(e)
		{
			e.preventDefault();

			if ($(review).val() && $(review).val() != '')
			{
				$(form).submit();
			}
		});
	},

	confirmStatusChange: function()
	{
		// Confirm revert
		if ($("#action-revert").length)
		{
			$("#action-revert").on('click', function(e)
			{
				if (HUB.Projects)
				{
					e.preventDefault();
					HUB.Projects.addConfirm($("#action-revert"), 'Are you sure you want to revert this to draft?', 'yes, revert', 'cancel');
					if ($('#confirm-box')) {
						$('#confirm-box').css('margin-left', '-100px');
						$('#confirm-box').css('font-size', '100%');
					}
				}
			});
		}
	},

	confirmDelete: function()
	{
		var $ = this.jQuery;
		var links = $(".item-remove");

		if (!links.length)
		{
			return false;
		}

		// Confirm delete
		$(links).each(function(i, el)
		{
			$(el).on('click', function(e)
			{
				e.preventDefault();
				if (HUB.Projects)
				{
					HUB.Projects.addConfirm($(el), 'Delete this item?', 'yes, delete', 'cancel');
					if ($('#confirm-box')) {
						$('#confirm-box').css('margin-left', '-100px');
						$('#confirm-box').css('font-size', '100%');
					}
				}
			});
		});
	},

	// Saving publication version params
	saveParam: function()
	{
		var $ = this.jQuery;
		var links = $(".save-param");

		if (!links.length)
		{
			return false;
		}

		links.each(function(i, item)
		{
			$(item).on('click', function(e)
			{
				e.preventDefault();

				var url  	= $(item).attr('href');
				var input 	= $(item).parent().parent().find('input')[0];
				var name 	= $(input).attr('id');
				var val 	= $(input).val();
				url = url + '&param=' + name + '&value=' + escape(val);
				url = url + '&no_html=1&ajax=1';

				// Ajax call to get current status of a block
				$.post(url, {},
					function (out) {
					out = $.parseJSON(out);
					var output = $(item).parent().parent().find('.save-param-status')[0];

					if (out.success == 1)
					{
						if ($(output).length)
						{
							$(input).val(out.result);
							$(output).html('Name saved');
							$(output).removeClass('fail');
							HUB.ProjectPublicationsDraft.addMessageFade($(output));

						}
					}
					else
					{
						if ($(output).length)
						{
							$(input).val(out.result);
							$(output).html('Failed to save');
							$(output).addClass('fail');
							HUB.ProjectPublicationsDraft.addMessageFade($(output));
						}
					}
				});
			});
		});

	},

	addMessageFade: function(item)
	{
		var $ = this.jQuery;
		var keyupTimer = '';

		if ($(item).length > 0)
		{
			$(item).css('opacity', '1.0');
			var keyupTimer = setTimeout((function()
			{
				$(item).animate({opacity:0.0}, 2000, function() {
				    $(item).html('');
					$(item).css('opacity', '1.0');
				});
			}), 1000);
		}
	},

	// Show 'more' link for extensive side text
	showMoreText: function()
	{
		var $ = this.jQuery;
		$(".more-content").fancybox();

	},

	// Mark block element as complete/incomplete
	checkElementCompleteness: function(value, required, element)
	{
		var $ = this.jQuery;
		var complete = 1;

		if (!value)
		{
			HUB.ProjectPublicationsDraft.changeElementStatus(element, 'incomplete', required);
			if (required)
			{
				complete = 0;
			}
		}
		else
		{
			HUB.ProjectPublicationsDraft.changeElementStatus(element, 'complete', required);
		}

		var nav = $('#' + $(element).attr('id') + '-apply');
		if (nav.length)
		{
			HUB.ProjectPublicationsDraft.enableElementButton(nav, complete);
		}

		// Check how this affects block status
		HUB.ProjectPublicationsDraft.checkBlockCompleteness();
	},

	// Check block completion
	checkBlockCompleteness: function()
	{
		var $ = this.jQuery;

		var complete = 1;
		var done = 0;

		if (HUB.ProjectPublicationsDraft.completeness == 0)
		{
			var req = $('#required').length ? $('#required').val() : 0;
			if (req == 1)
			{
				complete = 0;
			}
		}

		if ($('.el-required').length && $('.el-required').length > $('.el-complete').length)
		{
			complete = 0;
		}

		if ($('.el-required').length > 0)
		{
			$('.el-required').each(function(i, item)
			{
				if ($(item).hasClass('el-incomplete'))
				{
					complete = 0;
				}
				else
				{
					done = done + 1;
				}
			});

			if (done == $('.el-required').length)
			{
				complete = 1;
			}
		}

		// Enable/disable control buttons
		HUB.ProjectPublicationsDraft.enableNav(complete);
	},

	// Enable/disable controls
	enableNav: function(complete)
	{
		var $ 			= this.jQuery;
		var buttons 	= $('.submitbutton');
		var submitarea 	= $('#submit-area');

		if (buttons.length  == 0)
		{
			return false;
		}

		if (complete == 0 && submitarea.length && !submitarea.hasClass('disabled'))
		{
			submitarea.addClass('disabled');
		}
		else if (complete == 1 && submitarea.hasClass('disabled'))
		{
			submitarea.removeClass('disabled');
		}

		// Enable / disable buttons
		buttons.each(function(i, item)
		{
			// Style buttons appropriately
			if (complete == 1 && $(item).hasClass('disabled'))
			{
				$(item).removeClass('disabled');
			}
			else if (complete == 0 && !$(item).hasClass('disabled'))
			{
				if ($(item).attr('id') != 'c-previous')
				{
					$(item).addClass('disabled');
				}
			}

			// Submit form
			$(item).on('click', function(e)
			{
				e.preventDefault();

				var id = $(item).attr('id');

				var task = (id == 'c-apply') ? 'apply' : 'save';
				task = (id == 'c-previous') ? 'rewind' : task;

				if (!$(item).hasClass('disabled'))
				{
					$('#action').val(task);
					if ($('#plg-form').length)
					{
						$('#plg-form').submit();
					}
				}
			});

		});

	},

	// Enable/disable controls
	enableElementNav: function()
	{
		var $ = this.jQuery;

		var buttons 	= $('.save-element');
		if (!buttons.length)
		{
			return false;
		}

		buttons.each(function(i, item)
		{
			var element = $(item).attr('id').replace('-apply','');
			var parent 	= $('#' + element);

			if ($(parent).length)
			{
				var complete = ($(parent).hasClass('el-complete') || $(parent).hasClass('el-optional') ) ? 1 : 0;

				HUB.ProjectPublicationsDraft.enableElementButton(item, complete);

				// Submit form
				$(item).on('click', function(e)
				{
					e.preventDefault();

					if (!$(item).hasClass('disabled'))
					{
						$('#action').val('apply');
						$('#next').val('1');
						if ($('#plg-form').length)
						{
							$('#plg-form').submit();
						}
					}
				});
			}

		});
	},

	// Change element CSS
	enableElementButton: function(item, complete)
	{
		var $ = this.jQuery;

		if (!$(item).length)
		{
			return false;
		}

		// Style buttons appropriately
		if (complete == 1 && $(item).hasClass('disabled'))
		{
			$(item).removeClass('disabled');
			$(item).parent().parent().removeClass('disabled');
		}
		else if (complete == 0 && !$(item).hasClass('disabled'))
		{
			$(item).addClass('disabled');
			$(item).parent().parent().addClass('disabled');
		}
	},

	// Change element CSS
	changeElementStatus: function(element, status, required)
	{
		var $ = this.jQuery;

		if (status == 'incomplete')
		{
			if ($(element).hasClass('el-complete'))
			{
				$(element).removeClass('el-complete');
			}
			if (!$(element).hasClass('el-incomplete'))
			{
				$(element).addClass('el-incomplete');
			}
		}

		if (status == 'complete')
		{
			if ($(element).hasClass('el-incomplete'))
			{
				$(element).removeClass('el-incomplete');
			}
			if (!$(element).hasClass('el-complete'))
			{
				$(element).addClass('el-complete');
			}
			if ($(element).hasClass('el-partial'))
			{
				$(element).removeClass('el-partial');
			}
		}
	},

	// Ajax call to get current block status
	checkBlockStatus: function(section)
	{
		var $ = this.jQuery;

		var url = HUB.ProjectPublicationsDraft.getPubUrl(1, section);
		url = url + '&action=checkstatus';

		response = '';

		// Ajax call to get current status of a block
		$.post(url, {},
			function (response) {

			response = $.parseJSON(response);
			clearTimeout(HUB.ProjectPublicationsDraft.timer);
		});

		return response;

	},

	onElementChange: function()
	{
		var $ = this.jQuery;

		if (typeof CKEDITOR === 'undefined') {
		    CKEDITOR = false;
		}

		var el = $('.blockelement');
		if (el.length > 0)
		{
			el.each(function(i, item)
			{
				var value 	 = '';
				var required = $(item).hasClass('el-required') ? 1 : 0;
				var editor   = $(item).hasClass('el-editor') ? 1 : 0;

				// Input field?
				var input = $(item).find('input');
				if (input.length)
				{
					$(input).on('keyup', function(e)
					{
						HUB.ProjectPublicationsDraft.timer = setTimeout(function(v,r,e) {
						   return function() { HUB.ProjectPublicationsDraft.checkElementCompleteness(v,r,e) }
						} ($(input).val(), required, $(item)), HUB.ProjectPublicationsDraft.doneTypingInterval);
					});
					$(input).on('keydown', function(e)
					{
						clearTimeout(HUB.ProjectPublicationsDraft.timer);
					});
				}

				// Textarea?
				var textarea = $(item).find('textarea');
				if (textarea.length)
				{
					$(textarea).on('keyup', function(e)
					{
						HUB.ProjectPublicationsDraft.timer = setTimeout(function(v,r,e) {
						   return function() { HUB.ProjectPublicationsDraft.checkElementCompleteness(v,r,e) }
						   } ($(textarea).val(), required, $(item)), HUB.ProjectPublicationsDraft.doneTypingInterval);
					});
					$(textarea).on('keydown', function(e)
					{
						clearTimeout(HUB.ProjectPublicationsDraft.timer);
					});
				}

				// CKEditor?
				if (editor && CKEDITOR)
				{
					var editorArea = $(item).find('textarea');
					var editorId = editorArea.attr('id');

					if (CKEDITOR.instances[editorId])
					{
						var timer = setInterval(function()
						{
							 var val = CKEDITOR.instances[editorId].getData();
							 HUB.ProjectPublicationsDraft.checkElementCompleteness(val, required, $(item))
						}, HUB.ProjectPublicationsDraft.doneTypingInterval);
					}
				}

			});
		}
	},

	getPubUrl: function(no_html, section)
	{
		var $ = this.jQuery;

		var projectid 	= $('#projectid').length ? $('#projectid').val() : 0;
		var pid 		= $('#pid').length ? $('#pid').val() : 0;
		var provisioned = $('#provisioned').length ? $('#provisioned').val() : 0;
		var section	 	= !section && $('#section').length ? $('#section').val() : section;
		var version	 	= $('#version').length ? $('#version').val() : '';
		var step	 	= $('#step').length ? $('#step').val() : 0;

		if (provisioned == 1)
		{
			var url = '/publications/submit/?pid=';
		}
		else {
			var url = '/projects/' + projectid + '/publications/';
		}
		url = url + pid + '/?version=' + version + '&p=' + section + '-' + step;

		if (no_html == 1)
		{
			url = url + '&no_html=1&ajax=1';
		}
		return url;
	},

	getProjectUrl: function(no_html)
	{
		var $ = this.jQuery;

		var projectid 	= $('#projectid').length ? $('#projectid').val() : 0;
		var pid 		= $('#pid').length ? $('#pid').val() : 0;
		var provisioned = $('#provisioned').length ? $('#provisioned').val() : 0;

		if (provisioned == 1)
		{
			var url = '/publications/submit/?pid=' + pid;
		}
		else
		{
			var url = '/projects/' + projectid + '/publications/?pid=' +  pid;
		}

		if (no_html == 1)
		{
			url = url + '&no_html=1&ajax=1';
		}
		return url;
	},

	enableBlock: function()
	{
		var $ = this.jQuery;

		// Which section is active?
		var section = '';
		if ($('#section').length)
		{
			section = $('#section').val();
		}

		if (section == 'authors')
		{
			HUB.ProjectPublicationsDraft.panelAuthors();
		}
		if (section == 'status')
		{
			HUB.ProjectPublicationsDraft.panelStatus();
		}
		if (section == 'tags')
		{
			HUB.ProjectPublicationsDraft.panelTags();
		}
		if (section == 'license')
		{
			HUB.ProjectPublicationsDraft.panelLicense();
		}
		if (section == 'review')
		{
			HUB.ProjectPublicationsDraft.panelReview();
		}
	},

	panelReview: function()
	{
		var $ = this.jQuery;
		if ($('#publish_date').length > 0) {
			$( "#publish_date" ).datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				maxDate: '+10Y'
			});
		}
	},

	reorder: function(list)
	{
		var $ = this.jQuery;

		if ($('.reorder').length == 0 || $(list).length == 0 || $(list).hasClass('noedit'))
		{
			return false;
		}

		// Drag items
		$(list).sortable(
		{
			items: "> li.reorder",
			update: function()
			{
			    HUB.ProjectPublicationsDraft.displayOrdering();
				HUB.ProjectPublicationsDraft.saveOrder();
		   	}
		});
	},

	saveOrder: function()
	{
		var $ = this.jQuery;
		var section = $('#section').length ? $('#section').val() : '';

		if (!section)
		{
			return false;
		}

		// Collect items in new order
		var items = HUB.ProjectPublicationsDraft.gatherSelections('pick-');

		// Send AJAX request to save new order
		var url = HUB.ProjectPublicationsDraft.getPubUrl(1, section);
		url = url + '&action=reorder&list=' + items;
		$.post(url, {},
			function (response) {

			response = $.parseJSON(response);

			if (response.error)
			{
				$('#status-msg').html('<p>' + response.error + '</p>');
				$('#status-msg').css({'opacity':100});
			}
			else if (response.message)
			{
				$('#status-msg').html('<p>' + response.message + '</p>');
				$('#status-msg').css({'opacity':100});
			}

		});

	},

	displayOrdering: function()
	{
		var $ = this.jQuery;
		var nums = $('.item-order');
		var o	 = 1;

		if (nums.length > 0)
		{
			nums.each(function(i, item)
			{
				$(item).html(o);
				o++;
			});
		}
	},

	gatherSelections: function(replacement)
	{
		var $ = this.jQuery;
		var items = $('.pick');
		var selections = '';

		if(items.length > 0) {
			items.each(function(i, item)
			{
				var id = $(item).attr('id');

				if (replacement)
				{
					id = id.replace(replacement, '');
				}

				if (id != '' && id != ' ')
				{
					selections = selections + id + '-' ;
				}
			});
		}
		return selections;
	},

	// AUTHORS
	panelAuthors: function()
	{
		var $ = this.jQuery;

		// Enable reordering
		HUB.ProjectPublicationsDraft.reorder($('#author-list'));

	},

	// TAGS
	panelTags: function()
	{
		var $ = this.jQuery;

		var input = $('#actags');
		var element = $('#tagsPick');

		if (!element.length || !input.length)
		{
			return false;
		}

		var required = $(element).hasClass('el-required') ? 1 : 0;

		// Timed checker
		var timer = setInterval(function()
		{
			 var tags = HUB.ProjectPublicationsDraft.getSelectedTags();
			 HUB.ProjectPublicationsDraft.checkElementCompleteness(tags, required, $(element))
		}, HUB.ProjectPublicationsDraft.doneTypingInterval);

	},

	getSelectedTags: function()
	{
		var $ = this.jQuery;

		// Get selected tags
		var selected = '';
		var tags = $('.token-input-token-act p');

		if (tags.length > 0)
		{
			tags.each(function(i, item)
			{
				selected = selected ? selected + ',' + $(item).html() : $(item).html();
			});
		}

		return selected;

		//$('#actags').val(selected);
	},

	// License
	panelLicense: function()
	{
		var $ = this.jQuery;

		var ltext 	= $('#license-text');
		var agree 	= $('#agreement');
		var element = $('#licensePick');

		if (!element.length)
		{
			return false;
		}

		if (ltext.length)
		{
			ltext.unbind();
			ltext.on('keyup', function(e)
			{
				HUB.ProjectPublicationsDraft.checkLicense();
			});
		}

		if (agree.length) {
			agree.unbind();
			agree.on('click', function(e)
			{
				HUB.ProjectPublicationsDraft.checkLicense();
			});
		}

	},

	// License
	checkLicense: function()
	{
		var $ = this.jQuery;

		var complete 	= 1;
		var ltext 		= $('#license-text');
		var agree 		= $('#agreement');
		var element 	= $('#licensePick');
		var required 	= $(element).hasClass('el-required') ? 1 : 0;

		// Check for default text
		if (ltext.length && !HUB.ProjectPublicationsDraft(ltext.val()))
		{
			complete = 0;
		}
		if (agree.length && agree.attr('checked') != 'checked')
		{
			complete = 0;
		}

		// Enable/disable control buttons
		HUB.ProjectPublicationsDraft.enableNav(complete);

		if (complete)
		{
			HUB.ProjectPublicationsDraft.changeElementStatus(element, 'complete', required);
		}
		else
		{
			HUB.ProjectPublicationsDraft.changeElementStatus(element, 'incomplete', required);
			$(element).addClass('el-partial');
		}
	},

	checkLicenseText: function(text)
	{
		var $ = this.jQuery;
		if(text == '') {
			return false;
		}
		var defaults = [
			'YEAR',
			'OWNER',
			'ORGANIZATION',
			'ONE LINE DESCRIPTION',
			'URL'
		];

		var matches = /YEAR/;
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /OWNER/;
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /ORGANIZATION/;
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /URL/;
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /ONE LINE DESCRIPTION/;
		if(text.match(matches) != null) {
			return false;
		}

		return true;
	},

	// STATUS
	panelStatus: function()
	{
		var $ = this.jQuery;
		var vlabel = $('#edit-vlabel');

		if (vlabel.length)
		{
			// Edit version label (dev)
			vlabel.on('click', function(e)
			{
				var original = vlabel.html();
				HUB.ProjectPublicationsDraft.addEditForm (vlabel, original);
				if ($('#v-label') && !$('#v-label').hasClass('hidden'))
				{
					$('#v-label').addClass('hidden');
				}
			});
		}
	},

	addEditForm: function (el, original)
	{
		var $ = this.jQuery;

		if ($('#editv').length > 0) {
			return;
		}

		$(el).addClass('hidden');

		// Add form
		$(el).parent().append('<label id="editv">' +
			'<input type="text" name="label" value="' + original + '" maxlength="10" class="vlabel" />' +
			'<input type="submit" value="save" class="btn btn-secondary" />' +
			'<input type="button" value="cancel" class="cancel" id="cancel-rename" />' +
		'</label>');

		$('#cancel-rename').on('click', function(e){
			e.preventDefault();
			$('#editv').remove();
			$(el).removeClass('hidden');
			if($('#v-label') && $('#v-label').hasClass('hidden')) {
				$('#v-label').removeClass('hidden');
			}
		});
	}
}

/* Initialize from within view - needs to load after the rest */
/*
jQuery(document).ready(function($){
	HUB.ProjectPublicationsDraft.initialize();
});
*/