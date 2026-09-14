/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * The moving parts of the front page
 *
 * Two of them, and they answer to different things.
 *
 * The clouds along the foot of the hero drift as the page scrolls, which is
 * what a horizon does when you move past it.
 *
 * The laptops behind the monitor come out as the pointer approaches it and
 * tuck back as it leaves. The welcome template slid them on scroll, but a hero
 * is gone within a screen and a half: by the time there was enough scroll to
 * see the movement, the thing that moved had left. Approach is a better cue -
 * it happens while the hero is sitting still, and it rewards somebody looking
 * at the picture rather than somebody already on their way past it.
 *
 * Nothing here is content. If the script never runs, or the browser is asked
 * to keep motion still, everything sits where the stylesheet put it.
 */
(function () {
	'use strict';

	// This is loaded in the head, so there is no page yet to find any of it in
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

	var band   = head.querySelector('.clouds-band');
	var hill   = head.querySelector('.clouds-hill');
	var screen = head.querySelector('.device-screen');
	var web    = head.querySelector('.device-web');
	var tool   = head.querySelector('.device-tool');

	// Somebody who has asked their system to reduce motion means it. Asked
	// each time rather than once at startup: the setting can be changed while
	// the page is open, and a page that only honours it if you reload honours
	// it by accident.
	var still = window.matchMedia
		? window.matchMedia('(prefers-reduced-motion: reduce)')
		: null;

	function motion() {
		return (still && still.matches) ? 0 : 1;
	}

	// ---------------------------------------------------------- the clouds

	if (band && hill) {
		var waiting = false;

		var drift = function () {
			waiting = false;

			var y = window.pageYOffset || document.documentElement.scrollTop || 0;

			if (y > head.offsetHeight) {
				return;
			}

			var k = motion();

			// The far band drifts sideways and sinks; the near ridge lifts.
			// Two directions read as distance in a way that two speeds do not.
			band.style.transform = 'translate3d(' + (y * 0.16 * k) + 'px, ' + (y * 0.38 * k) + 'px, 0)';
			hill.style.transform = 'translate3d(' + (y * -0.09 * k) + 'px, ' + (y * -0.14 * k) + 'px, 0)';
		};

		var onScroll = function () {
			if (!waiting) {
				waiting = true;
				window.requestAnimationFrame(drift);
			}
		};

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll, { passive: true });

		if (still) {
			if (still.addEventListener) {
				still.addEventListener('change', onScroll);
			} else if (still.addListener) {
				still.addListener(onScroll);
			}
		}

		drift();
	}

	// --------------------------------------------------------- the laptops

	if (!screen || !web || !tool) {
		return;
	}

	// How far out they go, and the distances from the monitor between which
	// they travel. Inside NEAR they are all the way out; past FAR, away.
	var OUT  = 16;
	var NEAR = 140;
	var FAR  = 560;

	// Where a device with no pointer leaves them. Something has to be showing
	// on a touch screen, or the composition is a monitor and two slivers.
	var RESTING = (window.matchMedia && window.matchMedia('(hover: hover)').matches) ? 0 : 0.65;

	var target = RESTING;
	var at     = RESTING;
	var running = false;

	function apply() {
		var out = at * OUT * motion();

		web.style.transform  = 'translate3d(' + (-out) + '%, 0, 0)';
		tool.style.transform = 'translate3d(' + out + '%, 0, 0)';
	}

	function step() {
		// Ease towards the target rather than tracking the pointer exactly,
		// so a flick across the page reads as the laptops following rather
		// than as them twitching.
		at += (target - at) * 0.12;

		if (Math.abs(target - at) < 0.002) {
			at = target;
			running = false;
		}

		apply();

		if (running) {
			window.requestAnimationFrame(step);
		}
	}

	function aim(next) {
		target = next;

		if (!running) {
			running = true;
			window.requestAnimationFrame(step);
		}
	}

	function onPointer(e) {
		var box = screen.getBoundingClientRect();

		// Nothing to answer to while the hero is off screen
		if (box.bottom < 0 || box.top > window.innerHeight) {
			return;
		}

		var dx = e.clientX - (box.left + box.width / 2);
		var dy = e.clientY - (box.top + box.height / 2);
		var d  = Math.sqrt(dx * dx + dy * dy);

		var t = (FAR - d) / (FAR - NEAR);

		aim(t < 0 ? 0 : (t > 1 ? 1 : t));
	}

	window.addEventListener('pointermove', onPointer, { passive: true });

	// Leaving the window entirely puts them back
	document.addEventListener('pointerleave', function () {
		aim(RESTING);
	});

	apply();
}
}());
