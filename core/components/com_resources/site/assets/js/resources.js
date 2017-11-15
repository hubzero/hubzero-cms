/**
 * @package     hubzero-cms
 * @file        components/com_resources/site/assets/js/resources.js
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

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};
String.prototype.tmplComponent = function () {
	if (this.indexOf('?') == -1) {
		return this + '?tmpl=component';
	} else {
		return this + '&tmpl=component';
	}
};

HUB.Resources = {
	resizeInlineHubpresenter: function(height)
	{
		$('.embedded-hubpresenter').animate({
			height: height
		}, 300);
	},
	resizeInlineVideo: function(height)
	{
		$('.embedded-video').animate({
			height: height
		}, 300);
	},
	popoutInlineHubpresnter: function(time)
	{
		var source = $('.embedded-hubpresenter iframe').attr('src');
		HUBpresenter_window = window.open(source+'&time='+time+'&auto-resume=true','name','height=800,width=1100');
		$('.hubpresenter').trigger('click');
	},
	popoutInlineVideo: function(time, directSource)
	{
		var source = directSource === undefined ? $('.embedded-video iframe').attr('src') : directSource;
		HUBpresenter_window = window.open(source+'&time='+time+'&auto-resume=true','name','height=800,width=1100');
		if (directSource === undefined)
		{
			$('.video').trigger('click');
		}
	},
	fullscreenHubpresenter: function()
	{
		var iframe = $('.embedded-hubpresenter iframe').get(0);

		if (iframe.requestFullscreen) {
			iframe.requestFullscreen();
		} else if (iframe.webkitRequestFullscreen) {
			iframe.webkitRequestFullscreen();
		} else if (iframe.mozRequestFullScreen) {
			iframe.mozRequestFullScreen();
		} else if (iframe.msRequestFullscreen) {
			iframe.msRequestFullscreen();
		}
	},
	exitFullscreenHubpresenter: function()
	{
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		}
	},
};

jQuery(document).ready(function($){
	$('a.play').fancybox({
		type: 'ajax',
		width: 750,
		height: 500,
		autoSize: false,
		fitToView: false,
		beforeLoad: function() {
			href = this.element.attr('href').nohtml();

			this.element.attr('href', href);
			this.href = href;

			if (this.element.attr('class')) {
				var sizeString = this.element.attr('class').split(' ').pop();
				if (sizeString && sizeString.match(/\d+x\d+/i)) {
					var sizeTokens = sizeString.split('x');
					if (parseInt(sizeTokens[0]))
					{
						this.width  = parseInt(sizeTokens[0]);
					}
					if (parseInt(sizeTokens[1]))
					{
						this.height = parseInt(sizeTokens[1]);
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

	$('#filter-type').on('change', function(e){
		$('#resourcesform').submit();
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
			$(item)
				.on('mouseover', function() {
					$(ex).addClass('active');
				})
				.on('mouseout', function() {
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
		event.preventDefault();
		if ($('.embedded-hubpresenter').length)
		{
			// remove embedded content
			$('.embedded-hubpresenter').animate({
				height: 0
			}, 400, function(){
				$('.embedded-hubpresenter').remove();
			});

			// change bbb text
			$(this).text('View Presentation');
		}
		else
		{
			var source = $(this).attr('href').tmplComponent();
			var content = '<section class="embedded-hubpresenter"><iframe src="' + source + '"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></section>';
			$('section.upperpane').after(content);
			$('.embedded-hubpresenter iframe').load(function(event){
				var height = $(this).contents().height();
				
				// make embed area height of iframe
				$('.embedded-hubpresenter').animate({
					height: height
				}, 800, function() {
					$(this).addClass('loaded');
				});

				// scroll to embedded media
				$('body').animate({
					scrollTop: $('.embedded-hubpresenter').offset().top
				}, 1000);
			});

			// change bbb text
			$(this).text('Hide Presentation');
		}
	});
	
	//html5 video open
	$(".com_resources, #tagbrowser").on('click', '.video', function(event) {
		event.preventDefault();

		if ($('.embedded-video').length)
		{
			// remove embedded content
			$('.embedded-video').animate({
				height: 0
			}, 400, function(){
				$('.embedded-video').remove();
			});

			// change bbb text
			$(this).text('View Presentation');
		}
		else
		{
			var source = $(this).attr('href').tmplComponent();
			var popout = $(this).data('popOut');
			if (popout == true)
			{ 
				HUB.Resources.popoutInlineVideo('00:00:00', source);
			}
			else
			{
				var content = '<section class="embedded-video"><iframe src="' + source + '"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></section>';
				$('section.upperpane').after(content);
				$('.embedded-video iframe').load(function(event){
					var iframe = $(this);
					setTimeout(function() {
						var height = '75vh';
						
						// make embed area height of iframe
						$('.embedded-video').animate({
							height: height
						}, 800, function() {
							$(this).addClass('loaded');
						});

						// scroll to embedded media
						$('body').animate({
							scrollTop: $('.embedded-video').offset().top
						}, 1000);
					}, 100);
					
				});
			}

			// change bbb text
			$(this).text('Hide Presentation');
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
});
