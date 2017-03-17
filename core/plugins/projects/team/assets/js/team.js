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
// Project Team JS
//----------------------------------------------------------

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

HUB.ProjectTeam = {
	jQuery: jq,
	bchecked: 0,
	bselected: new Array(),
	bgroups: new Array(),

	initialize: function()
	{
		var $ = this.jQuery;

		// Show manage options
		if ($('#team-manage').length > 0)
		{
			if ($('#team-manage').hasClass('hidden'))
			{
				$('#team-manage').removeClass('hidden');
			}
		}

		// Edit role inline
		if ($('.frole').length > 0)
		{
			$('.frole').each(function(i, item)
			{
				$(item).on('click', function(e)
				{
					HUB.ProjectTeam.editRole(item);
				});
			});
		}

		// Check boxes for team members
		HUB.ProjectTeam.checkMembers();

		// Activate team management buttons
		HUB.ProjectTeam.activateOptions();

	}, // end initialize

	checkMembers: function ()
	{
		var $ = this.jQuery;
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bgroups = this.bgroups;
		var group = '';

		var boxes = $('.checkmember');

		// Check boxes for team members
		if (boxes.length > 0)
		{
			boxes.each(function(i, item)
			{
				$(item).on('click', function(e)
				{
					var group = $(this).attr('data-group');

					// Is item checked?
					//if ($(item).attr('checked') != 'checked')
					if ($(item).prop('checked') != true)
					{
						bchecked = bchecked - 1;
						//var idx = bselected.indexOf($(item).val());
						var idx = HUB.Projects.getArrayIndex($(item).val(), bselected);
						if (idx!=-1)
						{
							bselected.splice(idx, 1);
						}
						/*if (group)
						{
							//var gidx = bgroups.indexOf(group);
							var gidx = HUB.Projects.getArrayIndex(group, bgroups);
							if (gidx!=-1)
							{
								HUB.ProjectTeam.selectGroup(group, 'uncheck');
								bgroups.splice(gidx, 1);
							}
						}*/
					}
					else
					{
						bchecked = bchecked + 1;
						bselected.push($(item).val());
						/*if (group)
						{
							HUB.ProjectTeam.selectGroup(group, 'check');
							bgroups.push(group);
						}*/
					}

					HUB.ProjectTeam.watchSelections(bchecked);
				});
			});
		}
	},

	selectGroup: function (group, action)
	{
		var $ = this.jQuery;
		var boxes = $('.checkmember');

		if (boxes.length > 0)
		{
			boxes.each(function(i, item)
			{
				if ($(item).attr('data-group') == group)
				{
					if (action == 'check')
					{
						$(item).attr('checked','checked');
					}
					else
					{
						$(item).removeAttr("checked");
					}
				}
			});
		}
	},

	activateOptions: function ()
	{
		var $ = this.jQuery;
		var bchecked = this.bchecked;
		var bselected = this.bselected;
		var bgroups = this.bgroups;

		var ops = $('.manage');
		if (ops.length > 0)
		{
			// Control options
			ops.each(function(i, item)
			{
				// disable options until a box is checked
				$(item)
					.addClass('inactive')
					.on('click', function(e) {
						e.preventDefault();
						var aid = $(item).attr('id');

						if ($(item).hasClass('inactive'))
						{
							// do nothing
							return;
						}

						// Clean up url
						var clean = $(item).attr('href').split('&owner[]=', 1);
						$(item).attr('href', clean);

						// Add our checked boxes variables
						if (bselected.length > 0)
						{
							for (var k = 0; k < bselected.length; k++)
							{
								// Prevents a double ? in the URL building
								if (k == 0)
								{
									$(item).attr('href', $(item).attr('href') + '?owner[]=' + bselected[k]);
								}
								else
								{
									$(item).attr('href', $(item).attr('href') + '&owner[]=' + bselected[k]);
								}

							}
						}
						// Add our selected groups variables
						if (bgroups.length > 0)
						{
							for (var k = 0; k < bgroups.length; k++)
							{
								$(item).attr('href', $(item).attr('href') + '&group[]=' + bgroups[k]);
							}
						}

						var href = $(item).attr('href');
						if (href.search('&no_html=1') == -1) {
							href = href + '&no_html=1';
						}
						if (href.search('&ajax=1') == -1) {
							href = href + '&ajax=1';
						}
						$(item).attr('href', href);

						// make AJAX call
						$.fancybox(this,{
							type: 'ajax',
							width: 600,
							height: 'auto',
							autoSize: false,
							fitToView: false,
							wrapCSS: 'sbp-window',
							afterShow: function() {
								if ($('#cancel-action')) {
									$('#cancel-action').on('click', function(e) {
										$.fancybox.close();
									});
								}
							}
						});
					});
			});
		}
	},

	editRole: function (el)
	{
		var $ = this.jQuery;
		var owner = $(el).attr('data-owner');
		var role  = $(el).attr('data-role');

		var m_selected = role == 1 ? ' selected="selected"' : '';
		var c_selected = (role == 0 || role == 2 || role == 3) ? ' selected="selected"' : '';
		var r_selected = role == 5 ? ' selected="selected"' : '';

		// Hide your target element
		$(el).addClass('hidden');
		var form   = 'form_' + $(el).attr('id');
		var save   = 'save_' + $(el).attr('id');
		var cancel = 'cancel_' + $(el).attr('id');

		if ($(form) && $(form).hasClass('hidden'))
		{
			$(form).removeClass('hidden');
		}

		$(el).after(
			'<form id="' + form + '" class="editable" action="index.php">' +
				'<label>' +
					'<select name="role">' +
						'<option value="1" ' + m_selected + '>manager</option>' +
						'<option value="0" ' + c_selected + '>collaborator</option>' +
						'<option value="5" ' + r_selected + '>reviewer (read-only)</option>' +
					'<select>' +
				'</label>' +
				'<input type="hidden" name="ajax" value="1" />' +
				'<input type="hidden" name="no_html" value="1" />' +
				'<input type="hidden" name="owner" value="' + owner + '" />' +
				'<input type="submit" class="btn btn-success active" id="' + save + '" value="save" />' +
				'<input type="button" class="btn btn-cancel" id="' + cancel + '" value="cancel" />' +
			'</form>'
		);

		$('#' + cancel).on('click', function(e){
			e.preventDefault();
			$('#' + form).addClass('hidden');
			$(el).removeClass('hidden');
		});
		var formAction = '/projects/' + $('#pid').val() + '/team/assignrole/?1=1';

		$('#' + save).on('click', function(e){
			e.preventDefault();
			$.ajax({ type:'POST', url: formAction, data:$('#' + form).serialize(), success: function(response) {
				$('#cbody').html(response);
				HUB.ProjectTeam.initialize();
			}});
		});
	},

	watchSelections: function ( bchecked )
	{
		var $ = this.jQuery;

		// Get number of members
		var num = $('#n_members').val();

		if (bchecked == 0)
		{
			$('.manage').each(function(i, el) {
				if (!$(el).hasClass('inactive')) {
					$(el).addClass('inactive');
				}
			});
		}
		else if (bchecked > 0)
		{
			if ($('#t-delete').length && $('#t-delete').hasClass('inactive')) {
				$('#t-delete').removeClass('inactive');
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectTeam.initialize();

	var go = $('.group-options');
	if (go.length) {
		go.on('click', 'input[type=radio]', function(e) {
			$(this).closest('form').submit();
		});
	}

	$('#choosemember').fancybox({
		type: 'ajax',
		width: 600,
		height: 600,
		autoSize: false,
		fitToView: false,
		titleShow: false,
		wrapCSS: 'sbp-window',
		beforeLoad: function() {
			href = $(this).attr('href');
			$(this).attr('href', href.nohtml());
		},
		afterShow: function() {
			if ($('#cancel-action').length) {
				$('#cancel-action').on('click', function(e) {
					$.fancybox.close();
				});
			}
		}
	});
});
