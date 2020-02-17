$(document).ready(function() {

  // Header animations on load
  var $introBcg = $('.oer-header'),
      $introH2 = $('.intro h2'),
      $introH3 = $('.intro h3'),
      $introP = $('.intro p'),
      $introLink = $('.intro a'),
      $anchor = $('.oer'),
      $tlLoad = new TimelineMax();

   $(window).on('load', function() {

     // Fade in intro
     $tlLoad
       .from($introBcg, .8, {autoAlpha: 0})
       .from($introH2, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.5')
       .from($introH3, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.5')
       .from($introP, 0.8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
       .from($introLink, 0.8, {y:10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
       .staggerFrom($anchor, .6, {y: 10, autoAlpha: 0, ease: Back.easeInOut.config(1.4)}, 0.2, '-=.5');
   });

});
