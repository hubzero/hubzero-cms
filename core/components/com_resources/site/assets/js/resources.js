/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		var iframe = $('#presenter-container').get(0);

        if (!document.fullscreenElement) {
            iframe.requestFullscreen()
        } else {
            iframe.exitFullscreen();
        }
	},
	exitFullscreenHubpresenter: function()
	{
        document.exitFullscreen();
	}
};

jQuery(document).ready(function($){
	// Pop-up viewer for "play" links: the primary "View ..." button and any
	// supporting document with link_action=2. A native <dialog> sized to the
	// viewport (keeping the aspect ratio given by the link's WxH class) is
	// filled with the no_html render of the target, which is what the old
	// fancybox 'ajax' popup injected too, so every content type (SWF via
	// Ruffle, images, video, audio, documents) keeps working.
	HUB.Resources.lightbox = (function () {
		var dlg, frame, titleEl, openEl, content, opener = null, aspect = 16 / 9;
		var icons = {
			open:  '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>',
			full:  '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9V4h5M15 4h5v5M20 15v5h-5M9 20H4v-5"/></svg>',
			close: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>'
		};

		function build() {
			dlg = document.createElement('dialog');
			dlg.className = 'rs-lightbox';
			dlg.setAttribute('aria-labelledby', 'rs-lightbox-title');
			dlg.innerHTML =
				'<div class="rs-lightbox__frame">' +
					'<div class="rs-lightbox__head">' +
						'<h2 class="rs-lightbox__title" id="rs-lightbox-title"></h2>' +
						'<div class="rs-lightbox__actions">' +
							'<a class="rs-lightbox__btn rs-lightbox__open" target="_blank" rel="noopener" title="Open in a new tab" aria-label="Open in a new tab">' + icons.open + '</a>' +
							'<button type="button" class="rs-lightbox__btn rs-lightbox__full" title="Full screen" aria-label="Full screen">' + icons.full + '</button>' +
							'<button type="button" class="rs-lightbox__btn rs-lightbox__close" title="Close" aria-label="Close">' + icons.close + '</button>' +
						'</div>' +
					'</div>' +
					'<div class="rs-lightbox__body"><div class="rs-lightbox__content"></div></div>' +
				'</div>';
			document.body.appendChild(dlg);

			frame   = dlg.querySelector('.rs-lightbox__frame');
			titleEl = dlg.querySelector('.rs-lightbox__title');
			openEl  = dlg.querySelector('.rs-lightbox__open');
			content = dlg.querySelector('.rs-lightbox__content');

			dlg.querySelector('.rs-lightbox__close').addEventListener('click', function () { dlg.close(); });
			dlg.querySelector('.rs-lightbox__full').addEventListener('click', function () {
				if (document.fullscreenElement) {
					document.exitFullscreen();
				} else if (frame.requestFullscreen) {
					frame.requestFullscreen();
				}
			});
			// A click on the backdrop lands on the dialog element itself.
			dlg.addEventListener('click', function (e) {
				if (e.target === dlg) {
					dlg.close();
				}
			});
			dlg.addEventListener('close', function () {
				// Dropping the content stops whatever was playing in it.
				$(content).empty();
				dlg.classList.remove('rs-lightbox--loading', 'rs-lightbox--error');
				document.body.classList.remove('rs-lightbox-open');
				if (opener && opener.focus) {
					opener.focus();
				}
				opener = null;
			});
			document.addEventListener('fullscreenchange', fit);
			window.addEventListener('resize', fit);
		}

		// Size the content box to the largest WxH-shaped rectangle that fits
		// the viewport (or the screen, in full screen) under the header.
		function fit() {
			if (!dlg || !dlg.open) {
				return;
			}
			var full   = !!document.fullscreenElement && document.fullscreenElement === frame;
			var margin = full ? 0 : (window.innerWidth < 700 ? 8 : 24);
			var headH  = dlg.querySelector('.rs-lightbox__head').offsetHeight;
			var availW = window.innerWidth  - 2 * margin;
			var availH = window.innerHeight - 2 * margin - headH;
			var w = Math.min(availW, full ? availW : 1400, availH * aspect);
			var h = w / aspect;
			if (full) {
				// Fill the screen; letterbox inside the (black) body instead.
				w = availW;
				h = availH;
			}
			content.style.width  = Math.max(280, Math.round(w)) + 'px';
			content.style.height = Math.max(160, Math.round(h)) + 'px';
		}

		function open(link) {
			if (!dlg) {
				build();
			}
			opener = link;

			var href = link.getAttribute('href');
			var m, classes = (link.getAttribute('class') || '').split(/\s+/);
			aspect = 16 / 9;
			for (var i = 0; i < classes.length; i++) {
				if ((m = classes[i].match(/^(\d+)x(\d+)$/)) && +m[1] > 0 && +m[2] > 0) {
					aspect = m[1] / m[2];
				}
			}

			var heading = document.querySelector('.upperpane h2');
			titleEl.textContent = link.getAttribute('title')
				|| (heading ? heading.textContent.replace(/\s+/g, ' ').trim() : '')
				|| link.textContent.replace(/\s+/g, ' ').trim();
			openEl.setAttribute('href', href);

			$(content).empty();
			dlg.classList.add('rs-lightbox--loading');
			dlg.classList.remove('rs-lightbox--error');
			document.body.classList.add('rs-lightbox-open');
			dlg.showModal();
			fit();

			$.get(href.nohtml())
				.done(function (html) {
					if (!dlg.open) {
						return;
					}
					dlg.classList.remove('rs-lightbox--loading');
					// jQuery runs the scripts the page carries (e.g. the Ruffle loader).
					$(content).html(html);
				})
				.fail(function () {
					dlg.classList.remove('rs-lightbox--loading');
					dlg.classList.add('rs-lightbox--error');
					$(content).html('<p class="rs-lightbox__msg">Sorry, this item could not be loaded. <a href="' + href + '" target="_blank" rel="noopener">Open it in a new tab.</a></p>');
				});
		}

		return { open: open, fit: fit };
	})();

	$(document).on('click', 'a.play', function (e) {
		// Modified clicks and browsers without <dialog> get the plain link.
		if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.which === 2 || !window.HTMLDialogElement) {
			return;
		}
		e.preventDefault();
		HUB.Resources.lightbox.open(this);
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
	    // To avoid opening videos that require user to be logged in, just go to the video page and let the code check the permissions
        return;

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
			$('.embedded-hubpresenter iframe').on('load', function(event){
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
				$('.embedded-video iframe').on('load', function(event){
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
