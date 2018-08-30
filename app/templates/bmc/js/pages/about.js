$(document).ready(function() {

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

  //Display AoF information and members when appropriate button is clicked

  $('.aof-btn').click(function() {

    var $btn = $(this),
        $otherBtn = $('.aof-btn').not($btn),
        $aofTeam = $('.aof-team'),
        $card = $('.team-wrapper > .card'),
        $aofBusiness = $('.aof-business'),
        $aofPartners = $('.aof-partners'),
        $aofFmn = $('.aof-fmn'),
        $aofSite = $('.aof-site'),
        $aofEvaluation = $('.aof-evaluation'),
        $business = $('.pm'),
        $partners = $('.ps'),
        $fmn = $('.pd'),
        $site = $('.ci'),
        $evaluation = $('.et');

    if($btn.hasClass('focus')) {

      //Do nothing

    } else {

      $btn.addClass('focus');
      $otherBtn.removeClass('focus');

    }

    if($btn.hasClass('team')) {
      $aofTeam.addClass('focus');
      $('div').not($aofTeam).removeClass('focus');

      if($card.hasClass('unfocus')) {
        $card.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
      } else {
        //Do nothing
      }
    }

    if($btn.hasClass('pm')) {
      $aofBusiness.addClass('focus');
      $('div').not($aofBusiness).removeClass('focus');

      if($business.hasClass('unfocus')) {
        $business.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
        $card.not($business).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      } else {
        $card.not($business).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      }
    }

    if($btn.hasClass('ps')) {
      $aofPartners.addClass('focus');
      $('div').not($aofPartners).removeClass('focus');

      if($partners.hasClass('unfocus')) {
        $partners.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
        $card.not($partners).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      } else {
        $card.not($partners).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      }
    }

    if($btn.hasClass('pd')) {
      $aofFmn.addClass('focus');
      $('div').not($aofFmn).removeClass('focus');

      if($fmn.hasClass('unfocus')) {
        $fmn.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
        $card.not($fmn).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      } else {
        $card.not($fmn).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      }
    }

    if($btn.hasClass('ci')) {
      $aofSite.addClass('focus');
      $('div').not($aofSite).removeClass('focus');

      if($site.hasClass('unfocus')) {
        $site.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
        $card.not($site).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      } else {
        $card.not($site).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      }
    }

    if($btn.hasClass('et')) {
      $aofEvaluation.addClass('focus');
      $('div').not($aofEvaluation).removeClass('focus');

      if($evaluation.hasClass('unfocus')) {
        $evaluation.removeClass('green').removeClass('unfocus').addClass('dark-blue').addClass('focus');
        $card.not($evaluation).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      } else {
        $card.not($evaluation).removeClass('dark-blue').removeClass('focus').addClass('green').addClass('unfocus');
      }
    }
  });

  // Loading the intro
  $(window).on('load', function () {
    var $bcg = $('.bcg-mission'),
        $mission = $('.mission-statement'),
        tlIntro = new TimelineMax();
    tlIntro
      .from($bcg, 1, {autoAlpha: 0, ease:Power0.easeNone})
      .from($mission, 1, {autoAlpha: 0, ease:Power0.easeNone});
  });

  //Init ScrollMagic
  var controller = new ScrollMagic.Controller();

  //Parallax for intro
  var introParallax = new ScrollMagic.Scene({
    triggerElement: '.mission',
    triggerHook: 1,
    duration: '140%'
  })
  .setTween(TweenMax.from('.bcg-mission', 1, {y:'-40%', ease:Power0.easeNone}))
  .addTo(controller);

  //Scene About Us
  var $aboutBcg = $('.about'),
      $aboutContent1 = $('.content1'),
      tlScene2 = new TimelineMax();
  tlScene2
    .from($aboutBcg, 1, {css: {backgroundColor: '#FFFFFF'}, ease: Power0.easeNone})
    .from($aboutContent1, .5, {y: 10, autoAlpha: 0, ease: Power0.easeNone});

  var ourScene2 = new ScrollMagic.Scene({
    triggerElement: '.content1',
    triggerHook: .8
  })
  .setTween(tlScene2)
  .addIndicators()
  .addTo(controller);


  var $aboutP = $('.pane-2 p'),
      tlScene3 = new TimelineMax();
  tlScene3
    .staggerFrom($aboutP, 1, {y: 10, autoAlpha: 0, ease:Power0.easeNone}, 0.2);

  var ourScene3 = new ScrollMagic.Scene({
    triggerElement: '.pane-2',
    triggerHook: .8
  })
  .setTween(tlScene3)
  .addIndicators()
  .addTo(controller);

  // var $img2 = $('.pane-3 .img-container'),
  //     $content2 = $('.pane-3 p'),
  //     tlScene4 = new TimelineMax();
  // tlScene4
  //   .from($img2, 0.8, {autoAlpha: 0, ease:Power0.easeNone})
  //   .fromTo($img2, 0.8, {y: '+=150'}, {y: '-=300', ease:Power0.easeNone}, '-=0.8')
  //   .from($content2, 1, {autoAlpha: 0, ease:Power0.easeNone});
  //
  // var ourScene4 = new ScrollMagic.Scene({
  //   triggerElement: '.pane-3',
  //   triggerHook: .8
  // })
  // .setTween(tlScene4)
  // .addIndicators()
  // .addTo(controller);

  // Meet the Team
  var $teamHeader = $('.team .header'),
      $teamCards = $('.team-wrapper .card'),
      tlScene5 = new TimelineMax();

  tlScene5
    .from($teamHeader, 1, {y: 10, autoAlpha: 0})
    .staggerFrom($teamCards, 1, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, 0.2);

    var ourScene5 = new ScrollMagic.Scene({
      triggerElement: '.team',
      triggerHook: .6
    })
    .setTween(tlScene5)
    .addIndicators()
    .addTo(controller);

  // Board of Advisors
  var $advisorsH2 = $('#advisors'),
      $advisorsCards = $('.wrapper .card'),
      tlScene6 = new TimelineMax();

  tlScene6
    .from($advisorsH2, 1, {y: 50, autoAlpha: 0})
    .staggerFrom($advisorsCards, 1, {y: 50, autoAlpha: 0, ease: Power0.easeNone}, 0.2);

  var ourScene6 = new ScrollMagic.Scene({
    triggerElement: '.advisors',
    triggerHook: .6
  })
  .setTween(tlScene6)
  .addIndicators()
  .addTo(controller);
});
