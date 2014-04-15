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

jQuery(document).ready(function($){
	var recoms = $('#recommendations-section');
	if (recoms.length) {
		var sbjt = $('#recommendations-subject');
		if (sbjt.length) {

			var rid = $('#rid');
			if (rid.length) {
				$.get('/index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid='+rid.val(), {}, function(data) {
					$(sbjt).html(data);
				});
			}
		}
	}
});