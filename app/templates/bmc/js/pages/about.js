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

  //Init ScrollMagic
  var controller = new ScrollMagic.Controller();

  //build a Scene
  var ourScene = new ScrollMagic.Scene({

  })
  .setClassToggle('#mission', '.fade-in') //add class to #mission

});
