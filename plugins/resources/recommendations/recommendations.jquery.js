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
if (!jq) {
	var jq = $;
}

HUB.Plugins.ResourcesRecommendations = {
	jQuery: jq,
	
	initialize: function() {
		// Recommendations web service
		var recoms = $('#recommendations-section');
		if (recoms) {
			var sbjt = $('#recommendations-subject')
			if (sbjt) {
				//sbjt.empty();
				//imgpath = '/components/com_resources/images/loading.gif';
				//var p = new Element('p', {'id':'loading-section'});
				//var img = new Element('img', {'id':'loading-img','src':imgpath}).injectInside(p);
				//p.injectInside(sbjt);
			
				var rid = $('#rid');
				if (rid) {
					$.get('/index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid='+rid.val(), {}, function(data) {
						$(sbjt).html(data);
					});
				}
			}
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.ResourcesRecommendations.initialize();
});