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

HUB.Groups = {
	jQuery: jq,
	
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
		
		//scrolling asset browser
		HUB.Groups.scrollingAssetBrowser();
		
		//group availablity checker
		HUB.Groups.groupIdAvailability();
		
		//group menu alert fixer
		HUB.Groups.groupMenuFix();
	},
	
	//-----
	
	groupMenuFix: function()
	{
		var $ = this.jQuery;

		$("#page_menu li").each(function(index){
			var meta = $(this).find(".meta"),
				metawidth = meta.outerWidth(true),
				alrt = $(this).find(".alrt");
			
			if(alrt.length)
			{
				if(metawidth > 20)
				{
					alrt.css("right", 33+(metawidth-20));
				}
				else if(metawidth < 20 && metawidth != 0)
				{
					alrt.css("right", 33-(20-metawidth));
				}
			} 
		});
	},
	
	//-----
	
	membershipNotifications: function()
	{
		var $ = this.jQuery;
		
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
			content += requests.html();
		}
		
		if(content != "") 
		{	
			var timeout = setTimeout(function() {
				$.fancybox({
					autoSize:false,
					width: 600,
					height: 'auto',
					content: content,
					tpl: {
						wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
					},
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
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
		var $ = this.jQuery;
		
		var topBox = $("#top_box"),
			bottomBox = $("#bottom_box"),
			assetBox = $("#asset_browser"),
			max = 0, pos = 0;

		assetBox
			.css('width', ($(assetBox.parent()).width() - 10) + 'px')
			.css('padding', '10px');

		if(assetBox.length)
		{
			if(topBox.length && bottomBox.length)
			{
				max = (bottomBox.position().top + bottomBox.outerHeight(true)) - assetBox.outerHeight(true);
				pos = $(document).scrollTop() - 200;

				if( pos > 0 && pos < max)
				{
					assetBox.stop().animate({
						"top": pos
					});
				}
			}
			else
			{
				if (window.console) console.log('Missing needed DOM elements to make assest browser scroll.');
			}
		}
	},

	//-----
	
	specialGroups: function()
	{
		var $ = this.jQuery;
		
		$("#special-group-tab").on("click", function(event){
			$("#special-group-pane").slideToggle('slow', function() {
				$("#special-group-tab").toggleClass('active');
			});
			event.preventDefault();
		});
		
		var wh = $(window).height(),
			ph = $("#page_container").height();
		if(ph < wh)
		{
			//$("#page_container").css("height", wh);
			//$("#page_container_inner").css("height", wh);
		}
	},
	
	//-----
	
	groupIdAvailability: function()
	{
		var $ = this.jQuery; 
		$("#group_cn_field")
			.on("keydown", function(event) {
				$('.available, .not-available').remove();
			})
			.on("keyup", function(event) { 
				$.ajax({
					url: 'index.php?option=com_groups&task=groupavailability&no_html=1',
					data: { 'group' : $(this).val() },
					success: function(data) {
						var availability = jQuery.parseJSON(data);
						if(availability)
						{
							if(availability.available)
							{
								if(!$('.available').length)
								{
									$("#group_cn_field").after('<span class="available">Group Available</span>');
								}
								$('.not-available').remove();
							}
							else
							{
								$(".available").remove();
								if(!$('.not-available').length)
								{
									$("#group_cn_field").after('<span class="not-available">Group Not Available</span>');
								}
							}
						}
					}
				});
			});
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