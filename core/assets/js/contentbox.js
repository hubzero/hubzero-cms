/* ------------------ */
// Content box plugin //
/* ------------------ */

(function( $ ) {

	var settings = {},
		methods  = {
			init : function ( options ) {
				// Create some defaults, extending them with any options that were provided
				settings = $.extend( {
					element     : $('.content-box'),
					title       : 'Edit Item',
					src         : '/courses',
					tmpl        : [
							'<div class="content-box-overlay"></div>',
							'<div class="content-box">',
								'<h3 class="content-box-header">',
									'<span>Create a note</span>',
									'<div class="content-box-close"></div>',
								'</h3>',
								'<div class="content-box-inner">',
									'<div class="loading-bar"></div>',
								'</div>',
							'</div>'
						].join('\n'),
					onAfterLoad : function ( content ) {}
				}, options);

				// Add close on escape
				$(document).bind('keydown', function ( e ) {
					if(e.which == 27) {
						methods.close();
					}
				});

				if (!settings.element.length) {
					$('body').wrapInner('<div class="content-box-body-wrap"></div>');
					$('body').append(settings.tmpl);
					settings.element = $('.content-box');
				}

				// Try to prevent the background from scrolling
				$('html, body').css({
					'overflow' : 'hidden'
				});

				// Add close on click of close button
				settings.element.find('.content-box-close').on('click', function () {
					methods.close();
				});

				// Finally, execute show
				methods.show();
			},
			show : function () {
				$('.content-box-header span').html(settings.title);
				settings.element.find('.loading-bar').show();
				$('.content-box-body-wrap').addClass('content-box-body-wrap-active');
				$('.content-box-overlay').addClass('content-box-overlay-active');
				settings.element.show('slide', {'direction':'down'}, 500, function () {
					$(this).find('.content-box-inner').append('<iframe src="'+settings.src+'"></iframe>');

					// Execute after load function
					settings.element.find('iframe').load(function () {
						var content = $(this).contents();

						// Add close on escape within iframe as well
						content.bind('keydown', function ( e ) {
							if(e.which == 27) {
								methods.close();
							}
						});

						settings.element.find('.loading-bar').hide();
						settings.onAfterLoad( content );
					});
				});
			},
			close : function () {
				settings.element.find('iframe').remove();
				$('.content-box-body-wrap').removeClass('content-box-body-wrap-active');
				$('.content-box-overlay').removeClass('content-box-overlay-active');
				settings.element.hide('slide', {'direction':'down'}, 500, function () {
					settings.element.remove();
					$('.content-box-overlay').remove();
					$('.content-box-body-wrap').children().first().unwrap();
				});

				// Release background scrolling
				$('html, body').css({
					'overflow' : 'auto'
				});
			}
	};

	$.contentBox = function ( method ) {
		// Method calling logic
		if ( methods[ method ] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' + method + ' does not exist on jQuery.contentBox' );
		}
	};
})( jQuery );