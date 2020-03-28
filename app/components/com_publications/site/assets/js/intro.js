jQuery(document).ready(function() {

  $("a.modal-link").fancybox({
    maxWidth : 450
  });

  // Filter Panel
   var $chevronDown = 'core/assets/icons/chevron-down.svg',
      $chevronUp = 'core/assets/icons/chevron-up.svg',
      $filterIcon = $('.filter-icon'),
      $filterHeader = $('.filter-wrapper legend'),
      $filterPanel = $('.filter-panel');

  $filterHeader.click(function() {
    $(this).next('.filter-panel').toggleClass('collapsed');

    if ($(this).attr('aria-expanded') === 'true') {
      $(this).attr('aria-expanded', 'false');
      $(this).find('.filter-icon').attr('data', $chevronDown);
    } else {
      $(this).attr('aria-expanded', 'true');
      $(this).find('.filter-icon').attr('data', $chevronUp);
    }
  });

  // Set headers and subheaders to equal heights
  var $headers = $('.header'),
      $subHeaders = $('.sub-header');

  const setHeaderHeight = (header, height) => {
    // reset to auto or else we can't check height
    $(header).css({'height': 'auto'});

    // get highest value
    $(header).each(function(i, obj) {
      height = Math.max(height, $(obj).outerHeight())
    });

    // set the height
    $(header).css({'height': height + 'px'});
  }

  $(window).on('resize', function() {
    setHeaderHeight($headers, 0);
    setHeaderHeight($subHeaders, 0);
  });

  setHeaderHeight($headers, 0);
  setHeaderHeight($subHeaders, 0);

  var $browsePage = $('.browse-resources-wrapper'),
      $oerPage = $('.oer-wrapper'),
      $submitPage = $('.submit-resource-wrap');

  $('.nav-page li a').on('click', function() {
    $(this).addClass('active');
    $('.nav-page li a').not($(this)).removeClass('active');

    if ($(this).hasClass('oer-link')) {
      $oerPage.css({'display': 'block'});
      $browsePage.css({'display': 'none'});
      $submitPage.css({'display': 'none'});
    }

    if ($(this).hasClass('submit-link')) {
      $oerPage.css({'display': 'none'});
      $browsePage.css({'display': 'none'});
      $submitPage.css({'display': 'flex'});
    }

    if ($(this).hasClass('browse-link')) {
      $oerPage.css({'display': 'none'});
      $browsePage.css({'display': 'flex'});
      $submitPage.css({'display': 'none'});
    }
  });

  // Mobile filtering
  var $mobileFilter = $('.mobile-filter'),
      $filterWrap = $('.page-filter');

  $(window).on('load resize', function() {
    if ($mobileFilter.is(':visible')) {
      $filterWrap.addClass('collapsed');
    } else {
      $filterWrap.removeClass('collapsed');
    }
  });

  $('.mobile-filter').click(function() {
    $('.page-filter').toggleClass('collapsed');
  });

  //Sticky nav for Mobile
  const $navBar = $('.nav-page');
  const $subNavHeader = $('.sub');
  const $filterWrapper = $('.page-filter-wrapper');
  let scrollTop = 0;
  let windowTop = 0;

  $('.content-panel').on('resize scroll', function() {
    let $sticky = $navBar.offset().top;
    let $qubesHeaderHeight = $('.wrap-main').height();
    let $pageMenuHeight = $navBar.height();
    windowTop = $('.content-panel').scrollTop();

    if (windowTop > scrollTop) {
			scrollingDown = true;
		} else {
			scrollingDown = false;
		}
		scrollTop = windowTop;

    if ($mobileFilter.is(':visible')) {
      $('.page-filter-wrapper.sticky').css({'top': $qubesHeaderHeight + $pageMenuHeight + 'px'});
      if ($('.content-panel').scrollTop() > $sticky) {
        if (scrollingDown) {
          setTimeout(function() {
            $navBar.addClass('sticky');
            $filterWrapper.addClass('sticky');
          }, 300);
        }
      } else {
        $navBar.removeClass('sticky');
        $filterWrapper.removeClass('sticky');
      }
    } else {
      $navBar.removeClass('sticky');
      $filterWrapper.removeClass('sticky');
    }
  });
});
