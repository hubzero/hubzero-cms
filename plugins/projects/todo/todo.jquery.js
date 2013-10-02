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
// Project To-do JS
//----------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.ProjectTodo = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		// Fix up users with no JS
		if (HUB.Projects) 
		{
			HUB.Projects.fixJS();
		}
		
		var editid = 0;
		var edithtml = '';
		
		var frm = $('#plg-form');
		var todoid = $('#todoid');
		var pinboard = $('#pinboard');
		var container = $('#wrap');
		
		// Get IE version
		var IE = HUB.ProjectTodo.getInternetExplorerVersion();
				
		// Pick list
		HUB.ProjectTodo.pickList();
		
		// Sort list
		HUB.ProjectTodo.sortList();
		
		// Confirm delete
		if ($('#td-item')) {
			$('.confirm-it').each(function(i, item) {
				$(item).on('click', function(e) {	
					e.preventDefault();			
					HUB.Projects.addConfirm($(item), 
					'Permanently delete this item?', 
					'Yes, delete', 'No, do not delete');
				});
			});	
		}
		
		// Calendar (item page)
		if ($('#td-item') && $('#dued')) {
			$( "#dued" ).datepicker();
		}
		
		// Hide boxes to drag items to (doesn't yet work in jQuery)
		$('#todo-mine div').attr('class', 'hidden');
		$('#todo-completed div').attr('class', 'hidden');
				
		// Show delete list confirmation
		HUB.ProjectTodo.showDeleteListConfirm();
		
		// Drag items
		if ( $('#tdpage').val() == '' ) { 
			HUB.ProjectTodo.addDragging();
		}		
		
		// Comment form
		HUB.ProjectTodo.styleCommentForm();
	},
	
	addDragging: function()
	{
		var $ = this.jQuery;
		
		// Fix up instructions
		$('#td-instruct').html('Re-arrange items by dragging');
		
		// Drag items	 
		$( "#pinboard" ).sortable(
		{
		   	update: function() 
			{
			    var stringDiv = "";
	            $("#pinboard").children().each(function(i) {
	                var li = $(this);
					if(li.attr("id"))
					{
						var add = li.attr("id").replace('todo-', '');
						stringDiv = stringDiv + '&item[' + i + ']=' + add;
					}
	            });

				var action = 'index.php?option=com_projects&id='+ $('#pid').val() 
				+ '&active=todo&action=sortitems&ajax=1&no_html=1&list='+ $('#list').val() + stringDiv; 
				
				$.get( action, {}, function(data) {
					/*if(data)
					{
						$('#plg-content').html(data);
						HUB.ProjectTodo.initialize();	
					}*/
				}); 
		   	}
		});
	},
	
	showDeleteListConfirm: function()
	{
		var $ = this.jQuery;
		
		// Show list delete confirmation
		if ($('.dellist').length > 0) {	
			$('.dellist').each(function(i, item) {
				$(item).on('click', function(e) {
					e.preventDefault();
					var color = $(this).attr('id').replace('del-', '');
					var ops = $('#confirm-' + color);
					if (ops) {
						ops.css('display', 'block');
					}
					var cnl = $('#cnl-' + color);
					if (cnl) {
						cnl.on('click', function(ee) {
							ee.preventDefault();
							ops.css('display', 'none');
						});	
					}
				});	
			});	
		}
	},
	
	styleCommentForm: function ()
	{
		var $ = this.jQuery;

		$('.commentarea').each(function(i, item) {
			$(item).on('keyup', function(e) {
				HUB.Projects.setCounter(this);
			});

			if ($(item).val()=='') {
				$(item).css('color', '#999')
					.css('height', '20px');
			}
			$(item).on('focus', function(e) 
			{
				$(item).css('color', '#000')
					.css('height', '100px');
			});	
		});
		
		// Do not allow to post default values
		if ($('#c-submit')) {
			$('#c-submit').on('click', function(e){
				if ($('#td-comment').val() == '') {
					e.preventDefault();
					$('#td-comment').css('color', '#999')
						.css('height', '20px');
				}
			});	
		}
	},
	
	sortList: function ()
	{
		var $ = this.jQuery;
		// Sort list
		if ($('.sortoption').length > 0) {
			$('.sortoption').each(function(i, item) {
				$(item).on('change', function(e) {
					var action = 'index.php?option=com_projects&id='+ $('#pid').val() 
					+ '&task=view&list='+ $('#list').val() + '&sortby=' 
					+ $(this).val() + '&state=' 
					+ $('#tdstate').val() + '&active=todo&ajax=1&no_html=1';
					$.get(action, {}, function(data) {
						$('#plg-content').html(data);
						HUB.ProjectTodo.initialize();
					});
				});	
			});
		}
	},
	
	pickList: function ()
	{
		var $ = this.jQuery;
		
		// Pick list
		if ($('#pinselector')) {
			var keyupTimer = '';
			$('#pinselector').on('click', function(e){
				e.preventDefault();
				$('#pinoptions').css('display' , 'block');
				clearTimeout(keyupTimer);
				keyupTimer = setTimeout((function() { 
					$('#pinoptions').css('display' , 'none');
				}), 3000);
			});
			$('.listclicker').each(function(i, item) {
				$(item).on('click', function(e) {	
					$('#pinoptions').css('display', 'none');
					var css = $(item).val() ? 'pin_' + $(item).val() : 'pin_grey';
					$('#list').val($(item).val());
					if (css && !$('#pinner').hasClass(css)) {
						$('#pinner').attr('class', css);
					}
				});
			});
		}
	},
	
	getInternetExplorerVersion: function () 
	{
		var $ = this.jQuery;
		// Returns the version of Internet Explorer or a -1
		// (indicating the use of another browser).

		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) {
				rv = parseFloat( RegExp.$1 );
			}
		}
		return rv;
	}
}

jQuery(document).ready(function($){
	HUB.ProjectTodo.initialize();
});