/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
		browser = navigator.userAgent;
		
		//show loading graphic
		$jQ('<div id="overlayer"></div>').appendTo(document.body);
		
		//wrap video with container div
		//$jQ("#video-player").wrap('<div id="video-container" />');
		
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
		//get height & width of player
		resize_width = $jQ("#video-player").outerWidth(true);
		resize_height = $jQ("#video-player").outerHeight(true);
			
		if(flash) 
		{
			toolbar = 90;
			
			flash_width = $jQ("#video-flowplayer").outerWidth(true);
			flash_height = $jQ("#video-flowplayer").outerHeight(true);
			window.resizeTo(flash_width, flash_height + toolbar);
		}
		else
		{
			//get the height of the toolbar
			//get window padding if any
			toolbar = window.outerHeight - window.innerHeight;
			padding = window.outerWidth - window.innerWidth;
		
			//if we are not using firefox lets attempt to resize popup
			if( !browser.match(/Firefox/g) ) {
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
		HUB.Video.setVolume(0);
		
		//control bar
		HUB.Video.controlBar();
		
		//handle subtitles
		HUB.Video.subtitles();
	},
	
	//-----
	
	player: function()
	{
		//remove native controls from video
		$jQ('#video-player').removeAttr("controls");
		
		if(!flash) {
			//player events
			$jQ('#video-player').bind({
				timeupdate: function() {
					HUB.Video.setProgress();
				},
				volumechange: function( e ) {
					 HUB.Video.syncVolume();
				},
				canplay: function( e ) {
					HUB.Video.doneLoading();
					HUB.Video.locationHash();
				},
				seeked: function( e ) { }
			});
		} else {
			
			flowplayer("video-flowplayer", {src: "/core/components/com_resources/site/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, { 
			   	plugins: { controls: null }, 
				onStart: function() {
					HUB.Video.setVolume(0);
					HUB.Video.doneLoading();
					HUB.Video.locationHash();
					//HUB.Video.flashSyncSlides();
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
			$jQ("#play-pause").css('background','url(/core/components/com_resources/site/assets/img/pause.png)'); 
			if( click ) 
				player.play();
		} else {
			$jQ("#play-pause").css('background','url(/core/components/com_resources/site/assets/img/play.png)');
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
		$jQ('body')
			.on('mouseenter', "#video-container", function(e) {
				if(!$jQ('#video-toolbar').is(":visible") ) {
					$jQ('#video-toolbar').fadeIn('slow');
				}
			})
			.on('mouseleave', "#video-container", function(e) {
				$jQ("#video-toolbar").stop(true).fadeOut("slow", function() {
					$jQ(this).css('opacity', '');
				});
			})
			.on('mousemove', "#video-container", function(e) {
				if(!$jQ('#video-toolbar').is(":visible") ) {
					$jQ('#video-toolbar').fadeIn('slow');
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
	
	locationHash: function()
	{
		var time, time_parts, time_min, time_sec, time_total,
			hash = window.location.hash,
			time_regex = /time-\d{1,}\:\d{2}/;
		
		//if hash is a time
		if(hash.match(time_regex)) {
			time = hash.substr(6);
			time_parts = time.split(":");
			time_min = parseInt(time_parts[0]);
			time_sec = parseInt(time_parts[1]);
			time_total = ( time_min * 60 ) + time_sec;
			HUB.Video.seek( time_total );
		}
	},
	
	//-----------------------------------------------------
	//
	//-----------------------------------------------------
	
	subtitles: function()
	{
		var sub_titles = [];
		var auto = false;
		
		sub_titles = HUB.Video.getSubtitles();
		
		//create elements on page to hold subtitles
		if(sub_titles.length > 0) {
			$jQ("#video-toolbar").after("<div id=\"video-subtitles\"></div>");
			$jQ("#full-screen").after("<ul id=\"subtitle-picker\"><li><a href=\"javascript:void(0);\">CC</a><ul id=\"cc\"><li><a class=\"active\" rel=\"\" href=\"#\">None</a></ul></li></ul>");

			for(n=0; n<sub_titles.length; n++) {
				var sub = sub_titles[n];
				var sub_lang = sub.lang;
				
				//do we automatically want to show a sub
				if( sub_lang.substr(sub_lang.length - 4) == "auto" ) {
					lang_text = sub_lang.substr(0, (sub_lang.length - 5))
					track = sub_lang;
					auto = true;
				} else {
					lang_text = sub_lang
				}
				
				$jQ("#video-subtitles").append("<div id=\"" + sub_lang + "\"></div>");
				$jQ("#cc").append("<li><a rel=\"" + sub_lang + "\" class=\"" + sub_lang + "\" href=\"javascript:void(0);\">" + HUB.Video.ucfirst(lang_text) + "</a></li>");
			}
			
			//if we are auto showing subs make sure picker reflects that
			if(auto) {
				$jQ("#subtitle-picker a").addClass("active");
				$jQ("#cc a").removeClass("active");
				$jQ("#cc a." + track).addClass("active");
			}
			
			
			$jQ("#subtitle-picker ul a").on("click", function(e) {
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
			
			var syncInterval = setInterval( 
				function() {
					HUB.Video.syncSubtitles( sub_titles );
				}, 100);
		}
	},
	
	//-----
	
	syncSubtitles: function( sub_titles )
	{
		current = HUB.Video.getCurrent();
		
		//get the subs for the track we have selected
		for(i in sub_titles) {
			if(sub_titles[i].lang == track) {
				var subs = sub_titles[i].subs;
			}
		}
		
		//clear the subtitle tracks between 
		$jQ("#video-subtitles div").html("");
		
		for(i in subs) {
			start = subs[i].start;
			end = subs[i].end;
			text = subs[i].text;
			if(current >= start && current <= end) {
				$jQ("#video-subtitles #" + track).html( text.replace( "\n","<br />") );
			}
		}
	},
	
	//-----
	
	getSubtitles: function()
	{
		var count = 0,
			parsed = "",
			subs = new Array(),
			sub_files = $jQ("#video-player div[data-type=subtitle]");
		
		//loop through each subs file and get the contents then add to subs object
		sub_files.each(function(i){
			var lang = $jQ(this).attr("data-lang").toLowerCase(),
				src = $jQ(this).attr("data-src");
			
			$jQ.ajax({
				url: src,
				async: false,
				success: function( content ) {
					parsed = HUB.Video.parseSubtitles( content );
					sub = { "lang" : lang, "subs" : parsed };
					subs.push(sub);
					count++;
				}
			});
		});
		
		//return subs object
		if(count == sub_files.length) {
			return subs;
		}
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

