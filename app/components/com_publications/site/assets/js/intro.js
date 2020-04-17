jQuery(document).ready(function() {

  String.prototype.nohtml = function () {
  	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
  };


  $("a.modal-link").fancybox({
    maxWidth : 450
  });

  // Load browse as default
  var $liveUpdate = $('#live-update-wrapper')
  $(window).on('load', function() {

    if ($('.browse-link').hasClass('active')) {
      $.get($('.browse-link').attr('href').nohtml(), function(result) {
        $liveUpdate.html(result);
        $.getScript('app/components/com_publications/site/assets/js/search.js');
      });
    }
  });

  // Update live area via ajax
  $('.nav-page-link').on('click', function(e) {
    e.preventDefault();

    var container = $($(this).attr('data-target'));

    if (container.length) {
      $.get($(this).attr('href').nohtml(), function(result) {
        container.html(result);
        $.getScript('app/components/com_publications/site/assets/js/search.js');
      });

      $(this).addClass('active');
      $('.nav-page-link').not($(this)).removeClass('active');
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
