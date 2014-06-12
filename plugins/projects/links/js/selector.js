/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectLinksSelect = {
	jQuery: jq,
	selections: new Array(),
	typingTimer: '',
	preview: '',
	input: '',
	type: '',
	action: 'parseurl',

	initialize: function() {
		var $ = this.jQuery;
		
		var isMSIE = /*@cc_on!@*/0;
				
		// Enable selection
		HUB.ProjectLinksSelect.selector();
				
		// Enable save button
		HUB.ProjectLinksSelect.enableButton();
		HUB.ProjectLinksSelect.enableSave();
		
		// Link to add citation manually
		HUB.ProjectLinksSelect.newCite();	
		
		// Enable adding new citation
		HUB.ProjectLinksSelect.enableAdd();	
		
		if ($('#cancel-action')) {
			$('#cancel-action').on('click', function(e) {
				$.fancybox.close();
			});
		}		
	},
	
	newCite: function()
	{
		var $ = this.jQuery;
		
		var link = $('#newcite-question');
		var abox = $('#abox-content-wrap');
		
		if (!link.length)
		{
			return false;
		}
		
		var url = link.attr('href');
		var url = url + '&ajax=1&no_html=1';
		
		link.on('click', function(e) 
		{
			e.preventDefault();
			
			// Ajax call to get current status of a block
			$.post(url, {}, 
			function (response) 
			{
				if (response)
				{
					$(abox).html(response);
				}

				// Re-enable js
				jQuery(document).trigger('ajaxLoad');

			});
			
		});		
	},

	enableSave: function()
	{
		var $ = this.jQuery;
		var btn  = $('#b-save');
		var form = $('#select-form');
				
		if (!btn.length || !form.length)
		{
			return false;
		}
		
		// Send data
		btn.on('click', function(e) 
		{
			e.preventDefault();

			if (!btn.hasClass('disabled')) 
			{ 					
				form.submit();				
			}
			
		});
	},
	
	fadeMessage: function()
	{
		var $ = this.jQuery;
		
		if (!$('#status-box').length)
		{
			return false;
		}
		$("#status-box").animate({opacity:0.0}, 2000, function() {
		    $('#status-box').html('');
			$("#status-box").css('opacity', '1.0');
		});
	},
	
	enableButton: function()
	{
		var $ = this.jQuery;
		var btn = $('#b-save');
				
		if (!btn.length)
		{
			return false;
		}
		
		success = HUB.ProjectLinksSelect.checkProgress();
		if (success == true)
		{
			if (btn.hasClass('disabled'))
			{
				btn.removeClass('disabled');
			}			
		}
		else
		{
			if (!btn.hasClass('disabled'))
			{
				btn.addClass('disabled');
			}
		}
	},
	
	checkProgress: function () 
	{	
		var $ = this.jQuery;
		var success = false;
				
		// Check that we satisfy minimum/maximum requirements
		if ($('#parse-url').val() && !$('#link-loading').html())
		{
			success = true;
		}

		return success;
	},
	
	selector: function () 
	{	
		var $ = this.jQuery;
		
		if (!$('#parse-url').length)
		{
			return false;
		}
		
		var preview = $('#preview-wrap');
		var input 	= $('#parse-url');
		var type 	= $('#section').val() == 'citations' ? 'citations' : 'content';
		var action 	= $('#parseaction').length ? $('#parseaction').val() : 'parseurl';
				
		// Input parse
		HUB.ProjectLinksSelect.readLink(input, preview, type, action);
	
	},
	
	readLink: function(input, preview, type, action)
	{
		var $ = this.jQuery;
		
		if (!$(input).length || !$(preview).length)
		{
			return false;
		}
				
		$(input).unbind();
		             
		var doneTypingInterval = 1000;
		
		// When adding link as primary content				
		$(input).on('keyup', function(e) 
		{
			clearTimeout(HUB.ProjectLinksSelect.typingTimer);
			
			// Clean up
			if ( type == 'content')
			{
				HUB.ProjectLinksSelect.cleanUp();
			}		
			
			HUB.ProjectLinksSelect.typingTimer = setTimeout(function(i,p,t,a) {  
			                 return function() { HUB.ProjectLinksSelect.parseLink(i,p,t,a) } }(input, preview, type, action), doneTypingInterval);
		});
		
		
		$(input).on('keydown', function(e) {
			clearTimeout(HUB.ProjectLinksSelect.typingTimer);
		});
		
	},
	
	parseLink: function(input, preview, type, action)
	{
		var $ = this.jQuery;
				
		if (!$(input.length) || !$(preview.length))
		{
			return false;
		}
				
		// Hide selection ul
		if ( type == 'content')
		{
			if (!$(preview).hasClass('hidden'))
			{
				$(preview).addClass('hidden');
			}
		}
		
		if ($('#submit-citation').length)
		{
			$('#submit-citation').remove();
		}
		
		if (!$('#link-content').length)
		{
			$(preview).after('<div id="link-loading" class="notice"></div>'); 
			$(preview).before('<div id="link-content">' + $(input).val() + '</div>');
			$('#link-loading').after('<div id="link-preview" class="link-preview"></div>'); 
			
			HUB.ProjectLinksSelect.showElement($('#link-loading'), 'hide');			
		}
		
		HUB.ProjectLinksSelect.showElement($('#link-content'), 'hide');
		HUB.ProjectLinksSelect.showElement($('#link-preview'), 'hide');
						
		if (!HUB.ProjectLinksSelect.isValidURL($(input).val()))
	    {          
			HUB.ProjectLinksSelect.showElement($('#link-loading'), 'show');
			$('#link-loading').html('Please enter a valid URL starting with http:// or https://');
			$('#block').val(1);
			return false;
        }
		else
		{
			// Loading preview
			$('#link-loading').html(HUB.ProjectLinksSelect.loadingIma('Loading link preview...'));
			HUB.ProjectLinksSelect.showElement($('#link-loading'), 'show');			
		}
		
		var projectid = $('#projectid').length ? $('#projectid').val() : 0;
		var parseUrl  = $('#parseurl').length ? $('#parseurl').val() : "/projects/" + projectid + "/links/";

		// Show selected link
		if ($(input).val())
		{									
			$.post(parseUrl + "?active=links&action=" + action + "&no_html=1&ajax=1&url="+escape($(input).val()), {}, 
				function (response) {
					
				response = $.parseJSON(response);
				clearTimeout(HUB.ProjectLinksSelect.typingTimer);
				
				// Clean up
				if ( type == 'content')
				{
					HUB.ProjectLinksSelect.cleanUp();
				}
				
				if (response.error)
				{
					$('#link-loading').html(response.error);
					HUB.ProjectLinksSelect.showElement($('#link-loading'), 'show');
					HUB.ProjectLinksSelect.showElement($('#link-preview'), 'hide');
					HUB.ProjectLinksSelect.showElement($('#link-content'), 'hide');
				}
				else
				{
					$('#link-loading').html('');
					HUB.ProjectLinksSelect.showElement($('#link-loading'), 'hide');
				}
				
				if (response.url)
				{										
					// Allow to save
					$('#link-content').html('<span class="links">&nbsp;</span>' + response.url );
					HUB.ProjectLinksSelect.showElement($('#link-content'), 'show');
					$(input).val(response.url);																
				}
				else
				{
					HUB.ProjectLinksSelect.showElement($('#link-content'), 'hide');					
				}
				
				// Save link title
				if (response.title && $('#parse-title').length)
				{
					$('#parse-title').val(response.title);
				}
				// Save link description
				if (response.description && $('#parse-description').length)
				{
					$('#parse-description').val(response.description);
				}
				
				HUB.ProjectLinksSelect.enableButton();
				
				// Show preview
				if (response.preview)
				{					
					if (response.rtype && response.rtype == 'doi')
					{
						response.preview = '<p>' + response.preview + '</p>';
					}
					
					$('#link-preview').html(response.preview);
					HUB.ProjectLinksSelect.showElement($('#link-preview'), 'show');
				}
				else if (response.message)
				{
					$('#link-preview').html('<p class="noresults">' + response.message + '</p>');
					HUB.ProjectLinksSelect.showElement($('#link-preview'), 'show');
				}
				else
				{
					HUB.ProjectLinksSelect.showElement($('#link-preview'), 'hide');
				}	
			});					
		}	
		else
		{
			HUB.ProjectLinksSelect.showElement($('#link-loading'), 'hide');
		}
	},
	
	isValidURL: function(url)
	{
		var $ = this.jQuery; 
		return true;  
	
		var expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
	 	var regex = new RegExp(expression);
			
	    if (regex.test(url))
		{
	       return true;
	    } 
		else
		{
	       return false;
	    }
	},
	
	loadingIma: function(txt)
	{
		var $ = this.jQuery;
		
		var html = '<span id="fbwrap">' + 
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ' +
			txt +
			'</span>' +
		'</span>';
		
		return html;
	},
	
	cleanUp: function()
	{
		var picked = $('.link-picked');
		var con    = $('#c-continue');
		if (picked.length > 0) {
			picked.each(function(i, item)  
			{
				item.remove();
			});
		}
		
		if (con.length && !con.hasClass('disabled')) 
		{
			con.addClass('disabled');
		}
	},
	
	enableAdd: function()
	{
		var $ = this.jQuery;
		var btn  		= $('#b-add');
		var form 		= $('#add-cite');
		var statusBox 	= $('#status-box');
		
		if (!btn.length || !form.length)
		{
			return false;
		}
		
		// Send data
		btn.on('click', function(e) 
		{
			e.preventDefault();

			var passed = HUB.ProjectLinksSelect.checkRequired();
				
			if (passed == false)
			{
				statusBox.html('<p class="status-error">Please make sure all fields are filled</p>');
				HUB.ProjectLinksSelect.fadeMessage();
			}
			else
			{
				form.submit();
			}
			
		});		
	},
	
	checkRequired: function () 
	{	
		var $ = this.jQuery;
		var success = true;
		
		var fields = $('.inputrequired');
		
		if (fields.length == 0)
		{
			return true;
		}
		
		fields.each(function(i, item)  
		{
			if (!$(item).val() || $(item).val() == '')
			{
				success = false;
			}
		});

		return success;
	},
	
	showElement: function(area, action)
	{
		var $ = this.jQuery;
		
		if (!area.length)
		{
			return false;
		}
		
		if (action == 'hide')
		{
			if (!area.hasClass('hidden'))
			{
				area.addClass('hidden');
			}
			area.html('');
		}
		else
		{
			if (area.hasClass('hidden'))
			{
				area.removeClass('hidden');
			}
		}
	}
};

jQuery(document).ready(function($){
	HUB.ProjectLinksSelect.initialize();
});

// Register the event
jQuery(document).on('ajaxLoad', HUB.ProjectLinksSelect.initialize);
