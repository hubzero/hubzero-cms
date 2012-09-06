
window.addEvent('domready', function(){
	if ($('hubzilla')) {
		var audio = $("hubzilla-roar")[0];
		$('hubzilla').addEvent('mouseenter', function() {
			audio.play();
		});
		$('hubzilla').addEvent('click', function() {
			audio.play();
		});
	}
});
