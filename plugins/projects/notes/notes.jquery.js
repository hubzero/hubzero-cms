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
// Project Notes JS
//----------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.ProjectNotes = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		var frm = $('#hubForm');
		var notetitle = $('#title');
		var default_title = 'New Note';
		var default_temp_title = 'Template: New';
		
		// Check that title is there
		if (notetitle) {
			HUB.ProjectNotes.checkTitle(notetitle, default_title, default_temp_title);
			notetitle.on('keyup', function(e) {
				HUB.ProjectNotes.checkTitle(notetitle, default_title, default_temp_title);
			});
		}
		
		// Prevent form submission if no title
		if ($('#hubForm') && notetitle && $('#page-submit')) {
			$('#page-submit').on('click', function(e) {							
				if (notetitle.val() == '' || notetitle.val() == default_title || notetitle.val() == default_temp_title ) {
					// can't submit
					e.preventDefault();
				}
			});
		}
		
		// Remove file uploader button
		if ($('#file-uploader').length > 0)
		{
			$('#file-uploader').addClass('hidden');	
		}
		if ($('#file-uploader-list').length > 0)
		{
			$('#file-uploader-list').remove();	
		}		
	},
	
	checkTitle: function(title, default_title, default_temp_title) 
	{
		var $ = this.jQuery;
		value = title.val();
		
		if ((value == '' || value == default_title || value == default_temp_title)) {
			title.addClass('wrongvalue');
			var stoptext = 'Please provide a new or different page title before saving this note.';
			if (value == default_temp_title) {
				stoptext = 'Please provide a new or different template page title (starting with Template:) before saving this note.';
			}
			
			if (!$('#stopit').length) 
			{
				$('#hubForm').append('<p id="stopit" class="witherror"></p>');
				$('#stopit').html(stoptext);
			}

		} else if (title.hasClass('wrongvalue')) {
			title.removeClass('wrongvalue');
			if ($('#stopit').length) {
				$('#stopit').remove();
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectNotes.initialize();
});
