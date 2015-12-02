$(document).ready(function() {

	var cartRedirectUrl = $('#cartRedirectUrl').attr('href');
	console.log(cartRedirectUrl);

	setTimeout(function() {
		window.location = cartRedirectUrl;
	}, 2000);

});
