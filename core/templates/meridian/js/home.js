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

	// Somebody who has asked their system to reduce motion means it. Asked
	// every frame rather than once at startup: the setting can be changed
	// while the page is open, and a page that only honours it if you reload
	// honours it by accident.
	var still = window.matchMedia
		? window.matchMedia('(prefers-reduced-motion: reduce)')
		: null;

	var waiting = false;

	function place() {
		waiting = false;

		var y = window.pageYOffset || document.documentElement.scrollTop || 0;

		// Once the hero has scrolled past there is nothing left to move
		if (y > head.offsetHeight) {
			return;
		}

		// Everything below is scaled by this, so turning motion off puts the
		// pictures back where the stylesheet had them instead of freezing
		// them wherever they had got to.
		var k = (still && still.matches) ? 0 : 1;

		// The far band drifts sideways and sinks; the near ridge lifts. Two
		// directions read as distance in a way that two speeds do not.
		band.style.transform = 'translate3d(' + (y * 0.16 * k) + 'px, ' + (y * 0.38 * k) + 'px, 0)';
		hill.style.transform = 'translate3d(' + (y * -0.09 * k) + 'px, ' + (y * -0.14 * k) + 'px, 0)';

		// The two laptops come out from behind the monitor, the way they did
		// on the welcome template. That took 900 pixels of scroll there, on a
		// page four screens long; the hero is gone by 500, so they travel in
		// the distance there is.
		if (web && tool) {
			var out = Math.min(y / 420, 1) * 16 * k;

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

	// And settle the pictures the moment the preference changes, rather than
	// leaving them where they were until something else scrolls
	if (still) {
		if (still.addEventListener) {
			still.addEventListener('change', onScroll);
		} else if (still.addListener) {
			still.addListener(onScroll);
		}
	}

	place();
}
}());
