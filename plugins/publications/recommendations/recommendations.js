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
HUB.Plugins.PublicationRecommendations = {
	initialize: function() {
		// Recommendations web service
		var recoms = $('recommendations-section');
		if (recoms) {
			var sbjt = $('recommendations-subject')
			if (sbjt) {
				sbjt.empty();
				imgpath = '/components/com_publications/assets/img/loading.gif';
				var p = new Element('p', {'id':'loading-section'});
				var img = new Element('img', {'id':'loading-img','src':imgpath}).injectInside(p);
				p.injectInside(sbjt);
			
				var rid = $('rid');
				if (rid) {
					new Ajax('/index.php?option=com_publications&task=plugin&trigger=onPublicationsRecoms&no_html=1&rid='+rid.value,{
							'method' : 'get',
							'update' : sbjt
						}).request();
				}
			}
		}
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.PublicationRecommendations.initialize);