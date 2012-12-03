/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/presenter/js/app.js
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

//-----

HUB.Presenter = {

	jQuery: jq,
	activeSlide: '0',
	tolerance: 0.3,

	//-----

	loading: function()
	{
		var $ = this.jQuery;

		flash = false;
		seeking = false;
		mouseover = false;
		
		//add class presenter to body
		$("body").addClass("presenter");
			
		//insert an overlay
		$('#presenter-container').append('<div id="overlayer"></div>');
		
		//can we play HTML5 video
		flash = (!!document.createElement('video').canPlayType) ? false : true;
		
		//is flash installed
		flash_installed = (FlashDetect.installed) ? true : false;

		//is this audio
		audio = ($("#player").get(0).tagName == 'AUDIO') ? true : false;
		
		//if we are using flash and its an audio clip get the duration
		if(flash && audio) {
			flash_audio_duration = $("#flowplayer").attr("duration");
		}

		//start presentation
		HUB.Presenter.init();
	},

	//-----

	doneLoading: function()
	{
		var $ = this.jQuery;

		//remove the overlay
		$('#overlayer').remove();
		
		//play video
		if(!flash) {
			$('#player').get(0).play();
		}

		//hide the control bar after 3 seconds
		$("#control-box").delay(3000).fadeOut("slow");
	},

	//-----

	init: function()
	{
		var $ = this.jQuery;

		//javascript is enabled
		HUB.Presenter.jsEnabled();
		
		//go to the first slide
		HUB.Presenter.showSlide( HUB.Presenter.activeSlide );
		
		//keyboard access
		HUB.Presenter.keyboard();
		
		//slide list
		HUB.Presenter.slideList();
		
		//shortcuts popup
		HUB.Presenter.shortcuts();
		
		//mobile
		HUB.Presenter.mobile();
		
		//player
		HUB.Presenter.player();
		
		//slide Player
		HUB.Presenter.slidePlayer();
		
		//control bar
		HUB.Presenter.controlBar();

		//show download link for those who dont have flash
		if(!flash_installed && flash) {
			HUB.Presenter.noFlash();
		}
		
		//set the volume to 3/4 initally
		HUB.Presenter.setVolume(0.75);
		
		//Nav bar
		HUB.Presenter.navBar();
		
		//automatically move video to main section
		if( $("#media").hasClass("move-left") )
		{
			HUB.Presenter.switchVideo();
		}
	},
	
	//-----
	
	player: function()
	{
		var $ = this.jQuery;

		if(!flash) {
			$('#player').bind({
				timeupdate: function() {
					if(!seeking)
						HUB.Presenter.syncSlides();
				},
				volumechange: function( e ) {
					HUB.Presenter.syncVolume();
				},
				canplay: function( e ) {
					HUB.Presenter.doneLoading();
					
					//seeek to time if in hash
					HUB.Presenter.locationHash();
				},
				seeked: function( e ) {
					seeking = true;
					var timeout = setTimeout("seeking=false;", 1000);
				},
				ended: function( e ) {
					HUB.Presenter.replay();
				},
				error: function(e) {
					throw "An error occured while trying to load the media.";
				},
				stalled: function(e) {
					throw "For some reason the player stalled while trying to load the media. Verify the media location.";
				}
			});
		} else {
			//flash fallback stuff
			if(audio) {
				flowplayer("flowplayer", {src: "/components/com_courses/assets/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
					clip: {
						duration: flash_audio_duration
					},
					plugins: {
						controls: null
					},
					onStart: function() {
						HUB.Presenter.doneLoading();
						HUB.Presenter.flashSyncSlides();
					},
					onFinish: function() {
						HUB.Presenter.replay();
					}
				});
			} else {
				flowplayer("flowplayer", {src: "/components/com_courses/assets/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
					plugins: {
						controls: null
					},
					onStart: function() {
						HUB.Presenter.doneLoading();
						HUB.Presenter.flashSyncSlides();
					},
					onFinish: function() {
						HUB.Presenter.replay();
					}
				});
			}
		}
	},
	
	//-----
	
	slidePlayer: function()
	{
		var $ = this.jQuery;

		flowplayer(".flowplayer_slide", {src: "/components/com_courses/assets/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
			plugins: {
				controls: null
			}
		});
	},
	
	//-----
	
	showSlide: function( slide )
	{
		var $ = this.jQuery;

		//set the active slide
		HUB.Presenter.activeSlide = slide;
		
		//hide all of the slides
		$('#slides ul li').css('display','none');
		
		//set the passed in slide to be visible
		$('#slide_' + slide).css('display','block');
		
		//get the type of element for the current slide
		var slide_child = $('#slide_' + slide).children(),
			slide_child_type = $(slide_child).get(0).tagName;
		
		//if the slide is video play video
		if(slide_child_type == 'VIDEO') {
			if(!flash) {
				$(".slidevideo").get(0).play();
			} else {
				//flowplayer("flowplayer_slide_" + slide).play();
			}
		}
		
		//get the list item we want to go to
		if( $("#list_" + slide).length ) {
			list_item  = "#list_" + slide;
		} else {
			list_item = HUB.Presenter.getListItem( slide, 'backward' );
		}
		
		//show all time points in list items
		$("#list_items .time").css("display","block");
		
		//hide the current list items time
		$( list_item + " .time").css("display","none");
		
		//List item progress bar
		HUB.Presenter.slideListProgressBar( list_item.substr(6) );
		
		//if we are not scrolling in the list box scroll to the list position
		if(!mouseover) {
			$('#list_items').scrollTo( list_item , 1000, 'easeInOutQuad' );
		}
		
		//add active class to the current slide list item
		$('#list_items li').removeClass('active');
		$( list_item ).addClass('active');
		
		//update the title
		//$('#slide_title').html($('#slide_' + slide).attr('title'));
	},
	
	//-----
	
	syncSlides: function()
	{
		var $ = this.jQuery;

		//vars being used for syncing
		var slide_progress,
			cur_slide = {},
			next_slide = {},
			current = HUB.Presenter.getCurrent(),
			duration = HUB.Presenter.getDuration(),
			total_progress = (current * 100) / duration;

		//set the progress
		HUB.Presenter.setProgress( current );
		
		//sync native video/audio control bar with custom control bar
		HUB.Presenter.playPause();
		
		//update the slide list progress bar
		HUB.Presenter.slideListProgressUpdate( HUB.Presenter.activeSlide );
		
		//projess bar set position
		$('#progress-bar').slider( "option", "value", total_progress );
		
		//loop through all slides and get max slide based on current time
		$('#slides ul li').each(function( index, element ) {
			var slide = $(element),
				time = slide.attr('time'),
				id = slide.attr('id').substr(6,7);
			
			if(current >= time) {
				cur_slide.id = id;
				cur_slide.time = time;
			}
		});
		
		//get the next slide after the current max
		next_slide.id = parseInt(cur_slide.id) + 1;
		
		//get the next slides time and if doesnt exist set to arbitrary high value
		if( $('#slide_' + next_slide.id).length ) {
			next_slide.time = $('#slide_' + next_slide.id).attr('time');
		} else {
			next_slide.time = 99999999;
		}
		
		//if the current time is greater then the current slide and less then the next slide and
		//isnt set as the active slide(meaning hasnt been activated yet) - go to slide
		if(cur_slide.time <= current && next_slide.time > current) {
			if(HUB.Presenter.activeSlide !== cur_slide.id) {
				HUB.Presenter.showSlide( cur_slide.id );
			}
		}
	},
	
	//-----
	
	flashSyncSlides: function()
	{
		var $ = this.jQuery;

		var flashSyncSlides = setInterval("HUB.Presenter.syncSlides();", 500);
	},
	
	//----------------------------------------
	//	Slide List
	//----------------------------------------
	
	slideList: function()
	{
		var $ = this.jQuery;

		//show the list of slides
		$('#list').css('display','block');
		
		//format time display in list of slides
		$('#list_items li .time').each(function() {
			this.innerHTML = HUB.Presenter.formatTime(this.innerHTML);
		});
		
		//define height of list
		$('#list_items').height( 238 );
		
		//bind click events to scene selector
		$('#list_items li').bind('click', function(e) {
			var id = this.id.substr(5,6),
				time = $('#slide_' + id ).attr('time'),
				timeadjust = parseFloat(time) + HUB.Presenter.tolerance;
				
			//seek the video
			HUB.Presenter.seek( timeadjust );
			e.preventDefault();
		});
		
		//are we mousing over the list items
		$('#list_items').bind({
			mouseenter: function() {
				mouseover = true;
			},
			mouseleave: function() {
				mouseover = false;
			}
		});
	},
	
	//-----
	
	slideListProgressBar: function( list_item )
	{
		var $ = this.jQuery;

		var min, max, next, time, text_time, pos;
		
		//get the next list item
		next = $("#list_" + list_item).next().attr("id");
		
		//get the min time
		min = $("#slide_" + list_item).attr("time");
		
		//get the max time
		if( !$("#list_" + list_item).next().length ) {
			max = HUB.Presenter.getDuration();
		} else {
			max = $("#slide_" + next.substr(5)).attr("time");
		}
		
		//hide all list sliders and progress bars
		$(".list-slider").css("display","none");
		$(".list-progress").css("display","none");
		
		//show the list progres bar and progress
		$("#list_" + list_item + " .list-slider, #list_" + list_item + " .list-progress").css("display", "block");
	
		//create the list item slider
		$("#list-slider-" + list_item).slider({
			step: 0.1,
			range: "min",
			slide: function(e, ui) {
				seeking = true;
				pos = ((max - min) * ui.value) / 100;
				text_time = HUB.Presenter.formatTime( pos ).substr(3) + "/" + HUB.Presenter.formatTime( max - min ).substr(3);
				$(".list-progress").text(text_time);
			},
			start: function(e, ui) {
				seeking = true;
			},
			stop: function(e, ui) {
				seeking = false;
				time = ( ((max - min) * ui.value) / 100 ) + parseFloat(min);
				HUB.Presenter.seek( time );
			}
		});
	},
	
	//-----
	
	slideListProgressUpdate: function( active )
	{
		var $ = this.jQuery;

		var start, end, next, slide_progress, time;
		
		//get the active list item
		if( !$("#list_" + active).length ) {
			active = HUB.Presenter.getListItem( active, "backward" );
			active = active.substr(6);
		}
		
		//get the start of this segment
		start = $("#slide_" + active).attr("time");
		
		//get the next list item
		next = $("#list_" + active).next().attr("id");
	
		//get the end of the segment
		if( !$("#list_" + active).next().length ) {
			end = HUB.Presenter.getDuration();
		} else {
			end = $("#slide_" + next.substr(5)).attr("time");
		}
		
		//calculate the slide progress
		slide_progress = (current - start) / (end - start);
		
		//if we are not seeking set the current progress in slider and in text
		if(!seeking) {
			$(".list-slider").slider("option","value", (slide_progress * 100));
			time = HUB.Presenter.formatTime( current - start ).substr(3) + "/" + HUB.Presenter.formatTime( end - start ).substr(3);
			$(".list-progress").text( time );
		}
	},
	
	
	//----------------------------------------
	//	Control Bar
	//----------------------------------------
	
	controlBar: function()
	{
		var $ = this.jQuery;

		//hide the control bar
		var timeout, hideControlBarMobile;
		
		//play pause functionality
		$('#play-pause').bind('click', function(e) {
			HUB.Presenter.playPause(true);
			e.preventDefault();
		});
		
		//next slide functionality
		$('#next').bind('click', function(e) {
			HUB.Presenter.nextSlide();
			e.preventDefault();
		});
		
		//previous link functionality
		$('#previous').bind('click', function(e) {
			HUB.Presenter.previousSlide();
			e.preventDefault();
		});
		
		//if we have audio only dont display switch button
		if(audio || flash) {
			$("#switch").css("display","none");
		}
		
		//swith video and slides
		$("#switch").bind('click', function(e) {
			HUB.Presenter.switchVideo();
			e.preventDefault();
		});
		
		$("#link").bind('click', function(e) {
			HUB.Presenter.linkVideo();
			e.preventDefault();
		});
		
		//progress bar functionality
		HUB.Presenter.progressBar();
		
		//volume bar functionality
		HUB.Presenter.volumeBar();
		
		//show control bar when hovering over slide area
		$("#presenter-container").bind({
			mouseenter: function(e) {
				if(!$('#control-box').is(":visible") ) {
					$('#control-box').fadeIn('slow');
				}
			},
			mouseleave: function(e) {
				$('#control-box').stop(true).fadeOut('slow', function() {
					$(this).css('opacity', '');
				});
			},
			mousemove: function(e) {
				if(!$('#control-box').is(":visible") ) {
					$('#control-box').fadeIn('slow');
				}
			},
			touchstart: function(e) {
				clearTimeout(hideControlBarMobile);
					
				if(!$('#control-box').is(":visible") ) {
					$('#control-box').fadeIn('slow');
				}
			},
			touchend: function(e) {
				if(seeking === false) {
					hideControlBarMobile = setTimeout('$("#control-box").stop(true).fadeOut("slow", function() { $(this).css("opacity", ""); });', 5000);
				} else {
					timeout = setTimeout('$("#presenter-container").trigger("touchend");' ,200);
				}
			}
		});
		
		//make the control bar draggable
		$("#control-box").draggable({
			cursor:'move',
			containment: '#presenter-content',
			opacity:'0.8'
		});
	},

	//-----
	
	playPause: function( click )
	{
		var $ = this.jQuery;

		var paused = HUB.Presenter.isPaused(),
			player = HUB.Presenter.getPlayer();

		if( paused ) {
			$("#play-pause").css('background','url(/components/com_courses/assets/presenter/img/play.png)');
			if( click )
				player.play();
		} else {
			$("#play-pause").css('background','url(/components/com_courses/assets/presenter/img/pause.png)');
			if( click )
				player.pause();
		}
	},
	
	//-----
	
	nextSlide: function()
	{
		var $ = this.jQuery;

		var active = HUB.Presenter.activeSlide,
			next = parseInt(active, 10) + 1;

		//get the active list item
		if( !$("#list_" + next).length ) {
			next = HUB.Presenter.getListItem( next, "forward" );
			next = parseInt(next.substr(6), 10);
		}

		//if we have a next slide to move to, seek to that slide
		if( $('#slide_' + next ).length ) {
			var next_slide_time = $("#slide_" + next).attr("time"),
				time_adjust = parseFloat(next_slide_time) + HUB.Presenter.tolerance;
			
			HUB.Presenter.seek( time_adjust );
		}
	},
	
	//-----
	
	previousSlide: function()
	{
		var $ = this.jQuery;

		var active = HUB.Presenter.activeSlide,
			previous = parseInt(active, 10) - 1;

		if(previous >= 0) {
			//get the active list item
			if( !$("#list_" + previous).length ) {
				previous = HUB.Presenter.getListItem( previous, "backward" );
				previous = parseInt(previous.substr(6), 10);
			}

			var previous_slide_time = $("#slide_" + previous).attr("time"),
				time_adjust = parseFloat(previous_slide_time) + HUB.Presenter.tolerance;

			HUB.Presenter.seek( time_adjust );
		}
	},

	//-----
	
	switchVideo: function()
	{
		var $ = this.jQuery;

		var player = HUB.Presenter.getPlayer(),
			paused = HUB.Presenter.isPaused();
		
		if( $("#presenter-left #media").length ) {
			$("#media").prependTo("#presenter-right");
			$("#slides").prependTo("#presenter-left");
		} else {
			$("#media").prependTo("#presenter-left");
			$("#slides").prependTo("#presenter-right");
		}
		
		if(!paused) {
			player.play();
		}
	},
	
	//-----
	
	linkVideo: function()
	{
		var $ = this.jQuery;

		var time_hash,
			url = window.location,
			time = HUB.Presenter.getCurrent();
			
		//make time usable
		time = HUB.Presenter.formatTime( time );
		parts = time.split(":");
		
		//create hash based on current time and then prompt user with link
		time_hash = "#time-" + ( (parseInt(parts[0]) * 60) + parseInt(parts[1]) ) + ":" + parts[2];
		prompt("Link to Current Position in Presentation", url + time_hash);
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
				HUB.Presenter.setProgress( HUB.Presenter.getDuration() * (ui.value / 100) );
			},
			start: function( event, ui ) {
				seeking = true;
			},
			stop: function( event, ui ) {
				seeking = false;
				HUB.Presenter.seek( HUB.Presenter.getDuration() * (ui.value / 100)  );
			}
		});
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
				HUB.Presenter.volumeIcon(ui.value * 100);
				HUB.Presenter.setVolume( ui.value );
			}
		});
		
		//sync the volume slider and player
		if(!flash)
			HUB.Presenter.syncVolume();
	},
	
	
	//----------------------------------------
	//	Keyboard Shortcuts
	//----------------------------------------
	
	keyboard: function()
	{
		var $ = this.jQuery;

		//Pause/Play
		$(document.body).bind('keydown','p', function() { HUB.Presenter.playPause(true); return false; });
		$(document.body).bind('keydown','space', function() { HUB.Presenter.playPause(true); return false; });
		
		//next slide
		$(document.body).bind('keydown','down', function() { HUB.Presenter.nextSlide(); return false; });
		$(document.body).bind('keydown','right', function() { HUB.Presenter.nextSlide(); return false; });
		
		//previous slide
		$(document.body).bind('keydown','up', function() { HUB.Presenter.previousSlide(); return false; });
		$(document.body).bind('keydown','left', function() { HUB.Presenter.previousSlide(); return false; });

		//mute player
		$(document.body).bind('keydown','m', function() { HUB.Presenter.setVolume(0); return false; });
	
		//increase volume
		$(document.body).bind('keydown','»', function() {
			var volume = HUB.Presenter.getVolume();
			volume = (volume <= 0.90) ? volume += 0.1 : volume = 1.0;
			HUB.Presenter.setVolume(volume);
			return false;
		});
		
		//decrease volume
		$(document.body).bind('keydown','½', function() {
			var volume = HUB.Presenter.getVolume();
			volume = (volume >= 0.1) ? volume -= 0.1 : volume = 0.0;
			HUB.Presenter.setVolume(volume);
			return false;
		});
		
		//hide the shortcuts box
		$(document.body).bind('keydown','esc', function() {
			if( $("#presenter-shortcuts-box").is(":visible") ) {
				$("#presenter-shortcuts-box").fadeOut("slow");
			}
			return false;
		});
	},
	
	//-----
	
	shortcuts: function()
	{
		var $ = this.jQuery;

		$('#shortcuts').bind("click", function(e) {
			if( $("#presenter-shortcuts-box").is(":visible") ) {
				$("#presenter-shortcuts-box").fadeOut("slow");
			} else {
				$("#presenter-shortcuts-box").hide().fadeIn("slow");
			}
			e.preventDefault();
		});
		
		$("#shortcuts-close").bind("click", function(e) {
			$("#presenter-shortcuts-box").fadeOut("slow");
			e.preventDefault();
		});
	},
	
	
	//----------------------------------------
	//	Mobile
	//----------------------------------------
	
	mobile: function()
	{
		var $ = this.jQuery;

		//if we are on a mobile device
		if(mobile) {
			//add controls to player
			if(!audio) {
				$("#player").attr("controls","controls");
			}
			
			//remove the switch video button
			$("#switch").css("display","none");
			
			//remove the shortcuts button
			$("#shortcuts").css("display","none");
			
			//remove the volume bar as volume is controlled but the device
			$("#volume-bar").css("display","none");
			$("#volume-icon").css("display","none");
			
			//display the two finger tip
			//$("#twofinger").css("display","block");
			
			//replace all in-slide videos with images
			$(".slidevideo").css("display","none");
			$(".imagereplacement").css("display","block");
			 
			//if we using an audio clip and on mobile
			if(audio) {
				//add visible play button
				$('#media').css("position","relative").prepend("<a id=\"mobile-play-audio\" href=\"#\">Click to Play</a>");
				
				//when play button is clicked hide button and play clip
				$('#mobile-play-audio').bind('click', function(e) {
					$(this).fadeOut("slow");
					$('#player').get(0).play();
					e.preventDefault();
				});
			}
		}
	},
	
	
	//----------------------------------------
	//	Replay
	//----------------------------------------
	
	replay: function()
	{
		var $ = this.jQuery;

		$("#presenter-container").css("position","relative");
		
		var replay = "<div id=\"replay\"> \
						<div id=\"replay-details\"> \
							<div id=\"title\"></div> \
							<div id=\"link\"> \
								<span>Share:</span><input type=\"text\" id=\"replay-link\" value=" + window.location + " /> \
								<a href=\"http://www.facebook.com/share.php?u=" + window.location + "\" id=\"facebook\" title=\"Share on Facebook\">Facebook</a> \
								<a href=\"http://twitter.com/home?status=Currently Watching: " + window.location +"\" id=\"twitter\" title=\"Share on Twitter\">Twitter</a> \
							</div> \
						</div> \
						<a id=\"replay-back\" href=\"#\">&laquo; Back to site</a> \
						<a id=\"replay-now\" href=\"#\">Replay Presentation</a> \
					  </div>";
		
		$( replay ).hide().appendTo("#presenter-container").fadeIn("slow");
		
		$("#replay-details #title").html( "<span>Title:</span> " + $("#presenter-header #title").html() );
		
		$("#replay-link").live("click", function(e) {
			this.select();
			e.preventDefault();
		});
		
		$("#replay-now").live("click", function(e) {
			$(this).hide();
			HUB.Presenter.doReplay();
			e.preventDefault();
		});
		
		$("#replay-back").live("click", function(e) {
			window.close();
			e.preventDefault();
		});
	},
	
	//-----
	
	doReplay: function()
	{
		var $ = this.jQuery;

		$("#replay").fadeOut("slow", function() {
			$(this).remove();
			$("#presenter-container").css("position","static");
			HUB.Presenter.seek( 0 );
			
			player = HUB.Presenter.getPlayer();
			player.play();
		});
	},
	
	
	//----------------------------------------
	//	Nav Bar
	//----------------------------------------
	
	navBar: function()
	{
		var $ = this.jQuery;

		//back home
		$("#nanohub").bind("click",function(e) {
			if(!mobile) {
				e.preventDefault();
				window.close();
			}
		});
		
		//presentation picker
		$("#presentation").bind("change", function(e) {
			if(this.value != "") {
				$("#presentation-picker").submit();
			}
		});
	},
	
	
	
	//----------------------------------------
	//	Helper Functions
	//----------------------------------------

	jsEnabled: function() {
		var $ = this.jQuery;

		//Only show 1st slide and show control bar
		$('#slides ul').removeClass('no-js');
		
		//show control bar
		$('#control-box').removeClass('no-controls');
		
		//show shortcuts button
		$("#shortcuts").css("display","block");
		
		//remove player controls
		$("#player").removeAttr("controls");
	},
	
	//-----

	noFlash: function() {
		var $ = this.jQuery;

		//remove the overlay
		$("#overlayer").remove();
		 
		$("#presenter-container").css("position","relative");
		
		var noFlash = "<div id=\"no-flash\"> \
					       <h2>You Must Install the Flash Plugin</h2> \
						   <p><a id=\"download\" title=\"Download Flash Player\" rel=\"external\" href=\"http://get.adobe.com/flashplayer\">1. Download Flash Here</a></p> \
						   <p><a id=\"refresh\" title=\"Refresh the Player\" href=\"#\">2. Refresh The Player</a></p> \
						</div>";
		
		$( noFlash ).hide().appendTo("#presenter-container").fadeIn("slow");
		
		$("#refresh").live("click", function(e) {
			window.location = window.location;
			$("#no-flash").remove();
			$("#presenter-container").css("position","static");
			e.preventDefault();
		});
		
	},
	
	//-----
	
	getPlayer: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $("#player").get(0) : flowplayer("flowplayer");
	},
	
	//-----
	
	isPaused: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $("#player").get(0).paused : flowplayer("flowplayer").isPaused();
	},
	
	//-----
	
	setProgress: function( time )
	{
		var $ = this.jQuery;

		//get clip duration
		var progress,
			current = time,
			duration = HUB.Presenter.getDuration();
		
		//calculate the progress
		progress = HUB.Presenter.formatTime( current ) + "/" + HUB.Presenter.formatTime( duration );
				
		//set media progress
		$('#media-progress').html(progress);
	},
	
	//-----
	
	getCurrent: function()
	{
		var $ = this.jQuery;

		return current = (!flash) ? $("#player").get(0).currentTime : flowplayer("flowplayer").getTime();
	},
	
	//-----
	
	getDuration: function()
	{
		var $ = this.jQuery;

	 	return duration = (!flash) ? $("#player").get(0).duration : flowplayer("flowplayer").getClip().duration;
	},
	
	//-----
	
	seek: function( time )
	{
		var $ = this.jQuery;

		if(!flash) {
			$('#player').get(0).currentTime = time;
		} else {
			flowplayer("flowplayer").seek(time);
		}
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
	
	getListItem: function( slide, direction )
	{
		var $ = this.jQuery;

		if(direction == 'forward') {
			slide++;
		} else {
			slide--;
		}
		
		var item = "#list_" + slide;
		
		return ( $(item).length ) ? item : HUB.Presenter.getListItem(slide, direction);
	},

	//-----
	
	hideControlBar: function()
	{
		var $ = this.jQuery;

		var box = {
			'top': $("#presenter-left").position().top,
			'bottom': ($("#presenter-left").position().top + $("#presenter-left").height()),
			'left': $("#presenter-left").position().left,
			'right': ($("#presenter-left").position().left + $("#presenter-left").width())
		};
		
		$(document).mousemove(function(e) {
			if(e.pageX < box.left || e.pageX > box.right || e.pageY < box.top || e.pageY > box.bottom) {
				$("#control-box").fadeOut("slow");
				clearInterval(hideControls);
			}
		});
	},
	
	//-----
	
	syncVolume: function()
	{
		var $ = this.jQuery;

		//get the current volume
		var volume = HUB.Presenter.getVolume();
		
		//format the icon
		HUB.Presenter.volumeIcon( volume * 100 );
		
		//set the volume slider
		$('#volume-bar').slider( "option", "value", volume );
	},
	
	//-----
	
	setVolume: function( level )
	{
		var $ = this.jQuery;

		if(!flash) {
			$('#player').get(0).volume = level;
		} else {
			flowplayer("flowplayer").setVolume( level * 100 );
		}
	},
	
	//-----
	
	getVolume: function()
	{
		var $ = this.jQuery;

		return (!flash) ? $('#player').get(0).volume : flowplayer("flowplayer").getVolume() / 100;
	},
	
	//-----
	
	volumeIcon: function( volume )
	{
		var $ = this.jQuery;

		var icon = $('#volume-icon');
		
		if(volume == 0)
			icon.css('background-position','0 0');
			
		if( volume > 0 && volume < 33) 
			icon.css('background-position','-24px 0');
			
		if( volume > 33 && volume < 66) 
			icon.css('background-position','-48px 0');
			
		if( volume > 66) 
			icon.css('background-position','-72px 0');
	},
	
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
			time_min = parseInt(time_parts[0], 10);
			time_sec = parseInt(time_parts[1], 10);
			time_total = ( time_min * 60 ) + time_sec;
			HUB.Presenter.seek( time_total );
		}
	}

};

//---------------------------------------------------------
//	Start HUBpresenter When Document is Ready
//---------------------------------------------------------

$(document).ready(function($) {
	if($("#presenter-header").length)
		HUB.Presenter.loading();
});

//-----

var mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;

//-----

if(mobile) {
	$(window).load(function(e) {
		HUB.Presenter.doneLoading();
	});
}

//---------------------------------------------------------
//	Preload Images Used in HUBpresenter App
//---------------------------------------------------------

function preload( images )
{
	var base = "/components/com_courses/assets/presenter/img/",
		image = new Image();
		
	for(i=0; i<images.length; i++) {
		image.src = base + images[i];
	}
}

//-----

var app_images = [
	'ajax-loader-1.gif',
	'bg.png',
	'close.png',
	'facebook.png',
	'handle.png',
	'keyboard.png',
	'link.png',
	'nanohub.png',
	'next.png',
	'pause.png',
	'play-button.png',
	'play.png',
	'previous.png',
	'replay.png',
	'slide-handle-hover.png',
	'slide-handle.png',
	'speaker.png',
	'switch.png',
	'twitter.png',
	'twofingers.png',
	'volume.png'
	];

//-----

preload( app_images );

//-----