/**
 * @package     hubzero-cms
 * @file        components/com_resources/resources.js
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
// Publication Ranking pop-ups
//----------------------------------------------------------

HUB.Publications = {

	initialize: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 750, y: 500} });
			}
			
			// Modal boxes for presentations
			$$('a.play').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();

					w = 750;
					h = 500;
					if (this.className) {
						var sizeString = this.className.split(' ').pop();
						if (sizeString && sizeString != 'play') {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
					}

					SqueezeBoxHub.fromElement(el,{handler: 'url', size: {x: w, y: h}, ajaxOptions: {method: 'get',evalScripts:true}});
				});
			});
			
			var win = new Element('a', {
				id: 'sbox-newwindow',
				href: '#',
				title: 'Open in a new window',
				events: {
					'click': function(event) {
						OpenWindow = window.open('', "newwin", "height=500,width=700,toolbar=no,scrollbars=yes,menubar=no,resizable=yes,location=no,scrollbars=no,directories=no,status=no");
						OpenWindow.document.write('<html>');
						OpenWindow.document.write('<title>Presentation</title>');
						OpenWindow.document.write('<body style="margin:0;padding:0;border:0;">');
						OpenWindow.document.write( $('sbox-window').innerHTML );
						OpenWindow.document.write('</body>');
						OpenWindow.document.write('</html>');
						OpenWindow.document.close();
						self.name = 'main';
						SqueezeBoxHub.close();
					}
				}
			});
			var res = new Element('div', {
				id: 'sbox-resizehandle',
				alt: 'Resize'
			})
			
			if($('sbox-window'))
			{
				$('sbox-window').adopt(win, res);

				// Init the resizing capabilities
				$('sbox-window').makeResizable({
					handle:$('sbox-resizehandle'),
					onComplete: function(el) {
						var size = el.getCoordinates();
						SqueezeBoxHub.resize({x:size.width,y:size.height},false)
					}
				});	
			}
		}
		
		// Fixed resource tooltips
		var rTTips = new MooTips($$('.fixedResourceTip'), {
			showDelay: 500,
			maxTitleChars: 100,
			className: 'resource',
			fixed: true,
			offsets: {'x':20,'y':5}
		});
		
		// Ranking info pop-up
		var metadata = $$('.metadata');
		if (metadata) {
			/*
			metadata.each(function(meta) {
				meta.addEvent('mouseover', function(e) {
					var el = this.getElement('.rankinfo');
					el.addClass('active');
				});
				meta.addEvent('mouseout', function(e) {
					var el = this.getElement('.rankinfo');
					el.removeClass('active');
				});
			});
			*/
			metadata.each(function(meta) {
				$$('.rankinfo').addEvent('mouseover', function(e) {
						this.addClass('active');
					});
				$$('.rankinfo').addEvent('mouseout', function(e) {
						this.removeClass('active');
				});
			});
		}
		
		// Audience info pop-up
		var explainscale = $$('.explainscale');
		if (explainscale) {
			var ex = metadata.getElement('.explainscale');		
			$$('.usagescale').each(function(item) {
					
					item.addEvent('mouseover', function() {					
						ex.addClass('active');
					});
			});
			$$('.usagescale').each(function(item) {
				
					item.addEvent('mouseout', function() {					
						ex.removeClass('active');
					});
			});
		}
		
		// Primary-document info pop-up
		var primarydoc = $('primary-document');
		var primarydocpop = $('primary-document_pop');
		if (primarydoc && primarydocpop) {
			primarydoc.addEvent('mouseover', function(e) {
				//new Event(e).stop();
				primarydocpop.style.display = "block";
			});
			
			primarydoc.addEvent('mouseout', function(e) {
				primarydocpop.style.display = "none";
			});
		} 
		
		
		//Hubpresenter
		$$(".hubpresenter, .video").each(function(el) {
			if (el.href.indexOf('?') == -1) {
				el.href = el.href + '?tmpl=component';
			} else {
				el.href = el.href + '&tmpl=component';
			}
		});
		
		//HUBpresenter open window
		$$(".hubpresenter").addEvent("click", function(e) {
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if(!mobile) {
				new Event(e).stop();
		 		HUBpresenter_window = window.open(this.href,'name','height=650,width=1100');
			}
		});
		
		$$(".video").addEvent("click", function(e) {
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if(!mobile) {
				new Event(e).stop();
				var w = 0,
					h = 0,
					dw = 900,
					dh = 600;
			
				//get the dimensions from classs name
				dim = this.className.split(" ").pop();
				
				//if we have dimensions then parse them
				if(dim.match(/[0-9]{2,}x[0-9]{2,}/g)) {
					dim = dim.split("x");
					w = dim[0];
					h = dim[1];
				} else {
					w = dw;
					h = dh;
				}
				
				//open poup
		 		video_window = window.open( this.href,'videowindow','height=' + h + ', width=' + w + ', menubar=no, toolbar=yes, titlebar=no, resizable=yes');
			}
		});
		
		//------------------------
		// screenshot thumbnail slider
		//------------------------
		
		var target = $$('.showcase-pane')[0];
        	
		if($('showcase') && target) {	
			var sidemargin = 4;
  			var thumbwidth = 110;
			var moveto = 0;
			var active = 0;
			var panels = 0;
			
			var next = $('showcase-next');
			var prev = $('showcase-prev');
			
			thwidth = $$('.thumbima').length * sidemargin * 2 + $$('.thumbima').length * thumbwidth;
			var win_width = $('showcase-window').offsetWidth;
			
			if(thwidth/win_width < 1) {
				next.addClass('inactive');
				prev.addClass('inactive');
			}
					
			// go next		
			if (next) {
				next.addEvent('mouseover', function() {
					//var win_width = $('showcase-window').getStyle('width').toInt();
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
						this.addClass('inactive');
						prev.addClass('inactive');
					}
					else {
						this.removeClass('inactive');
						prev.removeClass('inactive');
					}
				});								
				next.addEvent('click', function() {
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
					 	panels = 0;	
					}
					else {
						panels = Math.round(thwidth/win_width);
					}
					
					if(panels >= 1 && active < panels) {
						active ++;
						moveto -= win_width;
								
						var fx = new Fx.Styles(target, {duration: 600, wait: false});
						 fx.start({
							'left': [moveto]
						});
					}
				});
			}
			
			// go prev
			if (prev) {
				prev.addEvent('mouseover', function() {
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
						this.addClass('inactive');
						next.addClass('inactive');
					}
					else {
						this.removeClass('inactive');
						next.removeClass('inactive');
					}
				});	
				prev.addEvent('click', function() {
					var win_width = $('showcase-window').offsetWidth;
					var panels = Math.round(thwidth/win_width);	
					
					if(panels >= 1 && active > 0) {
									
						active --;
						moveto += win_width;
	
						var fxright = new Fx.Styles(target, {duration: 600, wait: false});
						 fxright.start({
							'left': [moveto]
						});
					}
				});
			}
		
		}
	} // end initialize
}

window.addEvent('domready', HUB.Publications.initialize);