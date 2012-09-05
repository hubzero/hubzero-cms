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
	
	addConfirms: function()
	{
		var $ = this.jQuery;
		// Confirm delete
		$('#suspend').on('click', function(e) 
		{
			e.preventDefault();			
			HUB.Projects.addConfirm($('#suspend'), 
			'Are you sure you want to suspend this project?', 
			'Yes, suspend', 'No, do not suspend');
		});

		// Confirm revert
		$('#confirm-revert').on('click', function(e) 
			{
				e.preventDefault();			
				HUB.Projects.addConfirm($('#confirm-revert'), 
				'Are you sure you want to revert this project to draft mode?', 
				'Yes, revert', 'No, keep as pending');
		});		
	},
	
	// Refresh counts of project activities and resources
	refreshCount: function(what) 
	{	
		var $ = this.jQuery;
		if ($('#projectid')) {
			var url = 'index.php?option=com_projects&task=showcount&no_html=1&pid=' + $('#projectid').val();
			
			if (what=='publications' && $('#c-publications') && $('#c-publications-num')) {
				$.get(url + '&what=publication', {}, function(data) {
					$('#c-publications-num').html(data);
				});
			}
			if (what=='files' && $('#c-files') && $('#c-files-num')) {
				$.get(url + '&what=files', {}, function(data) {
					$('#c-files-num').html(data);
				});
			}
			if (what=='team' && $('#c-team') && $('#c-team-num')) {
				$.get(url + '&what=team', {}, function(data) {
					$('#c-team-num').html(data);
				});
			}
			if (what=='todo' && $('#c-todo') && $('#c-todo-num')) {
				$.get(url + '&what=todo', {}, function(data) {
					$('#c-todo-num').html(data);
				});
			}
			if (what=='newactivity' && $('#c-new') && $('#c-new-num')) {
				$.get(url + '&what=newactivity', {}, function(data) {
					$('#c-new-num').html(data);
				});
			}
		}
	},
	
	// Launch SqueezeBox with Ajax actions	
	launchBox: function() 
	{
		var $ = this.jQuery;
		$('.showinbox').each(function(i, item) {
			// Clean up
			$(item).off('click');
			
			var href = $(item).attr('href');
			if (href.search('&no_html=1') == -1) {
				href = href + '&no_html=1';
			}
			if (href.search('&ajax=1') == -1) {
				href = href + '&ajax=1';
			}
			$(item).attr('href', href);			
			
			// Open box on click
			$(item).on('click', function(e) {
				e.preventDefault();

				if (!$(this).hasClass('inactive')) {
					// Modal box for actions
					$.fancybox(this,{
						type: 'ajax',
						width: 600,
						height: 500,
						autoSize: false,
						fitToView: false,
						wrapCSS: 'sbp-window',
						afterShow: function() {
							if ($('#cancel-action')) {
								$('#cancel-action').on('click', function(e) {
									$.fancybox.close();
								});
							}
							if ($('#ajax-selections') && $('#section')) {
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
		if ($('#grant_approval') && $('#rejected')) {
			$('#grant_approval').on('keyup', function(e) {
				if ($('#grant_approval').val() != '') {
					$('#rejected').attr('checked', 'checked');
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
		
		$('html, body').animate({
			scrollTop: $(link).offset().top
		}, 2000);
		
		$('#confirm-box').css('left', coord.left).css('top', coord.top + 200);
	}
}		
	
jQuery(document).ready(function($){
	HUB.Projects.initialize();
});