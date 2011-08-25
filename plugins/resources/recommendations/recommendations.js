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
HUB.Plugins.ResourcesRecommendations = {
	initialize: function() {
		// Recommendations web service
		var recoms = $('recommendations-section');
		if (recoms) {
			var sbjt = $('recommendations-subject')
			if (sbjt) {
				sbjt.empty();
				imgpath = '/components/com_resources/images/loading.gif';
				var p = new Element('p', {'id':'loading-section'});
				var img = new Element('img', {'id':'loading-img','src':imgpath}).injectInside(p);
				p.injectInside(sbjt);
			
				var rid = $('rid');
				if (rid) {
					new Ajax('/index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid='+rid.value,{
							'method' : 'get',
							'update' : sbjt
						}).request();
				}
			}
		}
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.ResourcesRecommendations.initialize);