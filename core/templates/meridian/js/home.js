/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * The clouds along the foot of the hero
 *
 * Two layers moving at different rates as the page scrolls, which is what
 * gives the band its depth. The old welcome template did this with skrollr and
 * a pair of data- attributes on every element; this is the same effect in the
 * dozen lines it actually takes, with no library behind it.
 *
 * Nothing here is content. If the script never runs, or the browser is asked
 * to keep motion still, the clouds sit where the stylesheet put them and the
 * page is exactly as usable.
 */
(function () {
	'use strict';

	// This is loaded in the head, so there is no page yet to find the clouds in
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}

function start() {
	var head = document.querySelector('.home-head');

	if (!head) {
		return;
	}

	var band = head.querySelector('.clouds-band');
	var hill = head.querySelector('.clouds-hill');
	var web  = head.querySelector('.device-web');
	var tool = head.querySelector('.device-tool');

	if (!band || !hill) {
		return;
	}

	// Somebody who has asked their system to reduce motion means it
	var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');

	if (still && still.matches) {
		return;
	}

	var waiting = false;

	function place() {
		waiting = false;

		var y = window.pageYOffset || document.documentElement.scrollTop || 0;

		// Once the hero has scrolled past there is nothing left to move
		if (y > head.offsetHeight) {
			return;
		}

		// The far band drifts sideways and sinks; the near ridge lifts. Two
		// directions read as distance in a way that two speeds do not.
		band.style.transform = 'translate3d(' + (y * 0.08) + 'px, ' + (y * 0.16) + 'px, 0)';
		hill.style.transform = 'translate3d(' + (y * -0.04) + 'px, ' + (y * -0.05) + 'px, 0)';

		// The two laptops come out from behind the monitor, which is what the
		// welcome template did over its first 900 pixels of scroll. They stop
		// where it stopped them, just over a tenth of the way to either side.
		if (web && tool) {
			var out = Math.min(y / 900, 1) * 12;

			web.style.transform  = 'translate3d(' + (-out) + '%, 0, 0)';
			tool.style.transform = 'translate3d(' + out + '%, 0, 0)';
		}
	}

	function onScroll() {
		if (waiting) {
			return;
		}

		waiting = true;
		window.requestAnimationFrame(place);
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });

	place();
}
}());
