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
      $('p').not($aofTeam).removeClass('focus');

      if($card.hasClass('unfocus')) {
        $card.removeClass('unfocus').addClass('focus');
      } else {
        //Do nothing
      }
    }

    if($btn.hasClass('pm')) {
      $aofBusiness.addClass('focus');
      $('p').not($aofBusiness).removeClass('focus');

      if($business.hasClass('unfocus')) {
        $business.removeClass('unfocus').addClass('focus');
        $card.not($business).removeClass('focus').addClass('unfocus');
      } else {
        $card.not($business).removeClass('focus').addClass('unfocus');
      }
    }

    if($btn.hasClass('ps')) {
      $aofPartners.addClass('focus');
      $('p').not($aofPartners).removeClass('focus');

      if($partners.hasClass('unfocus')) {
        $partners.removeClass('unfocus').addClass('focus');
        $card.not($partners).removeClass('focus').addClass('unfocus');
      } else {
        $card.not($partners).removeClass('focus').addClass('unfocus');
      }
    }

    if($btn.hasClass('pd')) {
      $aofFmn.addClass('focus');
      $('p').not($aofFmn).removeClass('focus');

      if($fmn.hasClass('unfocus')) {
        $fmn.removeClass('unfocus').addClass('focus');
        $card.not($fmn).removeClass('focus').addClass('unfocus');
      } else {
        $card.not($fmn).removeClass('focus').addClass('unfocus');
      }
    }

    if($btn.hasClass('ci')) {
      $aofSite.addClass('focus');
      $('p').not($aofSite).removeClass('focus');

      if($site.hasClass('unfocus')) {
        $site.removeClass('unfocus').addClass('focus');
        $card.not($site).removeClass('focus').addClass('unfocus');
      } else {
        $card.not($site).removeClass('focus').addClass('unfocus');
      }
    }

    if($btn.hasClass('et')) {
      $aofEvaluation.addClass('focus');
      $('p').not($aofEvaluation).removeClass('focus');

      if($evaluation.hasClass('unfocus')) {
        $evaluation.removeClass('unfocus').addClass('focus');
        $card.not($evaluation).removeClass('focus').addClass('unfocus');
      } else {
        $card.not($evaluation).removeClass('focus').addClass('unfocus');
      }
    }


  });

});
