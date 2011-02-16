//
//	resource slideshow
//

if (!HUB) {
	var HUB = {};
}
HUB.ResourceIntroVideo = {


		clip: function(clips)
		//databases
		{
		var autoPlay = false;
		if (clips.length >= 2)
			autoPlay = true;
		
		flowplayer("hubfancy-player","http://builds.flowplayer.netdna-cdn.com/65034/35393/flowplayer.commercial-3.2.5-6.swf",  			{

			controls: {
				 playlist: true
			  },		
			play: {
					opacity: 0
				},
			clip: {			
					autoPlay: autoPlay,
					autoBuffering: false
						
				},
			playlist: clips
						
		});
		}
	
}

	
	
