jQuery(function($)
{
	var timer = null;
	$('#curl').mouseover(function() {
		if (timer) {
			clearInterval(timer);
		}
		document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/bigcurl.png');
	});

	$('#curl').mouseout(function() {
		timer = setTimeout(function() {
			document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/smallcurl.png');
		}, 800);
	});
});
