
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	if ($('#hubzilla')) {
		var audio = $('#hubzilla-roar')[0];
		$('#hubzilla').mouseenter(function() {
			audio.play();
		}).click(function() {
			audio.play();
		});
	}
});
