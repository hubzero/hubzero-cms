$(document).ready(function() {

  var $cell = $('.card');

  //Open and close card when card is clicked
  $cell.find('.js-expander').on('click', function() {

    var $thisCell = $(this).closest('.card');

    if ($thisCell.hasClass('is-collapsed')) {

      $cell
      .not($thisCell)
      .removeClass('is-expanded')
      .addClass('is-collapsed');

      $thisCell
      .removeClass('is-collapsed')
      .addClass('is-expanded');

    } else {

      $thisCell
      .removeClass('is-expanded')
      .addClass('is-collapsed');

    }
  });

  //Close card when clicking on cross

  $cell.find('.js-collapser').on('click', function() {

    var $thisCell = $(this).closest('.card');

    $thisCell.removeClass('is-expanded').addClass('is-collapsed');

  });

});
