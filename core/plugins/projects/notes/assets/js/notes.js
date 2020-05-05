/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
