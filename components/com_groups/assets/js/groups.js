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
		// General Group Stuff
		HUB.Groups.general();
		
		// Group Page Manager
		HUB.Groups.pages();
		HUB.Groups.modules();
		HUB.Groups.categories();
	},
	
	general: function()
	{
		// group availablity checker
		HUB.Groups.groupIdAvailability();
		
		// group menu alert fixer
		HUB.Groups.groupMenuFix();
		
		// member ship notifications
		HUB.Groups.membershipNotifications();
		
		// verify wanting to cancel from group
		HUB.Groups.confirmCancelMembership();
		
		// logo picker to show logo picked in preview
		HUB.Groups.customizePickLogo();
		
		// toggle between public and private group desc
		HUB.Groups.togglePublicPrivateDescription();
		
		//scrolling asset browser
		HUB.Groups.scrollingAssetBrowser();
		
		// add fancyselect to places
		HUB.Groups.fancyselect();
	},
	
	//-----
	
	fancyselect: function()
	{
		var $ = this.jQuery;
		
		if ($('.fancy-select').length)
		{
			$('.fancy-select').HUBfancyselect();
		}
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
	
	customizePickLogo: function()
	{
		var $ = this.jQuery;
		
		var logo = "",
			logoSelecter = $("#group_logo");
		
		if(logoSelecter.length)
		{
			$('#group-logo-label').on("change", "#group_logo", function(e) {
				
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

	imagesUploaded: function()
	{
		var $ = this.jQuery;
	
		if ($("#group_logo").length)
		{
			var current = $("#group_logo").val();

			//load a refreshed version of the logo list
			//reset option previously selected by user
			$("#group-logo-label").load(window.location.href + ' #group-logo-label > select', function() {
				$("#group_logo").val(current);
			});
		}
	},
	
	//-----
	
	scrollingAssetBrowser: function()
	{
		var $ = this.jQuery;
		
		// append floating iframe toggle button to floating iframe container
		var floatingAssetBrowser = $('.floating-iframe-container');
		if (floatingAssetBrowser.length)
		{
			floatingAssetBrowser.append('<a href="#" class="floating-iframe-toggle"></a>');
			$('.floating-iframe-toggle')
				.on('hover', function(event) {
					event.preventDefault();
					floatingAssetBrowser.toggleClass('hovered');
				})
				.on('click', function(event) {
					event.preventDefault();
					floatingAssetBrowser.toggleClass('opened');
					$(this).toggleClass('opened');
				});
		}
		
		// initialize scolling asset browser
		HUB.Groups._moveScrollingAssetBrowser();
	},
	
	//-----
	
	_moveScrollingAssetBrowser: function()
	{
		var $ = this.jQuery;
		
		var floatingIframe = $('.floating-iframe'),
			floatingIframeToggle = $('.floating-iframe-toggle');
		
		if (floatingIframe.length)
		{
			var scrollTop = $(document).scrollTop(),
				min       = floatingIframe.parents('form').find('fieldset').first().offset().top,
				max       = floatingIframe.parents('form').find('fieldset').last().offset().top + floatingIframe.parents('form').find('fieldset').last().outerHeight(true) - floatingIframe.outerHeight(true);
			
			if ( scrollTop > min && scrollTop < max)
			{
				floatingIframe.css({
					top: scrollTop - min
				});
				
				if (floatingIframeToggle.length)
				{
					floatingIframeToggle.css({
						top: scrollTop - min + 50
					});
				}
			}
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
	},
	
	//-----
	
	pages: function()
	{
		var $ = this.jQuery;
		
		// Tabbed Interface
		HUB.Groups.pagesTabs();
		
		// Reorder Pages
		HUB.Groups.pagesReorderPages();
		
		// Page Preview
		HUB.Groups.pagesPagePreview();
		
		// page category
		HUB.Groups.pagesEditPageCategory();
		
		// Page & module Search
		$('.group-page-manager').on('keyup', '.toolbar .search input', function(event) {
			var term = $(this).val(),
				list = $(this).parents('fieldset').find('ul.item-list').first();
			
			HUB.Groups.pagesFilterClear();
			HUB.Groups.pagesSearchList(term, list);
		});
		
		// Page & Module Filter
		if ($('.toolbar .filter select').length) 
		{
			$('.toolbar .filter select').HUBfancyselect({
				onSelected: function(t, d) {
					var filter = ($(this).attr('data-value')) ? $(this).attr('data-value') : '',
						list = $(this).parents('fieldset').find('ul.item-list').first();
					
					// build filter
					if (filter != '')
					{
						if (list.hasClass('pages'))
						{
							filter = 'category-' + filter;
						}
						else if (list.hasClass('modules'))
						{
							filter = 'position-' + filter;
						}
					}
					
					HUB.Groups.pagesSearchClear();
					HUB.Groups.pagesFilterList(filter, list);
				}
			});
			
			var filter = HUB.Groups._getUrlParam('filter');
			if (filter != null)
			{
				$('.toolbar .filter select').HUBfancyselect('selectValue', filter);
			}
		}
		
		// jquery case insensitve search
		jQuery.expr[':'].caseInsensitiveContains = function(a,i,m) {
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0; 
		};
	},
	
	//-----
	
	pagesEditPageCategory: function()
	{
		var $ = this.jQuery;
		
		if ($('.page-category').length)
		{
			$('.page-category').HUBfancyselect({
				onSelected: function(t, data) {
					var val = $(this).attr('data-value'),
						newCategory = '';
						
					if (val == 'other')
					{
						$.fancybox({
							type: 'ajax',
							href: $('.page-category').attr('data-url'),
							afterShow: function() {
								// instantiate color picker
								HUB.Groups.categoriesColorPicker();
								
								// submit of category
								$('form.editcategory').on('submit', function(event){
									event.preventDefault();
						
									// get title value
									var title = $(this).find('input[type=text]').first();
						
									// make sure we have title
									if (title.val() == '')
									{
										alert('Category must have title.');
										title.focus();
										return false;
									}
						
									// set the category
									newCategory = title.val();
						
									// submit form
									$.post($(this).attr('action'),$(this).serialize(), function(data) {
										$.fancybox.close();  
									});
								});
							},
							afterClose: function() {
								$('.page-category-label').load(window.location.href + ' .page-category-label > *', function(){
									HUB.Groups.pagesEditPageCategory();
									$('.page-category').HUBfancyselect('selectText', newCategory);
								});
							}
						});
					}
				}
			});
		}
	},
	
	//-----
	
	pagesSearchList: function(search, list)
	{
		var $ = this.jQuery;
		
		if (search)
		{
			list.addClass('filtered').find('.no-results').remove();
			list.find('.item-title:not(:caseInsensitiveContains("'+search+'"))').parents('li').hide();
			list.find('.item-title:caseInsensitiveContains("'+search+'")').parents('li').show();
			
			//add no results node
			if (list.find('li:visible').length == 0)
			{
				list.append("<li class=\"no-results\"><p>Sorry no items matching your search.</p></li>");
			}
		}
		else
		{
			list.find('li').show();
			list.removeClass('filtered').find('.no-results').remove();
		}
	},
	
	//-----
	
	pagesSearchClear: function()
	{
		var $ = this.jQuery;
		
		if ($('.toolbar:visible .filter select').val() != '')
		{
			$('.toolbar:visible .search input').val('');
		}
	},
	
	//-----
	
	pagesFilterList: function(filter, list)
	{
		var $ = this.jQuery;
		
		if (filter != '')
		{
			list.addClass('filtered').find('.no-results').remove();
			list.find('> li:not(.' + filter + ')').hide();
			list.find('.' + filter).show();
			
			//add no results node
			if (list.find('li:visible').length == 0)
			{
				list.append("<li class=\"no-results\"><p>Sorry no items matching your search.</p></li>");
			}
		}
		else
		{
			list.removeClass('filtered').find('.no-results').remove();
			list.find('li').show();
		}
	},
	
	//-----
	
	pagesFilterClear: function()
	{
		var $ = this.jQuery;
		
		if ($('.toolbar:visible .filter select').val() != '')
		{
			$('.toolbar:visible .filter select').HUBfancyselect('clear');
		}
	},
	
	//-----
	
	pagesTabs: function()
	{
		var $ = this.jQuery;
		
		// create tabbed interface
		$('.group-page-manager .tabs')
			.show()
			.tabs('.group-page-manager form > fieldset', {
				//history: true
			});
	},
	
	//-----
	
	pagesReorderPages: function()
	{
		var $ = this.jQuery;
		
		// sortable pages
		$('.group-page-manager .pages').sortable({
			axis: 'y',
			containment: $('.pages').parents('fieldset'),
			handle: '.order-grabber',
			items: ' > li',
			helper: 'clone',
			opacity: 0.8,
			revert: true,
			update: function(event, ui) {
				// get new order
				var sort = $(this).sortable('toArray');
				
				// show loader
				$('.pages').addClass('rebuilding');
				
				// ajax call to save page order
				$.ajax({
					url: $('.pages').attr('data-url'),
					type: 'post',
					dataType: 'json',
					data: {
						order: sort
					},
					success: function( data, status, jqXHR )
					{
						$('.pages').load(window.location.href + " .pages > *", function(){
							$('.pages').removeClass('rebuilding');
						});
					}
				});
			}
		});//.disableSelection();
	},
	
	//-----
	
	pagesPagePreview: function()
	{
		var $ = this.jQuery;
		
		// preview page lightbox
		$('.item-list .page-preview').fancybox({
			type: 'iframe',
			width: 1024
		});
	},
	
	//-----
	
	modules: function()
	{
		var $ = this.jQuery;
		
		$('#field-assignment').on('change', function(event) {
			event.preventDefault();
			
			//do we want on all pages
			if ($(this).val() == '0')
			{
				$('fieldset.assignment').attr('disabled', 'disabled');
				HUB.Groups.moduleMenuSelectAll();
			} 
			else
			{
				$('fieldset.assignment').removeAttr('disabled');
			}
		});
		
		$('#selectall').on('click', function(event){
			event.preventDefault();
			HUB.Groups.moduleMenuSelectAll();
		});
		
		$('#clearselection').on('click', function(event){
			event.preventDefault();
			HUB.Groups.moduleMenuClearSelection();
		});
	},
	
	//-----
	
	moduleMenuSelectAll: function()
	{
		var $ = this.jQuery;
		
		$('fieldset.assignment').find('input[type=checkbox]:not(:checked)').attr('checked','checked');
	},
	
	//-----
	
	moduleMenuClearSelection: function()
	{
		var $ = this.jQuery;
		
		$('fieldset.assignment').find('input[type=checkbox]:checked').removeAttr('checked');
	},
	
	//-----
	
	categories: function()
	{
		HUB.Groups.categoriesColorPicker();
	},
	
	//-----
	
	categoriesColorPicker: function()
	{
		var $ = this.jQuery;
		
		var categoryColorPicker = $('#field-category-color');
		
		if (categoryColorPicker.length)
		{
			// init color on input
			var color = '#' + categoryColorPicker.val();
			categoryColorPicker.css('border-color', color);
			
			// create color picker
			categoryColorPicker.colpick({
				layout: 'hex',
				submit: 0,
				onChange: function(hsb,hex,rgb,fromSetColor) {
					if(!fromSetColor)
						categoryColorPicker.val(hex).css('border-color','#' + hex);
				}
			});
		}
	},
	
	//-----
	
	_getUrlParam: function( paramName )
	{
		var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
		var match = window.location.search.match(reParam) ;
		return ( match && match.length > 1 ) ? match[ 1 ] : null ;
	},
}

//-----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Groups.initialize();
});

//-----------------------------------------------------------

jQuery(window).scroll(function($){
	HUB.Groups._moveScrollingAssetBrowser();
});