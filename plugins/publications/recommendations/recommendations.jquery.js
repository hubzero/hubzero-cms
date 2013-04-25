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

HUB.Plugins.PublicationRecommendations = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		// Recommendations web service
		var recoms = $('#recommendations-section');
		if (recoms) {
			var sbjt = $('#recommendations-subject')
			if (sbjt) {
				var rid = $('#rid');
				if (rid) {
					$.get('/index.php?option=com_publications&task=plugin&trigger=onPublicationsRecoms&no_html=1&rid='+rid.val(), {}, function(data) {
						$(sbjt).html(data);
					});
				}
			}
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.PublicationRecommendations.initialize();
});