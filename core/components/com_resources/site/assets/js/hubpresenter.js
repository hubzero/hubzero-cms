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
		subtitles = null;
		transcriptLineActive = 0;
		transcriptBoxScrolling = false;
		canSendTracking = true;
		sendingTracking = false;
		detailedTrackingId = null;
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
	
		// video preview
		HUB.Presenter.previews();
	},
	
	//-----
	
	init: function() 
	{                        
		//javascript is enabled
		HUB.Presenter.jsEnabled();
		
		//go to the first slide 
		HUB.Presenter.showSlide( HUB.Presenter.activeSlide );
		
		//slide list
		HUB.Presenter.slideList();
		
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

		// handle popout
		HUB.Presenter.popout();
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
			if (audio) {
				flowplayer("flowplayer", {src: "/core/components/com_resources/site/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
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
				flowplayer("flowplayer", {src: "/core/components/com_resources/site/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, { 
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
		flowplayer(".flowplayer_slide", {src: "/core/components/com_resources/site/assets/swf/flowplayer-3.2.7.swf", wmode: "transparent"}, {
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
				//jQ("#slides ul").css('margin-top', margin);
			}
		}
		
		//get the type of element for the current slide
		var slide_child = jQ('#slide_' + slide).children(),
			slide_child_type = jQ(slide_child).get(0).tagName;
		
		//if the slide is video play video
		if(slide_child_type == 'VIDEO') {
			if(!flash) {
				// get slide video
				// restart video & play
				var videoSlide = slide_child.first().get(0);
				videoSlide.currentTime = 0;
				videoSlide.play();
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
		//call stop incase user keeps clicking prev/next
		if(!mouseover) {
			jQ('#list_items').stop().scrollTo( list_item , 1000, 'easeInOutQuad' );
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
				cur_slide.type = slide.children().first().get(0).tagName;
			}
		});

		//get the next slide after the current max
		next_slide.id = parseInt(cur_slide.id) + 1;

		//get the next slides time and if doesnt exist set to arbitrary high value
		if( jQ('#slide_' + next_slide.id).length ) {
			next_slide.time = jQ('#slide_' + next_slide.id).attr('time');
			next_slide.type = jQ('#slide_' + next_slide.id).children().first().get(0).tagName;
		} else {
			next_slide.time = 99999999;
			next_slide.type = 'IMG';
		}
		
		//if the current time is greater then the current slide and less then the next slide and 
		//isnt set as the active slide(meaning hasnt been activated yet) - go to slide
		if(cur_slide.time <= current && next_slide.time > current) {
			if(HUB.Presenter.activeSlide !== cur_slide.id) {
				HUB.Presenter.showSlide( cur_slide.id );
			}
		}

		// keep slide videos in sync
		if (cur_slide.type == 'VIDEO')
		{
			var videoSlide     = jQ('#slide_' + cur_slide.id).find('.slidevideo').get(0),
				videoSlideTime = videoSlide.currentTime,
				shouldBeAtTime = current - cur_slide.time,
				timeDifference = videoSlideTime - shouldBeAtTime;
			
			// only adjust time as needed
			if (timeDifference > 0.5 || timeDifference < -0.5)
			{
				// pause video & then set time
				videoSlide.pause();
				videoSlide.currentTime = shouldBeAtTime;
				
				//only play if video is playing
				if (!HUB.Presenter.isPaused())
				{
					videoSlide.play();
				}
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
		var height = jQ('#slides').height() - jQ('#media').height() - 1; //+ jQ('#control-box').height()
		jQ('#list_items').height( height );
		
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

		// link video
		jQ('#link').bind('hover', function(e) {
			e.preventDefault();
			HUB.Presenter.linkVideo();
		});

		// change speed
		jQ('#speed').on('change', function(e){
			var player = HUB.Presenter.getPlayer(),
				rate   = jQ(this).val();
			player.playbackRate = rate;
		});

		// theme changer
		jQ('#theme').on('change', function(e) {
			jQ('#control-box').attr('data-theme', jQ(this).val());
			if (localStorage)
			{
				localStorage.setItem('resources.hubpresenter.theme', jQ(this).val());
			}
		});

		// do we have a saved theme
		if (localStorage && localStorage.getItem('resources.hubpresenter.theme'))
		{
			var theme = localStorage.getItem('resources.hubpresenter.theme');
			jQ('#theme').val(theme)
			jQ('#control-box').attr('data-theme', theme);
		}

		// do we want to display captions automatically
		if (localStorage && localStorage.getItem('resources.' + jQ('#presenter-container').attr('data-id') + '.captions'))
		{
			var track = localStorage.getItem('resources.' + jQ('#presenter-container').attr('data-id') + '.captions');
			if (track != '')
			{
				// wait a second before selecting value
				// gives time for subtitles to be setup
				setTimeout(function() {
					jQ('#subtitle-selector option[value='+lang+']').attr('selected', 'selected');
					jQ('#subtitle-selector').trigger('change');
				}, 1000);
			}
		}
		
		// do we want to display transcript automatcially
		if (localStorage && localStorage.getItem('resources.' + jQ('#presenter-container').attr('data-id') + '.transcript'))
		{
			var lang = localStorage.getItem('resources.' + jQ('#presenter-container').attr('data-id') + '.transcript');
			if (lang != '')
			{
				// wait a second before selecting value
				// gives time for transcripts to be setup
				setTimeout(function() {
					jQ('.transcript-selector option[value='+lang+']').attr('selected', 'selected');
					jQ('.transcript-selector').trigger('change');
				}, 1000);
			}
		}

		//progress bar functionality
		HUB.Presenter.progressBar();
		
		//volume bar functionality
		HUB.Presenter.volumeBar();
	},

	//-----
	
	previews: function()
	{
		var p = HUB.Presenter.getPlayer();

		// only append once
		if (!jQ('#control-box .preview').length)
		{
			jQ('#control-box').append('<div class="preview"><video src="' + p.currentSrc + '"></video><div class="tip"></div></div>');
		}

		// get scale based on progress bar width and video length
		var scale = jQ('#progress-bar').width() / HUB.Presenter.getDuration();

		// show preview on mousemove
		jQ('#progress-bar')
			.on('mousemove', function(e) {
				var origPos = e.pageX - jQ('#progress-bar').offset().left,
					pos     = origPos,
					min     = jQ('.preview').outerWidth() / 2,
					max     = jQ('#progress-bar').width() - min,
					tipPos  = pos,
					tipMin  = jQ('.preview .tip').outerWidth() / 2,
					tipMax  = jQ('#progress-bar').width();

				// set the current time
				jQ('.preview video').get(0).currentTime = pos / scale;
				
				// position thumb
				if (pos < min)
				{
					pos = min;
				}
				else if (pos > max)
				{
					pos = max;
				}

				// position tip
				if (origPos > 0 && origPos < min)
				{
					tipPos = origPos - 6;
				}
				else if (origPos > max && origPos < tipMax)
				{
					p = origPos - 6;
					tipPos = p - pos + min;
				}
				else
				{
					tipPos = min - 6;
				}

				// set position
				jQ('.preview').css('left', pos);
				jQ('.preview .tip').css('left', tipPos);
			})
			.on('hover', function(e) {
				jQ('.preview').toggleClass('visible');
			});
	},

	//-----

	playPause: function( clicking )
	{
		var paused = HUB.Presenter.isPaused(),
			player = HUB.Presenter.getPlayer();

		if( paused ) {
			jQ("#play-pause").removeClass('playing').addClass('paused');
			jQ('#presenter-content').addClass('paused');
			if( clicking ) 
				player.play();
		} else {
			jQ("#play-pause").removeClass('paused').addClass('playing');
			jQ('#presenter-content').removeClass('paused');
			if( clicking )
				player.pause();
		}

		// pause play slide videos
		jQ('.slidevideo').each(function(index, element)
		{
			// only act when clicking
			if (clicking)
			{
				if (paused)
				{
					element.play();
				}
				else
				{
					element.pause();
				}
			}
		});
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
			jQ("#slides").css({'padding-left':'50px', 'padding-right':'55px'});
		} else {
			jQ("#media").prependTo("#presenter-left");
			jQ("#slides").prependTo("#presenter-right");
			jQ("#presenter-left").css('width', '745px');
			jQ("#presenter-right").css('width', '320px');
			jQ("#slides").css('padding', '0px');
		}
		
		jQ("#slides ul").css('margin-top', 0);
		
		var leftWidth = jQ("#presenter-left").width();
		var rightWidth = jQ("#presenter-right").width();
		var totalWidth = leftWidth + rightWidth;	
		
		jQ("#presenter-content").width(totalWidth);	

		if(!paused) {
			player.play();
		}
	},
	
	//-----
	
	linkVideo: function(input)
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
		url = url + time_hash;

		// set val and select
		jQ('.link-controls input')
			.val(url)
			.on('click', function(event){
				jQ(this).select();
			});
	},

	//-----
	
	popout: function()
	{
		if (parent.HUB.Resources)
		{
			jQ('.embed-popout').css('display', 'inline-block').on('click', function() {
				var current = HUB.Presenter.formatTime(HUB.Presenter.getCurrent());
				parent.HUB.Resources.popoutInlineHubpresnter(current);
			});

			jQ('.embed-fullscreen').css('display', 'inline-block').on('click', function() {
				if (jQ(this).text() == 'Fullscreen')
				{
					jQ(this)
						.removeClass('icon-fullscreen')
						.addClass('icon-exit-fullscreen')
						.text('Exit Fullscreen');
					parent.HUB.Resources.fullscreenHubpresenter();
				}
				else
				{
					jQ(this)
						.removeClass('icon-exit-fullscreen')
						.addClass('icon-fullscreen')
						.text('Fullscreen');
					parent.HUB.Resources.exitFullscreenHubpresenter();
				}
			});
		}
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
			orientation: 'vertical',
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
						<a class=\"btn icon-close\" id=\"replay-back\" href=\"#\">Close Presentation</a> \
						<a class=\"btn btn-info icon-replay\" id=\"replay-now\" href=\"#\">Replay Presentation</a> \
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
			HUB.Presenter.replayMediaTracking();
			
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
	       
	noFlash: function()
	{  
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
		progress = HUB.Presenter.formatTime( current ) + " / " + HUB.Presenter.formatTime( duration )
				
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
			HUB.Presenter.syncSlides();
		} else {
			flowplayer("flowplayer").seek(time);
			HUB.Presenter.flashSyncSlides();
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
		var icon = jQ('#volume');

		if(volume == 0)
			icon.removeClass('low medium high')
				.addClass('none');
			
		if( volume > 0 && volume < 33) 
			icon.removeClass('zero medium high')
				.addClass('low');
			
		if( volume > 33 && volume < 66) 
			icon.removeClass('zero low high')
				.addClass('medium');
			
		if( volume > 66) 
			icon.removeClass('zero low medium')
				.addClass('high');
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
		// auto resume
		var queryString = window.location.search;
		if (queryString.match(/auto-resume=true/g))
		{
			// use timeout to allow media to load
			setTimeout(function()
			{
				HUB.Presenter.playPause(true);
			}, 250);
			return;
		}

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
							<a class=\"btn icon-restart\" id=\"restart-video\" href=\"#\">Play from the Beginning</a> \
							<a class=\"btn btn-info icon-play\" id=\"resume-video\" href=\"#\">Resume Video</a> \
						  </div>";
			
			//add replay to video container
			jQ( resume ).hide().appendTo("#presenter-container").fadeIn("slow");
			var leftWidth = jQ("#presenter-left").width();
			var rightWidth = jQ("#presenter-right").width();
			var totalWidth = leftWidth + rightWidth;	
			jQ("#resume").width(totalWidth);
			
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
	
	replayMediaTracking: function()
	{
		HUB.Presenter.mediaTrackingEvent('replay');
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
				dataType: 'json',
				url: url,
				data: { 
					event: eventType,
					resourceid: resourceId,
					time: playerTime,
					duration: playerDuration,
					detailedTrackingId: detailedTrackingId
				},
				error: function( jqXHR, status, error )
				{
					console.log(error);
				},
				success: function( data, status, jqXHR )
				{
					detailedTrackingId = data.detailedId;
				},
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
		var sub_titles = HUB.Presenter.getSubtitles();
		
		//create elements on page to hold subtitles
		if(sub_titles.length > 0)
		{	
			//setup subtitle picker
			HUB.Presenter.setupSubtitlePicker( sub_titles );
			
			//setup transcript viewer
			HUB.Presenter.transcriptSetup( sub_titles );
			
			//sync subtitles
			var syncInterval = setInterval( 
				function() {
					HUB.Presenter.syncSubtitles( sub_titles );
				}, 300);
		}
	},
	
	//-----
	
	setupSubtitlePicker: function( sub_titles )
	{
		var auto = false;
		
		// show subtitle button
		jQ('#subtitle').show();

		// add each subtitle to caption divs & selector
		for(n=0; n<sub_titles.length; n++)
		{
			var sel = '',
				sub = sub_titles[n],
				sub_lang = sub.lang.toLowerCase(),
				sub_lang_text = sub.lang;
			
			//do we auto play
			if (parseInt(sub.auto))
			{
				auto = true;
				track = sub_lang;
				sel = 'selected="selected"';
			}
			
			jQ("#video-subtitles").append("<div id=\"" + sub_lang + "\"></div>");
			jQ('#subtitle-selector').append('<option ' + sel + ' value="' + sub_lang + '">' + sub_lang_text + '</option>');
		}
		
		//if we are auto showing subs make sure picker reflects that
		if (auto)
		{
			jQ('#subtitle').addClass('on');
		}

		// handle subtitle changes
		jQ("#subtitle-selector").on("change", function(e) {
			track = jQ(this).val();
			if (track == '')
			{
				jQ('#subtitle').removeClass('on');
			}
			else
			{
				jQ('#subtitle').addClass('on');
			}

			// set transcript lang
			if (localStorage)
			{
				localStorage.setItem('resources.' + jQ('#presenter-container').attr('data-id') + '.captions', track);
			}
		});

		// show options
		jQ('.subtitle-controls .options-toggle').on('click', function(event) {
			var title = (jQ(this).html() == 'Options') ? 'Hide Options' : 'Options';
			jQ(this).html(title);
			jQ('.subtitle-settings').slideToggle();
			jQ('.subtitle-controls').toggleClass('fixed');
		});

		// font selector
		jQ('#font-selector').on('change', function() {
			jQ('.subtitle-settings-preview .test').css('font-family', jQ(this).val());
		});

		// font size selector
		jQ('#font-size-selector').on('change', function() {
			jQ('.subtitle-settings-preview .test').css('font-size', jQ(this).val() + 'px');
		});

		// font color picker
		jQ('#font-color').colpick({
			layout: 'hex',
			submit: 1,
			onChange: function(hsb,hex,rgb,fromSetColor) 
			{
				if(!fromSetColor)
					jQ('.subtitle-settings-preview .test').css('color', '#' + hex);
			},
			onSubmit: function(hsb,hex,rgb,fromSetColor)
			{
				// color chooser & hide colpick
				jQ('#font-color')
					.attr('data-color', '#' + hex)
					.css('background-color', '#' + hex)
					.colpickHide();
			}
		});
		
		// background color picker
		jQ('#background-color').colpick({
			layout: 'hex',
			submit: 1,
			onChange: function(hsb,hex,rgb,fromSetColor)
			{
				if(!fromSetColor)
					jQ('.subtitle-settings-preview .test').css('background-color', '#' + hex);
			},
			onSubmit: function(hsb,hex,rgb,fromSetColor)
			{
				// set chooser color & close colpic
				jQ('#background-color')
					.attr('data-color', '#' + hex)
					.css('background-color', '#' + hex)
					.colpickHide();
			}
		});

		// save settings
		jQ('#subtitle-settings-save').on('click', function(event) {
			event.preventDefault();
			var font            = jQ('#font-selector').val(),
				fontSize        = jQ('#font-size-selector').val() + 'px',
				fontColor       = jQ('#font-color').attr('data-color'),
				backgroundColor = jQ('#background-color').attr('data-color');

			// style the subtitles
			jQ('#video-subtitles div').css({
				'font-family'     : font,
				'font-size'       : fontSize,
				'color'           : fontColor,
				'background-color': backgroundColor
			});

			// store in localstorage or cookie
			if (localStorage)
			{
				localStorage.setItem('resources.hubpresenter.font-family', font);
				localStorage.setItem('resources.hubpresenter.font-size', fontSize);
				localStorage.setItem('resources.hubpresenter.font-color', fontColor);
				localStorage.setItem('resources.hubpresenter.background-color', backgroundColor);
			}

			// reset options toggle text
			jQ('.subtitle-controls .options-toggle').html('Options');

			// remove fixed class on control box and hide options
			jQ('.subtitle-controls').toggleClass('fixed');
			jQ('.subtitle-settings').slideToggle();
		});

		// retrieve saved values
		// check for any key to be saved
		if (localStorage && localStorage.getItem('resources.hubpresenter.font-family'))
		{
			var font            = localStorage.getItem('resources.hubpresenter.font-family'),
				fontSize        = localStorage.getItem('resources.hubpresenter.font-size'),
				fontColor       = localStorage.getItem('resources.hubpresenter.font-color'),
				backgroundColor = localStorage.getItem('resources.hubpresenter.background-color');

			// prefill options
			jQ('#font-selector').val(font);
			jQ('#font-size-selector').val(fontSize.replace('px', ''));
			jQ('#font-color').attr('data-color', fontColor).css('background-color', fontColor);
			jQ('#background-color').attr('data-color', backgroundColor).css('background-color', backgroundColor);

			// style the preview & actual subtitles
			jQ('#video-subtitles div, .subtitle-settings-preview .test').css({
				'font-family'     : font,
				'font-size'       : fontSize,
				'color'           : fontColor,
				'background-color': backgroundColor
			});
		}
	},
	
	//-----
	
	syncSubtitles: function( sub_titles )
	{
		// get current time
		current = HUB.Presenter.getCurrent();

		// get the subs for the track we have selected
		for (i = 0; i < sub_titles.length; i++) { 
			if(sub_titles[i].lang.toLowerCase() == track) {
				var subs = sub_titles[i].subs;
			}
		}
		
		// clear the subtitle tracks between
		jQ("#video-subtitles div").removeClass('showing').hide().html("");
		
		for(i in subs) {
			start = subs[i].start;
			end = subs[i].end;
			text = subs[i].text;
			if(current >= start && current <= end) {
				jQ("#video-subtitles #" + track).addClass('showing');
				jQ("#video-subtitles #" + track).show().html( text.replace( "\n","<br />") );
			}
		}
	},
	
	//-----
	
	getSubtitles: function()
	{
		var subs = new Array(),
			sub_files = jQ("div[data-type=subtitle]");
		
		//loop through each subs file and get the contents then add to subs object
		sub_files.each(function(i){
			var lang = jQ(this).attr("data-lang"),
				src  = jQ(this).attr("data-src"),
				auto = jQ(this).attr("data-autoplay");
			
			jQ.ajax({
				url: src,
				async: false,
				dataType: 'html',
				success: function( content ) {
					var parsed = HUB.Presenter.parseSubtitles( content );
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
	
	transcriptSetup: function( sub_titles )
	{
		//dont add transcript stuff if we dont have needed html
		if (!jQ('#transcript-container').length)
		{
			return;
		}

		subtitles = sub_titles;
		
		//add subtitles
		for (var i = 0, n = sub_titles.length; i < n; i++)
		{
			var language = sub_titles[i].lang,
				subs     = sub_titles[i].subs;
			
			//add language to subtitle language picker
			jQ('.transcript-selector').append('<option value="' + language.toLowerCase() + '">' + language + '</option>');
			
			//add container for each language transcript
			jQ('#transcript-container #transcripts').append('<div class="transcript transcript-' + language.toLowerCase() + '"></div>')
			
			for(var a = 0, b = subs.length; a < b; a++)
			{
				var time = subs[a].start,
					text = subs[a].text;
					line = '<div class="transcript-line" data-time="' + time + '"><div class="transcript-line-time">' + HUB.Presenter.formatTime( time ) + '</div><div class="transcript-line-text">' + text + '</div></div>'
				jQ('.transcript-' + language.toLowerCase()).append( line );
			}
		}
		
		//show first transcript
		jQ('#transcripts').find('.transcript').first().show();
		
		//further setup
		HUB.Presenter.transcriptToggle();
		HUB.Presenter.transcriptFontChanger();
		HUB.Presenter.transcriptSearch();
		HUB.Presenter.transcriptJumpTo();
		
		//sync transcript
		setInterval(function() {
			HUB.Presenter.transcriptSync();
		}, 300);
	},
	
	//-----
	
	transcriptToggle: function()
	{	
		//handle switching languages
		jQ('.transcript-selector').on('change', function(event) {
			var language = jQ(this).val();

			if (language)
			{
				jQ('#transcript-container').slideDown(function() {
					// resize parent
					if (parent.HUB.Resources)
					{
						parent.HUB.Resources.resizeInlineHubpresenter(jQ('body').outerHeight() + 20);
					}
				});
			}
			else
			{
				jQ('#transcript-container').slideUp(function(){
					// resize parent
					if (parent.HUB.Resources)
					{
						parent.HUB.Resources.resizeInlineHubpresenter(jQ('body').outerHeight() + 20);
					}
				});
			}
			jQ('#transcript-select').html(jQ('.transcript-selector option:selected').text());
			jQ('#transcripts').find('.transcript').hide();
			jQ('#transcripts').find('.transcript-' + language).show();

			// set transcript lang
			if (localStorage)
			{
				localStorage.setItem('resources.' + jQ('#presenter-container').attr('data-id') + '.transcript', jQ(this).val());
			}
		});
	},
	
	//-----
	
	transcriptFontChanger: function()
	{
		//make font smaller
		jQ('#font-smaller').on('click',function(event){
			event.preventDefault();
			var transcriptLines   = jQ('.transcript-line'), 
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
						jQ('#font-smaller').addClass('inactive');
					}
				}
				
				jQ('#font-bigger').removeClass('inactive');
		});
		
		//make font bigger
		jQ('#font-bigger').on('click',function(event){
			event.preventDefault();
			var transcriptLines   = jQ('.transcript-line'), 
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
					jQ('#font-bigger').addClass('inactive');
				}
			}
			
			jQ('#font-smaller').removeClass('inactive');
		});

		// resync
		HUB.Presenter.transcriptSync();
	},
	
	//-----
	
	transcriptSearch: function()
	{
		jQ('#transcript-search').on('keyup change', function(event) {
			jQ('.transcript-line-text').removeHighlight();
			jQ('.transcript-line-text').highlight( jQ(this).val() );
		});
	},
	
	//-----
	
	transcriptJumpTo: function()
	{
		jQ('.transcript-line').on('click', function(event) {
			event.preventDefault();
			var time = jQ(this).data('time');
			HUB.Presenter.seek( time );
		});
	},
	
	//-----
	
	transcriptSync: function()
	{
		var currentTime = HUB.Presenter.getCurrent(),
			currentTranscript = jQ('.transcript-selector').val();
		
		//get the subs for the track we have selected
		for(i in subtitles) {
			if(subtitles[i].lang.toLowerCase() == currentTranscript) {
				var subs = subtitles[i].subs;
			}
		}
		
		//flag to know if user is scrolling in box
		jQ('#transcripts').on('scroll', function(event) {
			transcriptBoxScrolling = true;
			clearTimeout(jQ.data(this, 'scrollTimer'));
			jQ.data(this, 'scrollTimer', setTimeout(function() {
				transcriptBoxScrolling = false;
			}, 250));
		});
		
		//remove all previously set active lines
		jQ('.transcript-line').removeClass('active');
		
		//set our active transcript line
		for (i in subs)
		{
			var start = subs[i].start,
				end   = subs[i].end,
				text  = subs[i].text;
				
			if (currentTime >= start && currentTime <= end)
			{
				//add active class to active transcript line
				jQ('.transcript-line').eq(i).addClass('active')
				
				//if were not scrolling in the box
				//only scroll if we just switched to a new line
				if (!transcriptBoxScrolling && transcriptLineActive != i)
				{
					//get height of each transcript line to know how far to scroll down
					var lineHeight = jQ('.transcript-line').outerHeight(true);
					
					//only scroll after the half way point down
					if (lineHeight * i > jQ('#transcripts').outerHeight(true) / 2)
					{
						var middle = jQ('#transcripts').outerHeight(true) / 2;
						
						jQ('#transcripts').scrollTo(lineHeight * i - middle + lineHeight, 300, 'easeInOutQuad' );
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


/*
highlight v4
Highlights arbitrary terms.
<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>
MIT license.
Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>
*/
jQuery.fn.highlight=function(e){function t(e,n){var r=0;if(e.nodeType==3){var i=e.data.toUpperCase().indexOf(n);if(i>=0){var s=document.createElement("span");s.className="highlight";var o=e.splitText(i);var u=o.splitText(n.length);var a=o.cloneNode(true);s.appendChild(a);o.parentNode.replaceChild(s,o);r=1}}else if(e.nodeType==1&&e.childNodes&&!/(script|style)/i.test(e.tagName)){for(var f=0;f<e.childNodes.length;++f){f+=t(e.childNodes[f],n)}}return r}return this.length&&e&&e.length?this.each(function(){t(this,e.toUpperCase())}):this};jQuery.fn.removeHighlight=function(){return this.find("span.highlight").each(function(){this.parentNode.firstChild.nodeName;with(this.parentNode){replaceChild(this.firstChild,this);normalize()}}).end()}
