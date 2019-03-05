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
		if (windowTop < bannerHeight + $scontainer.height() && $moreMenu.is(":hidden")) {
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
	function mobileNav() {
		// Media query triggered and window is mobile width
		if ($moreMenu.is(':visible')) {

			// Move remaining links to the more menu
			$('.sidebar-nav > li:nth-child(5)').nextAll().prependTo('.more-links');

		// Not mobile width
		} else {
			if ($moreLinks.children().length > 0) {
				$moreLinks.children().appendTo($sidebarNav);
			}
			if ($sidebarNav.hasClass('more-menu-expanded')) {
				$sidebarNav.removeClass('more-menu-expanded');
			}
		}
		if ($sidebarWrap.hasClass('sidebar-wrapper-fixed-top') && ($moreLinks.children().length > 0)) {
			$moreLinks.children().appendTo($sidebarNav);
		}
		if ($moreLinks.hasClass('links-visible') && $moreMenu.is(':hidden')) {
			$moreLinks.removeClass('links-visible');
			$sidebarNav.removeClass('more-menu-expanded');
			$sidebarWrap.removeClass('fullscreen');
			$('body, html').removeClass('no-scroll');
			console.log('hamburger is hidden');
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
			$sidebarWrap.addClass('fullscreen');
			$sidebarNav.addClass('more-menu-expanded');
			$('body, html').addClass('no-scroll');
		} else {
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
	$('.hidden-links .option-select').remove();

	var $nav = $('.super-group-menu');
	var $btn = $('.super-group-menu button');
	var $vlinks = $('.super-group-menu .visible-links');
	var $hlinks = $('.super-group-menu .hidden-links');
	var $alinks = $vlinks.children(); + $hlinks.children();
	var breaks = [];

	function updateNav() {

		var availableSpace = $btn.hasClass('hidden') ? $nav.width() : $nav.width() - $btn.width() - 30;
		// console.log('Visible link children is ' + $vlinks.children().length);
		// console.log('All links is ' + $alinks.length);
			// The visible list is overflowing the nav
			if ($vlinks.width() > availableSpace) {

				// Record the width of the list
				breaks.push($vlinks.width());

				//Move item to the hidden list
				$vlinks.children().last().prependTo($hlinks);


				//Show the dropdown btn
				if ($btn.hasClass('hidden')) {
					$btn.removeClass('hidden');
				}

			// The visible list is not overflowing
			} else {

				//There is space for another item in the nav
				if (availableSpace > breaks[breaks.length-1]) {

					// Move the item to the visible list
					$hlinks.children().first().appendTo($vlinks);
					breaks.pop();
				}

				// Hide the dropdown btn if hidden list is empty
				if (breaks.length < 1) {
					$btn.addClass('hidden');
					$hlinks.addClass('hidden');
				}
			}

		// Keep counter updated
		$btn.attr("count", breaks.length);

		// Recur if the visibile list is still overflowing the nav
		if ($vlinks.width() > availableSpace) {
			updateNav();
		}

		// Rerun function if visible links < total links
		if ($vlinks.children().length < $alinks.length) {
			timer = setTimeout(updateNav, 1000);
		}
	}
	updateNav();

	// Window listeners
	var priorityNav = debounce(function() {

		updateNav();

	}, 100);

	$(window).on('resize', priorityNav);

	$btn.click(function() {
		$hlinks.toggleClass('hidden');
	});

	// Close hidden list when clicking somewhere else
  $('body').click(function(e) {
    if($(e.target).closest('.super-group-menu').length === 0) {
      $hlinks.addClass('hidden');
    }
  });

	// Change hover to tap for touchscreen for main navbar
	//https://stackoverflow.com/questions/42066301/hover-menu//-is-not-working-on-touch-device-because-link-gets-trigg//ered
	window.USER_IS_TOUCHING = false;
	window.addEventListener('touchstart', function onFirstTouch() {
    window.USER_IS_TOUCHING = true;
	 	// we only need to know once that a human touched the screen, so we can stop listening now
  	window.removeEventListener('touchstart', onFirstTouch, false);
	}, false);

  function is_touch_device() {
    return 'ontouchstart' in window        // works on most browsers
        || navigator.maxTouchPoints;       // works on IE10/11 and Surface
  };
  $('ul > li > a').click(function(e){
      var target = $(e.target);
      var parent = target.parent(); // the li
      if(is_touch_device() || window.USER_IS_TOUCHING){
          if(target.hasClass("hover-effect")){
              //run default action of the link
          }
          else{
              e.preventDefault();
              //remove class active from all links
              $('ul > li > a.hover-effect').removeClass('hover-effect');

              //set class active to current link
              target.addClass("hover-effect");
              parent.addClass("hover-effect");
          }
      }
  });
  $('ul > li').click(function(e){
    //remove class active from all links if li was clicked
    if (e.target == this){
      $(".hover-effect").removeClass('hover-effect');
    }
  });

	// Close dropdown menu if tap on body
	$('body').click(function(e) {
    if($(e.target).closest('.super-group-menu').length === 0) {
      $('ul > li').removeClass('hover-effect');
    }
  });

});
