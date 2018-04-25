$(document).ready(function() {

  var $cell = $('.card');

  // $cell.find('.js-expander').hover(function(e) {
  //
  //   var $thisCell = $(this).closest('.card');
  //   var $otherCells = $cell.not($thisCell);
  //   var $info = $thisCell.find('.fa-info-circle');
  //
  //   if ($thisCell.hasClass('is-collapsed')) {
  //     $info.addClass('active');
  //   } else {
  //     $info.removeClass('active');
  //   }
  //
  // }, function(e) {
  //   if ($thisCell.hasClass('is-collapsed')) {
  //     $info.removeClass('active');
  //   } else {
  //     $info.addClass('active');
  //   }
  // });

  //open and close card when clicked on card
  $cell.find('.js-expander').click(function() {

    var $thisCell = $(this).closest('.card');
    var $otherCells = $cell.not($thisCell);

    if ($thisCell.hasClass('is-collapsed')) {

      $otherCells
        .removeClass('is-expanded')
        .addClass('is-collapsed');
      $otherCells
        .find('.fa-info-circle')
        .removeClass('active');
      $thisCell
        .find('.fa-info-circle')
        .addClass('active');
      $thisCell
        .delay(350)
        .queue(function() {
          $(this)
            .removeClass('is-collapsed')
            .addClass('is-expanded')
            .dequeue();
        });

    } else {
      $thisCell.removeClass('is-expanded').addClass('is-collapsed')
        .find('.fa-info-circle').removeClass('active');
    }
  });

  //close card when click on cross
  $cell.find('.js-collapser').click(function() {

    var $thisCell = $(this).closest('.card');

    $thisCell
      .removeClass('is-expanded')
      .addClass('is-collapsed')
      .find('.fa-info-circle')
      .removeClass('active');
  });
});
