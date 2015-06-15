$(document).ready(function() {
	
	$('#content form').hide();
	
	var opts = {
		lines: 13, // The number of lines to draw
		length: 7, // The length of each line
		width: 4, // The line thickness
		radius: 10, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		color: '#000', // #rgb or #rrggbb
		speed: 1, // Rounds per second
		trail: 60, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spin', // The CSS class to assign to the spinner
		zIndex: 10000, // The z-index
		top: '0', // Top position relative to parent in px
		left: '0' // Left position relative to parent in px
	};
	
	var overlay = $('<div id="submitOverlay"></div>').css('opacity', 0.95);
	var spinner = $('<div id="spinner"></div>').spin(opts);
	var overlayMessage = $('<div id="overlayMessage">Submitting your information to the payment provider. Please wait.</div>').append(spinner);
	$('body').append(overlay).append(overlayMessage);

	// submit the form
	$('#content form').submit();

});
