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

HUB.Groups = {	
	initialize: function() 
	{	
		//
		HUB.Groups.membershipNotifications();
		
		//verify wanting to cancel from group
		HUB.Groups.confirmCancelMembership();
		
		//confirm messages
		HUB.Groups.confirmLeaveArea();
		
		//logo picker to show logo picked in preview
		HUB.Groups.customizePickLogo();
		
		//show or hide custom content box
		HUB.Groups.customizeOverviewContent();
		
		//group page preview in lightbox
		HUB.Groups.customizePreviewPage();
		
		//hide all empty paragraphs generated from wiki
		//HUB.Groups.fixEmptyParagraphs();
		
		//toggle between public and private group desc
		HUB.Groups.togglePublicPrivateDescription();
		
		//special groups
		HUB.Groups.specialGroups();
		
		//
		HUB.Groups.scrollingAssetBrowser();
	},
	
	//----
	
	membershipNotifications: function()
	{
		var content = "",
			invites = $(".invites"),
			requests = $(".requests");
		
		if(invites.length) 
		{
			invites.addClass("hide");
			content += invites.html();
		}
		
		if(requests.length) 
		{
			requests.addClass("hide");
			content += "<hr />" + requests.html();
		}
		
		if(content != "") 
		{	
			var timeout = setTimeout(function() {
				$.fancybox({
					autoSize:false,
					width: 600,
					height: 'auto',
					content: content,
					beforeload: function() {
						invites.toggleClass("hide");
						requests.toggleClass("hide");
					}
				});
			}, 2000);
		}
	},
	
	//-----
	
	fixEmptyParagraphs: function()
	{
		$('p').each(function(i, el) {
			var text = el.innerHTML;
			if(escape(text) == '%3Cbr%3E%0A' || text == "") {
				el.addClass('hide');
			}
		});
	},
	
	//-----
	
	togglePublicPrivateDescription: function()
	{
		var publicDesc = $("#public"),
			privateDesc = $("#private"),
			toggleSwitch = $("#toggle_description");
			
		
		if(toggleSwitch.length)
		{
			toggleSwitch.removeClass('hide');
			
			toggleSwitch.bind("click", function(e) {
				e.preventDefault();
				
				if(publicDesc.hasClass("hide"))
				{
					toggleSwitch.html('Show Private Description (-)');
				}
				else
				{
					toggleSwitch.html('Show Public Description (+)');
				}
				
				publicDesc.toggleClass("hide");
				privateDesc.toggleClass("hide");
			});
		}
	},
	
	//-----
	
	confirmCancelMembership: function()
	{
		$(".cancel_group_membership").live('click', function(e) {
			e.preventDefault();
			
			var answer = confirm('Are you sure you would like to cancel your group membership?');
			if(answer) 
			{ 
				window.location = this.href;
			}
		});
	},
	
	//-----
	
	confirmLeaveArea: function()
	{
		$('.leave_area').bind('click', function(e) {
			e.preventDefault();
			
			var question = $(this).attr('rel'),
				answer = confirm(question);
				
			if(answer) { 
				window.location = this.href;
			}
		});
	},
	
	//-----
	
	customizePickLogo: function()
	{
		var logo = "",
			logoSelecter = $("#group_logo");
		
		if(logoSelecter.length)
		{
			logoSelecter.bind("change", function(e) {
				
				if(this.value == "")
				{
					logo = '<img src="/components/com_groups/assets/img/group_default_logo.png" />';
				}
				else 
				{
					logo = '<img src="'+this.value+'" />';
				}
				
				$("#logo_picked").html(logo)
			});
		}
	},
	
	//-----
	
	customizeOverviewContent: function()
	{
		var overview = $('#overview_content'),
			customOverview = $('#group_overview_type_custom').attr("checked");
		
		if(overview.length)
		{
			if(!customOverview)
			{
				overview.addClass("hide-left");
			}
			
			$("input[type=radio]").bind("click", function(e){
				$('p.side-by-side').removeClass('checked');
				$(this).parents(".side-by-side").addClass("checked");
				if(this.value == 1) 
				{
					overview.removeClass('hide-left');
				}
				else 
				{
					overview.addClass('hide-left');
				}
			});
		}
	},
	
	//-----
	
	customizePreviewPage: function()
	{
		$('.quick-view').fancybox({
			type: 'inline',
			width: 600,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false
		});
	},
	
	//-----
	
	scrollingAssetBrowser: function()
	{
		var topBox = $("#top_box"),
			bottomBox = $("#bottom_box"),
			assetBox = $("#asset_browser");

		if(assetBox.length)
		{   
			var element = $(document);
			min = (topBox.length != 0) ? (topBox.offset().top) : 0;
			max = (bottomBox.length != 0) ? bottomBox.offset().top+bottomBox.outerHeight(true)-assetBox.outerHeight(true)-min : 0;
			margin = 0;
			
			//margin-top of asset-browser, not sure why .outerheight(true) is calculating right
			max -= 7;
			
			if(element.scrollTop() > max) 
			{
				margin = (max-element.scrollTop());
			}
			
			$(".explaination").css({
				'position':'fixed',
				'right':'80px',
				'z-index':'999',
				'width':'24%',
				'margin-top': margin
			});
		}
	},

	//-----
	
	specialGroups: function()
	{
		
	}
}

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Groups.initialize();
});

//-----------------------------------------------------------

jQuery(window).scroll(function($){
	HUB.Groups.scrollingAssetBrowser();
});