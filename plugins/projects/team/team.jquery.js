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
// Project Team JS
//----------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.ProjectTeam = {
	jQuery: jq,
	bchecked: 0,
	bselected: new Array(),
	bgroups: new Array(),
	
	initialize: function() 
	{
		var $ = this.jQuery;
				
		// Show manage options
		if ($('#team-manage')) 
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
					var classes = $(item).attr('class').split(" ");
					for (k=classes.length-1;k>=0;k--) 
					{
						if (classes[k].search("group:") >= 0)
						{
							var group = classes[k].split(":")[1];	
						}
					}
					
					// Is item checked?
					if ($(item).attr('checked') != 'checked') 
					{
					 	bchecked = bchecked - 1;
						//var idx = bselected.indexOf($(item).val());
						var idx = HUB.Projects.getArrayIndex($(item).val(), bselected);
						if (idx!=-1) bselected.splice(idx, 1);
						if (group)
						{
							//var gidx = bgroups.indexOf(group);
							var gidx = HUB.Projects.getArrayIndex(group, bgroups);
							if (gidx!=-1) 
							{
								HUB.ProjectTeam.selectGroup(group, 'uncheck');
								bgroups.splice(gidx, 1);
							}
						}
					} 
					else 
					{
						bchecked = bchecked + 1;
						bselected.push($(item).val());
						if (group)
						{
							HUB.ProjectTeam.selectGroup(group, 'check');
							bgroups.push(group);
						}
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
				if ($(item).attr('class').search('group:' + group) >= 0)
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
				$(item).addClass('inactive');
				$(item).on('click', function(e) {
					e.preventDefault();
					var aid = $(item).attr('id');
					
					if ($(item).hasClass('inactive')) 
					{
						// do nothing
					} 
					else 
					{
						// Clean up url
						var clean = $(item).attr('href').split('&owner[]=', 1);
						$(item).attr('href', clean);
					
						// Add our checked boxes variables
						if (bselected.length > 0) 
						{
							for (var k = 0; k < bselected.length; k++) 
							{
								$(item).attr('href', $(item).attr('href') + '&owner[]=' + bselected[k]);
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
					}
				});
			});
		}		
	},
	
	editRole: function (el)
	{
		var $ = this.jQuery;
		var classes = $(el).attr('class').split(" ");
		var owner = '';
		var role = '';

		for ( i=classes.length-1; i>=0; i-- ) {
			if (classes[i].search("owner:") >= 0)
			{
				owner = classes[i].split(":")[1];
			}
			if (classes[i].search("role:") >= 0)
			{
				role = classes[i].split(":")[1];
			}
		}
		
		var m_selected = role == 1 ? ' selected="selected"' : '';
		var c_selected = role == 0 ? ' selected="selected"' : '';
		
		// Hide your target element
		$(el).addClass('hidden');
		var form = 'form_' + $(el).attr('id');
		var save = 'save_' + $(el).attr('id');
		var cancel = 'cancel_' + $(el).attr('id');
				
		if ($(form) && $(form).hasClass('hidden'))
		{
			$(form).removeClass('hidden');
		}
		
		$(el).after('<form id="' + form + '" class="editable" action="index.php">' + 
			'<input type="hidden" name="option" value="com_projects" />' +
			'<input type="hidden" name="id" value="' + $('#pid').val() + '" />' +
			'<input type="hidden" name="task" value="view" />' +
			'<input type="hidden" name="active" value="team" />' +
			'<input type="hidden" name="action" value="assignrole" />' +
			'<label>' + 
				'<select name="role">' + 
					'<option value="1" ' + m_selected + '>manager</option>' + 
					'<option value="0" ' + c_selected + '>collaborator</option>' + 
				'<select>' + 
			'</label>' +
			'<input type="hidden" name="ajax" value="1" />' +
			'<input type="hidden" name="no_html" value="1" />' +
			'<input type="hidden" name="owner" value="' + owner + '" />' +
			'<input type="submit" class="btn btn-success active" id="' + save + '" value="save" />' +
			'<input type="button" class="btn btn-cancel" id="' + cancel + '" value="cancel" />' +
		'</form>');
				
		$('#' + cancel).on('click', function(e){
			e.preventDefault();
			$('#' + form).addClass('hidden');
			$(el).removeClass('hidden');
		});
		
		$('#' + save).on('click', function(e){
			e.preventDefault();
			$.ajax({ type:'POST', url: 'index.php', data:$('#' + form).serialize(), success: function(response) {
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
		else if (bchecked > 0 ) 
		{
			if ($('#t-delete') && $('#t-delete').hasClass('inactive')) {
				$('#t-delete').removeClass('inactive');
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectTeam.initialize();
});