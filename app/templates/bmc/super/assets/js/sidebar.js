function initMenu() {
  $('#menu ul').hide();
  $('#menu ul').children('.current').parent().show();
  $('#menu li a').click(
	function() {
	  var checkElement = $(this).next();
	  if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
		return false;
		}
	  if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
		$('#menu ul:visible').slideUp('normal');
		checkElement.slideDown('normal');
		return false;
		}
	  }
	);
}

// Make sure content area is at least as large as sidebar, plus wiggle room
function adjustContentBody() {
	$('.super-group-content-wrap').css("min-height", function() {
		return $('#sidebar-wrapper').outerHeight(true);
	});
}

$(document).ready(function() {
	// initMenu();   // Not sure what this does anymore.  Commenting out for now.
	adjustContentBody();
});