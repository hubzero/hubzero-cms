/**
 * @package     hubzero-cms
 * @file        templates/hubbasic2013/js/hub.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//  Create our namespace
if (typeof HUB === "undefined") {
	var HUB = {};
}
HUB.Base = {};
HUB.template = {};

// Fallback support for browsers that don't have console.log
if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

// Support for jQuery noConflict mode
if (!jq) {
	var jq = $;
}

(function(TPL, $, undefined) {
	TPL.win = {};
	TPL.screenSize = null;
	TPL.page = $(this);
	TPL.status = 'none';

	var body;

	// Sticky header vars
	var scrolled = false;
	var lastScrollVal = 0;
	var page;
	var nav;
	var sub;

	// Search panel show/hide
	var searchButton;
	var searchPanel;
	var searchPanelCloseButton;

	// Dashboard panel show/hide
	var dashboardButton;
	var dashboardPanel;
	var dashboardScroller;
	var dashboardCloseButton;

	// Mobile search
	var mobileSearchButton;

	// Breadcrumbs
	var secondaryNav;
	var breadcrumbs;
	var breadcrumbsTrigger;

	// Navigation
	var mainNav;
	var aux;

	// Other header stuff
	var headerMain;

	// No other way I can think of -- shame on me -- manually update this value -- brand's width, including margin.
	var brandW = 196;

	// Mobile navigation
	var mobileNavButton;
	var mobileNav;
	var mobileNavClose;

	var updateWin = function() {
		var wh = $(window).height();
		var ww = $(window).width();

		var smallScreen = 800;
		var largeScreen = 961;
		var extraLargeScreen = 1201;

		TPL.win.h = wh; // New height
		TPL.win.w = ww; // New width

		body.removeClass('slim');
		nav.removeClass('slim');

		// Size-specific logic
		if(TPL.win.w >= extraLargeScreen) {
			TPL.screenSize = 'xl';
		}
		else if(TPL.win.w >= largeScreen) {
			TPL.screenSize = 'l';
		}
		else if(TPL.win.w >= smallScreen) {
			TPL.screenSize = 'm';
		}
		else {
			body.addClass('slim');
			nav.addClass('slim');
			TPL.screenSize = 's';
		}
	};

	// Check if the secondary nav and breadcrumbs collide -- if so force the crumbs to collapse
	var checkBreadcrumbs = function() {
		var breadWrap = $('.breadcrumbs-wrap');

		if(breadWrap.length > 0) {
			var secondaryNavRightEdge = secondaryNav.offset().left + secondaryNav.outerWidth();
			if (secondaryNav.hasClass('empty')) {
				secondaryNavRightEdge = secondaryNav.offset().left - parseInt(secondaryNav.css('margin-left'));
			}
			var breadCrumbsLeftEdge = breadWrap.offset().left;
			// Allow some space between the nav and the logo
			var breadcrumbsGap = 10;

			if ((secondaryNavRightEdge + breadcrumbsGap) > breadCrumbsLeftEdge) {
				breadcrumbs.addClass('collides');
				hideBreadcrumbs();
			}
			else {
				breadcrumbs.removeClass('collides');
				showBreadcrumbs();
			}
		}
	};

	var showBreadcrumbs = function() {
		breadcrumbs.removeClass('collapsed');
		if (breadcrumbs.hasClass('collides')) {
			secondaryNav.addClass('hide');
		}
	};

	var hideBreadcrumbs = function() {
		if (breadcrumbs.hasClass('collides')) {
			breadcrumbs.addClass('collapsed');
			secondaryNav.removeClass('hide');
		}
	};

	var hideSearch = function() {
		searchPanel.removeClass('show');
	};

	// Check if the header too small
	var checkNav = function() {
		var mainNavRightEdge = mainNav.offset().left + mainNav.outerWidth();
		var auxLeftEdge = aux.offset().left;
		// Allow some space between
		var navGap = 30;

		var totalW = mainNav.outerWidth() + aux.outerWidth() + brandW + navGap;

		var containerW = headerMain.width();

		if (totalW > containerW) {
			body.addClass('mobile');
			nav.addClass('mobile');
		}
		else {
			body.removeClass('mobile');
			nav.removeClass('mobile');
		}
	};


	TPL.init = function() {
		body = $('body');

		// Sticky header vars
		page = $('body > .wrap > .content-panel');
		nav = $('header.page');
		sub = nav.find('.sub');

		// Search panel show/hide
		searchButton = $('header.page .buttons .search');
		searchPanel = $('header.page .search-panel');
		searchPanelCloseButton = searchPanel.find('.close');

		// Dashboard panel show/hide
		dashboardButton = $('header.page .buttons .dashboard, button.dashboard');
		dashboardPanel = $('.dashboard-panel');
		dashboardScroller = $('.dashboard-panel-inner .scroller');
		dashboardCloseButton = dashboardPanel.find('.close');

		// Mobile search
		mobileSearchButton = $('button.search');

		// Breadcrumbs
		secondaryNav = $('header.page > .sub > nav');
		breadcrumbs = $('.breadcrumbs');
		breadcrumbsTrigger = breadcrumbs.find('.icon');

		// Navigation
		mainNav = $('header.page nav.nav-primary');
		aux = $('header.page .aux');

		// Other header stuff
		headerMain = $('header.page .main');

		// Mobile navigation
		mobileNavButton = $('.mobile-menu');
		mobileNav = $('.mobile-panel');
		mobileNavClose = $('.mobile-panel .close');

		TPL.status = 'initialized';

		// Display the correct subnav
		// get the subnav
		var subnavId = body.data('subnav');
		secondaryNav.removeClass('empty');

		if(subnavId && subnavId != 'none') {
			// find the subnav parent
			var subnavParent = $('nav.nav-primary > ul').children("[data-alias='" + subnavId + "']");

			if(subnavParent.length > 0) {
				var subnavContent = subnavParent.children('ul');

				// add the subnav
				var subnavContainer = nav.find('.sub nav');
				if(subnavContainer.length > 0) {
					//subnavContent.prependTo(subnavContainer);
					subnavContainer.html(subnavContent);

					// set the parent to 'active'
					subnavParent.addClass('active');
				}
			}
		}
		else {
			var subnavContainer = nav.find('.sub nav');

			// see if there is a 'active' parent
			var subnavActiveParent = nav.find('.nav-primary .parent.active');
			if(subnavActiveParent.length > 0) {
				var subnavContent = subnavActiveParent.children('ul');

				// add the subnav
				if(subnavContainer.length > 0) {
					//subnavContent.prependTo(subnavContainer);
					subnavContainer.html(subnavContent);
				}
			}
			else {
				// see if there is an active item in the subnav
				var subnavActiveLink = nav.find('.nav-primary .active');
				var subnavParent = subnavActiveLink.closest('.parent');

				if(subnavParent.length > 0) {
					var subnavContent = subnavParent.children('ul');
					subnavParent.addClass('active');

					//subnavContent.prependTo(subnavContainer);
					subnavContainer.html(subnavContent);
				}
				else {
					secondaryNav.addClass('empty');
				}
			}
		}

		// Resize routine
		TPL.resize();

		$(window).resize(function() {
			TPL.resize();
		});

		// ---------------------------------
		// Sticky header business

		$(page).scroll(function(e) {
			scrolled = true;
		});

		setInterval(function() {
			if (scrolled) {
				TPL.handleScroll(true);
				scrolled = false;
			}
		}, 250);
		// ---------- end sticky header --------


		// ---------------------------------
		// Search panel

		if(searchButton.length > 0) {
			searchButton.on('click', function (e) {
				if (!(searchPanel.hasClass('show'))) {
					searchPanel.addClass('show');
				}
				else {
					hideSearch();
				}

				e.preventDefault();
			});
		}

		if(searchPanelCloseButton.length > 0) {
			searchPanelCloseButton.on('click', function (e) {
				hideSearch();

				e.preventDefault();
			});
		}
		// ---------- end search panel --------

		// ---------------------------------
		// Mobile navigation panel

		if(mobileNavButton.length > 0 && mobileNav.length > 0) {
			mobileNavButton.on('click', function (e) {
				if (!(mobileNav.hasClass('show'))) {
					showMobileNavigation();
				}
				else {
					hideMobileNavigation();
				}

				e.preventDefault();
			});
		}

		if(mobileNavClose.length > 0) {
			mobileNavClose.on('click', function (e) {
				hideMobileNavigation();

				e.preventDefault();
			});
		}

		var showMobileNavigation = function() {
			mobileNav.addClass('show');
			mobileNav.addClass('menu');
		};

		var hideMobileNavigation = function() {
			mobileNav.removeClass('show');
			mobileNav.removeClass('menu');
		};

		// ---------- end mobile navigation panel --------

		// ---------------------------------
		// Mobile search panel

		if(mobileSearchButton.length > 0 && mobileNav.length > 0) {
			mobileSearchButton.on('click', function (e) {
				if (!(mobileNav.hasClass('show'))) {
					showMobileSearch();
				}
				else {
					hideMobileNavigation();
				}

				e.preventDefault();
			});
		}

		var showMobileSearch = function() {
			mobileNav.addClass('show');
		};

		var hideMobileSearch = function() {
			mobileNav.removeClass('show');
		};

		// ---------- end mobile search panel --------

		// ---------------------------------
		// Dashboard panel

		var showDashboard = function() {
			dashboardPanel.addClass('show');
			body.addClass('dashboard-show');

			dashboardPanel.on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',
				function(e) {
					TPL.resize();
				});
		};

		var hideDashboard = function() {
			dashboardPanel.removeClass('show');
			body.removeClass('dashboard-show');

			dashboardPanel.on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',
				function(e) {
					TPL.resize();
				});
		};

		if(dashboardButton.length > 0) {
			dashboardButton.on('click', function (e) {
				if(!$(this).hasClass('loggedin')) {
					window.location = '/login';
					return;
				}

				if (!(dashboardPanel.hasClass('show'))) {
					showDashboard();
				}
				else {
					hideDashboard();
				}

				e.preventDefault();
			});
		}

		if(mobileNavClose.length > 0) {
			dashboardCloseButton.on('click', function (e) {
				hideDashboard();

				e.preventDefault();
			});
		}

		// I'm not sure how well it works, test it
		dashboardScroller.on( 'mousewheel DOMMouseScroll', function (e) {

			var e0 = e.originalEvent;
			var delta = e0.wheelDelta || -e0.detail;

			this.scrollTop += delta * -1;
			e.preventDefault();
		});

		// swipe
		var dp = document.getElementById('dashboard-panel');
		Hammer(dp).on("swiperight", function() {
			hideDashboard();
		});


		// ---------- end dashboard panel --------


		// ---------------------------------
		// Breadcrumbs

		if(breadcrumbsTrigger.length > 0) {
			breadcrumbsTrigger.on('click', function (e) {
				if (!(breadcrumbs.hasClass('collapsed'))) {
					hideBreadcrumbs();
				}
				else {
					showBreadcrumbs();
				}

				e.preventDefault();
			});
		}

		// ---------- end breadcrumbs --------

		// header logo
		$('header.page .brand a').click(function(e) {
			if(nav.hasClass('mobile')) {
				showMobileNavigation();
				e.preventDefault();
			}
		});
	};

	TPL.resize = function(auto) {
		updateWin();
		checkBreadcrumbs();
		checkNav();
	};

	TPL.handleScroll = function(check) {
		var scrollVal = page.scrollTop();
		var navHeight = nav.outerHeight();

		if(check && Math.abs(lastScrollVal - scrollVal) <= 5)
		{
			return;
		}

		hideBreadcrumbs();
		//hideSearch();

		if (scrollVal > 10) {
			nav.addClass('skinny');
		}
		else {
			nav.removeClass('skinny');
		}

		if (scrollVal > lastScrollVal) {
			// Scroll Down
			if(scrollVal > (50)) {
				sub.addClass('out');
			}
		}
		else {
			// Scroll Up
			sub.removeClass('out');
		}

		lastScrollVal = scrollVal;
	};

}( window.TPL = window.TPL || {}, jQuery ));

// Let's get down to business...
jQuery(document).ready(function(jq) {
	var $ = jq,
		w = 760,
		h = 520,
		templatepath = '/templates/bmc/';

	// Set focus on username field for login form
	if ($('#username').length > 0) {
		$('#username').focus();
	}

	// Turn links with specific classes into popups
	$('a').each(function(i, trigger) {
		if ($(trigger).is('.demo, .popinfo, .popup, .breeze')) {
			$(trigger).on('click', function (e) {
				e.preventDefault();

				if ($(this).attr('class')) {
					var sizeString = $(this).attr('class').split(' ').pop();
					if (sizeString && sizeString.match(/\d+x\d+/)) {
						var sizeTokens = sizeString.split('x');
						w = parseInt(sizeTokens[0]);
						h = parseInt(sizeTokens[1]);
					} else if (sizeString && sizeString == 'fullxfull') {
						w = screen.width;
						h = screen.height;
					}
				}

				window.open($(this).attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
			});
		}
		if ($(trigger).attr('rel') && $(trigger).attr('rel').indexOf('external') !=- 1) {
			$(trigger).attr('target', '_blank');
		}
	});

	if (jQuery.fancybox) {
		// Set the overlay trigger for launch tool links
		$('.launchtool').on('click', function(e) {
			$.fancybox({
				closeBtn: false,
				href: templatepath + 'images/anim/circling-ball-loading.gif'
			});
		});

		// Set overlays for lightboxed elements
		$('a[rel=lightbox]').fancybox();
	}

	// Init tooltips
	if (jQuery.ui && jQuery.ui.tooltip) {
		$(document).tooltip({
			items: '.hasTip, .tooltips',
			position: {
				my: 'center bottom',
				at: 'center top'
			},
			// When moving between hovering over many elements quickly, the tooltip will jump around
			// because it can't start animating the fade in of the new tip until the old tip is
			// done. Solution is to disable one of the animations.
			hide: false,
			content: function () {
				var tip = $(this),
					tipText = tip.attr('title');

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip.attr('title', parts[1]);
				}
				return $(this).attr('title');
			},
			tooltipClass: 'tooltip'
		});

		// Init fixed position DOM: tooltips
		$('.fixedToolTip').tooltip({
			relative: true
		});
	}

	//test for placeholder support
	var test = document.createElement('input'),
		placeholder_supported = ('placeholder' in test);

	//if we dont have placeholder support mimic it with focus and blur events
	if (!placeholder_supported) {
		$('input[type=text]:not(.no-legacy-placeholder-support)').each(function(i, el) {
			var placeholderText = $(el).attr('placeholder');

			//make sure we have placeholder text
			if (placeholderText != '' && placeholderText != null) {
				//add plceholder text and class
				if ($(el).val() == '') {
					$(el).addClass('placeholder-support').val(placeholderText);
				}

				//attach event listeners to input
				$(el)
					.on('focus', function() {
						if ($(el).val() == placeholderText) {
							$(el).removeClass('placeholder-support').val('');
						}
					})
					.on('blur', function(){
						if ($(el).val() == '') {
							$(el).addClass('placeholder-support').val(placeholderText);
						}
					});
			}
		});

		$('form').on('submit', function(event){
			$('.placeholder-support').each(function (i, el) {
				$(this).val('');
			});
		});
	}

	// ******************************************************************************************************
	// Template
	// ******************************************************************************************************

	TPL.init();
});

$(window).load(function() {

});
