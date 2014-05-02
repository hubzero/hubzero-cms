/**
 * @package     hubzero-cms
 * @file        components/com_user/assets/js/link.jquery.js
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
// User link account js
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.UserLink = {
	jQuery: jq,

	initialize: function() {
		var $ = HUB.UserLink.jQuery,
			n = $('.next'),
			p = $('.previous');

		window.onpopstate = function ( e ) {
			if (e.state && e.state.step) {
				var pn = $('.prompt'+e.state.step);

				pn.addClass('incoming');
				$('.prompt-container').fadeOut();
				$('.prompt-wrap').animate({'height': pn.height()});
				pn.fadeIn(function ( e ) {
					$(this).removeClass('incoming');
				});
			} else {
				history.pushState({'step':1}, 'Account Setup', window.location.href);
			}
		};

		n.click(function ( e ) {
			e.preventDefault();

			var step = $(this).data('step'),
				next = step + 1,
				pc   = $('.prompt'+step),
				pn   = $('.prompt'+next);

			pn.addClass('incoming');
			pc.fadeOut();
			$('.prompt-wrap').animate({'height': pn.height()});
			pn.fadeIn(function ( e ) {
				$(this).removeClass('incoming');
			});

			history.pushState({'step':next}, 'Account Setup', $(this).parent().attr('href'));
		});

		p.click(function ( e ) {
			e.preventDefault();

			var step = $(this).data('step'),
				next = step - 1,
				pc   = $('.prompt'+step),
				pn   = $('.prompt'+next);

			pn.addClass('incoming');
			pc.fadeOut();
			$('.prompt-wrap').animate({'height': pn.height()});
			pn.fadeIn(function ( e ) {
				$(this).removeClass('incoming');
			});

			history.pushState({'step':next}, 'Account Setup', $(this).parent().attr('href'));
		});
	}
};

jQuery(document).ready(function($){
	HUB.UserLink.initialize();
});