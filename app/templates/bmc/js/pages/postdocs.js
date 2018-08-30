$(document).ready(function() {

  // Advertisements
  $("a.postdoc-link").fancybox({
    maxWidth : 900
  });

  //Open and close card on click

  $('.card > .btn-action').click(function() {
    var $card = $(this).parent('.card'),
        $icon = $(this).children('i');

    $icon.addClass('icon-spinner');

    if($card.hasClass('active')) {

      $card.removeClass('active');

      window.setTimeout(function() {

        $icon.removeClass('arrow-left').removeClass('icon-spinner').addClass('menu');
      }, 800);

    } else {

      $card.addClass('active');

      window.setTimeout(function() {
        $icon.removeClass('menu').removeClass('icon-spinner').addClass('arrow-left');
      }, 800);
    }

  });

  // Loading animation
  var $top = $('.one');

  $(window).on('load', function () {
    
    TweenMax.from($top, 1, {y: 10, autoAlpha:0});

  });


});
