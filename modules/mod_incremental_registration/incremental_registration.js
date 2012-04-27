window.addEvent('domready', function()
{
	var lg = new Image('/modules/mod_incremental_registration/images/curl-lg.png');
	var timer = null;
	document.getElementById('curl').addEvent('mouseover', function()
	{
		if (timer)
			clearInterval(timer);
		document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/curl-lg.png');
	});

	document.getElementById('curl').addEvent('mouseout', function()
	{
		timer = setTimeout(function()
		{
			document.getElementById('curl-img').setAttribute('src', '/modules/mod_incremental_registration/images/curl-sm.png');
		}, 800);
	});
});
