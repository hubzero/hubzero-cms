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
// Project Micro Blog JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectMicroblog = {

	jQuery: jq,

	initialize: function()
	{
		var $ = this.jQuery;

		// Showing comment area
		$('.showc').each(function(i, item)
		{
			$(item).on('click', function(e)
			{
				e.preventDefault();
				var id = $(item).attr('id').replace('addc_', '');
				var acid = '#commentform_' + id;
				var scrollTo = $(acid).offset().top - 100;

				if ($(acid) && $(acid).hasClass('hidden'))
				{
					$(acid).removeClass('hidden');

					$('html, body').animate({
						scrollTop: scrollTo
					}, 1000);
					var txt = $(acid).find('.commentarea')[0];
					HUB.ProjectMicroblog.focusTextArea(txt);
				}
				else if ($(acid) && !$(acid).hasClass('hidden'))
				{
					$('html, body').animate({
						scrollTop: scrollTo
					}, 1000);
				}
				$(txt).focus();
			});
		});

		// Show more
		$('.more-content').each(function(i, el)
		{
			$(el).on('click', function(e)
			{
				e.preventDefault();
				var shortBody = $(el).parent().parent().find("span.body");
				var longBody  = $(el).parent().parent().find("span.fullbody");

				$(shortBody).addClass('hidden');
				$(longBody).removeClass('hidden');
			});
		});

		// Comment form
		$('.commentarea').each(function(i, item)
		{
			// Submit comments on enter
			$(item).bind("enterKey",function(e)
			{
				var form = $(item).parent().parent().parent().parent().find("form");

				// Submit if not empty
				if ($(item).val() != '')
				{
					$(form).submit();
				}
			});

			$(item).on('keypress', function(e)
			{
				if (e.keyCode == 13)
			    {
			        e.preventDefault();
			    }
			});

			$(item).on('keyup', function(e)
			{
				if (e.keyCode == 13)
			    {
			        $(this).trigger("enterKey");
			    }
			});

			if ($(item).val() == '')
			{
				HUB.ProjectMicroblog.unfocusTextArea(this);
			}
			$(item).on('focus', function(e)
			{
				HUB.ProjectMicroblog.focusTextArea(this);
			});
		});

		// Blog entry form
		if ($('#blogentry'))
		{
			if ($('#blogentry').val() == '')
			{
				HUB.ProjectMicroblog.unfocusTextArea($('#blogentry'));
				$('#blog-submit').addClass('hidden');
				$('#blog-submitarea').css('height', '0');
			}

			$('#blogentry').on('focus', function(e)
			{
				HUB.ProjectMicroblog.focusTextArea($('#blogentry'));
				$('#blog-submit').removeClass('hidden');
				$('#blog-submitarea').css('height', '20px');
			});

			// On click outside
			if ($('#blog-submitarea')) {
				$('#blog-submitarea').on('click', function(e) {
						HUB.ProjectMicroblog.unfocusTextArea($('#blogentry'));
						$('#blog-submit').addClass('hidden');
						$('#blog-submitarea').css('height', '0');
				});
			}
		}

		// Do not allow to post empty values
		if ($('#blog-submit')) {
			$('#blog-submit').on('click', function(e){
				if ($('#blogentry').val() == '') {
					e.preventDefault();
					HUB.ProjectMicroblog.unfocusTextArea($('#blogentry'));
					$('#blog-submit').addClass('hidden');
					$('#blog-submitarea').css('height', '0');
				}
			});
		}
		$('.c-submit').each(function(index, item) {
			$(item).addClass('hidden');
			$(item).on('click', function(e){

				var cid = $(this).attr('id').replace('cs_', '');
				var caid = '#ca_' + cid;
				var acid = '#commentform_' + cid;
				if ($(caid)) {
					if ($(caid).val() == '') {
						e.preventDefault();
						$(acid).addClass('hidden');
					}
				}
			});
		});

		// Confirm delete
		$('.delit').each(function(i, el) {
			var link = $(el).find("a");
			$(link).on('click', function(e) {
				e.preventDefault();
				if (HUB.Projects) {
					HUB.Projects.addConfirm($(link), 'Permanently delete this entry?', 'yes, delete', 'cancel');
					if ($('#confirm-box')) {
						$('#confirm-box').css('margin-left', '-100px');
					}
				}
			});
		});

		// Show more updates
		if ($('#more-updates') && $('#pid')) {
			$('#more-updates').on('click', function(e) {
				e.preventDefault();

				var link = $('#more-updates').find("a");

				if (link.length)
				{
					var url = link.attr('href') + '&no_html=1&ajax=1&action=update';

					$.get(url, {}, function(data) {
						$('#latest_activity').html(data);
						HUB.ProjectMicroblog.initialize();
					});
				}
			});
		}
	},

	focusTextArea: function(item)
	{
		var $ = this.jQuery;

		$(item).css('color', '#000')
			   .css('height', '100px')
			   .css('border', '2px solid #6bb7d6');
	},

	unfocusTextArea: function(item)
	{
		var $ = this.jQuery;

		$(item).css('color', '#999')
			   .css('height', '35px')
			   .css('border', '1px solid #CCC');
	}
}

jQuery(document).ready(function($){
	HUB.ProjectMicroblog.initialize();
});