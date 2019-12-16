/*
The following two functions help set category tag text color based
on background color set by user.  It is rather inelegant, as it will
convert rgb sent via css to hex, which then gets used in getContrastYIQ
by immediately converting back to hex.  Too lazy now to optimize...
*/

// https://stackoverflow.com/a/3627747
function rgb2hex(rgb) {
    if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;

    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

// Calculating Color Contrast by Brian Suda: https://24ways.org/2010/calculating-color-contrast/
function getContrastYIQ(hexcolor){
	var r = parseInt(hexcolor.substr(0,2),16);
	var g = parseInt(hexcolor.substr(2,2),16);
	var b = parseInt(hexcolor.substr(4,2),16);
	var yiq = ((r*299)+(g*587)+(b*114))/1000;
	return (yiq >= 128) ? 'black' : 'white';
}

$(document).ready(function(){

  // Open and close card on click
  $('.card > .btn-action').click(function() {
    var $card = $(this).parent('.card'),
        $icon = $(this).children('i'),
        $links = $card.find('.sub-menu a'),
        $content = $card.find('.card-content');

    $icon.addClass('icon-spinner');

    if($card.hasClass('active')) {

      $card.removeClass('active');
      $links.attr('aria-hidden', 'true');
      $links.attr('tabindex', '-1');
      $content.attr('tabindex', '-1');

      window.setTimeout(function() {

        $icon.removeClass('arrow-left').removeClass('icon-spinner').addClass('menu');
      }, 800);

    } else {

      $card.addClass('active');
      $links.attr('aria-hidden', 'false');
      $links.attr('tabindex', '0');
      $content.attr('tabindex', '0');
      $content.focus();

      window.setTimeout(function() {
        $icon.removeClass('menu').removeClass('icon-spinner').addClass('arrow-left');
      }, 800);
    }

  });

  // Adjust CSS of sponsor logos if Featured ROW exists
    $('.card').each(function() {
      var $sponsor = $(this).find('.logo-wrap'),
          $featured = $(this).find('.featured');

      if ($featured.length > 0 && $sponsor.length > 0) {
        $sponsor.css('top', '1.25em');
      } else {
        // do nothing
      }
    });

  // Legacy
  $('.demo-two-card').hover(function(){

      var description = $(this).find('.description'),
          fork = $(this).find('.fork'),
          abstract = $(this).find('.abstract'),
          favorites = $(this).find('.favorites-alt'),
          watch = $(this).find('.fa-eye'),
          share = $(this).find('.fa-share-alt'),
          watchhub = $(this).find('.watch'),
          sharehub = $(this).find('.share-alt'),
          viewRecord = $(this).find('.show-more');

      description.stop().animate({
          height: 'toggle',
          opacity: 'toggle'
      }, 300);

      if (description.prop('scrollHeight') > 150) {
        viewRecord.show();
      }

      if (fork.prop('scrollHeight') > 27) {
        fork.addClass('fade-fork');
      }

      if (abstract.prop('scrollHeight') > 90) {
        abstract.addClass('fade-abstract');
      }
  });

  $("span.primary.cat").each(function(index) {
    $(this).css("color", getContrastYIQ(rgb2hex($(this).css("background-color"))));
  });
});
