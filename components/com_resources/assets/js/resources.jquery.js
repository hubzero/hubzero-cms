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
// Resource Ranking pop-ups
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Resources = {
	
	jQuery: jq,

	settings: {
	},

	initialize: function() {
		var $ = this.jQuery;
		
		$('a.play').fancybox({
			type: 'ajax',
			width: 750,
			height: 500,
			autoSize: false,
			fitToView: false,
			beforeLoad: function() {
				href = this.element.attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				
				this.element.attr('href', href);
				this.href = href;

				if (this.element.attr('class')) {
					var sizeString = this.element.attr('class').split(' ').pop();
					if (sizeString) { // && sizeString.match('/\d+x\d+/gi')) {
						var sizeTokens = sizeString.split('x');
						if (parseInt(sizeTokens[0])) {
							this.width  = parseInt(sizeTokens[0]) - 20;
						}
						if (parseInt(sizeTokens[1])) {
							this.height = parseInt(sizeTokens[1]) - 60;
						}
					}
				}
			},
			afterShow: function() {
				if ($('#hubForm-ajax')) {
					$('#hubForm-ajax').on('submit', function(e) {
						e.preventDefault();
						$.post($(this).attr('action'));
						$.fancybox.close();
						return false;
					});
				}
			}
		});
		
		$('.fixedResourceTip').tooltip({
			position:'TOP RIGHT',
			offset: [10,-20],
			onBeforeShow: function(event, position) {
				var tip = this.getTip(),
					tipText = tip[0].innerHTML;
					
				if (tipText.indexOf(" :: ") != -1) {
					var parts = tipText.split(" :: ");
					tip[0].innerHTML = "<span class=\"tooltip-title\">"+parts[0]+"</span><span>"+parts[1]+"</span>";
				}
			}
		});
		
		$('.metadata').each(function(i, meta) {
			$('.rankinfo')
				.on('mouseover', function(e) {
					$(this).addClass('active');
				})
				.on('mouseout', function(e) {
					$(this).removeClass('active');
				});
		});
		
		// Audience info pop-up
		$('.explainscale').each(function(k, ex) {	
			$('.usagescale').each(function(i, item) {
				$(item).on('mouseover', function() {					
					$(ex).addClass('active');
				});
			});
			$('.usagescale').each(function(i, item) {
				$(item).on('mouseout', function() {
					$(ex).removeClass('active');
				});
			});
		});
		
		// Primary-document info pop-up
		if ($('#primary-document').length && $('#primary-document_pop').length) {
			$('#primary-document')
				.on('mouseover', function(e) {
					$('#primary-document_pop').show();
				})
				.on('mouseout', function(e) {
					$('#primary-document_pop').hide();
				});
		}
		
		//HUBpresenter open window
		$('.com_resources').on('click', '.hubpresenter', function(event) {
			var href = $(this).attr('href');
			if ($(this).attr('href').indexOf('?') == -1) 
			{
				href += '?tmpl=component';
			} 
			else 
			{
				href += '&tmpl=component';
			}
			
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if (!mobile) {
				event.preventDefault();
		 		HUBpresenter_window = window.open(href,'name','height=650,width=1100');
			}
		});
		
		//html5 video open
		$(".com_resources").on('click', '.video', function(event) {
			var href = $(this).attr('href');
			if ($(this).attr('href').indexOf('?') == -1) 
			{
				href += '?tmpl=component';
			} 
			else 
			{
				href += '&tmpl=component';
			}
			
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if (!mobile) {
				event.preventDefault();
				var w = 0,
					h = 0,
					dw = 900,
					dh = 600;
			
				//get the dimensions from classs name
				dim = $(this).attr('class').split(" ").pop();
				
				//if we have dimensions then parse them
				if (dim.match(/[0-9]{2,}x[0-9]{2,}/g)) {
					dim = dim.split("x");
					w = dim[0];
					h = dim[1];
				} else {
					w = dw;
					h = dh;
				}
				
				//open poup
		 		video_window = window.open( href,'videowindow','height=' + h + ', width=' + w + ', menubar=no, toolbar=no, titlebar=no, resizable=no');
			}
		});
		
		//------------------------
		// screenshot thumbnail slider
		//------------------------
		
		var target = $('.showcase-pane')[0];

		if ($('#showcase').length && target) {	
			var sidemargin = 4,
				thumbwidth = 110,
				moveto = 0,
				active = 0,
				panels = 0;
			
			var next = $('#showcase-next'),
				prev = $('#showcase-prev');

			thwidth = $('.thumbima').length * sidemargin * 2 + $('.thumbima').length * thumbwidth;
			var win_width = $('#showcase-window').offset().left;
			
			if (thwidth/win_width < 1) {
				next.addClass('inactive');
				prev.addClass('inactive');
			}

			// go next		
			if (next.length > 0) {
				next.on('mouseover', function() {
					var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
						$(this).addClass('inactive');
						prev.addClass('inactive');
					} else {
						$(this).removeClass('inactive');
						prev.removeClass('inactive');
					}
				});

				next.on('click', function() {
					var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
					 	panels = 0;	
					} else {
						panels = Math.round(thwidth/win_width);
					}

					if (panels >= 1 && active < panels) {
						active ++;
						moveto -= win_width;

						$(target).css('left', moveto);
					}
				});
			}
			
			// go prev
			if (prev.length > 0) {
				prev.on('mouseover', function() {
					var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
						$(this).addClass('inactive');
						next.addClass('inactive');
					} else {
						$(this).removeClass('inactive');
						next.removeClass('inactive');
					}
				});
				
				prev.on('click', function() {
					var win_width = $('#showcase-window').offset().left,
						panels = Math.round(thwidth/win_width);	
					
					if (panels >= 1 && active > 0) {
						active --;
						moveto += win_width;

						$(target).css('left', moveto);
					}
				});
			}
		
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Resources.initialize();
});