/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.jquery.js
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

HUB.Courses = {
	jQuery: jq,
	
	initialize: function() 
	{	
		//
		HUB.Courses.membershipNotifications();
		
		//verify wanting to cancel from course
		HUB.Courses.confirmCancelMembership();
		
		//confirm messages
		HUB.Courses.confirmLeaveArea();
		
		//logo picker to show logo picked in preview
		HUB.Courses.customizePickLogo();
		
		//show or hide custom content box
		HUB.Courses.customizeOverviewContent();
		
		//course page preview in lightbox
		HUB.Courses.customizePreviewPage();
		
		//hide all empty paragraphs generated from wiki
		//HUB.Courses.fixEmptyParagraphs();
		
		//toggle between public and private course desc
		HUB.Courses.togglePublicPrivateDescription();
		
		//special courses
		HUB.Courses.specialCourses();
		
		// Sortable
		HUB.Courses.makeSortable();
		
		//scrolling asset browser
		HUB.Courses.scrollingAssetBrowser();
		
		//course availablity checker
		HUB.Courses.courseIdAvailability();
		
		//course menu alert fixer
		HUB.Courses.courseMenuFix();
	},
	
	//-----
	
	courseMenuFix: function()
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
					padding:0,
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
		
		$(".cancel_course_membership").on('click', function(e) {
			e.preventDefault();
			
			var answer = confirm('Are you sure you would like to cancel your course membership?');
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
			logoSelecter = $("#course_logo");
		
		if(logoSelecter.length)
		{
			logoSelecter.bind("change", function(e) {
				
				if(this.value == "")
				{
					logo = '<img src="/components/com_courses/assets/img/course_default_logo.png" />';
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
			customOverview = $('#course_overview_type_custom').attr("checked");
		
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
			max = 0;

		if(assetBox.length && topBox.length && bottomBox.length)
		{
			max = (bottomBox.position().top + bottomBox.outerHeight(true)) - assetBox.outerHeight(true);
			
			if( $(document).scrollTop() > 0 && $(document).scrollTop() < max)
			{
				assetBox.stop().animate({
					"top": $(document).scrollTop()
				});
			}
		}
	},

	//-----
	
	specialCourses: function()
	{
		var $ = this.jQuery;

		$("#special-course-pane").hide();

		$("#special-course-tab").on("click", function(event){
			$("#special-course-pane").slideToggle('slow', function() {
				$("#special-course-tab").toggleClass('active');
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

	makeSortable: function()
	{
		var $ = this.jQuery;

		$(".sortable").sortable({
			placeholder: "placeholder",
			forcePlaceholderSize: true,
			revert: true,
			tolerance: 'pointer',
			opacity: '0.8',
			items: 'li',
			axis: 'y',
			start: function(){
				$(".placeholder").css('height', $(event.target).height());
			}
		});

		// Turn span "titles" into editable fields
		$(".sortable").on('click', "span.title", function(event){
			event.stopPropagation();
			event.preventDefault();
			var parent = $(this).parent();
			var width  = parent.find("span").width();
			var value  = $(this).html();

			$(this).replaceWith('<div><input type="text" value="' + value + '" />');

			var input = parent.find('input[type="text"]:first');
			input.css("width", width+20);
			input.after('<input type="submit" value="Save" /><input type="reset" value="Cancel" /></div>');

			// Store away the previous val in the div data
			div = parent.find("div:first");
			div.data('prevVal', value);
		});

		// Turn editable fields back into spans on cancel
		$(".sortable").on('click', "input[type='reset']", function(){
			event.preventDefault();
			var parent = $(this).parent("div");
			parent.replaceWith('<span>' + parent.data('prevVal') + '</span>');
		});

		// Save editable fields on save
		$(".sortable").on('click', "input[type='submit']", function(event){
			event.preventDefault();
			var parent = $(this).parent("div");
			parent.replaceWith('<span>' + parent.find("input[type='text']:first").val() + '</span>');
		});

		// Hide and show the add button on hover
		//$("ul div.add").hide();
		$(".sortable").on('mouseenter', 'li', function(event){
			event.preventDefault();
			event.stopPropagation();
			var add = $(this).find('div.add:first');
			add.show();
		});

		$(".sortable").on('mouseleave', 'li', function(event){
			event.preventDefault();
			event.stopPropagation();
			var add = $(this).find('div.add:first');
			add.hide();
		});

		// Add a new list item when clicking 'add'
		$(".sortable").on('click', ".add", function(event){
			event.preventDefault();
			event.stopPropagation();
			var parent = $(this).parent("ul");

			var appendtext =  '<li><span>new item...</span>';
			if($(this).parents('ul').length < 4) {
				appendtext     += '<ul class="sortable"><div class="add"></div></ul>';
			}
			appendtext     += '</li>';

			parent.append(appendtext);
		});
	},

	//-----
	
	courseIdAvailability: function()
	{
		var $ = this.jQuery; 
		$("#course_cn_field")
			.on("keydown", function(event) {
				$('.available, .not-available').remove();
			})
			.on("keyup", function(event) { 
				$.ajax({
					url: 'index.php?option=com_courses&task=courseavailability&no_html=1',
					data: { 'course' : $(this).val() },
					success: function(data) {
						var availability = jQuery.parseJSON(data);
						if(availability)
						{
							if(availability.available)
							{
								if(!$('.available').length)
								{
									$("#course_cn_field").after('<span class="available">Course Available</span>');
								}
								$('.not-available').remove();
							}
							else
							{
								$(".available").remove();
								if(!$('.not-available').length)
								{
									$("#course_cn_field").after('<span class="not-available">Course Not Available</span>');
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
	HUB.Courses.initialize();
});

//-----------------------------------------------------------

jQuery(window).scroll(function($){
	HUB.Courses.scrollingAssetBrowser();
});