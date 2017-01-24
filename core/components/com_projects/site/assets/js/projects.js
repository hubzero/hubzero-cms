/**
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Project Component JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Projects = {
	jQuery: jq,

	initialize: function()
	{
		// Fix up users with no JS
		HUB.Projects.fixJS();

		// Activate boxed content
		HUB.Projects.launchBox();

		// Add confirms
		HUB.Projects.addConfirms();

		// Reviewers
		HUB.Projects.addFiltering();

		// Fade-out status message
		HUB.Projects.addMessageFade();
	},

	addFiltering: function()
	{
		var $ = this.jQuery;
		// Browse projects - filtering
		$(".filterby").each(function(i, item) {
			$(item).on('change', function(e) {
				if ($('#browseForm')) {
					$('#browseForm').submit();
				}
			});
		});
	},

	addMessageFade: function()
	{
		var $ = this.jQuery;

		if ($("#status-msg").length > 0)
		{
			var keyupTimer = setTimeout((function()
			{
				if (!$("#status-msg").hasClass('ajax-loading') && $('#status-msg').html().trim() != '')
				{
					$("#status-msg").animate({opacity:0.0}, 2000, function() {
					    $('#status-msg').html('');
					});
				}
			}), 4000);
		}
	},

	addConfirms: function()
	{
		var $ = this.jQuery;

		// Confirm project delete delete
		if ($('#delproject').length)
		{
			$('#delproject').on('click', function(e)
			{
				e.preventDefault();
				HUB.Projects.addConfirm($('#delproject'),
				'Are you sure you want to delete this project? <br />This is a permanent action and cannot be undone.',
				'Yes, delete', 'No, do not delete');
			});
		}

		// Confirm revert
		if ($('#confirm-revert').length)
		{
			$('#confirm-revert').on('click', function(e)
				{
					e.preventDefault();
					HUB.Projects.addConfirm($('#confirm-revert'),
					'Are you sure you want to revert this publication to draft mode?',
					'Yes, revert', 'No, keep as pending');
			});
		}
	},

	// Launch SqueezeBox with Ajax actions
	launchBox: function()
	{
		var $ = this.jQuery;
		var bWidth 	= 600;
		var bHeight = 500;
		var css 	= 'sbp-window';
		var cBtn	= 1;

		$('.showinbox').each(function(i, item) {
			// Clean up
			$(item).off('click');

			var href = $(item).attr('href');
			if (href.search('no_html=1') == -1)
			{
				if (href.indexOf('?') == -1)
				{
					href = href + '?no_html=1';
				}
				else
				{
					href = href + '&no_html=1';
				}
			}
			if (href.search('&ajax=1') == -1) {
				href = href + '&ajax=1';
			}
			$(item).attr('href', href);

			// TEX compiler view
			if ($(item).hasClass('tex-menu') || $(item).hasClass('box-expanded'))
			{
				bWidth = 800;
			}

			// Open box on click
			$(item).on('click', function(e) {
				e.preventDefault();

				// New publication process: fileselector
				if ($(this).hasClass('item-add'))
				{
					bWidth = 700;
				}
				if ($(this).hasClass('nox'))
				{
					cBtn = 0;
				}

				if (!$(this).hasClass('inactive')) {
					// Modal box for actions
					$.fancybox(this,{
						type: 'ajax',
						width: bWidth,
						height: 'auto',
						autoSize: false,
						fitToView: false,
						wrapCSS: css,
						closeBtn: cBtn,
						afterShow: function() {
							jQuery(document).trigger('afterShowinboxLoad');

							if ($('#cancel-action').length) {
								$('#cancel-action').on('click', function(e) {
									$.fancybox.close();
								});
							}

							// Publication process
							if ($('#ajax-selections').length && $('#section').length) {
								if (HUB.ProjectPublications) {
									var replacement = '';
									if ($('#section').val() == 'gallery' || $('#section').val() == 'content') {
										replacement = 'clone-';
									} else {
										replacement = 'clone-author::';
									}
									var selections = HUB.ProjectPublications.gatherSelections(replacement);
									$('#ajax-selections').val(selections);
								}
							}
							if (HUB.ProjectTodo)
							{
								HUB.ProjectTodo.initialize();
							}
							// Reviewers
							HUB.Projects.resetApproval();
						}
					});
				}
			});
		});
	},

	resetApproval: function()
	{
		var $ = this.jQuery;

		if ($('#grant_approval').length && $('#rejected').length) {
			$('#grant_approval').on('keyup', function(e) {
				if ($('#grant_approval').val() == '') {
					$('#rejected').attr('checked', true);
				}
				else
				{
					$('#rejected').attr('checked', false);
				}
			});
			$('#rejected').on('click', function(e) {
				if ($('#rejected').attr('checked') != 'undefined' && $('#rejected').attr('checked') == 'checked') {
					$('#grant_approval').val('');
				}
			});
		}
	},

	setCounter: function(el, numel)
	{
		var $ = this.jQuery;
		var maxchars = 250,
			current_length = $(el).val().length,
			remaining_chars = maxchars-current_length;

		if (remaining_chars < 0) {
			remaining_chars = 0;
		}

		if ($(numel).length) {
			if (remaining_chars <= 10) {
				$(numel).css('color', '#ff0000').html(remaining_chars + ' chars remaining');
			} else {
				$(numel).css('color', '#999999').html('');
			}
		}

		if (remaining_chars == 0) {
			$(el).val($(el).val().substr(0, maxchars));
		}
	},

	cleanupText: function(text)
	{
		// Clean up entered value
		var cleaned = text.toLowerCase()
						  .replace('_', '')
						  .replace('-', '')
						  .replace(' ', '')
						  .replace(/[|&;$%@"<>()+,#!?.~*^=-_]/g, '');
		return cleaned;
	},

	fixJS: function()
	{
		var $ = this.jQuery;
		var js_off = $(".nojs"),
			js_on = $(".js");

		// Hide all no-js messages
		if (js_off.length)
		{
			js_off.each(function(i, item) {
				$(item).css('display', 'none');
			});
		}

		// Show all js-only options
		if (js_on.length)
		{
			js_on.each(function(i, item) {
				$(item).removeClass('js');
			});
		}
	},

	addConfirm: function (link, question, yesanswer, noanswer)
	{
		var $ = this.jQuery;
		if ($('#confirm-box')) {
			$('#confirm-box').remove();
		}

		var href = $(link).attr('href');

		// Add confirmation
		var ancestor = $(link).parent().parent();
		$(ancestor).after('<div class="confirmaction" id="confirm-box" style="display:block;">' +
			'<p>' + question + '</p>' +
			'<p>' +
				'<a href="' + href + '" class="confirm">' + yesanswer + '</a>' +
				'<a href="#" class="cancel" id="confirm-box-cancel">' + noanswer + '</a>' +
			'</p>' +
		'</div>');

		$('#confirm-box-cancel').on('click', function(e){
			e.preventDefault();
			$('#confirm-box').remove();
		});

		// Move close to item
		var coord = $($(link).parent()).position();

		/*
		$('html, body').animate({
			scrollTop: $(link).offset().top
		}, 2000); */

		$('#confirm-box').css('left', coord.left).css('top', coord.top + 200);
	},

	loadingIma: function(txt)
	{
		var html = '<span id="fbwrap">' +
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ';

		if (txt)
		{
			html = html + txt;
		}

		html = html +
			'</span>' +
		'</span>';

		return html;
	},

	setStatusMessage: function (txt, loading)
	{
		var $ = this.jQuery;

		var log = $('#status-msg').empty();

		if (loading == 1)
		{
			$('#status-msg').addClass('ajax-loading');

			if (!txt)
			{
				txt = 'Please wait while we are performing your request...';
			}

			var html = HUB.Projects.loadingIma(txt);
		}
		else
		{
			var html = txt;
		}

		// Add element
		if (txt)
		{
			$('#status-msg').html(html);
			$('#status-msg').css({'opacity':100});
		}
		else
		{
			$('#status-msg').css('opacity', 0);
			return;
		}
	},

	getArrayIndex: function (obj, arr)
	{
		if (!Array.indexOf)
		{
			// Fix for indexOf in IE browsers
			for (var i = 0, j = arr.length; i < j; i++)
			{
			   if (arr[i] === obj) { return i; }
			}
			return -1;
		}
		else
		{
			return arr.indexOf(obj);
		}
	}
}

jQuery(document).ready(function($){
	var el = $('.projecttools');

	if (el.length) {
		el.addClass('jsm');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : 'Select ...' //el.attr('data-label')
		}).appendTo(select);

		el.find("a").each(function() {
			var elm = $(this);

			if (elm.hasClass('alrt')) {
				return;
			}

			var opts = {
				"value"   : elm.attr("href"),
				"text"    : elm.text()
			};
			if ($(elm.parent()).hasClass('active')) {
				opts.selected = 'selected';
			}
			$("<option />", opts).appendTo(select);
		});

		var li = $("<li />").addClass('option-select');

		select.appendTo(li);
		li.appendTo(el[0]);
	}

	$('.entries-filters select').on('change', function(e){
		$(this).closest('form').submit();
	});

	HUB.Projects.initialize();
});