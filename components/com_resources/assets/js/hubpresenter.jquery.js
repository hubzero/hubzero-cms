if(!HUB) {
	var HUB = {};
}

//-----

HUB.Presenter = {
	
	activeSlide: '0',
	tolerance: 0.3,
	
	//-----
	
	loading: function() 
	{
		flash = false;
		seeking = false;
		mouseover = false;
		track = null;
		canSendTracking = true;
		sendingTracking = false;
		doneLoading = false;
		
		//add class presenter to body
		jQ("body").addClass("presenter");
			
		//insert an overlay
		jQ('<div id="overlayer"></div>').appendTo(document.body);
		
		//can we play HTML5 video
		flash = (!!document.createElement('video').canPlayType) ? false : true;   
		
		//is flash installed
		flash_installed = (FlashDetect.installed) ? true : false;           
		                
		//is this audio
		audio = (jQ("#player").get(0).tagName == 'AUDIO') ? true : false;
		
		//if we are using flash and its an audio clip get the duration
		if(flash && audio) {
			flash_audio_duration = jQ("#flowplayer").attr("duration");
		}       
		
		//start presentation
		HUB.Presenter.init();
	},
	
	//-----
	
	doneLoading: function() 
	{
		//make sure we didnt already run this - Firefox bug
		if(doneLoading)
		{
			return;
		}
		
		//remove the overlay
		jQ('#overlayer').remove();
		
		//play video
		if(!flash) {
			jQ('#player').get(0).play();
		}
		
		//hide the control bar after 3 seconds
		jQ("#control-box").delay(3000).fadeOut("slow");
	},
	
	//-----
	
	init: function() 
	{                        
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
		if( jQ("#media").hasClass("move-left") )
		{
			HUB.Presenter.switchVideo();
		}
		
		//handle subtitles
		HUB.Presenter.subtitles();
	},
	
	//-----
	
	player: function() 
	{
		//start media tracking if not flash (start for flash after load)
		if(!flash)
		{
			HUB.Presenter.startMediaTracking();
		}
		
		if(!flash) {
			jQ('#player').bind({
				timeupdate: function() {
					if(!seeking) 
					{
						HUB.Presenter.syncSlides();
						HUB.Presenter.updateMediaTracking();
					}
				},
				volumechange: function( e ) {
					HUB.Presenter.syncVolume();
				},
				canplay: function( e ) {
					HUB.Presenter.doneLoading();
					HUB.Presenter.locationHash();
				},
				seeked: function( e ) {
					seeking = true;
					var timeout = setTimeout("seeking=false;", 1000);
				},
				ended: function( e ) {
					HUB.Presenter.endMediaTracking();
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
				flowplayer("flowplayer", {src: "/components/com_resources/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
					clip: {
						duration: flash_audio_duration
					}, 
				   	plugins: {                                             
					   controls: null
					}, 
					onStart: function() {
						HUB.Presenter.doneLoading();
						HUB.Presenter.flashSyncSlides();
						HUB.Presenter.syncVolume();
						HUB.Video.startMediaTracking();
					},
					onFinish: function() {
						HUB.Presenter.endMediaTracking();
						HUB.Presenter.replay();
					}
				});
			} else {
				flowplayer("flowplayer", {src: "/components/com_resources/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, { 
				   	plugins: {                                             
					   controls: null
					}, 
					onStart: function() {
						HUB.Presenter.doneLoading();
						HUB.Presenter.syncVolume();
						HUB.Presenter.flashSyncSlides();
						HUB.Video.startMediaTracking();
					},
					onFinish: function() {
						HUB.Presenter.endMediaTracking();
						HUB.Presenter.replay();
					}
				});
			}   
			
		}
	},
	
	//-----
	
	slidePlayer: function() 
	{
	 	flowplayer(".flowplayer_slide", {src: "/components/com_resources/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
			 plugins: {                                             
			   controls: null
			}
		}); 
	},
	
	//-----
	
	showSlide: function( slide ) 
	{
		//set the active slide
		HUB.Presenter.activeSlide = slide;
		
		//hide all of the slides
		jQ('#slides ul li').css('display','none');
		
		//set the passed in slide to be visible
		jQ('#slide_' + slide).css('display','block');
		
		//center slide vertically
		slideContainerHeight = jQ("#slides").height();
		slideHeight = jQ("#slides ul li").height();
		if(slideContainerHeight > slideHeight)
		{
			diff = slideContainerHeight - slideHeight;
			if(diff < 100)
			{
				margin = diff / 2;
				jQ("#slides ul").css('margin-top', margin);
			}
		}
		
		//get the type of element for the current slide
		var slide_child = jQ('#slide_' + slide).children(),
			slide_child_type = jQ(slide_child).get(0).tagName;
		
		//if the slide is video play video
		if(slide_child_type == 'VIDEO') {
			if(!flash) {
				slide_child.first().get(0).play()
				//jQ(".slidevideo").get(0).play();
			} else {
				//flowplayer("flowplayer_slide_" + slide).play();
			}
		}    
		
		//get the list item we want to go to
		if( jQ("#list_" + slide).length ) {
			list_item  = "#list_" + slide;
		} else {
			list_item = HUB.Presenter.getListItem( slide, 'backward' );
		}
		
		//show all time points in list items
		jQ("#list_items .time").css("display","block");
		
		//hide the current list items time
		jQ( list_item + " .time").css("display","none");
		
		//List item progress bar
		HUB.Presenter.slideListProgressBar( list_item.substr(6) );
		
		//if we are not scrolling in the list box scroll to the list position
		if(!mouseover) {
			jQ('#list_items').scrollTo( list_item , 1000, 'easeInOutQuad' );
		}
		
		//add active class to the current slide list item
		jQ('#list_items li').removeClass('active');
		jQ( list_item ).addClass('active');
		
		//update the title
		//jQ('#slide_title').html(jQ('#slide_' + slide).attr('title'));
	},
	
	//-----
	
	syncSlides: function() 
	{
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
		jQ('#progress-bar').slider( "value", total_progress );
		
		//loop through all slides and get max slide based on current time
		jQ('#slides ul li').each(function( index, element ) {
			var slide = jQ(element),
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
		if( jQ('#slide_' + next_slide.id).length ) {
			next_slide.time = jQ('#slide_' + next_slide.id).attr('time');
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
		var flashSyncSlides = setInterval(function() {
			HUB.Presenter.syncSlides();
			HUB.Presenter.updateMediaTracking();
		}, 1000);
	},
	     
	
	//----------------------------------------
	//	Slide List
	//----------------------------------------
	
	slideList: function()
	{
		//show the list of slides
		jQ('#list').css('display','block');
		
		//format time display in list of slides
		jQ('#list_items li .time').each(function() {
			this.innerHTML = HUB.Presenter.formatTime(this.innerHTML);
		});
		
		//define height of list   
		jQ('#list_items').height( (jQ('#slides').height() - jQ('#media').height() - 1) );  //238
		
		//bind click events to scene selector
		jQ('#list_items li').bind('click', function(e) {
			var id = this.id.substr(5,6),
				time = jQ('#slide_' + id ).attr('time'),
				timeadjust = parseFloat(time) + HUB.Presenter.tolerance;
				
			//seek the video
			HUB.Presenter.seek( timeadjust );
			e.preventDefault();
		});
		
		//are we mousing over the list items
		jQ('#list_items').bind({
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
		var min, max, next, time, text_time, pos;
		
		//get the next list item
		next = jQ("#list_" + list_item).next().attr("id")
		
		//get the min time
		min = jQ("#slide_" + list_item).attr("time");
		
		//get the max time
		if( !jQ("#list_" + list_item).next().length ) {
			max = HUB.Presenter.getDuration();
		} else {
	 		max = jQ("#slide_" + next.substr(5)).attr("time");
		}
		
		//hide all list sliders and progress bars
		jQ(".list-slider").css("display","none");
		jQ(".list-progress").css("display","none");
		
		//show the list progres bar and progress 
		jQ("#list_" + list_item + " .list-slider, #list_" + list_item + " .list-progress").css("display", "block");
	
		//create the list item slider
		jQ("#list-slider-" + list_item).slider({
			step: .1,
			range: "min",
			slide: function(e, ui) {
				seeking = true;
				pos = ((max - min) * ui.value) / 100;
				text_time = HUB.Presenter.formatTime( pos ).substr(3) + "/" + HUB.Presenter.formatTime( max - min ).substr(3);
				jQ(".list-progress").text(text_time);
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
		var start, end, next, slide_progress, time;
		
		//get the active list item
		if( !jQ("#list_" + active).length ) {
			active = HUB.Presenter.getListItem( active, "backward" );
			active = active.substr(6);
		}
		
		//get the start of this segment
		start = jQ("#slide_" + active).attr("time");
		
		//get the next list item	
		next = jQ("#list_" + active).next().attr("id");
	
		//get the end of the segment
		if( !jQ("#list_" + active).next().length ) {
			end = HUB.Presenter.getDuration();
		} else {
	 		end = jQ("#slide_" + next.substr(5)).attr("time");
		}
		
		//calculate the slide progress
		slide_progress = (current - start) / (end - start);
		
		//if we are not seeking set the current progress in slider and in text
		if(!seeking) {
			jQ(".list-slider").slider({range:'min'});
			jQ(".list-slider").slider( "value", (slide_progress * 100));
			time = HUB.Presenter.formatTime( current - start ).substr(3) + "/" + HUB.Presenter.formatTime( end - start ).substr(3);
			jQ(".list-progress").text( time );
		}
	},
	
	
	//----------------------------------------
	//	Control Bar
	//----------------------------------------
	
	controlBar: function()
	{                 
		//hide the control bar
		var timeout, hideControlBarMobile;
		
		//play pause functionality
		jQ('#play-pause').bind('click', function(e) {
			HUB.Presenter.playPause(true);
			e.preventDefault();
		});
		
		//next slide functionality
		jQ('#next').bind('click', function(e) {
			HUB.Presenter.nextSlide();
			e.preventDefault();
		});
		
		//previous link functionality
		jQ('#previous').bind('click', function(e) {
			HUB.Presenter.previousSlide();
			e.preventDefault();
		});                     
		
		//if we have audio only dont display switch button
		if(audio || flash) {
			jQ("#switch").css("display","none");
		}    
		
		//swith video and slides
		jQ("#switch").bind('click', function(e) {
			HUB.Presenter.switchVideo();
			e.preventDefault();
		});
		
		jQ("#link").bind('click', function(e) {
			HUB.Presenter.linkVideo();
			e.preventDefault();
		});
		
		//progress bar functionality
		HUB.Presenter.progressBar();       
		
		//volume bar functionality
		HUB.Presenter.volumeBar();
		
		//show control bar when hovering over slide area
		jQ("#presenter-container").bind({
			mouseenter: function(e) {     
				if(!jQ('#control-box').is(":visible") ) {
					jQ('#control-box').fadeIn('slow');
				}
			},
			mouseleave: function(e) {
				jQ('#control-box').stop(true).fadeOut('slow', function() {
			   		jQ(this).css('opacity', '');
				});
			},
			mousemove: function(e) {
			   	if(!jQ('#control-box').is(":visible") ) {
					jQ('#control-box').fadeIn('slow');
				} 
			},
			touchstart: function(e) {
				clearTimeout(hideControlBarMobile);
					
				if(!jQ('#control-box').is(":visible") ) {
					jQ('#control-box').fadeIn('slow');
				}
			},
			touchend: function(e) {
				if(seeking === false) {
					hideControlBarMobile = setTimeout('jQ("#control-box").stop(true).fadeOut("slow", function() { jQ(this).css("opacity", ""); });', 5000);
				} else {
					timeout = setTimeout('jQ("#presenter-container").trigger("touchend");' ,200);
				}
			}
		});
		
		//make the control bar draggable
		jQ("#control-box").draggable({
			cursor:'move', 
			containment: '#presenter-content',
			opacity:'0.8'
		});
	},
	      
	//-----
	
	playPause: function( click )
	{    
		var paused = HUB.Presenter.isPaused(), 
			player = HUB.Presenter.getPlayer();         
	            	
		if( paused ) {
			jQ("#play-pause").css('background','url(/components/com_resources/assets/img/hubpresenter/play.png)');
			if( click ) 
				player.play();
		} else {
			jQ("#play-pause").css('background','url(/components/com_resources/assets/img/hubpresenter/pause.png)'); 
			if( click )
				player.pause();
		}  
	},
	
	//-----
	
	nextSlide: function()
	{
		var active = HUB.Presenter.activeSlide,
			next = parseInt(active) + 1;
		
		//if we have a next slide to move to, seek to that slide
		if( jQ('#slide_' + next ).length ) {
			var next_slide_time = jQ("#slide_" + next).attr("time"),
				time_adjust = parseFloat(next_slide_time) + HUB.Presenter.tolerance;
			
			HUB.Presenter.seek( time_adjust );
		}
	},
	
	//-----
	
	previousSlide: function()
	{
		var active = HUB.Presenter.activeSlide,
			previous = parseInt(active) - 1;

		if(previous >= 0) {
			//get the active list item
			if( !jQ("#list_" + previous).length ) {
				previous = HUB.Presenter.getListItem( previous, "backward" );
				previous = parseInt(previous.substr(6));
			}

			var previous_slide_time = jQ("#slide_" + previous).attr("time"),
				time_adjust = parseFloat(previous_slide_time) + HUB.Presenter.tolerance;

			HUB.Presenter.seek( time_adjust );
		}
	},

	//-----
	
	switchVideo: function() 
	{
		var player = HUB.Presenter.getPlayer(),
			paused = HUB.Presenter.isPaused();
		
		if( jQ("#presenter-left #media").length ) {
			jQ("#media").prependTo("#presenter-right");
			jQ("#slides").prependTo("#presenter-left");
		} else {
			jQ("#media").prependTo("#presenter-left");
			jQ("#slides").prependTo("#presenter-right");
		}
		
		jQ("#slides ul").css('margin-top', 0);
		
		if(!paused) {
			player.play();
		}
	},
	
	//-----
	
	linkVideo: function()
	{
		var time_hash,
			url = window.location.href,
			time = HUB.Presenter.getCurrent();
		
		//make time usable
		time = HUB.Presenter.formatTime( time );
		
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
	
	//-----
	
	progressBar: function()
	{                               
		jQ('#progress-bar').slider({
			step: .1,
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
		//volume slider
		jQ('#volume-bar').slider({
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
		//Pause/Play
		jQ(document.body).bind('keydown','p', function() { HUB.Presenter.playPause(true); return false; });
		jQ(document.body).bind('keydown','space', function() { HUB.Presenter.playPause(true); return false; });
		
		//next slide
		jQ(document.body).bind('keydown','down', function() { HUB.Presenter.nextSlide(); return false; });
		jQ(document.body).bind('keydown','right', function() { HUB.Presenter.nextSlide(); return false; });
		
		//previous slide
		jQ(document.body).bind('keydown','up', function() { HUB.Presenter.previousSlide(); return false; });
		jQ(document.body).bind('keydown','left', function() { HUB.Presenter.previousSlide(); return false; });

		//mute player
		jQ(document.body).bind('keydown','m', function() { HUB.Presenter.setVolume(0); return false; });
	
		//increase volume
		jQ(document.body).bind('keydown','»', function() { 
			var volume = HUB.Presenter.getVolume();
			volume = (volume <= 0.90) ? volume += 0.1 : volume = 1.0;
			HUB.Presenter.setVolume(volume);
			return false;
		});
		
		//decrease volume
		jQ(document.body).bind('keydown','½', function() {
			var volume = HUB.Presenter.getVolume();
			volume = (volume >= 0.1) ? volume -= 0.1 : volume = 0.0;
			HUB.Presenter.setVolume(volume);
			return false;
		});
		
		//hide the shortcuts box
		jQ(document.body).bind('keydown','esc', function() { 
			if( jQ("#presenter-shortcuts-box").is(":visible") ) {
				jQ("#presenter-shortcuts-box").fadeOut("slow");
			}
			return false;
		});
	},
	
	//-----
	
	shortcuts: function()
	{
		jQ('#shortcuts').bind("click", function(e) {
			if( jQ("#presenter-shortcuts-box").is(":visible") ) {
				jQ("#presenter-shortcuts-box").fadeOut("slow");
			} else {
				jQ("#presenter-shortcuts-box").hide().fadeIn("slow");
			}
			e.preventDefault();
		});
		
		jQ("#shortcuts-close").bind("click", function(e) {
			jQ("#presenter-shortcuts-box").fadeOut("slow");
			e.preventDefault();
		});
	},
	
	
	//----------------------------------------
	//	Mobile
	//----------------------------------------
	
	mobile: function() 
	{	
		//if we are on a mobile device
		if(mobile) {
			//add controls to player
			if(!audio) {
				jQ("#player").attr("controls","controls");
			}
			
			//remove the switch video button
			jQ("#switch").css("display","none");
			
			//remove the shortcuts button
			jQ("#shortcuts").css("display","none");
			
			//remove the volume bar as volume is controlled but the device
			jQ("#volume-bar").css("display","none");
			jQ("#volume-icon").css("display","none");	
			
			//display the two finger tip
			//jQ("#twofinger").css("display","block"); 
			
			//replace all in-slide videos with images
			jQ(".slidevideo").css("display","none");
			jQ(".imagereplacement").css("display","block");
			 
			//if we using an audio clip and on mobile
			if(audio) {
				//add visible play button
				jQ('#media').css("position","relative").prepend("<a id=\"mobile-play-audio\" href=\"#\">Click to Play</a>");
				
				//when play button is clicked hide button and play clip
				jQ('#mobile-play-audio').bind('click', function(e) {
					jQ(this).fadeOut("slow");                                 
					jQ('#player').get(0).play(); 
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
		jQ("#presenter-container").css("position","relative");
		
		var replay = "<div id=\"replay\"> \
						<div id=\"replay-details\"> \
							<div id=\"title\"></div> \
							<div id=\"link\"> \
								<span>Share:</span><input type=\"text\" id=\"replay-link\" value=" + window.location + " /> \
								<a target='_blank' href=\"http://www.facebook.com/share.php?u=" + window.location + "\" id=\"facebook\" title=\"Share on Facebook\">Facebook</a> \
								<a target='_blank' href=\"http://twitter.com/home?status=Currently Watching: " + window.location +"\" id=\"twitter\" title=\"Share on Twitter\">Twitter</a> \
							</div> \
						</div> \
						<a id=\"replay-back\" href=\"#\">&laquo; Close Presentation</a> \
						<a id=\"replay-now\" href=\"#\">Replay Presentation</a> \
					  </div>";
		
		jQ( replay ).hide().appendTo("#presenter-container").fadeIn("slow");
		
		//remove close presentation button if not in popup
		if (!window.opener)
		{
			jQ("#presenter-container").find('#replay-back').remove();
		}
		
		jQ("#replay-details #title").html( "<span>Title:</span> " + jQ("#presenter-header #title").html() );
		
		jQ("#replay-link").live("click", function(e) {
			this.select();
			e.preventDefault();
		});
		
		jQ("#replay-now").live("click", function(e) {
			HUB.Presenter.doReplay("#replay");
			e.preventDefault();
		});
		
		jQ("#replay-back").live("click", function(e) {
			window.close();
			e.preventDefault();
		});
	},
	
	//-----
	
	doReplay: function( element )
	{
		jQ( element ).fadeOut("slow", function() {
			//remove replay container
			jQ(this).remove();
			
			//reset video containter positioning
			jQ("#presenter-container").css("position","static");
			
			//seek to beginning
			HUB.Presenter.seek( 0 );  
			
			//start tracking again
			canSendTracking = true;
			sendingTracking = false;
			HUB.Presenter.startMediaTracking();
			
			//get player and play
			player = HUB.Presenter.getPlayer();
			player.play();
		});
	},
	
	
	//----------------------------------------
	//	Nav Bar 
	//----------------------------------------
	
	navBar: function() 
	{
		//back home
		jQ("#nanohub").bind("click",function(e) {
			if(!mobile) {
				e.preventDefault();
				window.close();
			}
		});
		
		//presentation picker
		jQ("#presentation").bind("change", function(e) {
			if(this.value != "") {
				jQ("#presentation-picker").submit();
			}
		});
	},
	
	
	
	//----------------------------------------
	//	Helper Functions
	//----------------------------------------
	       
	jsEnabled: function() {
		//Only show 1st slide and show control bar
		jQ('#slides ul').removeClass('no-js'); 
		
		//show control bar
		jQ('#control-box').removeClass('no-controls'); 
		
		//show shortcuts button 
		jQ("#shortcuts").css("display","block");
		
		//remove player controls
		jQ("#player").removeAttr("controls");
	},
	
	//----- 
	       
	noFlash: function() {  
		//remove the overlay
		jQ("#overlayer").remove();  
		 
		jQ("#presenter-container").css("position","relative");
		
		var noFlash = "<div id=\"no-flash\"> \
					       <h2>You Must Install the Flash Plugin</h2> \
						   <p><a id=\"download\" title=\"Download Flash Player\" rel=\"external\" href=\"http://get.adobe.com/flashplayer\">1. Download Flash Here</a></p> \
						   <p><a id=\"refresh\" title=\"Refresh the Player\" href=\"#\">2. Refresh The Player</a></p> \
						</div>";
		
		jQ( noFlash ).hide().appendTo("#presenter-container").fadeIn("slow");    
		
		jQ("#refresh").live("click", function(e) {
			window.location = window.location;
			jQ("#no-flash").remove();
			jQ("#presenter-container").css("position","static");
			e.preventDefault();
		}); 
		
		
	},
	
	//-----   
	
	getPlayer: function()
	{
		return (!flash) ? jQ("#player").get(0) : flowplayer("flowplayer");
	},
	
	//-----
	
	isPaused: function()
	{
		return (!flash) ? jQ("#player").get(0).paused : flowplayer("flowplayer").isPaused();
	},
	
	//-----
	
	setProgress: function( time )
	{
		//get clip duration
		var progress,
			current = time,
			duration = HUB.Presenter.getDuration();
		
		//calculate the progress
		progress = HUB.Presenter.formatTime( current ) + "/" + HUB.Presenter.formatTime( duration )
				
		//set media progress
		jQ('#media-progress').html(progress);
	},
	
	//-----
	
	getCurrent: function()
	{
		return current = (!flash) ? jQ("#player").get(0).currentTime : flowplayer("flowplayer").getTime();
	},
	
	//-----
	
	getDuration: function()
	{
	 	return duration = (!flash) ? jQ("#player").get(0).duration : flowplayer("flowplayer").getClip().duration;
	},
	
	//-----
	
	seek: function( time )
	{
		if(!flash) {
			jQ('#player').get(0).currentTime = time;
		} else {
			flowplayer("flowplayer").seek(time);
		}
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
	
	getListItem: function( slide, direction )
	{   
	    if(direction == 'forward') {
			slide++;
		} else {
			slide--;
		}
		
		var item = "#list_" + slide;
		
		return ( jQ(item).length ) ? item : HUB.Presenter.getListItem(slide, direction);
	},

	//-----
	
	hideControlBar: function()
	{
		var box = {
			'top': jQ("#presenter-left").position().top,
			'bottom': (jQ("#presenter-left").position().top + jQ("#presenter-left").height()),
			'left': jQ("#presenter-left").position().left,
			'right': (jQ("#presenter-left").position().left + jQ("#presenter-left").width())
		};
		
		jQ(document).mousemove(function(e) {
			if(e.pageX < box.left || e.pageX > box.right || e.pageY < box.top || e.pageY > box.bottom) {
				jQ("#control-box").fadeOut("slow");
				clearInterval(hideControls);
			}
		});
	},
	
	//-----
	
	syncVolume: function()
	{
		//get the current volume
		var volume = HUB.Presenter.getVolume();
		
		//format the icon
		HUB.Presenter.volumeIcon( volume * 100 );
		
		//set the volume slider
		jQ('#volume-bar').slider( "option", "value", volume );
	},
	
	//-----
	
	setVolume: function( level )
	{                                   
		if(!flash) {
			jQ('#player').get(0).volume = level;
		} else {
			flowplayer("flowplayer").setVolume( level * 100 );
		}
	},
	
	//-----
	
	getVolume: function()
	{
		return (!flash) ? jQ('#player').get(0).volume : flowplayer("flowplayer").getVolume() / 100;
	},
	
	//-----
	
	volumeIcon: function( volume )
	{
		var icon = jQ('#volume-icon');
		
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
		//make sure we didnt already run this - Firefox bug
		if(doneLoading)
		{
			return;
		}
		
		//var to hold time component
		var timeComponent = '';

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
		if(timeComponent != '' && timeComponent != '')
		{
			//get the hours, minutes, seconds
			var timeParts = timeComponent.split("=")[1].replace(/%3A/g, ':').split(':');

			//get time in seconds from hours, minutes, seconds
			var time = (parseInt(timeParts[0]) * 60 * 60) + (parseInt(timeParts[1]) * 60) + parseInt(timeParts[2]);

			//show resume & pause video
			HUB.Presenter.resume( HUB.Presenter.formatTime(time) );

			//seek to time
			HUB.Presenter.seek( time );
			HUB.Presenter.setProgress( time );

			//pause video
			var p = HUB.Presenter.getPlayer();
			p.pause();
			
			//we have handled
			doneLoading = true;
		}
	},
	
	resume: function( time )
	{
		if (!jQ("#presenter-container #resume").length)
		{
			//video container must be position relatively 
			jQ("#presenter-container").css('position', 'relative');

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
			jQ( resume ).hide().appendTo("#presenter-container").fadeIn("slow");
			
			//restart video button
			jQ("#restart-video").on('click',function(event){
				event.preventDefault();
				HUB.Presenter.doReplay("#resume");
			});
			
			//resume video button
			jQ("#resume-video").on('click',function(event){
				event.preventDefault();
				HUB.Presenter.doResume();
			});
			
			//stop clicks on resume
			jQ("#resume").on('click',function(event){
				if(event.srcElement.id != 'restart-video' && event.srcElement.id != 'resume-video')
				{
					event.preventDefault();
				}
			});
		}
	},
	
	doResume: function() 
	{
		jQ("#resume").fadeOut("slow", function() {
			//remove replay container
			jQ(this).remove();
			
			//reset video containter positioning
			jQ("#presenter-container").css("position","static");
			
			//play video
			var p = HUB.Presenter.getPlayer();
			p.play();
		});
	},
	
	//-----------------------------------------------------
	//	Media Tracking
	//-----------------------------------------------------
	
	startMediaTracking: function()
	{
		HUB.Presenter.mediaTrackingEvent('start');
		
		//start timer
		var timer = setInterval(function() {
			canSendTracking = true;
		}, 5000);
	},
	
	updateMediaTracking: function()
	{
		HUB.Presenter.mediaTrackingEvent('update');
	},
	
	endMediaTracking: function()
	{
		canSendTracking = true;
		sendingTracking = false;
		HUB.Presenter.mediaTrackingEvent('ended');
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
				resourceId = jQ(HUB.Presenter.getPlayer()).attr('data-mediaid');
				playerTime = HUB.Presenter.getCurrent();
				playerDuration = HUB.Presenter.getDuration();
			}
			else
			{
				resourceId = jQ("#"+HUB.Presenter.getPlayer().id()).attr('data-mediaid');
				playerTime = HUB.Presenter.getCurrent();
				playerDuration = HUB.Presenter.getDuration();
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
			jQ.ajax({
				type: 'POST',
				data: { event: eventType, resourceid: resourceId, time: playerTime, duration: playerDuration },
				url: url,
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
	
	subtitles: function()
	{
		var sub_titles = [];
		var auto = false;
		
		sub_titles = HUB.Presenter.getSubtitles();
		
		//create elements on page to hold subtitles
		if(sub_titles.length > 0) {
			jQ("#control-box").after("<div id=\"video-subtitles\"></div>");
			jQ("#switch").after("<ul id=\"subtitle-picker\"><li><a href=\"javascript:void(0);\">CC</a><ul id=\"cc\"><li><a class=\"active\" rel=\"\" href=\"#\">None</a></ul></li></ul>");

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
				
				jQ("#video-subtitles").append("<div id=\"" + sub_lang + "\"></div>");
				jQ("#cc").append("<li><a rel=\"" + sub_lang + "\" class=\"" + sub_lang + "\" href=\"javascript:void(0);\">" + sub_lang_text + "</a></li>");
			}
			
			//if we are auto showing subs make sure picker reflects that
			if(auto) {
				jQ("#subtitle-picker a").addClass("active");
				jQ("#cc a").removeClass("active");
				jQ("#cc a." + track).addClass("active");
			}
			
			
			jQ("#subtitle-picker ul a").live("click", function(e) {
				track = this.rel;
				
				if(track != "") {
					jQ("#subtitle-picker a").addClass("active");
				} else {
					jQ("#subtitle-picker a").removeClass("active");
				}
				
				jQ("#cc a").removeClass("active");
				jQ(this).addClass("active");
				
				e.preventDefault();
			});
			
			var syncInterval = setInterval( 
				function() {
					HUB.Presenter.syncSubtitles( sub_titles );
				}, 100);
		}
	},
	
	//-----
	
	syncSubtitles: function( sub_titles )
	{
		current = HUB.Presenter.getCurrent();
		
		//get the subs for the track we have selected
		for(i in sub_titles) {
			if(sub_titles[i].lang.toLowerCase() == track) {
				var subs = sub_titles[i].subs;
			}
		}
		
		//clear the subtitle tracks between 
		jQ("#video-subtitles div").hide().html("");
		
		for(i in subs) {
			start = subs[i].start;
			end = subs[i].end;
			text = subs[i].text;
			if(current >= start && current <= end) {
				jQ("#video-subtitles #" + track).show().html( text.replace( "\n","<br />") );
			}
		}
	},
	
	//-----
	
	getSubtitles: function()
	{
		var count = 0,
			parsed = "",
			subs = new Array(),
			sub_files = jQ("div[data-type=subtitle]");
		
		//loop through each subs file and get the contents then add to subs object
		sub_files.each(function(i){
			var lang = jQ(this).attr("data-lang"),
				src  = jQ(this).attr("data-src"),
				auto = jQ(this).attr("data-autoplay");
			
			jQ.ajax({
				url: src,
				async: false,
				success: function( content ) {
					parsed = HUB.Presenter.parseSubtitles( content );
					sub = { "lang" : lang, "subs" : parsed, "auto" : auto };
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
		content = HUB.Presenter.strip( content );
		
		//split up each
		srt = content.split("\n\n");
		
		//for each individual subtitle
		for(n=0; n<srt.length; n++) {
			parts = srt[n].split("\n");
			
			id = parseInt(parts[0]);
			
			//get the sub start time
			start = parts[1].split(' --> ')[0];
			start = HUB.Presenter.strip( start );
			start = HUB.Presenter.toSeconds( start );
			
			//get the sub end time
			end = parts[1].split(' --> ')[1];
			end = HUB.Presenter.strip( end );
		 	end = HUB.Presenter.toSeconds( end );
			
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
}

//---------------------------------------------------------
//	Start HUBpresenter When Document is Ready
//---------------------------------------------------------

var jQ = jQuery.noConflict();

jQ(document).ready(function(e) {
	if( jQ("#presenter-header").length )
		HUB.Presenter.loading();
});

//-----

var mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;

//-----

if(mobile) {
	jQ(window).load(function(e) {
		HUB.Presenter.doneLoading();
	});
}

//---------------------------------------------------------
//	Preload Images Used in HUBpresenter App
//--------------------------------------------------------- 

function preload( images )
{                       
	var base = "/components/com_resources/assets/img/hubpresenter/",
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