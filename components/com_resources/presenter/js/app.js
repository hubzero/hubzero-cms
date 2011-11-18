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
	},
	
	//-----
	
	player: function() 
	{   
		if(!flash) {
			jQ('#player').bind({
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
				flowplayer("flowplayer", {src: "/components/com_resources/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
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
				flowplayer("flowplayer", {src: "/components/com_resources/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, { 
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
	 	flowplayer(".flowplayer_slide", {src: "/components/com_resources/presenter/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
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
		
		//get the type of element for the current slide
		var slide_child = jQ('#slide_' + slide).children(),
			slide_child_type = jQ(slide_child).get(0).tagName;
		
		//if the slide is video play video
		if(slide_child_type == 'VIDEO') {
			if(!flash) {
				jQ(".slidevideo").get(0).play();
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
		jQ('#progress-bar').slider( "option", "value", total_progress );
		
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
		var flashSyncSlides = setInterval("HUB.Presenter.syncSlides();", 500);
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
		jQ('#list_items').height( 238 );  
		
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
			jQ(".list-slider").slider("option","value", (slide_progress * 100));
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
			jQ("#play-pause").css('background','url(/components/com_resources/presenter/img/play.png)');
			if( click ) 
				player.play();
		} else {
			jQ("#play-pause").css('background','url(/components/com_resources/presenter/img/pause.png)'); 
			if( click )
				player.pause();
		}  
	},
	
	//-----
	
	nextSlide: function()
	{
		var active = HUB.Presenter.activeSlide,
			next = parseInt(active) + 1;

		//get the active list item
		if( !jQ("#list_" + next).length ) {
			next = HUB.Presenter.getListItem( next, "forward" );
			next = parseInt(next.substr(6));
		}

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
		
		if(!paused) {
			player.play();
		}
	},
	
	//-----
	
	linkVideo: function()
	{
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
								<a href=\"http://www.facebook.com/share.php?u=" + window.location + "\" id=\"facebook\" title=\"Share on Facebook\">Facebook</a> \
								<a href=\"http://twitter.com/home?status=Currently Watching: " + window.location +"\" id=\"twitter\" title=\"Share on Twitter\">Twitter</a> \
							</div> \
						</div> \
						<a id=\"replay-back\" href=\"#\">&laquo; Back to nanoHUB.org</a> \
						<a id=\"replay-now\" href=\"#\">Replay Presentation</a> \
					  </div>";
		
		jQ( replay ).hide().appendTo("#presenter-container").fadeIn("slow");
		
		jQ("#replay-details #title").html( "<span>Title:</span> " + jQ("#presenter-header #title").html() );
		
		jQ("#replay-link").live("click", function(e) {
			this.select();
			e.preventDefault();
		});
		
		jQ("#replay-now").live("click", function(e) {
			$(this).hide();
			HUB.Presenter.doReplay();
			e.preventDefault();
		});
		
		jQ("#replay-back").live("click", function(e) {
			window.close();
			e.preventDefault();
		});
	},
	
	//-----
	
	doReplay: function()
	{
		jQ("#replay").fadeOut("slow", function() {
			jQ(this).remove();
			jQ("#presenter-container").css("position","static");
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
			HUB.Presenter.seek( time_total );
		}
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
	var base = "/components/com_resources/presenter/img/",
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