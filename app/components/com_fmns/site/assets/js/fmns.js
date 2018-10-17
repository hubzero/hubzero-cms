
$(document).ready(function() {

  // Same height for mentor bridge and ambassador texts
 var $mentorBridge = $('.bridge-text');
 var $ambassador = $('.ambassador-text');

 $(window).on('load resize', function() {

  var $mentorHeight = $mentorBridge.height();

  if ($(window).width() > 683) {
    $ambassador.height($mentorHeight);
  }
 });

 var $introBcg = $('.bcg-fmn'),
     $introH2 = $('.header h2'),
     $introH3 = $('.header h3'),
     $introP = $('.header p'),
     $introH4 = $('.header h4'),
     $signUp = $('.header .mailinglist-signup'),
     $anchor = $('.anchor-wrap'),
     $tlLoad = new TimelineMax();

 // Header animations on load
  $(window).on('load', function() {

    // Fade in intro
    $tlLoad
      .from($introBcg, .8, {autoAlpha: 0})
      .from($introH2, .8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.2')
      .from($introH3, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.5')
      .from($introP, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
      .from($introH4, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
      .from($signUp, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
      .staggerFrom($anchor, .6, {y: 10, autoAlpha: 0, ease: Back.easeInOut.config(1.4)}, 0.2, '-=.3');
  });

  //Init ScrollMagic
  var controller = new ScrollMagic.Controller();

  //Parallax for intro
  var introParallax = new ScrollMagic.Scene({
    triggerElement: '.header',
    triggerHook: 1,
    duration: '140%'
  })
  .setTween(TweenMax.from('.bcg-fmn', 1, {y:'-40%', ease:Power0.easeNone}))
  .addTo(controller);

  // New and Upcoming

  // Commenting out upcoming fmn animations
  // var $newH2 = $('#new'),
  //     $newWrap = $('.new-wrap'),
  //     $newBillboard = $('.module-upcoming > .billboard'),
  //     tlScene1 = new TimelineMax();
  //
  // tlScene1
  //   .from($newH2, 1, {x: -700})
  //   .from($newWrap, 0.3, {autoAlpha: 0}, '-=.4')
  //   .from($newH2, 1.5, {css: {color:'#cca231'}})
  //   .from($newWrap, 1.5, {css: {borderColor:'#cca231'}}, '-=1.5')
  //   .staggerFrom($newBillboard, 1, {scale: 0.5, opacity: 0, ease: Back.easeInOut.config(1.4)}, 0.2, '-=2');
  //
  //
  // var upcoming = new ScrollMagic.Scene({
  //   triggerElement: '.new',
  //   triggerHook: .6,
  //   reverse: false
  // })
  // .setTween(tlScene1)
  // .addIndicators()
  // .addTo(controller);

  // Benefits
  var $benefitsBcg = $('.benefits'),
      $benefitsH2 = $('#benefits'),
      $benefitsP = $('.benefits > p'),
      $benefitsList = $('.benefits-wrapper li'),
      $benefitsMap = $('.map-wrapper'),
      tlScene2 = new TimelineMax();

  tlScene2
    .from($benefitsH2, .8, {y: 10, autoAlpha: 0, ease: Power0.easeNone})
    .from($benefitsP, .8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=.4')
    .from($benefitsBcg, 1.5, {css: {backgroundColor:'#FFFFFF'}}, '-=1')
    .from($benefitsMap, 1.4, {autoAlpha: 0}, '-=1.5')
    .staggerFrom($benefitsList, 1, {y: 10, autoAlpha: 0}, 0.2, '-=.8');

  var benefits = new ScrollMagic.Scene({
    triggerElement: '.benefits',
    triggerHook: .6,
    reverse: false
  })
  .setTween(tlScene2)
  .addIndicators()
  .addTo(controller);

  // OER and Featured Resources
  var $oerH3 = $('#products'),
      $oerText = $('.oer p'),
      $oerImage = $('.oer-wrap img'),
      tlScene3 = new TimelineMax();

  tlScene3
    .from($oerH3, 1, {x: -500})
    .staggerFrom($oerText, .8, {y: 10, autoAlpha: 0}, 0.2)
    .from($oerImage, 1, {autoAlpha: 0}, '-=1.5');

  var products = new ScrollMagic.Scene({
    triggerElement: '.products',
    triggerHook: .6,
    reverse: false
  })
  .setTween(tlScene3)
  .addIndicators()
  .addTo(controller);

  // Commenting out featured resources animations
  // var $productsH2 = $('.products h2'),
  //     $productsWrap = $('.product-wrap'),
  //     $productsBillboard = $('.module-fmnProducts > .billboard'),
  //     tlScene4 = new TimelineMax();
  //
  // tlScene4
  //   .from($productsH2, 1, {x: -700})
  //   .from($productsWrap, 0.3, {autoAlpha: 0}, '-=.4')
  //   .from($productsH2, 1.5, {css: {color:'#cca231'}})
  //   .from($productsWrap, 1.5, {css: {borderColor:'#cca231'}}, '-=1.5')
  //   .staggerFrom($productsBillboard, 1, {scale: 0.5, opacity: 0, ease: Back.easeInOut.config(1.4)}, 0.2, '-=2');
  //
  //
  // var upcoming = new ScrollMagic.Scene({
  //   triggerElement: '.product-wrap',
  //   triggerHook: .6,
  //   reverse: false
  // })
  // .setTween(tlScene4)
  // .addIndicators()
  // .addTo(controller);

  var $bridgeH2 = $('#bridge'),
      $bridge = $('.bridge-wrap'),
      tlScene5 = new TimelineMax();

  tlScene5
    .from($bridgeH2, .8, {y: 10, autoAlpha: 0})
    .staggerFrom($bridge, .5, {y: 10, autoAlpha: 0}, 0.4, '-=.7');

  var bridgePrograms = new ScrollMagic.Scene({
    triggerElement: '.bridge',
    triggerHook: .6,
    reverse: false
  })
  .setTween(tlScene5)
  .addIndicators()
  .addTo(controller);

  // Benefits for Projects
  var $projectsH2 = $('#projects'),
      $projectsH4 = $('.projects h4'),
      $projectsP = $('.projects p'),
      $projectsList = $('.projects-wrap'),
      tlScene6 = new TimelineMax();

  tlScene6
    .from($projectsH2, .8, {y: 10, autoAlpha: 0})
    .from($projectsH4, .8, {y: 10, autoAlpha: 0}, '-=.5')
    .from($projectsP, .8, {y: 10, autoAlpha: 0}, '-=.5')
    .staggerFrom($projectsList, .6, {y: 10, autoAlpha: 0}, 0.1, '-=.6');

  var projects = new ScrollMagic.Scene({
    triggerElement: '.projects',
    triggerHook: .6,
    reverse: false
  })
  .setTween(tlScene6)
  .addIndicators()
  .addTo(controller);
});
