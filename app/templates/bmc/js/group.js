/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}


HUB.template = {};

jQuery(document).ready(function(jq){
	var $ = jq;

	// group pane toggle
	$('#group a.toggle, #group-info .close').on('click', function(event) {
		event.preventDefault();

		$('#group-info').slideToggle('normal');
		$('#group-body').toggleClass('opened');
	});

	// Template
	HUB.template.body = $('body');

	// Account panel
	HUB.template.accountTrigger = $('.user-account-link.loggedin');
	HUB.template.accountPanel = $('.account-details');
	HUB.template.accountCloseTrigger = HUB.template.accountPanel.find('.close');

	$(HUB.template.accountTrigger).on('click', function(e) {
		if(!(HUB.template.accountPanel.hasClass('open'))) {
			HUB.template.closeAllPanels();
			HUB.template.openAccountPanel();
		}
		else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$(HUB.template.accountCloseTrigger).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	HUB.template.openAccountPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.accountPanel.addClass('open');
	};

	HUB.template.closeAccountPanel = function() {
		HUB.template.accountPanel.removeClass('open');
	};

	// Member dash
	HUB.template.dashTrigger = $('.subnav-membership > .toggle');
	HUB.template.dashPanel = $('.group-dash');
	HUB.template.dashCloseTrigger = HUB.template.dashPanel.find('.close');

	$(HUB.template.dashTrigger).on('click', function(e) {
		if(!(HUB.template.dashPanel.hasClass('open'))) {
			HUB.template.closeAllPanels();
			HUB.template.openDashPanel();
		}
		else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$(HUB.template.dashCloseTrigger).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	HUB.template.openDashPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.dashPanel.addClass('open');
	};

	HUB.template.closeDashPanel = function() {
		HUB.template.dashPanel.removeClass('open');
	};

	// Escape button to the rescue for those who like to press it in a hope to close whatever is open
	$(document).keyup(function(e) {
		if(e.keyCode == 27) {
			HUB.template.closeAllPanels();
		}
	});

	HUB.template.closeAllPanels = function() {
		HUB.template.closeAccountPanel();
		HUB.template.closeDashPanel();
		HUB.template.body.removeClass('panel-open');
	};

	HUB.template.overlay = $('.hub-overlay');
	$(HUB.template.overlay).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	$(window).resize(function() {
		HUB.template.closeAllPanels();
	});

	HUB.template.init = function() {
	};

	HUB.template.init();

	//Super group JS fixes - So you don't have to retroactively fix each supergroup

	//Sidebar fix
	/* Fix content for sticky group announcements and respond to resizing of menu. */
	var $scontainer = $(".scontainer");
	var $menuWrap = $(".super-group-menu-wrap");
	var $contentWrap = $(".super-group-content-wrap");
	var $sidebarWrap = $("#sidebar-wrapper");
	var $moreMenu = $('.more-menu');
	var $sidebarNav = $('.sidebar-nav');
	var $moreLinks = $('.more-links');
	var $scontainerHeight = 0;

	// Make sure content is at least as large as sidebar size.
	$contentWrap.css("min-height", $sidebarWrap.outerHeight(true) + "px");

	$scontainer.css("margin-top", $menuWrap.css("height"));
	$contentWrap.css("margin-top", $scontainer.css("height"));
  	$menuWrap.css("margin-bottom", -1 * parseFloat($menuWrap.height()) + "px");
  	$contentWrap.css("padding-top", $menuWrap.height());
	new ResizeSensor(jQuery('.super-group-menu-wrap'), function() {
		$scontainer.css("margin-top", $menuWrap.css("height"));
		$contentWrap.css("margin-top", $scontainer.css("height"));
	  	$menuWrap.css("margin-bottom", -1 * parseFloat($menuWrap.height()) + "px");
	  	$contentWrap.css("padding-top", $menuWrap.height());
	});

	/* Smoothly readjust content after closing of announcements */
	$('.announcement .close').on('click', function() {
		$contentWrap.animate({marginTop: '-=' + $(this).parent().parent().outerHeight() + 'px'});
	});

	/* Sticky navbar */
	/* https://teamtreehouse.com/community/forum-tip-create-a-sticky-navigation-with-css-and-jquery-2 */
	//
	// Bug in login - had to comment out the following line
	//		$('#username, #password').placeholder();
	// in the file /www/dev/core/components/com_users/site/assets/js/login.js
	// to get it to work.

	var $headerId = $(".header-id");
	var $footerWrap = $(".super-group-footer-wrap");
	var poweredBy = document.getElementsByClassName("poweredby")[0];

	var scrollTop = 0;
	var bannerHeight = $(".super-group-header-overlay").height();
	var barHeight = $(".super-group-bar").height();

	var windowTop = 0;
	var startBarFade = 0;
	var pushAndPullSidebar = false;
	pushingDown = pushingUp = false;

	$(window).on("resize scroll", function() {
		windowTop = $(this).scrollTop();

		if (windowTop > scrollTop) {
			scrollingDown = true;
		} else {
			scrollingDown = false;
		}
		scrollTop = windowTop;

		// Fade effect for "poweredby QUBES"
		startBarFade = bannerHeight - barHeight;
		if (windowTop > startBarFade) {
			poweredBy.style["opacity"] = Math.max(1 - (4/startBarFade)*(windowTop-startBarFade), 0);
			poweredBy.style["cursor"] = "default";
			poweredBy.style["pointerEvents"] = "none";
		} else {
			poweredBy.style["opacity"] = 1.0;
			poweredBy.style["cursor"] = "inherit";	// Doesn't reset properly on Firefox
			poweredBy.style["pointerEvents"] = "inherit";
		}

		// Replace "poweredby QUBES" with group logo and title
		if (windowTop > bannerHeight - (barHeight/2)) {
			$headerId.addClass("header-id-scrolled");

		} else {
			$headerId.removeClass("header-id-scrolled");
		}

		if ($headerId.hasClass('header-id-scrolled') && $moreMenu.is(':visible')) {
			$('.header-id > a > span').addClass('hide');
		} else {
			$('.header-id > a > span').removeClass('hide');
		}

		// Fix menu directly under QUBES navbar
		if (windowTop > bannerHeight) {
			$menuWrap.addClass("super-group-menu-scrolled");
		} else {
			$menuWrap.removeClass("super-group-menu-scrolled");
		}

		// Negative padding on $menuWrap is a headache!
		// Fix sidebar directly under menu after announcements
		if ($scontainer.length) {
			$scontainerHeight = $scontainer.height();
		} else {
			// do nothing
		}
		if (windowTop < bannerHeight + $scontainerHeight && $moreMenu.is(":hidden")) {
			$sidebarWrap.css("top","");
			$sidebarWrap.removeClass();
		} else {
			pushAndPullSidebar = ($sidebarWrap.outerHeight(true) > ((windowTop + $(window).height()) - ($menuWrap.offset().top + $menuWrap.height())));

			if (!pushAndPullSidebar && $moreMenu.is(':hidden')) {
				$sidebarWrap.removeClass("sidebar-wrapper-fixed-bottom sidebar-wrapper-footer").addClass("sidebar-wrapper-fixed-top");
			} else {
				pushingUp = !scrollingDown && (($sidebarWrap.offset().top + 1) >
					(parseFloat($sidebarWrap.css("margin-top")) + $menuWrap.offset().top + $menuWrap.height()));
				pushingDown = scrollingDown && (($sidebarWrap.offset().top + $sidebarWrap.outerHeight(true) - 1) <
					(windowTop + $(this).height()))

				if (pushingDown) {
					if (scrollingDown && $moreMenu.is(':hidden')) {
						$sidebarWrap.removeClass("sidebar-wrapper-fixed-top sidebar-wrapper-footer").addClass("sidebar-wrapper-fixed-bottom");
					}
				} else {
					if ((!scrollingDown) && ($sidebarWrap.hasClass("sidebar-wrapper-fixed-bottom")) && ($moreMenu.is(":hidden"))) {
						$sidebarWrap.removeClass();
						$sidebarWrap.css("top", 44 - $sidebarWrap.outerHeight(true) + $(this).height() - 85 - 25 + windowTop + parseFloat($contentWrap.css("padding-top")) - $contentWrap.offset().top + "px");
					}
				}

				if (pushingUp) {
					if (!scrollingDown && $moreMenu.is(':hidden')) {
						$sidebarWrap.removeClass("sidebar-wrapper-fixed-bottom sidebar-wrapper-footer").addClass("sidebar-wrapper-fixed-top");
					}
				} else {
					if ((scrollingDown) && ($sidebarWrap.hasClass("sidebar-wrapper-fixed-top")) && ($moreMenu.is(":hidden"))) {
						$sidebarWrap.removeClass();
						$sidebarWrap.css("top", 42 + windowTop + parseFloat($contentWrap.css("padding-top")) - $contentWrap.offset().top + "px");
					}
				}
			}

			// Put sidebar at bottom when footer starts to encroach and scroll with page
			if (((pushAndPullSidebar && (windowTop + $(this).height() > $footerWrap.offset().top + 41 - 13)) ||
			   (!pushAndPullSidebar && ($sidebarWrap.offset().top + $sidebarWrap.height() + parseFloat($sidebarWrap.css("margin-bottom")) > $footerWrap.offset().top))) && $moreMenu.is(':hidden')) {
				$sidebarWrap.removeClass();
				$sidebarWrap.addClass("sidebar-wrapper-footer");
			}
			if (($sidebarWrap.css('position') == 'fixed')) {
				$sidebarWrap.removeClass('sidebar-wrapper-footer');
			}
		}
	});
});
