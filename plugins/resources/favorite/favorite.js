//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.ResourcesFavorite = {
	initialize: function() {
		// Add to favorites
		var fav = $('fav-this');
		if (fav) {
			fav.addEvent('click', function(e) {
				new Event(e).stop();
				
				var rid = $('rid').value;
				new Ajax('index.php?option=com_resources&task=plugin&trigger=onResourcesFavorite&no_html=1&rid='+rid,{
					method : 'get',
					update : $('fav-this'),
					onSuccess : function(){
						if (fav.hasClass('faved')) {
						fav.removeClass('faved');
							var img = '/components/com_resources/images/broken-heart.gif';
							var txt = 'Favorite removed.';
						} else {
							fav.addClass('faved');
							var img = '/components/com_resources/images/heart.gif';
							var txt = 'Favorite saved.';
						}
						if (typeof(Growl) != "undefined") {
							Growl.Bezel({
								image: img,
								title: txt,
								text: ''
							});
						}
					}
				}).request();
			});
		}
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.ResourcesFavorite.initialize);