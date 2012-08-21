
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	if ($('#hubzilla')) {
		//$('<audio id="gojira-roar" preload="auto"><source src="/templates/gojira/roar.mp3"></source><source src="/templates/gojira/roar.ogg"></source></audio>').appendTo('#gojira');
		//$('#hubzilla').append('<div id="hubzilla-hover" style="left: 60%;top: 90px;position: absolute;width: 175px;height: 238px;"></div>');

		var audio = $("#hubzilla-roar")[0];
		$("#hubzilla").mouseenter(function() {
			audio.play();
		}).click(function() {
			audio.play();
		});
	}
});
