/**
 * @package     hubzero-cms
 * @file        components/com_resources/resources.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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

HUB.Publications = {

	jQuery: jq,

	settings: {
	},

	initialize: function() {
		var $ = this.jQuery;
		
		$('#filter-type').on('change', function(e){
			$('#resourcesform').submit();
		});

		$('a.showBundle').fancybox({
			type: 'ajax',
			width: 800,
			height: 'auto',
			autoSize: false,
			fitToView: false
		});

		$('a.play').fancybox({
			type: 'ajax',
			width: 800,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			wrapCSS: 'sbp-window',
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);

				if ($(this).attr('class')) {
					var sizeString = $(this).attr('class').split(' ').pop();
					if (sizeString && sizeString.match('/\d+x\d+/')) {
						var sizeTokens = sizeString.split('x');
						$.fancybox().width = parseInt(sizeTokens[0]);
						$.fancybox().height = parseInt(sizeTokens[1]);
					}
				}
			},
			afterShow: function() {
				if ($('#embedded-content')) {
					$('#embedded-content').contents().find("body").css({'max-height':'460px'});
					$('#embedded-content').contents().find("video").css({'max-height':'450px'});
					$('#embedded-content').contents().find("body").css({'max-width':'800px'});
					$('#embedded-content').contents().find("video").css({'width':'100%'});
				}
				if ($('#hubForm-ajax')) {
					$('#hubForm-ajax').submit(function(e) {
						e.preventDefault();
						$.post($(this).attr('action'));
						$.fancybox().close();
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
			$('.rankinfo').live('mouseover', function(e) {
				$(this).addClass('active');
			});
			$('.rankinfo').live('mouseout', function(e) {
				$(this).removeClass('active');
			});
		});

		// Primary-document info pop-up
		if ($('#primary-document') && $('#primary-document_pop')) {
			$('#primary-document').live('mouseover', function(e) {
				$('#primary-document_pop').show();
			});
			$('#primary-document').live('mouseout', function(e) {
				$('#primary-document_pop').hide();
			});
		}

		// Launch options
		if ($('#launch-primary').length > 0 && $('#launch-choices').length > 0)
		{
			var keyupTimer = '';
			$('#launch-primary').click(function(e) {
				e.preventDefault();
				if (keyupTimer) {
					clearTimeout(keyupTimer);
				}
				if ($('#launch-choices').hasClass('hidden'))
				{
					$('#launch-choices').removeClass('hidden');
					var keyupTimer = setTimeout((function()
					{
						$('#launch-choices').addClass('hidden');
					}), 6000);
				}
			});
		}

		//Hubpresenter
		$(".hubpresenter, .video").each(function(i, el) {
			if ($(el).attr('href') && $(el).attr('href').indexOf('?') == -1) {
				$(el).attr('href', $(el).attr('href') + '?tmpl=component');
			} else {
				$(el).attr('href', $(el).attr('href') + '&tmpl=component');
			}
		});

		//HUBpresenter open window
		$(".hubpresenter").click(function(e) {
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if (!mobile) {
				e.preventDefault();
		 		HUBpresenter_window = window.open($(this).attr('href'),'name','height=650,width=1100');
			}
		});

		$(".video").click(function(e) {
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if (!mobile) {
				e.preventDefault();
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
		 		video_window = window.open( $(this).attr('href'),'videowindow','height=' + h + ', width=' + w + ', menubar=no, toolbar=no, titlebar=no, resizable=no');
			}
		});

		//------------------------
		// screenshot thumbnail slider
		//------------------------

		var target = $('.showcase-pane')[0];

		if ($('#showcase') && target) {
			var sidemargin = 4,
				thumbwidth = 110,
				moveto = 0,
				active = 1,
				panels = 0;

			var next = $('#showcase-next'),
				prev = $('#showcase-prev');

			thwidth = $('.thumbima').length * sidemargin * 2 + $('.thumbima').length * thumbwidth;
			var win_width = $('#showcase-window').outerWidth();

			if (thwidth/win_width < 1) {
				next.addClass('inactive');
				prev.addClass('inactive');
			}

			// go next
			if (next.length > 0) {
				next.on('mouseover', function() {
					//var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
						$(this).addClass('inactive');
						prev.addClass('inactive');
					} else {
						$(this).removeClass('inactive');
						prev.removeClass('inactive');
					}
				});

				next.on('click', function() {
					//var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
					 	panels = 0;
					} else {
						panels = Math.round(thwidth/win_width);
					}
					if (panels == 1 && thwidth > win_width)
					{
						panels = 2;
					}

					if (panels >= 1 && active < panels) {
						active ++;
						moveto -= win_width;

					//	$(target).css('left', moveto);
						$(target).animate({ "left": moveto }, 1000);
					}
				});
			}

			// go prev
			if (prev.length > 0) {
				prev.on('mouseover', function() {
					//var win_width = $('#showcase-window').offset().left;
					if (thwidth/win_width < 1) {
						$(this).addClass('inactive');
						next.addClass('inactive');
					} else {
						$(this).removeClass('inactive');
						next.removeClass('inactive');
					}
				});

				prev.on('click', function() {
					//var win_width = $('#showcase-window').offset().left;
					panels = Math.round(thwidth/win_width);
					if (panels >= 1 && active > 1) {
						active --;
						moveto += win_width;

					//	$(target).css('left', moveto);
						$(target).animate({ "left": moveto }, 1000);
					}
				});
			}
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Publications.initialize();
});