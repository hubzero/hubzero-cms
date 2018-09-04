$(document).ready(function() {

  // Header animations on load
  var $introBcg = $('.ambassador-header'),
      $introH2 = $('.intro h2'),
      $introP = $('.intro p'),
      $description = $('.ambassador'),
      $tlLoad = new TimelineMax();

   $(window).on('load', function() {

     // Fade in intro
     $tlLoad
       .from($introBcg, .8, {autoAlpha: 0})
       .from($introH2, .8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.5')
       .from($introP, .8, {y: 10, autoAlpha: 0, ease: Power0.easeNone}, '-=0.3')
       .from($description, .8, {y:10, autoAlpha: 0, ease: Power0.easeNone});
   });

});
