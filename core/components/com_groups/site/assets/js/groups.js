/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
		HUB.Groups.pageVersions();
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

		// confirms removal from group
		HUB.Groups.cancelMembership();
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

	cancelMembership: function()
	{
		var $ = this.jQuery;

		$('.cancel').on('click', function(){
			return confirm('Are you sure you want to leave this page?');
		});
	},

	//-----

	groupMenuFix: function()
	{
		var $ = this.jQuery;

		$("#page_menu li").each(function(index){
			var meta = $(this).find(".meta"),
				metawidth = meta.outerWidth(true),
				alrt = $(this).find(".alrt");

			if (alrt.length)
			{
				if (metawidth > 20)
				{
					alrt.css("right", 33+(metawidth-20));
				}
				else if (metawidth < 20 && metawidth != 0)
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

		if (invites.length)
		{
			invites.addClass("hide");
			content += invites.html();
		}

		if (requests.length)
		{
			requests.addClass("hide");
			content += requests.html();
		}

		if (content != "")
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


		if (toggleSwitch.length)
		{
			toggleSwitch.removeClass('hide');

			toggleSwitch.bind("click", function(e) {
				e.preventDefault();

				if (publicDesc.hasClass("hide"))
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

		if (logoSelecter.length)
		{
			$('#group-logo-label').on("change", "#group_logo", function(e) {

				if (this.value == "")
				{
					logo = '<img src="/core/components/com_groups/site/assets/img/group_default_logo.png" />';
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

		// fixed header
		HUB.Groups.pageManagerFixedToolbar();

		// Reorder Pages
		HUB.Groups.pagesReorderPages();

		// Page Preview
		HUB.Groups.pagesPagePreview();

		// action changer
		HUB.Groups.pagesEditPageActionChanger();

		// edit page order
		HUB.Groups.pagesEditPageOrder();

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

	pageManagerFixedToolbar: function()
	{
		var $ = this.jQuery;

		// make sure we have the page manager
		if (!$('.group-page-manager').length)
		{
			return;
		}

		// get toolbar position
		var toolbartop = $('.group-page-manager .tabs').offset().top;

		// force set width so looks good when position = fixed
		$('.group-page-manager .toolbar').width($('.group-page-manager fieldset:visible .toolbar').width());
		$('.group-page-manager .tabs').width($('.group-page-manager fi.tabs').width());

		// mark pinned after scroll distance
		$(window).scroll(function()
		{
			var scollpos = $(window).scrollTop();
			if (scollpos > toolbartop)
			{
				$('.group-page-manager').addClass('pinned');
			}
			else
			{
				$('.group-page-manager').removeClass('pinned');
			}
		});

		// handle window resize
		$(window).resize(function()
		{
			$('.group-page-manager .toolbar').width($('.group-page-manager').width() - 2);
			$('.group-page-manager .tabs').width($('.group-page-manager').width() - 2);
		});
	},

	//-----

	pagesEditPageActionChanger: function()
	{
		var $ = this.jQuery;

		// only continue if on edit group page form
		if (!$('.edit-group-page .form-controls').length)
		{
			return;
		}

		// allow save & apply actions
		$('.form-controls .dropdown-menu a').on('click', function(event) {
			event.preventDefault();

			// store the action for the user
			try {
				if (localStorage) {
					localStorage.setItem('groups.pagemanager.action', $(this).attr('data-action'));
				}
			}
			catch(e) {
				// ignore any local storage-related exceptions
			}

			// change which is active
			$('.form-controls .dropdown-menu a').removeClass('active');
			$(this).addClass('active');

			// update button text
			var button = $(this).parents('.form-controls').find('.btn-main');
			button.html($(this).html());

			// update hidden form input
			if ($(this).attr('data-action') == 'apply')
			{
				button.removeClass('icon-save').addClass('icon-apply');
				$('input[name="task"]').val('apply');
			}
			else
			{
				button.removeClass('icon-apply').addClass('icon-save');
				$('input[name="task"]').val('save');
			}
		});

		// pre-fill last choice
		try {
			if (localStorage && localStorage.getItem('groups.pagemanager.action')) {
				var action = localStorage.getItem('groups.pagemanager.action');
				var item = $('.form-controls .dropdown-menu a[data-action="' + action + '"]');
				item.trigger('click');
			}
		}
		catch(e) {
			// ignore any local storage-related exceptions
		}
	},

	//-----

	pagesEditPageOrder: function()
	{
		var $ = this.jQuery;

		if (!$('.page-parent').length)
		{
			return;
		}

		if ($('.page-ordering').length)
		{
			var fs = $('.page-ordering').data('fancyselect');
			var ordering = $('#fs-dropdown-' + fs.id);

			// manually create fancy select on parent selector to handle onSelected
			var parent = $('.page-parent').HUBfancyselect({
				onSelected: function(t, data)
				{
					ordering.find('.fs-dropdown-options-container li').addClass('hide');
					ordering.find('.fs-dropdown-options-container a[data-parent="'+data.value+'"]').parents('li').removeClass('hide');

					// auto select first option if our selected option is no longer available
					if (ordering.find('.fs-dropdown-options-container .fs-dropdown-option-selected').hasClass('hide'))
					{
						ordering.find('.fs-dropdown-options-container li:not(.hide) a').last().trigger('click');
					}
				}
			});

			// auto-select
			var parentId = parent.data('fancyselect').id;
			var text = $('#fs-dropdown-' + parentId).find('.fs-dropdown-option-selected a').first().text();
			$('.page-parent').HUBfancyselect('selectText', text);
		}
		else
		{
			$('.page-parent').HUBfancyselect();
		}
	},

	//-----

	pagesEditPageCategory: function()
	{
		var $ = this.jQuery;

		if ($('.page-category').length)
		{
			var currentCategory = $('.page-category').val();

			$('.page-category').HUBfancyselect({
				onSelected: function(t, data)
				{
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

								$('.cancel').on('click', function(event) {
									event.preventDefault();
									$.fancybox.close();
								});
							},
							afterClose: function() {
								if (newCategory != '')
								{
									$('.page-category-label').load(window.location.href + ' .page-category-label > *', function(){
										HUB.Groups.pagesEditPageCategory();
										$('.page-category').HUBfancyselect('selectText', newCategory);
									});
								}
								else
								{
									$('.page-category').HUBfancyselect('selectValue', currentCategory);
								}
							}
						});
					}
					else
					{
						currentCategory = val;
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
			$('.group-page-manager').addClass('searching');

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
			$('.group-page-manager').removeClass('searching');

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
			$('.group-page-manager').addClass('filtering');

			list.addClass('filtered').find('.no-results').remove();
			list.find('.item-container:not(.' + filter + ')').parents('li').hide();
			list.find('.item-container.' + filter).parents('li').show();

			//add no results node
			if (list.find('li:visible').length == 0)
			{
				list.append("<li class=\"no-results\"><p>Sorry no items matching your search.</p></li>");
			}
		}
		else
		{
			$('.group-page-manager').removeClass('filtering');

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

		if (!$('.group-page-manager').length)
		{
			return;
		}

		// show tabs & hide sections
		$('.group-page-manager .tabs').show();
		$('.group-page-manager form > fieldset').hide();

		//get active tab
		var activeTab = window.location.hash.replace('#', '');

		// make sure we have active tab and its valid
		if (activeTab == '')
		{
			activeTab = 'pages';
			window.location.hash = activeTab;
		}

		// listen for hash change event
		$(window).on('hashchange', function() {
			activeTab = window.location.hash.replace('#', '');
			$('.group-page-manager .tabs a').removeClass('current');
			$('.group-page-manager form > fieldset').hide();
			$('.group-page-manager .tabs a[data-tab="' + activeTab + '"]').addClass('current');
			$('.group-page-manager form > fieldset[data-tab-content="' + activeTab + '"]').show();
		});

		// trigger hash change
		$(window).trigger('hashchange');
	},

	//-----

	pagesReorderPages: function()
	{
		var $ = this.jQuery;

		// only do if we have page manager
		if (!$('.group-page-manager').length)
		{
			return;
		}

		// nested sortable page manager
		$('.group-page-manager .pages').nestedSortable({
            handle: '.item-mover',
            cursor: "move",
            items: 'li',
            listType: 'ul',
            helper: 'original',
            opacity: 0.8,
            revert: true,
            tolerance: 'pointer',
            tabSize: 35,
            protectRoot: true,
            scrollSensitivity: 100,
            maxLevels: $('.item-list.pages').attr('data-max-depth'),
            create: function()
            {
            	// save on initial load just to make sure we have everything setup correctly
            	HUB.Groups.pagesReorderPagesSave(false);
            },
            start: function(e, ui)
            {
        		ui.placeholder.height(ui.item.height());
    		},
            update: function()
            {
				$(window).on('beforeunload', function(){
					return 'The page order has not been saved. All changes will be lost if you dont save before leaving.';
				});

            	var orderButtons = $('<div class="page-order-actions"><button class="btn btn-info icon-save save-page-order">Save Order</button><button class="btn icon-ban-circle reset-page-order">Reset Order</button></div>');
            	$('.group-page-manager .tabs').append(orderButtons);
            }
        });

        $('.group-page-manager').on('click', '.save-page-order', function(event) {
        	event.preventDefault();
        	$(this).attr('disabled', 'disabled');
        	HUB.Groups.pagesReorderPagesSave(true);
        });

        $('.group-page-manager').on('click', '.reset-page-order', function(event) {
        	event.preventDefault();
        	$(this).attr('disabled', 'disabled');
        	HUB.Groups.pagesReorderPagesReset();
        });
	},

	//-----

	pagesReorderPagesSave: function(showRebuilding)
	{
		// get new order
		var sort = $('.group-page-manager .pages').nestedSortable('toArray', {
			excludeRoot: true,
		});

		// show loader
		if (showRebuilding)
		{
			$('.pages').addClass('rebuilding');
		}

		//ajax call to save page order
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
					$('.page-order-actions').fadeOut("slow", function() {
						$(this).remove();
					});
				});
				$(window).off('beforeunload');
			}
		});
	},

	//---

	pagesReorderPagesReset: function()
	{
		$('.pages').load(window.location.href + " .pages > *", function(){
			$('.page-order-actions').fadeOut("slow", function() {
				$(this).remove();
			});
		});

		$(window).off('beforeunload');
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

	pageVersions: function()
	{
		var $ = this.jQuery;
		var $versions = $('.versions');

		// make sure we have the version manager
		if (!$('.version-manager').length)
		{
			return;
		}

		// init cycle on versions
		$versions.cycle({
			log: false,
			slides: '> .version',
			fx: 'fade',
			paused: true,
			allowWrap: false,
			prev: '.version-prev',
			next: '.version-next',
			caption: '.version-title',
			captionTemplate: '{{title}}',
			autoHeight: 'container',
			startingSlide: ($('.versions .version').length - 1),
		});

		// jump between versions
		$('.version-jumpto').on('change', function(event) {
			$versions.cycle('goto', ($(this).val() - 1));
		});

		// update raw/restore button links
		// on initialize & after slide transition
		$versions
			.on('cycle-update-view', function(event, optionHash, slideOptionsHash, currentSlideEl) {
				$('.version-raw').attr('href', $(currentSlideEl).attr('data-raw-url'));
				$('.version-restore').attr('href', $(currentSlideEl).attr('data-restore-url'));

				if ($('.version-restore').attr('href') == '')
				{
					$('.version-restore').attr('disabled', 'disabled');
				}
				else
				{
					$('.version-restore').removeAttr('disabled');
				}

				// sync jumpto select
				$('.version-jumpto').val(optionHash.currSlide + 1);
			});

		// resize after a second to make sure we have properly sized for images
		setTimeout(function() {
			HUB.Groups.pageVersionMangerResize();
		}, 1000);

		// init source mode & fixed toolbar
		HUB.Groups.pageVersionMangerActions();
		HUB.Groups.pageVersionManagerFixedToolbar();
	},

	pageVersionMangerActions: function()
	{
		var $ = this.jQuery;

		// switch to source
		$('.version-source').on('click', function(event){
			event.preventDefault();
			$(this).toggleClass('active');
			$('.version-content').toggle();
			$('.version-code').toggle();

			HUB.Groups.pageVersionMangerResize();
		});

		$('.version-meta').on('click', function(event){
			event.preventDefault();
			$(this).toggleClass('active');
			$('.version-metadata').slideToggle(function(){
				HUB.Groups.pageVersionMangerResize();
			});
		});
	},

	//-----

	pageVersionManagerFixedToolbar: function()
	{
		var $ = this.jQuery;

		if (!$('.version-manager').length)
		{
			return;
		}

		// fixed toolbar
		var toolbartop = $('.toolbar').offset().top;
		$('.toolbar').width($('.toolbar').width());
		$(window).scroll(function() {
			var scollpos = $(window).scrollTop();
			if (scollpos > toolbartop)
			{
				$('.version-manager').addClass('pinned');
			}
			else
			{
				$('.version-manager').removeClass('pinned');
			}
		});
	},

	pageVersionMangerResize: function()
	{
		$('.versions').animate({
			height: $('.cycle-slide-active').outerHeight()
		}, 250);
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
				onChange: function(hsb,hex,rgb,el,fromSetColor) {
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
		var el = $('#page_menu');
	if (el.length) {
		el.addClass('js');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : 'Select ...' //el.attr('data-label')
		}).appendTo(select);

		el.find("a").each(function() {
			var elm = $(this),
				prfx = '';

			if (elm.hasClass('alrt')) {
				return;
			}

			if ($(elm.parent().parent()).hasClass('tab-options')) {
				prfx = '- ';
			}

			var opts = {
				"value"   : elm.attr("href"),
				"text"    : prfx + elm.text()
			};
			if ($(elm.parent()).hasClass('active')) {
				opts.selected = 'selected';
			}
			$("<option />", opts).appendTo(select);
		});

		var li = $("<li />").addClass('option-select');

		select.appendTo(li);
		li.appendTo(el);
	}

	HUB.Groups.initialize();
});

//-----------------------------------------------------------

jQuery(window).scroll(function($){
	HUB.Groups._moveScrollingAssetBrowser();
});