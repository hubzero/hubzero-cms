//--------------------------------------------------------------------------
//
//	Video Plugin
//
//	Author: 	Christopher Smoak
//	Version: 	1.0
//	Required: 	jQuery	
//
//--------------------------------------------------------------------------

if(!HUB) {
	var HUB = {};
}

//-----------

HUB.Video = {
	
	loading: function()
	{
		flash = false;
		seeking = false;
		track = "";
		transcriptLineActive = 0;
		transcriptBoxScrolling = false;
		browser = navigator.userAgent;
		canSendTracking = true;
		sendingTracking = false;
		doneLoading = false;
		
		//show loading graphic
		$jQ('<div id="overlayer"></div>').appendTo(document.body);
		
		//add the control bar
		$jQ("#video-container").append( HUB.Video.controls() );
		
		//can we play HTML5 video
		flash = (!!document.createElement('video').canPlayType) ? false : true;
		
		//watch player
		HUB.Video.player();
	},
	
	//-----
	
	doneLoading: function()
	{
		//make sure we didnt already run this - Firefox bug
		if(doneLoading)
		{
			return;
		}
		
		//get height & width of player
		resize_width = $jQ("#video-player").outerWidth(true);
		resize_height = $jQ("#video-player").outerHeight(true);
		
		var isPopup = (window.opener) ? true : false;
		
		if(flash) 
		{
			toolbar = 52;
			
			flash_width = $jQ("#video-flowplayer").outerWidth(true);
			flash_height = $jQ("#video-flowplayer").outerHeight(true);
			
			if (isPopup)
			{
				window.resizeTo(flash_width, flash_height + toolbar);
			}
		}
		else
		{
			//get the height of the toolbar
			//get window padding if any
			toolbar = window.outerHeight - window.innerHeight;
			padding = window.outerWidth - window.innerWidth;
		
			//if we are not using firefox lets attempt to resize popup
			if( !browser.match(/Firefox/g) && isPopup) {
				window.resizeTo(resize_width + padding, resize_height + toolbar);
			}
		}
		
		//remove the overlay
		$jQ('#overlayer').remove();
		
		//play video
		if(!flash) {
			$jQ('#video-player').get(0).play();
		}
		
		//set video Volume
		HUB.Video.setVolume(0.75);
		
		//control bar
		HUB.Video.controlBar();
		
		//handle subtitles
		HUB.Video.subtitles();
		
		if(flash)
		{
			$jQ('#full-screen').hide();
			
			//set height of player
			var player = HUB.Video.getPlayer();
			$jQ("#video-flowplayer").height( player.getClip().metaData.height );
		}
		
		
	},
	
	//-----
	
	player: function()
	{
		//remove native controls from video
		$jQ('#video-player').removeAttr("controls");
		
		//start media tracking if not flash (start for flash after load)
		if(!flash)
		{
			HUB.Video.startMediaTracking();
		}
		
		if(!flash) {
			//player events
			$jQ('#video-player').bind({
				canplay: function( e ) {
					HUB.Video.doneLoading();
					HUB.Video.locationHash();
				},
				timeupdate: function() {
					if(!seeking) 
					{
						HUB.Video.setProgress();
						HUB.Video.updateMediaTracking();
					}
				},
				volumechange: function( e ) {
					 HUB.Video.syncVolume();
				},
				seeked: function( e ) {
					seeking = true;
					var timeout = setTimeout("seeking=false;", 1000);
				},
				ended: function( e )
				{
					HUB.Video.endMediaTracking();
					HUB.Video.replay();
				}
			});
		} else {
			
			flowplayer("video-flowplayer", {src: "/components/com_resources/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, { 
				plugins: { controls: null },
				clip: { scaling: 'fit' },
				onStart: function() {
					HUB.Video.doneLoading();
					HUB.Video.locationHash();
					HUB.Video.syncVolume();
					HUB.Video.startMediaTracking();
					HUB.Video.flashSync();
				},
				onFinish: function() {
					HUB.Video.endMediaTracking();
					HUB.Video.replay();
				}
			});
			
		}
	},
	
	//-----
	
	controls: function()
	{
		
		return "<div id=\"video-toolbar\"> \
					<div id=\"volume-icon\"></div> \
					<div id=\"volume-bar\"></div> \
					<a id=\"play-pause\" href=\"#\" title=\"Play Video\">Pause</a> \
					<a id=\"link\" href=\"#\" title=\"Link to this Spot in Presentation\">Link</a> \
					<a id=\"full-screen\" href=\"#\" title=\"Full Screen\">Full Screen</a> \
					<div id=\"progress-bar\"></div> \
					<div id=\"media-progress\">00:00</div> \
					<div id=\"media-remainder\">00:00</div> \
				</div>";
	},
	
	//-----
	
	controlBar: function()
	{
		//fade out toolbar
		$jQ("#video-toolbar").delay(3000).fadeOut("slow");   
		
		//make the control bar draggable
		$jQ("#video-toolbar").draggable({
			cursor:'move', 
			containment: '#video-container',
			opacity:'0.8'
		});
		
		//play pause functionality
		$jQ('#play-pause').bind('click', function(e) {
			HUB.Video.playPause(true);
			e.preventDefault();
		});
		
		//play pause functionality
		$jQ('#link').bind('click', function(e) {
			HUB.Video.linkVideo();
			e.preventDefault();
		});
		
		//full screen mode
		$jQ('#full-screen').bind('click', function(e) {
			HUB.Video.fullScreen();
			e.preventDefault();
		});
		
		//progress bar
		HUB.Video.progressBar();
		
		//volume bar functionality
		HUB.Video.volumeBar();
		
		//hide or show controls based on mousemovement
		HUB.Video.toggleControls();
	},
	
	//-----
	
	playPause: function( click )
	{    
		var paused = HUB.Video.isPaused(),
			player = HUB.Video.getPlayer();
	            	
		if( paused ) {
			$jQ("#play-pause").css('background','url(/components/com_resources/assets/img/video/pause.png)'); 
			if( click ) 
				player.play();
		} else {
			$jQ("#play-pause").css('background','url(/components/com_resources/assets/img/video/play.png)');
			if( click )
				player.pause();
		}  
	},
	
	//-----
	
	getPlayer: function()
	{
		return (!flash) ? $jQ("#video-player").get(0) : flowplayer("video-flowplayer");
	},
	
	//-----
	
	isPaused: function()
	{
		return (!flash) ? $jQ("#video-player").get(0).paused : flowplayer("video-flowplayer").isPaused();
	},
	
	//-----
	
	fullScreen: function()
	{
		if(browser.match(/Safari|Chrome/g)) {
			$jQ("#video-player").get(0).webkitEnterFullScreen();
		} else if(browser.match(/Firefox/g)) {
			$jQ("#video-player").get(0).mozRequestFullScreen();
		}
	},
	
	//-----
	
	toggleControls: function()
	{
		$jQ("#video-container").live({
			mouseenter: function(e) {
				if(!$jQ('#video-toolbar').is(":visible") ) {
					$jQ('#video-toolbar').fadeIn('slow');
				}
			},
			mouseleave: function(e)
			{
				$jQ("#video-toolbar").stop(true).fadeOut("slow", function() {
					$jQ(this).css('opacity', '');
				});
			},
			mousemove: function(e) 
			{
				if(!$jQ('#video-toolbar').is(":visible") ) {
					$jQ('#video-toolbar').fadeIn('slow');
				}
			}
		});
	},
	
	//-----
	
	progressBar: function()
	{                               
		$jQ('#progress-bar').slider({
			step: .1,
			range: 'min',
			slide: function( event, ui ) {
				seeking = true;
				HUB.Video.setProgress( HUB.Video.getDuration() * (ui.value / 100) );
			},
			start: function( event, ui ) {
				seeking = true;
			}, 
			stop: function( event, ui ) {
				seeking = false;
				HUB.Video.seek( HUB.Video.getDuration() * (ui.value / 100)  );
			}
		});
	},
	
	//-----
	
	seek: function( time )
	{
		if(!flash) {
			$jQ('#video-player').get(0).currentTime = time;
		} else {
			flowplayer("video-flowplayer").seek(time);
		}
	},
	
	//-----
	
	flashSync: function() 
	{
		var flashSync = setInterval(function() {
			HUB.Video.setProgress();
			HUB.Video.updateMediaTracking();
		}, 1000);
	},
	
	//-----
	
	setProgress: function( time )
	{
		//get clip duration
		var progress,
			remainder,
			current = HUB.Video.getCurrent(),
			duration = HUB.Video.getDuration();
			
		//used the passed in value if we have one
		current = (time != null) ? time : current;
		
		//format the progress and whats left
		progress = HUB.Video.formatTime( current );
		remainder = HUB.Video.formatTime( (duration-current) );
		
		//if we can remvoe the first two 00's lets do so
		progress = (progress.substring(0,2) == 00) ? progress.substring(3) : progress;
		remainder = (remainder.substring(0,2) == 00) ? remainder.substring(3) : remainder;
		
		//insert times into sections in toolbar
		$jQ("#media-progress").html( progress );
		$jQ("#media-remainder").html( "-" + remainder );
		$jQ("#progress-bar").slider('value', ((current / duration) * 100));
	},
	
	//-----
	
	getCurrent: function()
	{
		return current = (!flash) ? $jQ("#video-player").get(0).currentTime : flowplayer("video-flowplayer").getTime();
	},
	
	//-----
	
	getDuration: function()
	{
	 	return duration = (!flash) ? $jQ("#video-player").get(0).duration : flowplayer("video-flowplayer").getClip().duration;
	},
	
	//-----
	
	formatTime: function( seconds )
	{
		var times = new Array(3600, 60, 1), time = '', tmp;
		   
		for(var i = 0; i < times.length; i++) 
		{
			tmp = Math.floor(seconds / times[i]);
		      
			if(tmp < 1) {
				tmp = '00';
			} else if(tmp < 10) {
		        tmp = '0' + tmp;
		    }
		    
			time += tmp;
		      
			if(i < 2) {
				time += ':';
			}
		      
			seconds = seconds % times[i];
		}
		return time;
	},
	
	//-----
	
	volumeBar: function()
	{
		//volume slider
		$jQ('#volume-bar').slider({
			step: 0.1,
			min:0,
			max:1,
			slide: function( event, ui ) {
				HUB.Video.volumeIcon( ui.value * 100 );
				HUB.Video.setVolume( ui.value );
			}
		});
	},
	
	//-----
	
	syncVolume: function()
	{
		//get the current volume
		var volume = HUB.Video.getVolume();
		
		//format the icon
		HUB.Video.volumeIcon( volume * 100 );
		
		//set the volume slider
		$jQ('#volume-bar').slider( "option", "value", volume );
	},
	
	//-----
	
	setVolume: function( level )
	{                                   
		if(!flash) {
			$jQ('#video-player').get(0).volume = level;
		} else {
			flowplayer("video-flowplayer").setVolume( level * 100 );
		}
	},
	
	//-----
	
	getVolume: function()
	{
		return (!flash) ? $jQ('#video-player').get(0).volume : flowplayer("video-flowplayer").getVolume() / 100;
	},
	
	//-----
	
	volumeIcon: function( volume )
	{
		var icon = $jQ('#volume-icon');
		
		if(volume == 0)
			icon.css('background-position','0 0');
			
		if( volume > 0 && volume < 33) 
			icon.css('background-position','-16px 0');
			
		if( volume > 33 && volume < 66) 
			icon.css('background-position','-32px 0');
			
		if( volume > 66) 
			icon.css('background-position','-48px 0');
	},
	
	//-----
	
	linkVideo: function()
	{
		var time_hash,
			url = window.location.href,
			time = HUB.Video.getCurrent();
		
		//make time usable
		time = HUB.Video.formatTime( time );
		
		//create time hash
		if(url.indexOf('?') == -1)
		{
			time_hash = '?time=' + time;
		}
		else
		{
			time_hash = '&time=' + time;
		}
		
		//remove current time form media tracking
		url = url.replace(/%3A/g, ':');
		url = url.replace(/&time=\d{2}:\d{2}:\d{2}/, '');
		
		//promt user with link to this spot in video
		prompt("Link to Current Position in Presentation", url + time_hash);
	},
	
	locationHash: function()
	{
		//make sure we didnt already run this - Firefox bug
		if(doneLoading)
		{
			return;
		}
		
		//var to hold time component
		var timeComponent;
		
		//get the url query string and clean up
		var urlQuery = window.location.search,
			urlQuery = urlQuery.replace("?", ""),
			urlQuery = urlQuery.replace(/&amp;/g, "&");
		
		//split query string into individual params
		var params = urlQuery.split('&');
		
		for(var i = 0; i < params.length; i++)
		{
			if(params[i].substr(0,4) == 'time')
			{
				timeComponent = params[i];
			}
		}
		
		// do we have a time component (time=00:00:00 or time=00%3A00%3A00)
		if(timeComponent != '' && timeComponent != null)
		{
			//get the hours, minutes, seconds
			var timeParts = timeComponent.split("=")[1].replace(/%3A/g, ':').split(':');
			
			//get time in seconds from hours, minutes, seconds
			var time = (parseInt(timeParts[0]) * 60 * 60) + (parseInt(timeParts[1]) * 60) + parseInt(timeParts[2]);
			
			//show resume & pause video
			HUB.Video.resume( HUB.Video.formatTime(time) );
			
			//seek to time
			HUB.Video.seek( time );
			HUB.Video.setProgress( time );
			
			//pause video
			var p = HUB.Video.getPlayer();
			p.pause();
			
			//we have handled
			doneLoading = true;
		}
	},
	
	//-----
	
	resume: function( time )
	{
		if (!$jQ("#video-container #resume").length)
		{
			//video container must be position relatively 
			//$jQ("#video-container").css('position', 'relative');
		
			//build replay content
			var resume = "<div id=\"resume\"> \
							<div id=\"resume-details\"> \
								<h2>Resume Playback?</h2> \
								<p>Would you like to resume video playback where you left off last time?</p> \
								<div id=\"time\">" + time + "</div> \
							</div> \
							<a id=\"restart-video\" href=\"#\">Play from the Beginning</a> \
							<a id=\"resume-video\" href=\"#\">Resume Video</a> \
						  </div>";
					
			//add replay to video container
			$jQ( resume ).hide().appendTo("#video-container").fadeIn("slow");
		
			//restart video button
			$jQ("#restart-video").on('click',function(event){
				event.preventDefault();
				HUB.Video.doReplay("#resume");
			});
		
			//resume video button
			$jQ("#resume-video").on('click',function(event){
				event.preventDefault();
				HUB.Video.doResume();
			});
		
			//stop clicks on resume
			$jQ("#resume").on('click',function(event){
				if(event.srcElement.id != 'restart-video' && event.srcElement.id != 'resume-video')
				{
					event.preventDefault();
				}
			});
		}
	},
	
	doResume: function() 
	{
		$jQ("#resume").fadeOut("slow", function() {
			//remove replay container
			$jQ(this).remove();
			
			//reset video containter positioning
			//$jQ("#video-container").css("position","static");
			
			//play video
			var p = HUB.Video.getPlayer();
			p.play();
		});
	},
	
	
	//-----------------------------------------------------
	//	Replay
	//-----------------------------------------------------
	
	replay: function()
	{
		//video container must be position relatively 
		//$jQ("#video-container").css('position', 'relative');
		
		//build replay content
		var replay = "<div id=\"replay\"> \
						<div id=\"replay-details\"> \
							<div id=\"title\"></div> \
							<div id=\"link\"> \
								<span>Share:</span><input type=\"text\" id=\"replay-link\" value=" + window.location + " /> \
								<a target='_blank' href=\"http://www.facebook.com/share.php?u=" + window.location + "\" id=\"facebook\" title=\"Share on Facebook\">Facebook</a> \
								<a target='_blank' href=\"http://twitter.com/home?status=Currently Watching: " + window.location +"\" id=\"twitter\" title=\"Share on Twitter\">Twitter</a> \
							</div> \
						</div> \
						<a id=\"replay-back\" href=\"#\">&laquo; Close Video</a> \
						<a id=\"replay-now\" href=\"#\">Replay Video</a> \
					  </div>";
					
		//add replay to video container
		$jQ( replay ).hide().appendTo("#video-container").fadeIn("slow");
		
		//remove close video button if not in popup
		if (!window.opener)
		{
			$jQ("#video-container").find('#replay-back').remove();
		}
		
		//set the title
		$jQ("#replay-details #title").html( "<span>Title:</span> " + document.title );
		
		//auto-select replay link
		$jQ("#replay-link").live("click", function(e) {
			this.select();
			e.preventDefault();
		});
		
		//replay video link
		$jQ("#replay-now").live("click", function(e) {
			HUB.Video.doReplay("#replay");
			e.preventDefault();
		});
		
		//close video
		$jQ("#replay-back").live("click", function(e) {
			window.close();
			e.preventDefault();
		});
	},
	
	doReplay: function( element )
	{
		//after replay container fades out
		$jQ( element ).fadeOut("slow", function() {
			//remove replay container
			$jQ(this).remove();
			
			//reset video containter positioning
			//$jQ("#video-container").css("position","static");
			
			//seek to beginning
			HUB.Video.seek( 0 ); 
			
			//start tracking again
			canSendTracking = true;
			sendingTracking = false;
			HUB.Video.startMediaTracking(); 
			
			//get player and play
			player = HUB.Video.getPlayer();
			player.play();
		});
	},
	
	//-----------------------------------------------------
	//	Media Tracking
	//-----------------------------------------------------
	
	startMediaTracking: function()
	{
		HUB.Video.mediaTrackingEvent('start');
		
		//start timer
		var timer = setInterval(function() {
			canSendTracking = true;
		}, 5000);
	},
	
	updateMediaTracking: function()
	{
		HUB.Video.mediaTrackingEvent('update');
	},
	
	endMediaTracking: function()
	{
		canSendTracking = true;
		sendingTracking = false;
		HUB.Video.mediaTrackingEvent('ended');
	},
	
	mediaTrackingEvent: function( eventType )
	{
		//check to make sure we can send tracking and that were not already in the progress
		if(canSendTracking && !sendingTracking)
		{
			//we have started sending
			sendingTracking = true;
			
			//get the resource ID and current player time
			var resourceId, playerTime, playerDuration;
			if(!flash)
			{
				resourceId = $jQ(HUB.Video.getPlayer()).attr('data-mediaid');
				playerTime = HUB.Video.getCurrent();
				playerDuration = HUB.Video.getDuration();
			}
			else
			{
				resourceId = $jQ("#"+HUB.Video.getPlayer().id()).attr('data-mediaid');
				playerTime = HUB.Video.getCurrent();
				playerDuration = HUB.Video.getDuration();
			}

			//craft post url
			var component = 'resources';
			var componentSearch = window.location.href.match(/\.(org|edu)\/([a-z]+)\//);

			if(componentSearch && componentSearch[2].length)
			{
				component = componentSearch[2];
			}

			var url = '/index.php?option=com_'+component+'&controller=media&task=tracking&no_html=1';

			//make ajax call
			$jQ.ajax({
				type: 'POST',
				dataType: 'json',
				url: url,
				data: {
					event: eventType,
					resourceid: resourceId,
					time: playerTime,
					duration: playerDuration
				},
				error: function( jqXHR, status, error )
				{
					console.log(error);
				},
				success: function( data, status, jqXHR )
				{},
				complete: function( jqXHR, status )
				{
					//we have to wait another 5 seconds to update
					canSendTracking = false;
					sendingTracking = false;
				}
			});
			
		}
	},
	
	//-----------------------------------------------------
	//	Subtitles
	//-----------------------------------------------------
	
	subtitles: function()
	{
		var auto       = false
			sub_titles = HUB.Video.getSubtitles();
		
		//create elements on page to hold subtitles
		if(sub_titles.length > 0) {
			
			//setup subtitle picker
			HUB.Video.setupSubtitlePicker( sub_titles );
			
			//setup transcript viewer
			HUB.Video.transcriptSetup( sub_titles );
			
			//sync subtitles
			var syncInterval = setInterval( 
				function() {
					HUB.Video.syncSubtitles( sub_titles );
				}, 100);
		}
	},
	
	//-----
	
	setupSubtitlePicker: function( sub_titles )
	{
		$jQ("#video-toolbar").after("<div id=\"video-subtitles\"></div>");
		$jQ("#full-screen").after("<ul id=\"subtitle-picker\"><li><a href=\"javascript:void(0);\">CC</a><ul id=\"cc\"><li><a class=\"active\" rel=\"\" href=\"#\">None</a></ul></li></ul>");

		for(n=0; n<sub_titles.length; n++) {
			var sub = sub_titles[n],
				sub_lang = sub.lang.toLowerCase(),
				sub_lang_text = sub.lang;
			
			//do we auto play
			if(parseInt(sub.auto))
			{
				auto = true;
				track = sub_lang;
			}
			
			$jQ("#video-subtitles").append("<div id=\"" + sub_lang + "\"></div>");
			$jQ("#cc").append("<li><a rel=\"" + sub_lang + "\" class=\"" + sub_lang + "\" href=\"javascript:void(0);\">" + sub_lang_text + "</a></li>");
		}
		
		//if we are auto showing subs make sure picker reflects that
		if(auto) {
			$jQ("#subtitle-picker a").addClass("active");
			$jQ("#cc a").removeClass("active");
			$jQ("#cc a." + track).addClass("active");
		}
		
		$jQ("#subtitle-picker ul a").live("click", function(e) {
			track = this.rel;
			
			if(track != "") {
				$jQ("#subtitle-picker a").addClass("active");
			} else {
				$jQ("#subtitle-picker a").removeClass("active");
			}
			
			$jQ("#cc a").removeClass("active");
			$jQ(this).addClass("active");
			
			e.preventDefault();
		});
	},
	
	//-----
	
	syncSubtitles: function( sub_titles )
	{
		current = HUB.Video.getCurrent();
		
		//get the subs for the track we have selected
		for(i in sub_titles) {
			if(sub_titles[i].lang.toLowerCase() == track) {
				var subs = sub_titles[i].subs;
			}
		}
		
		//clear the subtitle tracks between 
		$jQ("#video-subtitles div").hide().html("");
		
		for(i in subs) {
			start = subs[i].start;
			end = subs[i].end;
			text = subs[i].text;
			if(current >= start && current <= end) {
				$jQ("#video-subtitles #" + track).show().html( text.replace( "\n","<br />") );
			}
		}
	},
	
	//-----
	
	getSubtitles: function()
	{
		var subs = new Array(),
			sub_files = $jQ("div[data-type=subtitle]");
			
		//loop through each subs file and get the contents then add to subs object
		sub_files.each(function(i){
			var lang = $jQ(this).attr("data-lang"),
				src  = $jQ(this).attr("data-src"),
				auto = $jQ(this).attr("data-autoplay"); 
			
			$jQ.ajax({
				url: src,
				async: false,
				dataType: 'html',
				success: function( content ) {
					var parsed = HUB.Video.parseSubtitles( content );
					sub = { "lang" : lang, "subs" : parsed, "auto" : auto };
					subs.push(sub);
				}
			});
		});
		
		//return subs object
		return subs;
	},
	
	//-----
	
	parseSubtitles: function( subtitle_content )
	{	
		var content = "",
			srt 	= [],
			parts	= [],
			id		= "",
			start	= "",
			end		= "",
			text	= ""
			subtitles = [];
			
			
		//replace carriage returns
		content = subtitle_content.replace(/\r\n|\r|\n/g, '\n');
		
		//strip out empty spaces
		content = HUB.Video.strip( content );
		
		//split up each
		srt = content.split("\n\n");
		
		//for each individual subtitle
		for(n=0; n<srt.length; n++) {
			parts = srt[n].split("\n");
			
			id = parseInt(parts[0]);
			
			//get the sub start time
			start = parts[1].split(' --> ')[0];
			start = HUB.Video.strip( start );
			start = HUB.Video.toSeconds( start );
			
			//get the sub end time
			end = parts[1].split(' --> ')[1];
			end = HUB.Video.strip( end );
		 	end = HUB.Video.toSeconds( end );
			
			//get the sub text
			if(parts.length > 3) {
				for(i=2,text="";i<parts.length;i++) {
					text += parts[i] + "\n";
				}
			} else {
				text = parts[2];
			}
			
			//make sure we have text
			if(text == undefined)
			{
				text = '';
			}
			
			//remove extra chars
			text = text.replace(">>","");
			
			//create object for each sub
			subtitle = { "start" : start, "end" : end, "text" : text };
			subtitles.push(subtitle);
		}
		
		//return subtitles 
		return subtitles;
	},
	
	//-----
	
	transcriptSetup: function( sub_titles )
	{
		//dont add transcript stuff if we dont have needed html
		if (!$jQ('#transcript-container').length)
		{
			return;
		}
		
		//add transcript toggle and add container
		$jQ('#video-container')
			.append('<a href="javascript:void(0);" id="transcript-toggle">Show Transcript</a>');
		
		//add subtitles
		for (var i = 0, n = sub_titles.length; i < n; i++)
		{
			var language = sub_titles[i].lang,
				subs     = sub_titles[i].subs;
			
			//add language to subtitle language picker
			$jQ('#transcript-selector').append('<option value="' + language.toLowerCase() + '">' + language + '</option>');
			
			//add container for each language transcript
			$jQ('#transcript-container #transcripts').append('<div class="transcript transcript-' + language.toLowerCase() + '"></div>')
			
			for(var a = 0, b = subs.length; a < b; a++)
			{
				var time = subs[a].start,
					text = subs[a].text;
					line = '<div class="transcript-line" data-time="' + time + '"><div class="transcript-line-time">' + HUB.Video.formatTime( time ) + '</div><div class="transcript-line-text">' + text + '</div></div>'
				$jQ('.transcript-' + language.toLowerCase()).append( line );
			}
		}
		
		//show first transcript
		$jQ('#transcripts').find('.transcript').first().show();
		
		//further setup
		HUB.Video.transcriptToggle();
		HUB.Video.transcriptFontChanger();
		HUB.Video.transcriptSearch();
		HUB.Video.transcriptJumpTo();
		
		//sync transcript
		setInterval(function() {
			HUB.Video.transcriptSync( sub_titles );
		}, 500);
	},
	
	//-----
	
	transcriptToggle: function()
	{
		//add click event to transcript toggle
		$jQ('#video-container').on('click', '#transcript-toggle', function(event) {
			event.preventDefault();
			
			//if we opened via popup, must resize window
			if (window.opener)
			{
				var transcriptContainerHeight = $jQ('#transcript-container').outerHeight(true),
					windowWidth = (!flash) ? window.innerWidth : $jQ("#video-flowplayer").outerWidth(true),
					windowHeight = (!flash) ? window.outerHeight : $jQ("#video-flowplayer").outerHeight(true) + 52;
					
				if ($jQ('#transcript-container').is(':visible'))
				{
					window.resizeTo(windowWidth, windowHeight - transcriptContainerHeight);
				}
				else
				{
					window.resizeTo(windowWidth, windowHeight + transcriptContainerHeight);
				}
			}
			
			//change title
			if ($jQ(this).text() == 'Show Transcript')
			{
				$jQ(this).text('Hide Transcript');
			}
			else
			{
				$jQ(this).text('Show Transcript');
			}
			
			//slide toggle the transcript pane
			$jQ('#transcript-container').slideToggle();
		});
		
		//handle switching languages
		$jQ('#transcript-selector').on('change', function(event) {
			var language = $jQ(this).val();
			$jQ('#transcripts').find('.transcript').hide();
			$jQ('#transcripts').find('.transcript-' + language).show();
		});
	},
	
	//-----
	
	transcriptFontChanger: function()
	{
		//make font smaller
		$jQ('#font-smaller').on('click',function(event){
			event.preventDefault();
			var transcriptLines   = $jQ('.transcript-line'), 
				currentFontSize   = parseFloat(transcriptLines.css('font-size'), 10),
				currentLineHeight = parseFloat(transcriptLines.css('line-height'), 10),
				newFontSize       = currentFontSize - 2,
				newLineHeight     = currentLineHeight - 2;
				
				if (newFontSize >= 8)
				{
					transcriptLines.css({
						'font-size': newFontSize + 'px',
						'line-height': newLineHeight + 'px'
					});
					
					if (newFontSize == 8)
					{
						$jQ('#font-smaller').addClass('inactive');
					}
				}
				
				$jQ('#font-bigger').removeClass('inactive');
		});
		
		//make font bigger
		$jQ('#font-bigger').on('click',function(event){
			event.preventDefault();
			var transcriptLines   = $jQ('.transcript-line'), 
				currentFontSize   = parseFloat(transcriptLines.css('font-size'), 10),
				currentLineHeight = parseFloat(transcriptLines.css('line-height'), 10),
				newFontSize       = currentFontSize + 2,
				newLineHeight     = currentLineHeight + 2;
			
			if (newFontSize <= 18)
			{
				transcriptLines.css({
					'font-size': newFontSize + 'px',
					'line-height': newLineHeight + 'px'
				});
				
				if (newFontSize == 18)
				{
					$jQ('#font-bigger').addClass('inactive');
				}
			}
			
			$jQ('#font-smaller').removeClass('inactive');
		});
	},
	
	//-----
	
	transcriptSearch: function()
	{
		$jQ('#transcript-search').on('keyup change', function(event) {
			$jQ('.transcript-line-text').removeHighlight();
			$jQ('.transcript-line-text').highlight( $jQ(this).val() );
		});
	},
	
	//-----
	
	transcriptJumpTo: function()
	{
		$jQ('.transcript-line').on('click', function(event) {
			event.preventDefault();
			var time = $jQ(this).data('time');
			
			HUB.Video.seek( time );
		});
	},
	
	//-----
	
	transcriptSync: function( sub_titles )
	{
		var currentTime = HUB.Video.getCurrent(),
			currentTranscript = $jQ('#transcript-selector').val();
		
		//get the subs for the track we have selected
		for(i in sub_titles) {
			if(sub_titles[i].lang.toLowerCase() == currentTranscript) {
				var subs = sub_titles[i].subs;
			}
		}
		
		//flag to know if user is scrolling in box
		$jQ('#transcripts').on('scroll', function(event) {
			transcriptBoxScrolling = true;
			clearTimeout($jQ.data(this, 'scrollTimer'));
			$jQ.data(this, 'scrollTimer', setTimeout(function() {
				transcriptBoxScrolling = false;
			}, 250));
		});
		
		//remove all previously set active lines
		$jQ('.transcript-line').removeClass('active');
		
		//set our active transcript line
		for (i in subs)
		{
			var start = subs[i].start,
				end   = subs[i].end,
				text  = subs[i].text;
				
			if (currentTime >= start && currentTime <= end)
			{
				//add active class to active transcript line
				$jQ('.transcript-line').eq(i).addClass('active')
				
				//if were not scrolling in the box
				//only scroll if we just switched to a new line
				if (!transcriptBoxScrolling && transcriptLineActive != i)
				{
					//get height of each transcript line to know how far to scroll down
					var lineHeight = $jQ('.transcript-line').outerHeight(true);
					
					//only scroll after the half way point down
					if (lineHeight * i > $jQ('#transcripts').outerHeight(true) / 2)
					{
						var middle = $jQ('#transcripts').outerHeight(true) / 2;
						
						$jQ('#transcripts').scrollTo(lineHeight * i - middle + lineHeight, 300, 'easeInOutQuad' );
					}
				}
				
				//set active line
				transcriptLineActive = i
			}
		}
	},
	
	//-----
	
	strip: function( content )
	{
		return content.replace(/^\s+|\s+$/g,"");
	},
	
	//-----
	
	toSeconds: function( time )
	{
		var parts = [],
			seconds = 0.0;
			
		if( time ) {
			parts = time.split(':');
			
			for( i=0; i < parts.length; i++ ) {
	        	seconds = seconds * 60 + parseFloat(parts[i].replace(',', '.'))
			}
		}
		
		return seconds;
	},
	
	//-----
	
	ucfirst: function( string )
	{
		var first = string.substr(0, 1);
		return first.toUpperCase() + string.substr(1);
	}
};

//------------------------------------------------------

var $jQ = jQuery.noConflict();

$jQ(document).ready(function() {
	HUB.Video.loading();
});



/*

highlight v4

Highlights arbitrary terms.

<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>

MIT license.

Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>

*/

jQuery.fn.highlight = function(pat) {
 function innerHighlight(node, pat) {
  var skip = 0;
  if (node.nodeType == 3) {
   var pos = node.data.toUpperCase().indexOf(pat);
   if (pos >= 0) {
    var spannode = document.createElement('span');
    spannode.className = 'highlight';
    var middlebit = node.splitText(pos);
    var endbit = middlebit.splitText(pat.length);
    var middleclone = middlebit.cloneNode(true);
    spannode.appendChild(middleclone);
    middlebit.parentNode.replaceChild(spannode, middlebit);
    skip = 1;
   }
  }
  else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
   for (var i = 0; i < node.childNodes.length; ++i) {
    i += innerHighlight(node.childNodes[i], pat);
   }
  }
  return skip;
 }
 return this.length && pat && pat.length ? this.each(function() {
  innerHighlight(this, pat.toUpperCase());
 }) : this;
};

jQuery.fn.removeHighlight = function() {
 return this.find("span.highlight").each(function() {
  this.parentNode.firstChild.nodeName;
  with (this.parentNode) {
   replaceChild(this.firstChild, this);
   normalize();
  }
 }).end();
};
