/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/video.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Courses Videos
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Video = {

	jQuery: jq,

	loading: function()
	{
		var $ = this.jQuery;

		flash = false;
		seeking = false;
		track = "";
		browser = navigator.userAgent;

		// Show loading graphic
		$("#video-container").append('<div id="overlayer"></div>');

		// Add the control bar
		$("#video-container").append( HUB.Video.controls() );

		// Can we play HTML5 video
		flash = (!!document.createElement('video').canPlayType) ? false : true;

		// Watch player
		HUB.Video.player();
	},

	//---------------------

	doneLoading: function()
	{
		var $ = this.jQuery;

		//get height & width of player
		/*resize_width = $("#video-player").outerWidth(true);
		resize_height = $("#video-player").outerHeight(true);
			
		if(flash)
		{
			toolbar = 90;
			
			flash_width = $("#video-flowplayer").outerWidth(true);
			flash_height = $("#video-flowplayer").outerHeight(true);
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
		}*/
		
		//remove the overlay
		$('#overlayer').remove();
		
		//play video
		if(!flash) {
			$('#video-player').get(0).play();
		}

		//set video Volume
		HUB.Video.setVolume(0.7);
		
		//control bar
		HUB.Video.controlBar();
		
		//handle subtitles
		HUB.Video.subtitles();
	},
	
	//-----
	
	player: function()
	{
		var $ = this.jQuery;

		//remove native controls from video
		$('#video-player').removeAttr("controls");
		
		if(!flash) {
			//player events
			$('#video-player').bind({
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
			
			flowplayer("video-flowplayer", {src: "/components/com_courses/assets/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
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
		var $ = this.jQuery;

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
		var $ = this.jQuery;

		//fade out toolbar
		$("#video-toolbar").delay(3000).fadeOut("slow");

		//make the control bar draggable
		$("#video-toolbar").draggable({
			cursor:'move',
			containment: '#video-container',
			opacity:'0.8'
		});
		
		//play pause functionality
		$('#play-pause').bind('click', function(e) {
			HUB.Video.playPause(true);
			e.preventDefault();
		});
		
		//full screen mode
		$('#full-screen').bind('click', function(e) {
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
		var $ = this.jQuery;

		var paused = HUB.Video.isPaused(),
			player = HUB.Video.getPlayer();

		if( paused ) {
			$("#play-pause").css('background','url(/components/com_courses/assets/img/pause.png)');
			if( click )
				player.play();
		} else {
			$("#play-pause").css('background','url(/components/com_courses/assets/img/play.png)');
			if( click )
				player.pause();
		}
	},

	//-----

	getPlayer: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $("#video-player").get(0) : flowplayer("video-flowplayer");
	},
	
	//-----
	
	isPaused: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $("#video-player").get(0).paused : flowplayer("video-flowplayer").isPaused();
	},
	
	//-----
	
	fullScreen: function()
	{
		var $ = this.jQuery;

		if(browser.match(/Safari|Chrome/g)) {
			$("#video-player").get(0).webkitEnterFullScreen();
		} else if(browser.match(/Firefox/g)) {
			$("#video-player").get(0).mozRequestFullScreen();
		}
	},
	
	//-----
	
	toggleControls: function()
	{
		var $ = this.jQuery;

		$("#video-container").live({
			mouseenter: function(e) {
				if(!$('#video-toolbar').is(":visible") ) {
					$('#video-toolbar').fadeIn('slow');
				}
			},
			mouseleave: function(e)
			{
				$("#video-toolbar").stop(true).fadeOut("slow", function() {
					$(this).css('opacity', '');
				});
			},
			mousemove: function(e)
			{
				if(!$('#video-toolbar').is(":visible") ) {
					$('#video-toolbar').fadeIn('slow');
				}
			}
		});
	},
	
	//-----
	
	progressBar: function()
	{
		var $ = this.jQuery;

		$('#progress-bar').slider({
			step: 0.1,
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
		var $ = this.jQuery;

		if(!flash) {
			$('#video-player').get(0).currentTime = time;
		} else {
			flowplayer("video-flowplayer").seek(time);
		}
	},
	
	//-----
	
	setProgress: function( time )
	{
		var $ = this.jQuery;

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
		$("#media-progress").html( progress );
		$("#media-remainder").html( "-" + remainder );
	},
	
	//-----
	
	getCurrent: function()
	{
		var $ = this.jQuery;

		return current = (!flash) ? $("#video-player").get(0).currentTime : flowplayer("video-flowplayer").getTime();
	},
	
	//-----
	
	getDuration: function()
	{
		var $ = this.jQuery;

	 	return duration = (!flash) ? $("#video-player").get(0).duration : flowplayer("video-flowplayer").getClip().duration;
	},
	
	//-----
	
	formatTime: function( seconds )
	{
		var $ = this.jQuery;

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
		var $ = this.jQuery;

		//volume slider
		$('#volume-bar').slider({
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
		var $ = this.jQuery;

		//get the current volume
		var volume = HUB.Video.getVolume();
		
		//format the icon
		HUB.Video.volumeIcon( volume * 100 );
		
		//set the volume slider
		$('#volume-bar').slider( "option", "value", volume );
	},
	
	//-----
	
	setVolume: function( level )
	{
		var $ = this.jQuery;

		if(!flash) {
			$('#video-player').get(0).volume = level;
		} else {
			flowplayer("video-flowplayer").setVolume( level * 100 );
		}
	},
	
	//-----
	
	getVolume: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $('#video-player').get(0).volume : flowplayer("video-flowplayer").getVolume() / 100;
	},
	
	//-----
	
	volumeIcon: function( volume )
	{
		var $ = this.jQuery;

		var icon = $('#volume-icon');
		
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
		var $ = this.jQuery;

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
	
	//------
	
	subtitles: function()
	{
		var $ = this.jQuery;

		var sub_titles = [];
		var auto = false;
		
		sub_titles = HUB.Video.getSubtitles();
		
		//create elements on page to hold subtitles
		if(sub_titles.length > 0) {
			$("#video-toolbar").after("<div id=\"video-subtitles\"></div>");
			$("#full-screen").after("<ul id=\"subtitle-picker\"><li><a href=\"javascript:void(0);\">CC</a><ul id=\"cc\"><li><a class=\"active\" rel=\"\" href=\"#\">None</a></ul></li></ul>");

			for(n=0; n<sub_titles.length; n++) {
				var sub = sub_titles[n];
				var sub_lang = sub.lang;
				
				//do we automatically want to show a sub
				if( sub_lang.substr(sub_lang.length - 4) == "auto" ) {
					lang_text = sub_lang.substr(0, (sub_lang.length - 5));
					track = sub_lang;
					auto = true;
				} else {
					lang_text = sub_lang;
				}
				
				$("#video-subtitles").append("<div id=\"" + sub_lang + "\"></div>");
				$("#cc").append("<li><a rel=\"" + sub_lang + "\" class=\"" + sub_lang + "\" href=\"javascript:void(0);\">" + HUB.Video.ucfirst(lang_text) + "</a></li>");
			}
			
			//if we are auto showing subs make sure picker reflects that
			if(auto) {
				$("#subtitle-picker a").addClass("active");
				$("#cc a").removeClass("active");
				$("#cc a." + track).addClass("active");
			}
			
			
			$("#subtitle-picker ul a").live("click", function(e) {
				track = this.rel;
				
				if(track != "") {
					$("#subtitle-picker a").addClass("active");
				} else {
					$("#subtitle-picker a").removeClass("active");
				}
				
				$("#cc a").removeClass("active");
				$(this).addClass("active");
				
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
		var $ = this.jQuery;

		current = HUB.Video.getCurrent();
		
		//get the subs for the track we have selected
		for(i in sub_titles) {
			if(sub_titles[i].lang == track) {
				var subs = sub_titles[i].subs;
			}
		}
		
		//clear the subtitle tracks between
		$("#video-subtitles div").html("");
		
		for(i in subs) {
			start = subs[i].start;
			end = subs[i].end;
			text = subs[i].text;
			if(current >= start && current <= end) {
				$("#video-subtitles #" + track).html( text.replace( "\n","<br />") );
			}
		}
	},
	
	//-----
	
	getSubtitles: function()
	{
		var $ = this.jQuery;

		var count = 0,
			parsed = "",
			subs = new Array(),
			sub_files = $("#video-player div[data-type=subtitle]");
		
		//loop through each subs file and get the contents then add to subs object
		sub_files.each(function(i){
			var lang = $(this).attr("data-lang").toLowerCase(),
				src = $(this).attr("data-src");
			
			$.ajax({
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
		var $ = this.jQuery;

		var content = "",
			srt     = [],
			parts   = [],
			id      = "",
			start   = "",
			end     = "",
			text    = "",
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
		var $ = this.jQuery;

		return content.replace(/^\s+|\s+$/g,"");
	},
	
	//-----
	
	toSeconds: function( time )
	{
		var $ = this.jQuery;

		var parts = [],
			seconds = 0.0;
			
		if( time ) {
			parts = time.split(':');
			
			for( i=0; i < parts.length; i++ ) {
				seconds = seconds * 60 + parseFloat(parts[i].replace(',', '.'));
			}
		}
		
		return seconds;
	},
	
	//-----
	
	ucfirst: function( string )
	{
		var $ = this.jQuery;

		var first = string.substr(0, 1);
		return first.toUpperCase() + string.substr(1);
	}
};

//------------------------------------------------------

$(document).ready(function($) {
	HUB.Video.loading();
});

