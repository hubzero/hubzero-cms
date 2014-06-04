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
// Project Publication Curation Manager JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.PublicationsCuration = {
	jQuery: jq,
	timer: '',
	doneTypingInterval: 1000,
	completeness: 0,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		// Show 'more' link for extensive side text 
		HUB.PublicationsCuration.showMoreText();
		
		// Enable submit buttons
		HUB.PublicationsCuration.enableButtons();
		
		// Enable checkers
		HUB.PublicationsCuration.enableCheckers();
		
		// Enable checkers
		HUB.PublicationsCuration.allowEdits();
	},
	
	// Allow to edit notices
	allowEdits: function()
	{
		var $ = this.jQuery;
				
		// Allow editing of notices
		var edits = $('.edit-notice a');		
		if (edits.length)
		{
			edits.each(function(i, item) 
			{
				$(item).on('click', function(e) 
				{
					e.preventDefault();
					
					var element = $(item).parent().parent().parent().parent().find('.block-checker')[0];
					
					// Load box to ask why
					if ($(element).length)
					{
						HUB.PublicationsCuration.drawFailBox($(element));
					}
					
				});
			});
		}		
	},
	
	// Enable checkers
	enableCheckers: function()
	{
		var $ = this.jQuery;
		
		var checkers = $(".block-checker span");
		
		if (!checkers.length)
		{
			return false;
		}
		
		checkers.each(function(i, item) 
		{
			$(item).on('click', function(e) 
			{
				if ($(item).hasClass('picked') && !$(item).hasClass('updated'))
				{
					// Already picked
					return false;
				}
				
				if ($(item).hasClass('checker-fail'))
				{
					// Load box to ask why
					HUB.PublicationsCuration.drawFailBox($(item).parent());
				}
				else
				{
					e.preventDefault();

					if ($(item).hasClass('checker-pass'))
					{
						HUB.PublicationsCuration.changeStatus($(item).parent(), $(item), 'pass');
					}
				}
				
				// Enable submit buttons
				HUB.PublicationsCuration.enableButtons();
				
			});
		});	
	},
	
	// Change status of curation item
	changeStatus: function(element, item, action)
	{
		var $ = this.jQuery;
		
		if (!element.length || ! item.length)
		{
			return false;
		}
		var prop = element.attr('id');
		var pid  = $('#pid').length ? $('#pid').val() : 0;
		var vid  = $('#vid').length ? $('#vid').val() : 0;
		
		var url  = '/publications/curation/' + pid + '/save/?vid=' + vid ;
		url 	 = url + '&p=' + prop;
		url 	 = url + '&no_html=1&ajax=1';
		
		if (action == 'pass')
		{
			url = url + '&pass=1';

			// Ajax call to get current status of a block
			$.post(url, {}, 
				function (response) {
				response = $.parseJSON(response);
				
				if (response.success)
				{
					HUB.PublicationsCuration.markChecker(element, 'pass');	
				}
				if (response.error)
				{
					// TBD
				}
				
				// Enable submit buttons
				HUB.PublicationsCuration.enableButtons();
				
			});					
		}
	},
	
	// Mark element as passed
	markChecker: function(element, action)
	{
		var $ = this.jQuery;
		
		if (!element.length)
		{
			return false;
		}
		
		var checkers = $(element).find('span');
		
		checkers.each(function(i, item) 
		{
			var blockelement = $(element).parent().parent();
			if (action == 'pass')
			{
				if ($(item).hasClass('checker-pass'))
				{
					$(item).addClass('picked');
					$(item).removeClass('updated');
				}
				else if ($(item).hasClass('picked'))
				{
					$(item).removeClass('picked');
					$(item).removeClass('updated');
				}
				
				$(blockelement).addClass('el-passed');
				$(blockelement).removeClass('el-failed');
				$(blockelement).removeClass('el-updated');
			}
			else if (action == 'fail')
			{
				if ($(item).hasClass('checker-fail'))
				{
					$(item).addClass('picked');
					$(item).removeClass('updated');
				}
				else if ($(item).hasClass('picked'))
				{
					$(item).removeClass('picked');
					$(item).removeClass('updated');
				}
				
				$(blockelement).addClass('el-failed');
				$(blockelement).removeClass('el-passed');
				$(blockelement).removeClass('el-updated');
			}			
		});		
	},
	
	drawFailBox: function (element) 
	{	
		var $ 		= this.jQuery;
		var review 	= $('#notice-review');
		var submit 	= $('#notice-submit');
		var form 	= $('#notice-form');
		
		if (!review.length || !$('#addnotice').length || !element.length) 
		{
			return false;
		}
		
		var notice 	 = $(element).parent().find('.notice-text')[0];
		var text 	 = $(notice).html();
		
		// Write title
		var title = element.attr('rel');		
		$('#notice-item').html('<strong>Curation Item:</strong> ' + title);
		
		$(review).val('');
		if ($(notice).length)
		{
			$(review).val(text);
		}
		
		// Reload prop value
		$('#props').val('');
		var value = $(element).attr('id');
		$('#props').val(value);
		
		// Open form in fancybox
		$.fancybox( [$('#addnotice')] );
		
		$(form).unbind(); 		
		
		// Submit form
		$(form).on('submit', function(e) 
		{
			var url = form.attr('action');
			var formData = new FormData($(this)[0]);
		    
			// Ajax request
			$.ajax({
		           type: "POST",
		           url: url,
		           data: formData,
				   contentType: false,
				   processData: false,
		           success: function(response)
		           {		                						
						if (response)
						{
						    try {
						        response = $.parseJSON(response);
								if (response.error || response.error != false)
								{
									// error
								}
								else
								{										
									HUB.PublicationsCuration.markChecker(element, 'fail');
									
									var note 	 = $(element).parent().find('.notice-text')[0];
									$(note).html(response.notice);
																		
									// Enable submit buttons
									HUB.PublicationsCuration.enableButtons();
									
									HUB.PublicationsCuration.allowEdits();									
								}								
						    } 
							catch (e) 
							{
								// error
						    }
						}
						
						$.fancybox.close(); 				
		           }
		    });

		    return false;
		
		});
		
		// Submit only if reason is entered	
		$(submit).on('click', function(e) 
		{
			e.preventDefault();
			
			if ($(review).val() && $(review).val() != '')
			{	
				$(form).submit();
			}
		});

	},
	
	// Show 'more' link for extensive side text 
	showMoreText: function()
	{
		var $ = this.jQuery;
		$(".more-content").fancybox();
		
		var fb = $(".fancybox");
		
		$('.fancybox').each(function(i, item) {
			$(item).on('click', function(e) {
				e.preventDefault();
				$.fancybox(this,{
					type: 'ajax',
					width: 700,
					height: 'auto',
					autoSize: false,
					fitToView: false,
				});
			});
		});
	
	},
	
	// Enable submit buttons
	enableButtons: function()
	{
		var $ = this.jQuery;
		
		var btns = $(".btn-curate");
		
		if (!btns.length)
		{
			return false;
		}
		
		var complete = HUB.PublicationsCuration.checkCompleteness();
		var approved = HUB.PublicationsCuration.checkApproved();
		
		btns.each(function(i, item) 
		{
			if (complete)
			{			
				if (($(item).hasClass('curate-save') && approved) || ($(item).hasClass('curate-kickback') && !approved))
				{
					$(item).removeClass('disabled');
					$(item).parent().parent().parent().addClass('active');
				}
				else if (!$(item).hasClass('disabled'))
				{
					$(item).addClass('disabled');
				}			
			}
			else
			{
				$(item).addClass('disabled');
				$(item).parent().parent().parent().removeClass('active');
			}
			
			var action = $(item).hasClass('curate-save') ? 'approve' : 'kickback';
			
			// Submit form
			$(item).on('click', function(e) 
			{
				e.preventDefault();
				
				if (!$(item).hasClass('disabled')) 
				{ 
					if ($('#curation-form').length) 
					{	
						$('#task').val(action);
						$('#curation-form').submit();
					}
				}
			});			
		});
	},
	
	// Check completion
	checkCompleteness: function()
	{
		var $ = this.jQuery;
		var complete = 0;
		
		var checkers = $(".block-checker span");
		
		if (!checkers.length)
		{
			return false;
		}
		
		var checked = 0;
		checkers.each(function(i, item) 
		{
			if ($(item).hasClass('picked') && !$(item).hasClass('updated'))
			{
				checked = checked + 1;
			}
		});
		
		if (checked == checkers.length/2)
		{
			complete = 1;
		}
		
		return complete;
	},
	
	// Check for items requiring changes
	checkApproved: function()
	{
		var $ = this.jQuery;
		var complete = 0;
		
		var checkers = $(".block-checker span");
		var approved = $(".block-checker span.checker-pass");
		
		if (!checkers.length)
		{
			return false;
		}
		var required = checkers.length/2;
		var checked  = 0;
		
		approved.each(function(i, item) 
		{
			if ($(item).hasClass('picked') && !$(item).hasClass('updated'))
			{
				checked = checked + 1;
			}
		});
		
		if (checked == required)
		{
			complete = 1;
		}
		
		return complete;
	}
}

jQuery(document).ready(function($){
	HUB.PublicationsCuration.initialize();
});	
