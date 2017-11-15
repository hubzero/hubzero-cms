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

		// Confirm delete
		if ($('#td-item')) {
			$('.confirm-it').each(function(i, item) {
				$(item).on('click', function(e) {
					e.preventDefault();
					HUB.Projects.addConfirm($(item),
					'Delete this item?',
					'Yes, delete', 'No, do not delete');
				});
			});
		}

		// Confirm check-off
		if ($('.confirm-checkoff').length) {
			$('.confirm-checkoff').each(function(i, item) {
				$(item).on('click', function(e) {
					e.preventDefault();
					HUB.Projects.addConfirm($(item),
					'Mark this item as complete?',
					'Yes, check off', 'No, leave as is');
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
		HUB.ProjectTodo.addDragging();

		// Comment form
		HUB.ProjectTodo.styleCommentForm();
	},

	addDragging: function()
	{
		var $ = this.jQuery;

		// Fix up instructions
		$('#td-instruct').html('Re-arrange items by dragging');

		// Sort pinboard items
		if ($( "#pinboard" ).length && $( "#pinboard" ).hasClass('allow-sort'))
		{
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

					var action = '/projects/'+ $('#pid').val()
					+ '/todo/sortitems?ajax=1&no_html=1&list='+ $('#list').val() + stringDiv;

					$.get( action, {}, function(data) {});
				}
			});
		}

		// Sort table rows
		if ($( "#todo-table-body" ).length && $( "#todo-table-body" ).hasClass('allow-sort')) {
			$( "#todo-table-body" ).sortable(
			{
			   	update: function()
				{
				    var stringDiv = "";
		            $("#todo-table-body").children().each(function(i) {
		                var li = $(this);
						if (li.attr("id"))
						{
							var add = li.attr("id").replace('todo-', '');
							stringDiv = stringDiv + '&item[' + i + ']=' + add;
						}
		            });

					if (!stringDiv)
					{
						return false;
					}

					var action = 'index.php?option=com_projects&id='+ $('#pid').val()
					+ '&active=todo&action=sortitems&ajax=1&no_html=1&list='+ $('#list').val() + stringDiv;

					$.get( action, {}, function(data) {
					});
					HUB.ProjectTodo.displayOrdering();
			   	}
			});
		}
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

	displayOrdering: function()
	{
		var nums = $('.ordernum');
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

	styleCommentForm: function ()
	{
		var $ = this.jQuery;

		$('.commentarea').each(function(i, item) {
			$(item).on('keyup', function(e) {
				//HUB.Projects.setCounter(this);
			});

			if ($(item).val()=='') {
				$(item).css('color', '#999')
					.css('height', '60px');
			}
			$(item).on('focus', function(e)
			{
				$(item).css('color', '#000')
					.css('height', '150px');
			});
		});

		// Do not allow to post default values
		if ($('#c-submit')) {
			$('#c-submit').on('click', function(e){
				if ($('#td-comment').val() == '') {
					e.preventDefault();
					$('#td-comment').css('color', '#999')
						.css('height', '40px');
				}
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