/////////////////////////////////////////////////////////////////
//
//	Developers:
//
//	Please Be Mindful of javascript library being used within
//	hub (if any). Use no conflict mode to avoid library
//	compatibility. Native JS is always allowed.
//
/////////////////////////////////////////////////////////////////

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		el = $('.super-group-menu>ul');

	// For responsive (e.g., mobile) menus
	//
	// This runs through the menu and generates a
	// <select> list of all menu items, then adds
	// an onChange event to redirect the page to
	// whatever option is selected.
	if (el.length) {
		el.addClass('js');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : "Select ..." //el.attr('data-label')
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

	/*
		Enter custom JS code here.
	*/

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
			console.log($scontainerHeight);
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

	// Sidebar responsiveness
	// Copy each link to the mobile menu
	$('.sidebar-nav > li').each(function() {
		$(this).clone().appendTo('.more-links');
	});

	function mobileNav() {
		// Media query triggered and window is mobile width
		if ($moreMenu.is(':visible')) {

			// Only show first 4 links for the bottom nav
			if ($sidebarNav.children().length > 5) {
				$sidebarNav.children(':nth-child(4)').nextAll().addClass('hide');
			}

		// Not mobile width
		} else {
			$sidebarNav.children(':nth-child(4)').nextAll().removeClass('hide');

			if ($sidebarNav.hasClass('more-menu-expanded')) {
				$sidebarNav.removeClass('more-menu-expanded');
			}
		}
		// if ($sidebarWrap.hasClass('sidebar-wrapper-fixed-top') && ($moreLinks.children().length > 0)) {
		// 	$sidebarNav.children(':nth-child(4)').nextAll().removeClass('hide');
		// }
		if ($moreLinks.hasClass('links-visible') && $moreMenu.is(':hidden')) {
			$moreLinks.removeClass('links-visible');
			$sidebarNav.removeClass('more-menu-expanded');
			$sidebarWrap.removeClass('fullscreen');
			$('body, html').removeClass('no-scroll');
		}
	}

	// Window Listeners
	var cachedWidth = $(window).width();

	$(window).on('resize', function() {

		var newWidth = $(window).width();

        if(newWidth !== cachedWidth) {

					mobileNav();
          cachedWidth = newWidth;
        }
	});

	$(window).on('load', function() {

		mobileNav();
	});

	// Mobile menu button
	$moreMenu.on('click', function() {
		$moreLinks.toggleClass('links-visible');

		if ($moreLinks.hasClass('links-visible')) {
			$moreLinks.removeClass('hide');
			$moreMenu.attr('aria-expanded', 'true');
			$sidebarWrap.addClass('fullscreen');
			$sidebarNav.addClass('more-menu-expanded');
			$('body, html').addClass('no-scroll');
		} else {
			$moreLinks.addClass('hide');
			$moreMenu.attr('aria-expanded', 'false');
			$sidebarWrap.removeClass('fullscreen');
			$sidebarNav.removeClass('more-menu-expanded');
			$('body, html').removeClass('no-scroll');
		}
	});

	// Debounce function: https://davidwalsh.name/javascript-debounce-function
	// Returns a function, that, as long as it continues to be invoked, will not
	// be triggered. The function will be called after it stops being called for
	// N milliseconds. If `immediate` is passed, trigger the function on the
	// leading edge, instead of the trailing.

	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};


	// Modified Greedy Nav (Priority+ navigation) - lukejacksonn
	// https://codepen.io/lukejacksonn/pen/PwmwWV

	// Add class to the main navigation and remove the select dropdown
	$('.super-group-menu > ul.cf.js').addClass('visible-links');
	$('.visible-links .option-select').remove();
	$('.mobile-menu .option-select').remove();

	var $nav = $('.super-group-menu');
	var $btn = $('.super-group-menu .hidden-menu');
	var $vlinks = $('.super-group-menu .visible-links');
	var $totalMenuWidth = 0;

	// Get total width of menu before resizing
	$vlinks.children().each(function() {
		$totalMenuWidth +=$(this).outerWidth(true);
	});

	function updateNav() {
		var $availableSpace;

		// Get measurements
		$availableSpace = $nav.width() - 40;
		$menuWidth = 0;

		$vlinks.children().each(function() {
			$menuWidth += $(this).outerWidth(true);
		});

		// Make sure mobile menu is not open
		if (!$('.super-group-menu-wrap').hasClass('menuExpand')) {

			// If menu overflows the available space, change to mobile menu
			if ($menuWidth > $availableSpace) {
				$vlinks.addClass('hidden');
				$btn.removeClass('hidden');
				$nav.css('overflow', 'hidden');
			} else {
				$vlinks.removeClass('hidden');
				$btn.addClass('hidden');
				$nav.css('overflow', 'initial');
			}
		} else {

			// Go back to fullsize if menu fits in the window
			if ($totalMenuWidth < $(window).width()) {
				$vlinks.removeClass('hidden');
				$btn.addClass('hidden');
				$nav.css('overflow', 'initial');
				$('.super-group-menu-wrap').removeClass('menuExpand');
			}
		}

		if (!$('.super-group-menu-wrap').hasClass('menuExpand')) {
			$('body, html').removeClass('no-scroll');
		}
	}
	updateNav();

	// Window listeners
	// var priorityNav = debounce(function() {
	//
	// 	updateNav();
	//
	// }, 100);

	$(window).on('resize', updateNav);

	$('.super-group-menu > button').click(function() {
		$('.visible-links').toggleClass('hidden');
		$('.super-group-menu-wrap').toggleClass('menuExpand');
		if ($('.super-group-menu-wrap').hasClass('menuExpand')) {
			$('.super-group-menu').css({'overflow': ''});
			$('.super-group-menu > button').attr('aria-expanded', 'true');
			$('.visible-links').find('ul').addClass('subMenuExpand');
			$('body, html').addClass('no-scroll');
		} else {
			$('.super-group-menu').css({'overflow': 'hidden'});
			$('.super-group-menu > button').attr('aria-expanded', 'false');
			$('.visible-links').find('ul').removeClass('subMenuExpand');
			$('body, html').removeClass('no-scroll');
		}
	});

	// Make parent menu item as the first menu item in the submenu
	$('.visible-links > li').each(function() {
		if ($(this).children('ul').length) {
			var $subMenu = $(this).find('ul'),
					$parentLinkText = $(this).children('a').text();

			$subMenu.prepend('<li></li>');
			$(this).children('a').prependTo($subMenu.children().first('li'));
			$(this).addClass('menuItem').prepend('<button aria-label="menu" aria-haspopup="true" aria-expanded="false">' + $parentLinkText + '</button');

			if ($(this).hasClass('active')) {
				var $subMenu = $(this).find('ul');
				$subMenu.children().first('li').addClass('active');
			}
		}
	});

	$('.menuItem > button').click(function(e) {
		var $menuItem = $(this).parent(),
				$menuBtn = $(this);
		$menuItem.find('ul').toggleClass('subMenuExpand');
		$menuBtn.attr('aria-expanded', 'true');
		$('.menuItem').not($menuItem).find('ul').removeClass('subMenuExpand');
		$('.menuItem > button').not($menuBtn).attr('aria-expanded', 'false');

		if ($('.super-group-menu-wrap').hasClass('menuExpand')) {
			$('.subMenuExpand').css({'position': 'static'});
		} else {
			$('.subMenuExpand').css({'position': ''});
		}
		e.stopPropagation();
	});

	$('.visible-links li').each(function() {
		if ($(this).hasClass('active')) {
			$(this).closest('.menuItem').addClass('active');
		}
	});


	//Close submenu when clicking elsewhere
	$(document).click(function(e) {
		if ($(e.target).closest('a').length === 0) {
			$('.menuItem ul').removeClass('subMenuExpand');
			$('.menuItem > button').attr('aria-expanded', 'false');
		}
	});

	//Close submenu on escape
	$(document).keyup(function(e) {
		if (e.which == 27) {
			$(document).click();
			$('.menuItem').children().blur();
		}
	});

});
