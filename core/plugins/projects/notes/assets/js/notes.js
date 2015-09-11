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
