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
// Project Apps JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectLinks = {
	
	jQuery: jq,
	typingTimer: '',
	preview: '',
	input: '',
	type: '',
	action: 'parseurl',

	initialize: function() 
	{	
		var $ = this.jQuery;
		
		var preview = $('#c-filelist').length ? $('#c-filelist') : $('#citation-preview');
		var input 	= $('#parse-url').length ? $('#parse-url') : $('#citation-doi');
		var type 	= $('#parse-url').length ? 'content' : 'citations';
		var action 	= $('#citation-doi').length ? 'parsedoi' : 'parseurl';
		
		// Input parse
		HUB.ProjectLinks.readLink(input, preview, type, action);
	
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
			clearTimeout(HUB.ProjectLinks.typingTimer);
			
			// Clean up
			if ( type == 'content')
			{
				HUB.ProjectLinks.cleanUp();
			}			
			
			HUB.ProjectLinks.typingTimer = setTimeout(function(i,p,t,a) {  
			                 return function() { HUB.ProjectLinks.parseLink(i,p,t,a) } }(input, preview, type, action), doneTypingInterval);
		});
		
		
		$(input).on('keydown', function(e) {
			clearTimeout(HUB.ProjectLinks.typingTimer);
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
			$('#link-loading').after('<div id="link-preview"></div>'); 
			
			HUB.ProjectLinks.showElement($('#link-loading'), 'hide');			
		}
		
		HUB.ProjectLinks.showElement($('#link-content'), 'hide');
		HUB.ProjectLinks.showElement($('#link-preview'), 'hide');
						
		if (!HUB.ProjectLinks.isValidURL($(input).val()))
	    {          
			HUB.ProjectLinks.showElement($('#link-loading'), 'show');
			$('#link-loading').html('Please enter a valid URL starting with http:// or https://');
			$('#block').val(1);
			return false;
        }
		else
		{
			// Loading preview
			$('#link-loading').html(HUB.ProjectPublications.loadingIma('Loading link preview...'));
			HUB.ProjectLinks.showElement($('#link-loading'), 'show');			
		}
		
		var projectid = $('#projectid').length ? $('#projectid').val() : 0;
				
		// Show selected link
		if ($(input).val())
		{						
			$.post("/projects/" + projectid + "/links/?action=" + action + "&no_html=1&ajax=1&url="+escape($(input).val()), {}, 
				function (response) {
					
				response = $.parseJSON(response);
				clearTimeout(HUB.ProjectLinks.typingTimer);
				
				// Clean up
				if ( type == 'content')
				{
					HUB.ProjectLinks.cleanUp();
				}
				
				if (response.error)
				{
					$('#link-loading').html(response.error);
					HUB.ProjectLinks.showElement($('#link-loading'), 'show');
					HUB.ProjectLinks.showElement($('#link-preview'), 'hide');
					HUB.ProjectLinks.showElement($('#link-content'), 'hide');
				}
				else
				{
					HUB.ProjectLinks.showElement($('#link-loading'), 'hide');
				}
				
				if (response.url)
				{										
					// Allow to save
					if (type == 'content')
					{
						$('#link-content').html('<span class="links">&nbsp;</span>' + response.url );
						HUB.ProjectLinks.showElement($('#link-content'), 'show');
						
						$('#block').val(0);	
						$('#link-loading').after('<div id="clone-link::' + response.url + '" class="hidden c-drag link-picked"></div>');
						HUB.ProjectPublications.checkBtn();						
					}
					else if ( type == 'citations')
					{
						$('#link-preview').after('<p class="submit-citation" id="submit-citation"><input type="submit" class="btn" value="Add citation" /></p>'); 
					}																	
				}
				else
				{
					HUB.ProjectLinks.showElement($('#link-content'), 'hide');
					if (type == 'content')
					{
						$('#block').val(1);	
					}					
				}
				
				// Show preview
				if (response.preview)
				{					
					if (response.rtype && response.rtype == 'doi')
					{
						response.preview = '<p>' + response.preview + '</p>';
					}
					
					$('#link-preview').html(response.preview);
					HUB.ProjectLinks.showElement($('#link-preview'), 'show');
				}
				else
				{
					HUB.ProjectLinks.showElement($('#link-preview'), 'hide');
				}	
			});					
		}	
		else
		{
			HUB.ProjectLinks.showElement($('#link-loading'), 'hide');
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
}
	
jQuery(document).ready(function($){
	HUB.ProjectLinks.initialize();
});
