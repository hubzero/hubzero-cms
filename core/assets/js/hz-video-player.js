/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * HUBzero shared video player.
 *
 * Dependency-free, multi-instance wrapper around the native HTML5 <video>
 * element that adds a YouTube-like control bar, WebVTT captions, keyboard
 * access, and an optional clickable transcript. Every `.hz-video` container on
 * the page is enhanced independently, so any number can appear on one page.
 *
 * Markup contract (all controls are built by JS; author supplies only the
 * <video> and its <source>/<track> children):
 *
 *   <div class="hz-video" data-transcript="1">
 *     <video preload="metadata" playsinline>
 *       <source src="movie.mp4" type="video/mp4">
 *       <track kind="captions" src="cc.en.vtt" srclang="en" label="English">
 *     </video>
 *   </div>
 */
(function (window, document) {
	'use strict';

	var SVG = {
		play:  '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 5l12 7-12 7V5z" fill="currentColor"/></svg>',
		pause: '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="6" y="5" width="4" height="14" rx="1.5" fill="currentColor"/><rect x="14" y="5" width="4" height="14" rx="1.5" fill="currentColor"/></svg>',
		cc:    '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="2" y="5" width="20" height="14" rx="2.5" stroke="currentColor" stroke-width="1.75" fill="none"/><text x="5" y="16.5" font-family="sans-serif" font-size="9" font-weight="700" fill="currentColor">CC</text></svg>',
		full:  '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" stroke="currentColor" stroke-width="1.75" fill="none"/></svg>',
		vol:   '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 9v6h4l5 4V5L8 9H4z" fill="currentColor"/><path d="M16 8a5 5 0 010 8" stroke="currentColor" stroke-width="1.75" fill="none"/></svg>',
		mute:  '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 9v6h4l5 4V5L8 9H4z" fill="currentColor"/><path d="M16 9l5 6M21 9l-5 6" stroke="currentColor" stroke-width="1.75"/></svg>'
	};

	function fmt(t) {
		if (isNaN(t) || t === Infinity) { return '0:00'; }
		t = Math.floor(t);
		var h = Math.floor(t / 3600);
		var m = Math.floor((t % 3600) / 60);
		var s = t % 60;
		var out = (m < 10 && h ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
		return h ? h + ':' + out : out;
	}

	function el(tag, cls, html) {
		var e = document.createElement(tag);
		if (cls) { e.className = cls; }
		if (html != null) { e.innerHTML = html; }
		return e;
	}

	function btn(cls, label, html) {
		var b = el('button', 'hz-video__btn ' + cls, html);
		b.type = 'button';
		b.setAttribute('aria-label', label);
		return b;
	}

	function HZVideoPlayer(root) {
		var video = root.querySelector('video');
		if (!video || root.hzVideoReady) { return; }
		root.hzVideoReady = true;
		this.root = root;
		this.video = video;
		this.build();
		this.bind();
	}

	HZVideoPlayer.prototype.build = function () {
		var video = this.video;
		// Let us own the chrome; keep native controls as a no-JS fallback until now.
		video.removeAttribute('controls');
		if (!video.hasAttribute('playsinline')) { video.setAttribute('playsinline', ''); }

		// Big center play button
		this.big = el('button', 'hz-video__bigplay', SVG.play);
		this.big.type = 'button';
		this.big.setAttribute('aria-label', 'Play video');

		// Control bar
		var bar = el('div', 'hz-video__controls');

		this.seek = el('div', 'hz-video__progress');
		this.seek.setAttribute('role', 'slider');
		this.seek.setAttribute('aria-label', 'Seek');
		this.seek.setAttribute('tabindex', '0');
		this.seekFill = el('div', 'hz-video__progress-filled');
		this.seek.appendChild(this.seekFill);

		this.playBtn = btn('hz-video__play', 'Play', SVG.play);
		this.time = el('span', 'hz-video__time', '0:00 / 0:00');

		this.muteBtn = btn('hz-video__mute', 'Mute', SVG.vol);
		this.vol = el('input', 'hz-video__volume');
		this.vol.type = 'range';
		this.vol.min = 0; this.vol.max = 1; this.vol.step = 0.05; this.vol.value = 1;
		this.vol.setAttribute('aria-label', 'Volume');

		var spacer = el('span', 'hz-video__spacer');

		this.ccBtn = null;
		if (this.video.textTracks && this.video.textTracks.length) {
			this.ccBtn = btn('hz-video__cc', 'Toggle captions', SVG.cc);
			this.ccBtn.setAttribute('aria-pressed', 'false');
		}

		this.fsBtn = btn('hz-video__full', 'Full screen', SVG.full);

		var left = el('div', 'hz-video__group');
		left.appendChild(this.playBtn);
		left.appendChild(this.muteBtn);
		left.appendChild(this.vol);
		left.appendChild(this.time);

		var right = el('div', 'hz-video__group');
		if (this.ccBtn) { right.appendChild(this.ccBtn); }
		right.appendChild(this.fsBtn);

		var row = el('div', 'hz-video__row');
		row.appendChild(left);
		row.appendChild(spacer);
		row.appendChild(right);

		bar.appendChild(this.seek);
		bar.appendChild(row);

		this.root.appendChild(this.big);
		this.root.appendChild(bar);
		this.root.classList.add('hz-video--enhanced', 'hz-video--paused');

		// Hide native track rendering; we only use it to read cues (native CC UI varies).
		this.captionsOn = false;
		for (var i = 0; i < this.video.textTracks.length; i++) {
			this.video.textTracks[i].mode = 'hidden';
		}

		if (this.root.getAttribute('data-transcript') === '1') {
			this.buildTranscript();
		}
	};

	HZVideoPlayer.prototype.buildTranscript = function () {
		var tracks = this.video.textTracks;
		if (!tracks || !tracks.length) { return; }
		this.transcript = el('div', 'hz-video__transcript');
		this.transcript.setAttribute('aria-label', 'Transcript');
		this.root.appendChild(this.transcript);
		var self = this;
		// Cues may not be parsed until the track loads.
		var render = function () {
			var track = tracks[0];
			if (!track.cues || !track.cues.length) { return; }
			self.transcript.innerHTML = '';
			for (var i = 0; i < track.cues.length; i++) {
				(function (cue) {
					var line = el('button', 'hz-video__cue');
					line.type = 'button';
					line.textContent = cue.text.replace(/<[^>]+>/g, '');
					line.addEventListener('click', function () {
						self.video.currentTime = cue.startTime;
						if (self.video.paused) { self.video.play(); }
					});
					cue.onenter = function () { self.markCue(line); };
					self.transcript.appendChild(line);
				})(track.cues[i]);
			}
		};
		tracks[0].mode = 'hidden';
		if (tracks[0].cues && tracks[0].cues.length) { render(); }
		else { this.video.addEventListener('loadeddata', render); setTimeout(render, 800); }
	};

	HZVideoPlayer.prototype.markCue = function (line) {
		if (!this.transcript) { return; }
		var active = this.transcript.querySelector('.is-active');
		if (active) { active.classList.remove('is-active'); }
		line.classList.add('is-active');
	};

	HZVideoPlayer.prototype.bind = function () {
		var self = this, v = this.video;

		var toggle = function () { v.paused ? v.play() : v.pause(); };
		this.playBtn.addEventListener('click', toggle);
		this.big.addEventListener('click', toggle);
		v.addEventListener('click', toggle);

		v.addEventListener('play', function () {
			self.root.classList.remove('hz-video--paused');
			self.root.classList.add('hz-video--playing');
			self.playBtn.innerHTML = SVG.pause;
			self.playBtn.setAttribute('aria-label', 'Pause');
		});
		v.addEventListener('pause', function () {
			self.root.classList.add('hz-video--paused');
			self.root.classList.remove('hz-video--playing');
			self.playBtn.innerHTML = SVG.play;
			self.playBtn.setAttribute('aria-label', 'Play');
		});

		v.addEventListener('timeupdate', function () { self.progress(); });
		v.addEventListener('loadedmetadata', function () { self.progress(); });
		v.addEventListener('ended', function () {
			self.root.classList.add('hz-video--paused');
			self.root.classList.remove('hz-video--playing');
		});

		// Seek
		var scrub = function (e) {
			var rect = self.seek.getBoundingClientRect();
			var x = ((e.touches ? e.touches[0].clientX : e.clientX) - rect.left) / rect.width;
			x = Math.max(0, Math.min(1, x));
			if (v.duration) { v.currentTime = x * v.duration; }
		};
		this.seek.addEventListener('click', scrub);
		this.seek.addEventListener('keydown', function (e) {
			if (!v.duration) { return; }
			if (e.key === 'ArrowRight') { v.currentTime = Math.min(v.duration, v.currentTime + 5); e.preventDefault(); }
			if (e.key === 'ArrowLeft')  { v.currentTime = Math.max(0, v.currentTime - 5); e.preventDefault(); }
		});

		// Volume / mute
		this.vol.addEventListener('input', function () { v.volume = parseFloat(self.vol.value); v.muted = (v.volume === 0); });
		this.muteBtn.addEventListener('click', function () { v.muted = !v.muted; });
		v.addEventListener('volumechange', function () {
			self.muteBtn.innerHTML = (v.muted || v.volume === 0) ? SVG.mute : SVG.vol;
			self.muteBtn.setAttribute('aria-label', v.muted ? 'Unmute' : 'Mute');
			self.vol.value = v.muted ? 0 : v.volume;
		});

		// Captions
		if (this.ccBtn) {
			this.ccBtn.addEventListener('click', function () { self.toggleCaptions(); });
		}

		// Fullscreen
		this.fsBtn.addEventListener('click', function () { self.toggleFullscreen(); });
		document.addEventListener('fullscreenchange', function () {
			self.root.classList.toggle('hz-video--fullscreen', document.fullscreenElement === self.root);
		});

		// Keyboard on the player root
		this.root.setAttribute('tabindex', this.root.getAttribute('tabindex') || '0');
		this.root.addEventListener('keydown', function (e) {
			if (e.target.tagName === 'INPUT') { return; }
			if (e.key === ' ' || e.key === 'k') { toggle(); e.preventDefault(); }
			else if (e.key === 'f') { self.toggleFullscreen(); }
			else if (e.key === 'm') { v.muted = !v.muted; }
			else if (e.key === 'c' && self.ccBtn) { self.toggleCaptions(); }
		});
	};

	HZVideoPlayer.prototype.progress = function () {
		var v = this.video;
		var pct = v.duration ? (v.currentTime / v.duration) * 100 : 0;
		this.seekFill.style.width = pct + '%';
		this.seek.setAttribute('aria-valuenow', Math.floor(v.currentTime));
		this.seek.setAttribute('aria-valuetext', fmt(v.currentTime) + ' of ' + fmt(v.duration));
		this.time.textContent = fmt(v.currentTime) + ' / ' + fmt(v.duration);
	};

	HZVideoPlayer.prototype.toggleCaptions = function () {
		var tracks = this.video.textTracks;
		if (!tracks || !tracks.length) { return; }
		this.captionsOn = !this.captionsOn;
		tracks[0].mode = this.captionsOn ? 'showing' : 'hidden';
		this.ccBtn.setAttribute('aria-pressed', this.captionsOn ? 'true' : 'false');
		this.ccBtn.classList.toggle('is-active', this.captionsOn);
	};

	HZVideoPlayer.prototype.toggleFullscreen = function () {
		if (document.fullscreenElement === this.root) {
			if (document.exitFullscreen) { document.exitFullscreen(); }
		} else if (this.root.requestFullscreen) {
			this.root.requestFullscreen();
		} else if (this.video.webkitEnterFullscreen) {
			this.video.webkitEnterFullscreen(); // iOS Safari
		}
	};

	function initAll(ctx) {
		var nodes = (ctx || document).querySelectorAll('.hz-video');
		for (var i = 0; i < nodes.length; i++) { new HZVideoPlayer(nodes[i]); }
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { initAll(); });
	} else {
		initAll();
	}

	// Expose for dynamically-inserted players
	window.HZVideoPlayer = HZVideoPlayer;
	window.HZVideoPlayer.initAll = initAll;

})(window, document);
