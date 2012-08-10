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

HUB.ProjectNotes = {

	initialize: function() {
		var frm = $('hubForm');
		var notetitle = $('title');
		var default_title = 'New Note';		
		var default_temp_title = 'Template: New';	
		
		if(notetitle) {
			HUB.ProjectNotes.checkTitle(notetitle, default_title, default_temp_title);
			notetitle.addEvent('keyup', function(e) {
				HUB.ProjectNotes.checkTitle(notetitle, default_title, default_temp_title);
			});
		}
		
		if(frm && notetitle) {
			frm.addEvent('submit', function(e) {
				if(notetitle.value == '' || notetitle.value == default_title || notetitle.value == default_temp_title ) {
					new Event(e).stop();
				}
			});
		}
		
		// Remove file uploader button
		if ($('file-uploader'))
		{
			$('file-uploader').remove();	
		}
		if ($('file-uploader-list'))
		{
			$('file-uploader-list').remove();	
		}
		
		// Hide delete/new page menu
		if($('section-useroptions')) {
			if(frm) {
				var last = $('section-useroptions').getLast();
				if(last) {
					last.remove();
				}
			} else {
				$('section-useroptions').innerHTML = '';
			}
			
			// Add subpage link
			if($('add-subpage') && !$('clone-subpage')) {
				var clone = new Element('li', {
					'id': 'clone-subpage'
				}).inject($('section-useroptions'), 'bottom');
				clone.innerHTML = $('add-subpage').innerHTML;
			}
		}
	},
	
	checkTitle: function(title, default_title, default_temp_title) {
		var stopit = $('stopit');
		value = title.value;
		if((value == '' || value == default_title || value == default_temp_title) && !title.hasClass('wrongvalue') ) {
			title.addClass('wrongvalue');
			
				var stoptext = 'Please provide a new or different page title before saving this note.';
				if(value == default_temp_title) {
					stoptext = 'Please provide a new or different template page title (starting with Template:) before saving this note.';
				}
				
				if(!stopit) {
					var stop = new Element('p', {
						'id': 'stopit',
						'class': 'witherror'
					}).inject($('hubForm'), 'bottom');
					stop.innerHTML = stoptext;
				}
				
		}
		else {
			title.removeClass('wrongvalue');
			if(stopit) {
				stopit.remove();
			}
		}		
	}
}
	
window.addEvent('domready', HUB.ProjectNotes.initialize);