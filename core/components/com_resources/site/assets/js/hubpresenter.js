/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!HUB) {
    var HUB = {};
}

HUB.Presenter = (() => {
    // Private state
    const state = {
        activeSlide: '0',
        tolerance: 0.3,
        seeking: false,
        mouseover: false,
        track: null,
        subtitles: null,
        transcriptLineActive: 0,
        transcriptBoxScrolling: false,
        _transcriptScrollTimer: null,
        canSendTracking: true,
        sendingTracking: false,
        detailedTrackingId: null,
        doneLoading: false,
        suppressAutoplay: false,
        audio: false,
        current: 0,
        duration: 0
    };

    // Utility functions
    const utils = {
        formatTime(seconds) {
            const times = [3600, 60, 1];
            let time = '';

            for (let i = 0; i < times.length; i++) {
                let tmp = Math.floor(seconds / times[i]);
                tmp = tmp < 1 ? '00' : tmp < 10 ? `0${tmp}` : tmp;
                time += tmp + (i < 2 ? ':' : '');
                seconds = seconds % times[i];
            }
            return time;
        },

        strip(content) {
            return content.replace(/^\s+|\s+$/g, '');
        },

        toSeconds(time) {
            if (!time) return 0.0;

            const parts = time.split(':');
            return parts.reduce((seconds, part) =>
                seconds * 60 + parseFloat(part.replace(',', '.'))
                , 0);
        },

        ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        isMobile() {
            return /iPad|iPhone|iPod|Android/i.test(navigator.userAgent);
        }
    };

    // Player methods
    const player = {
        get() {
            return $('#player').get(0);
        },

        isPaused() {
            return this.get().paused;
        },

        getCurrent() {
            state.current = this.get().currentTime;
            return state.current;
        },

        getDuration() {
            state.duration = this.get().duration;
            return state.duration;
        },

        seek(time) {
            this.get().currentTime = time;
            slides.sync();
        },

        setVolume(level) {
            this.get().volume = level;
        },

        getVolume() {
            return this.get().volume;
        },

        syncVolume() {
            const volume = this.getVolume();
            volumeBar.updateIcon(volume * 100);
            $('#volume-bar').slider('option', 'value', volume);
        }
    };

    // Slide management
    const slides = {
        show(slide) {
            state.activeSlide = slide;
            $('#slides ul li').hide();
            $(`#slide_${slide}`).show();

            const slideChild = $(`#slide_${slide}`).children().first();
            const slideChildType = slideChild.get(0)?.tagName;

            if (slideChildType === 'VIDEO') {
                const videoSlide = slideChild.get(0);
                videoSlide.currentTime = 0;
                videoSlide.play();
            }

            const listItem = $(`#list_${slide}`).length
                ? `#list_${slide}`
                : this.getListItem(slide, 'backward');

            if (listItem) {
                $('#list_items .time').show();
                $(`${listItem} .time`).hide();

                slideList.updateProgressBar(listItem.substr(6));

                if (!state.mouseover) {
                    $('#list_items').stop().scrollTo(listItem, 1000, 'easeInOutQuad');
                }
            }

            $('#list_items li').removeClass('active');
            $(listItem).addClass('active');
        },

        sync() {
            const current = player.getCurrent();
            const duration = player.getDuration();

            progressBar.setProgress(current);
            controls.syncPlayPause();
            slideList.updateProgress(state.activeSlide);

            const curSlide = this.findCurrentSlide(current);
            const nextSlide = this.findNextSlide(curSlide);

            if (curSlide.time <= current && nextSlide.time > current) {
                if (state.activeSlide !== curSlide.id) {
                    this.show(curSlide.id);
                }
            }

            this.syncSlideVideo(curSlide, current);
        },

        findCurrentSlide(current) {
            let curSlide = {};
            $('#slides ul li').each((index, element) => {
                const slide = $(element);
                const time = parseFloat(slide.attr('time'));
                const id = slide.attr('id').substr(6, 7);

                if (current >= time) {
                    curSlide = {
                        id,
                        time,
                        type: slide.children().first().get(0).tagName
                    };
                }
            });
            return curSlide;
        },

        findNextSlide(curSlide) {
            const nextId = parseInt(curSlide.id) + 1;
            const nextSlideEl = $(`#slide_${nextId}`);

            if (nextSlideEl.length) {
                return {
                    id: nextId,
                    time: parseFloat(nextSlideEl.attr('time')),
                    type: nextSlideEl.children().first().get(0).tagName
                };
            }
            return { time: 99999999, type: 'IMG' };
        },

        syncSlideVideo(curSlide, current) {
            if (curSlide.type !== 'VIDEO') return;

            const videoSlide = $(`#slide_${curSlide.id}`).find('.slidevideo').get(0);
            const shouldBeAtTime = current - curSlide.time;
            const timeDifference = videoSlide.currentTime - shouldBeAtTime;

            if (Math.abs(timeDifference) > 0.5) {
                videoSlide.pause();
                videoSlide.currentTime = shouldBeAtTime;
                if (!player.isPaused()) {
                    videoSlide.play();
                }
            }
        },

        getListItem(slide, direction) {
            const newSlide = direction === 'forward' ? slide + 1 : slide - 1;
            const total = $('#list_items li').length;
            if (newSlide < 0 || newSlide >= total) {
                return null;
            }
            const item = `#list_${newSlide}`;
            return $(item).length ? item : this.getListItem(newSlide, direction);
        },

        next() {
            const next = parseInt(state.activeSlide) + 1;
            if ($(`#slide_${next}`).length) {
                const time = parseFloat($(`#slide_${next}`).attr('time')) + state.tolerance;
                player.seek(time);
            }
        },

        previous() {
            let previous = parseInt(state.activeSlide) - 1;
            if (previous >= 0) {
                if (!$(`#list_${previous}`).length) {
                    const listItem = this.getListItem(previous, 'backward');
                    if (!listItem) { return; }
                    previous = parseInt(listItem.substr(6));
                }
                const time = parseFloat($(`#slide_${previous}`).attr('time')) + state.tolerance;
                player.seek(time);
            }
        }
    };

    // Slide list management
    const slideList = {
        init() {
            $('#list').show();

            $('#list_items li .time').each(function () {
                this.innerHTML = utils.formatTime(this.innerHTML);
            });

            const height = $('#slides').height() - $('#media').height() - 1;
            $('#list_items').height(height);

            $('#list_items li').on('click', function (e) {
                e.preventDefault();
                const id = $(this).attr('id').substr(5, 6);
                const time = parseFloat($(`#slide_${id}`).attr('time')) + state.tolerance;
                player.seek(time);
            });

            $('#list_items')
                .on('mouseenter', () => state.mouseover = true)
                .on('mouseleave', () => state.mouseover = false);
        },

        updateProgressBar(listItem) {
            const next = $(`#list_${listItem}`).next().attr('id');
            const min = parseFloat($(`#slide_${listItem}`).attr('time'));
            const max = !$(`#list_${listItem}`).next().length
                ? player.getDuration()
                : parseFloat($(`#slide_${next.substr(5)}`).attr('time'));

            $('.list-slider, .list-progress').hide();
            $(`#list_${listItem} .list-slider, #list_${listItem} .list-progress`).show();

            $(`#list-slider-${listItem}`).slider({
                step: 0.1,
                range: 'min',
                slide: (e, ui) => {
                    state.seeking = true;
                    const pos = ((max - min) * ui.value) / 100;
                    const textTime = utils.formatTime(pos).substr(3) + '/' +
                        utils.formatTime(max - min).substr(3);
                    $('.list-progress').text(textTime);
                },
                start: () => state.seeking = true,
                stop: (e, ui) => {
                    state.seeking = false;
                    const time = (((max - min) * ui.value) / 100) + min;
                    player.seek(time);
                }
            });
        },

        updateProgress(active) {
            if (!$(`#list_${active}`).length) {
                const listItem = slides.getListItem(active, 'backward');
                if (!listItem) { return; }
                active = listItem.substr(6);
            }

            const start = parseFloat($(`#slide_${active}`).attr('time'));
            const next = $(`#list_${active}`).next().attr('id');
            const end = !$(`#list_${active}`).next().length
                ? player.getDuration()
                : parseFloat($(`#slide_${next.substr(5)}`).attr('time'));

            const slideProgress = (state.current - start) / (end - start);

            if (!state.seeking) {
                $('.list-slider').slider({ range: 'min' });
                $('.list-slider').slider('value', slideProgress * 100);
                const time = utils.formatTime(state.current - start).substr(3) + '/' +
                    utils.formatTime(end - start).substr(3);
                $('.list-progress').text(time);
            }
        }
    };

    // Control bar
    const controls = {
        init() {
            // Toggle popups on click; close when clicking outside
            $(document).on('click', '.control', function (e) {
                const btn = $(this);
                // Buttons with no popup (no .control-container child) do nothing here
                if (!btn.find('.control-container').length) return;
                e.stopPropagation();
                const isOpen = btn.hasClass('open');
                // Close all other open popups
                $('.control.open').not(btn).removeClass('open').attr('aria-expanded', 'false');
                // Toggle this one
                if (isOpen) {
                    btn.removeClass('open').attr('aria-expanded', 'false');
                } else {
                    btn.addClass('open').attr('aria-expanded', 'true');
                }
            });
            // Prevent clicks inside popup from closing it
            $(document).on('click', '.control-container', function (e) {
                e.stopPropagation();
            });
            // Close all popups when clicking outside
            $(document).on('click', function () {
                $('.control.open').removeClass('open').attr('aria-expanded', 'false');
            });
            // Close all popups on Escape key
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $('.control.open').removeClass('open').attr('aria-expanded', 'false');
                }
            });
            // Close all popups when the toolbar hides (mouse leaves presenter while playing)
            $('#presenter-content').on('mouseleave', function () {
                if (!$(this).hasClass('paused')) {
                    $('.control.open').removeClass('open').attr('aria-expanded', 'false');
                }
            });

            $('#play-pause').on('click', (e) => {
                e.preventDefault();
                this.playPause(true);
            });

            $('#next').on('click', (e) => {
                e.preventDefault();
                slides.next();
            });

            $('#previous').on('click', (e) => {
                e.preventDefault();
                slides.previous();
            });

            if (state.audio) {
                $('#switch').hide();
            }

            $('#switch').on('click', (e) => {
                e.preventDefault();
                this.switchVideo();
            });

            $('#link').on('click', () => {
                this.linkVideo();
            });

            $('#speed').on('change', function () {
                player.get().playbackRate = $(this).val();
            });

            // Theme handling without localStorage
            $('#theme').on('change', function () {
                $('#control-box').attr('data-theme', $(this).val());
            });

            progressBar.init();
            volumeBar.init();
            fullscreen.init();
        },

        playPause(clicking) {
            const paused = player.isPaused();

            if (paused) {
                $('#play-pause').removeClass('playing').addClass('paused')
                    .attr('aria-label', 'Play presentation').attr('aria-pressed', 'false');
                $('#play-pause .icon-pause').hide();
                $('#play-pause .icon-play').show();
                $('#presenter-content').addClass('paused');
                if (clicking) player.get().play();
            } else {
                $('#play-pause').removeClass('paused').addClass('playing')
                    .attr('aria-label', 'Pause presentation').attr('aria-pressed', 'true');
                $('#play-pause .icon-play').hide();
                $('#play-pause .icon-pause').show();
                $('#presenter-content').removeClass('paused');
                if (clicking) player.get().pause();
            }

            if (clicking) {
                $('.slidevideo').each((index, element) => {
                    paused ? element.play() : element.pause();
                });
            }
        },

        syncPlayPause() {
            this.playPause(false);
        },

        switchVideo() {
            const paused = player.isPaused();

            if ($('#presenter-left #media').length) {
                $('#media').prependTo('#presenter-right');
                $('#slides').prependTo('#presenter-left')
                    .css({ 'padding-left': '50px', 'padding-right': '55px' });
            } else {
                $('#media').prependTo('#presenter-left');
                $('#slides').prependTo('#presenter-right');
                $('#presenter-left').css('width', '745px');
                $('#presenter-right').css('width', '320px');
                $('#slides').css('padding', '0px');
            }

            $('#slides ul').css('margin-top', 0);

            const totalWidth = $('#presenter-left').width() + $('#presenter-right').width();
            $('#presenter-content').width(totalWidth);

            if (!paused) player.get().play();
        },

        linkVideo() {
            const url = this.getTimestampUrl();
            $('#timestamp-link')
                .val(url)
                .off('click')
                .on('click', function () {
                    $(this).select();
                });
        },

        getTimestampUrl() {
            let url = window.location.href;
            const time = utils.formatTime(player.getCurrent());
            const timeHash = url.indexOf('?') === -1 ? `?time=${time}` : `&time=${time}`;

            url = url.replace(/%3A/g, ':').replace(/&time=\d{2}:\d{2}:\d{2}/, '');
            return url + timeHash;
        }
    };

    // Progress bar
    const progressBar = {
        init() {
            $('#progress-bar').slider({
                step: 0.1,
                range: 'min',
                slide: (e, ui) => {
                    state.seeking = true;
                    this.setProgress(player.getDuration() * (ui.value / 100));
                },
                start: () => state.seeking = true,
                stop: (e, ui) => {
                    state.seeking = false;
                    player.seek(player.getDuration() * (ui.value / 100));
                }
            });
        },

        setProgress(time) {
            const duration = player.getDuration();
            const progress = `${utils.formatTime(time)} / ${utils.formatTime(duration)}`;
            $('#media-progress').html(progress);
        }
    };

    // Volume bar
    const volumeBar = {
        init() {
            $('#volume-bar').slider({
                step: 0.1,
                min: 0,
                max: 1,
                orientation: 'horizontal',
                slide: (e, ui) => {
                    this.updateIcon(ui.value * 100);
                    player.setVolume(ui.value);
                }
            });
            player.syncVolume();
        },

        updateIcon(volume) {
            $('#volume .icon-vol-high, #volume .icon-vol-medium, #volume .icon-vol-low, #volume .icon-vol-mute').hide();
            if (volume === 0) $('#volume .icon-vol-mute').show();
            else if (volume <= 33) $('#volume .icon-vol-low').show();
            else if (volume <= 66) $('#volume .icon-vol-medium').show();
            else $('#volume .icon-vol-high').show();
        }
    };

    // Media tracking
    const tracking = {
        start() {
            this.sendEvent('start');
            setInterval(() => state.canSendTracking = true, 5000);
        },

        update() {
            this.sendEvent('update');
        },

        end() {
            state.canSendTracking = true;
            state.sendingTracking = false;
            this.sendEvent('ended');
        },

        replay() {
            this.sendEvent('replay');
        },

        sendEvent(eventType) {
            if (!state.canSendTracking || state.sendingTracking) return;

            state.sendingTracking = true;

            const resourceId = $(player.get()).attr('data-mediaid');
            const playerTime = player.getCurrent();
            const playerDuration = player.getDuration();

            const componentMatch = window.location.href.match(/\.(org|edu)\/([a-z]+)\//);
            const component = componentMatch?.[2] || 'resources';
            const url = `/index.php?option=com_${component}&controller=media&task=tracking&no_html=1`;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url,
                data: {
                    event: eventType,
                    resourceid: resourceId,
                    time: playerTime,
                    duration: playerDuration,
                    detailedTrackingId: state.detailedTrackingId
                },
                success: (data) => {
                    state.detailedTrackingId = data.detailedId;
                },
                complete: () => {
                    state.canSendTracking = false;
                    state.sendingTracking = false;
                }
            });
        }
    };

    // Fullscreen
    const fullscreen = {
        get isFullscreen() {
            return !!(document.fullscreenElement || document.webkitFullscreenElement);
        },

        enter() {
            const el = document.getElementById('presenter-container');
            if (el.requestFullscreen) el.requestFullscreen();
            else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        },

        exit() {
            if (document.exitFullscreen) document.exitFullscreen();
            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
        },

        toggle() {
            this.isFullscreen ? this.exit() : this.enter();
        },

        syncButton() {
            const btn = $('#fullscreen');
            if (this.isFullscreen) {
                btn.addClass('is-fullscreen').attr('aria-label', 'Exit fullscreen').attr('title', 'Exit fullscreen');
                btn.find('.icon-enter-fs').hide();
                btn.find('.icon-exit-fs').show();
            } else {
                btn.removeClass('is-fullscreen').attr('aria-label', 'Enter fullscreen').attr('title', 'Fullscreen');
                btn.find('.icon-exit-fs').hide();
                btn.find('.icon-enter-fs').show();
            }
        },

        init() {
            $('#fullscreen').on('click', (e) => {
                e.preventDefault();
                this.toggle();
                // Remove focus so outline doesn't persist after fullscreen toggle
                e.currentTarget.blur();
            });

            $(document).on('fullscreenchange webkitfullscreenchange', () => {
                this.syncButton();
                // Resize slides to fill available space when entering/exiting
                slides.sync();
            });

            // Keyboard shortcut: F key
            $(document).on('keydown', (e) => {
                if (e.key === 'f' || e.key === 'F') {
                    // Don't trigger when typing in an input
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;
                    this.toggle();
                }
                if (e.key === 'Escape' && this.isFullscreen) {
                    this.exit();
                }
            });
        }
    };

    // Initialization
    const init = {
        loading() {
            $('body').addClass('presenter');
            $('<div id="overlayer"></div>').appendTo(document.body);

            if (!document.createElement('video').canPlayType) {
                this.updateBrowser();
                return;
            }

            state.audio = $('#player').get(0).tagName === 'AUDIO';
            this.start();
        },

        start() {
            this.jsEnabled();
            slides.show(state.activeSlide);
            slideList.init();
            this.mobile();
            this.playerSetup();
            controls.init();
            player.setVolume(0.75);
            this.navBar();

            if ($('#media').hasClass('move-left')) {
                controls.switchVideo();
            }

            subtitles.init();
            this.popout();
        },

        playerSetup() {
            tracking.start();

            $('#player').on({
                timeupdate: () => {
                    if (!state.seeking) {
                        slides.sync();
                        tracking.update();
                    }
                },
                volumechange: () => player.syncVolume(),
                canplay: () => {
                    this.locationHash();
                    this.doneLoading();
                },
                seeked: () => {
                    state.seeking = true;
                    setTimeout(() => state.seeking = false, 1000);
                },
                ended: () => {
                    tracking.end();
                    this.replay();
                }
            });
        },

        doneLoading() {
            if (state.doneLoading) return;
            state.doneLoading = true;
            $('#overlayer').remove();
            if (state.suppressAutoplay) {
                // Loaded at a specific timestamp — show paused state, don't autoplay
                player.get().pause();
                controls.playPause(false);
            } else {
                // Attempt autoplay; fall back to showing play button if browser blocks it
                const playPromise = player.get().play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        controls.playPause(false); // sync UI to playing state
                    }).catch(() => {
                        controls.playPause(false); // browser blocked autoplay; show play button
                    });
                } else {
                    controls.playPause(false);
                }
            }
            this.previews();
        },

        jsEnabled() {
            $('#slides ul').removeClass('no-js');
            $('#control-box').removeClass('no-controls');
            $('#shortcuts').show();
            $('#player').removeAttr('controls');
        },

        mobile() {
            if (!utils.isMobile()) return;

            if (!state.audio) {
                $('#player').attr('controls', 'controls');
            }
            $('#switch, #shortcuts, #volume-bar, #volume-icon').hide();
            $('.slidevideo').hide();
            $('.imagereplacement').show();

            if (state.audio) {
                $('#media').css('position', 'relative')
                    .prepend('<a id="mobile-play-audio" href="#">Click to Play</a>');
                $('#mobile-play-audio').on('click', function (e) {
                    e.preventDefault();
                    $(this).fadeOut('slow');
                    player.get().play();
                });
            }
        },

        navBar() {
            $('#presentation').on('change', function () {
                if (this.value) $('#presentation-picker').submit();
            });
        },

        updateBrowser() {
            $('#overlayer').remove();
            $('#presenter-container').css('position', 'relative');
            const html = `
        <div id="no-flash">
          <h2>Please upgrade your browser</h2>
          <p>This browser does not support video playback.</p>
          <p>Please consider upgrading your browser to the latest version or getting a new modern browser: 
            <a href='https://www.mozilla.org/en-US/firefox/' target='_blank' rel='noopener noreferrer'>Firefox</a>, 
            <a href='https://www.google.com/chrome/' target='_blank' rel='noopener noreferrer'>Chrome</a>
          </p>
        </div>`;
            $(html).hide().appendTo('#presenter-container').fadeIn('slow');
        },

        locationHash() {
            if (state.doneLoading) return;

            const urlQuery = window.location.search
                .replace('?', '')
                .replace(/&amp;/g, '&');

            const params = urlQuery.split('&');
            const timeParam = params.find(p => p.startsWith('time'));

            if (timeParam) {
                const timeParts = timeParam.split('=')[1]
                    .replace(/%3A/g, ':')
                    .split(':');
                const time = parseInt(timeParts[0]) * 3600 +
                    parseInt(timeParts[1]) * 60 +
                    parseInt(timeParts[2]);

                this.resume(utils.formatTime(time));
                player.seek(time);
                progressBar.setProgress(time);
                // Suppress autoplay when loading at a specific timestamp
                state.suppressAutoplay = true;
            }
        },

        resume(time) {
            const queryString = window.location.search;
            if (queryString.match(/auto-resume=true/g)) {
                setTimeout(() => controls.playPause(true), 250);
                return;
            }

            if ($('#presenter-container #resume').length) return;

            $('#presenter-container').css('position', 'relative');
            const html = `
        <div id="resume">
          <div id="resume-details">
            <h2>Resume Playback?</h2>
            <p>Would you like to resume video playback where you left off last time?</p>
            <div id="time">${time}</div>
          </div>
          <a class="btn icon-restart" id="restart-video" href="#">Play from the Beginning</a>
          <a class="btn btn-info icon-play" id="resume-video" href="#">Resume Video</a>
        </div>`;

            $(html).hide().appendTo('#presenter-container').fadeIn('slow');

            const totalWidth = $('#presenter-left').width() + $('#presenter-right').width();
            $('#resume').width(totalWidth);

            $('#restart-video').on('click', (e) => {
                e.preventDefault();
                this.doReplay('#resume');
            });

            $('#resume-video').on('click', (e) => {
                e.preventDefault();
                this.doResume();
            });
        },

        doResume() {
            $('#resume').fadeOut('slow', function () {
                $(this).remove();
                $('#presenter-container').css('position', 'static');
                player.get().play();
            });
        },

        replay() {
            $('#presenter-container').css('position', 'relative');
            const html = `
        <div id="replay">
          <div id="replay-details">
            <div id="title"></div>
            <div id="link">
              <span>Share:</span><input type="text" id="replay-link" value="${window.location}" />
              <a target='_blank' href="http://www.facebook.com/share.php?u=${window.location}" id="facebook" title="Share on Facebook">Facebook</a>
              <a target='_blank' href="http://twitter.com/intent/tweet?text=Currently Watching: ${window.location}" id="twitter" title="Share on Twitter">Twitter</a>
            </div>
          </div>
          <a class="btn icon-close" id="replay-back" href="#">Close Presentation</a>
          <a class="btn btn-info icon-replay" id="replay-now" href="#">Replay Presentation</a>
        </div>`;

            $(html).hide().appendTo('#presenter-container').fadeIn('slow');

            if (!window.opener) {
                $('#presenter-container #replay-back').remove();
            }

            $('#replay-details #title').html(`<span>Title:</span> ${$('#presenter-header #title').html()}`);

            $('#replay-link').on('click', function (e) {
                e.preventDefault();
                this.select();
            });

            $('#replay-now').on('click', (e) => {
                e.preventDefault();
                this.doReplay('#replay');
            });

            $('#replay-back').on('click', (e) => {
                e.preventDefault();
                window.close();
            });
        },

        doReplay(element) {
            $(element).fadeOut('slow', function () {
                $(this).remove();
                $('#presenter-container').css('position', 'static');
                player.seek(0);
                state.canSendTracking = true;
                state.sendingTracking = false;
                tracking.replay();
                player.get().play();
            });
        },

        popout() {
            if (!parent.HUB?.Resources) return;

            $('.embed-popout').css('display', 'inline-block').on('click', () => {
                const url = controls.getTimestampUrl();
                window.open(url + '&auto-resume=true&tmpl=component', 'name', 'height=800,width=1100');
                player.get().pause();
            });
        },

        previews() {
            const p = player.get();

            if (!$('#control-box .preview').length) {
                $('#control-box').append(`<div class="preview"><video src="${p.currentSrc}"></video><div class="tip"></div></div>`);
            }

            const scale = $('#progress-bar').width() / player.getDuration();

            $('#progress-bar')
                .on('mousemove', function (e) {
                    const origPos = e.pageX - $(this).offset().left;
                    let pos = origPos;
                    const min = $('.preview').outerWidth() / 2;
                    const max = $(this).width() - min;
                    let tipPos = pos;

                    $('.preview video').get(0).currentTime = pos / scale;

                    if (pos < min) pos = min;
                    else if (pos > max) pos = max;

                    if (origPos > 0 && origPos < min) {
                        tipPos = origPos - 6;
                    } else if (origPos > max && origPos < $(this).width()) {
                        tipPos = origPos - 6 - pos + min;
                    } else {
                        tipPos = min - 6;
                    }

                    $('.preview').css('left', pos);
                    $('.preview .tip').css('left', tipPos);
                })
                .on('hover', () => $('.preview').toggleClass('visible'));
        }
    };

    // Subtitles (simplified structure)
    const subtitles = {
        init() {
            const subTitles = this.getSubtitles();
            if (subTitles.length === 0) return;

            this.setupPicker(subTitles);
            transcript.setup(subTitles);

            setInterval(() => this.sync(subTitles), 300);
        },

        getSubtitles() {
            const subs = [];
            $('div[data-type=subtitle]').each(function () {
                const lang = $(this).attr('data-lang');
                const src = $(this).attr('data-src');
                const auto = $(this).attr('data-autoplay');

                $.ajax({
                    url: src,
                    async: false,
                    dataType: 'html',
                    success: (content) => {
                        const parsed = subtitles.parse(content);
                        subs.push({ lang, subs: parsed, auto });
                    }
                });
            });
            return subs;
        },

        parse(content) {
            content = content.replace(/\r\n|\r|\n/g, '\n');
            content = utils.strip(content);

            const srt = content.split('\n\n');
            const subtitles = [];

            srt.forEach(item => {
                const parts = item.split('\n');
                const times = parts[1].split(' --> ');

                const start = utils.toSeconds(utils.strip(times[0]));
                const end = utils.toSeconds(utils.strip(times[1]));
                const text = parts.slice(2).join('\n').replace('>>', '');

                subtitles.push({ start, end, text });
            });

            return subtitles;
        },

        setupPicker(subTitles) {
            let auto = false;

            $('#subtitle').css('display', 'inline-flex');

            subTitles.forEach(sub => {
                const subLang = sub.lang.toLowerCase();
                const sel = parseInt(sub.auto) ? 'selected="selected"' : '';

                if (parseInt(sub.auto)) {
                    auto = true;
                    state.track = subLang;
                }

                $('#video-subtitles').append(`<div id="${subLang}" role="status"></div>`);
                $('#subtitle-selector').append(`<option ${sel} value="${subLang}">${sub.lang}</option>`);
            });

            if (auto) {
                $('#subtitle').addClass('on').attr('aria-pressed', 'true');
            }

            $('#subtitle-selector').on('change', function () {
                state.track = $(this).val();
                const isOn = state.track !== '';
                $('#subtitle').toggleClass('on', isOn).attr('aria-pressed', String(isOn));
            });

            // Options toggle
            $('.options-toggle').on('click', function () {
                const $settings = $('.subtitle-settings');
                const isVisible = $settings.is(':visible');
                $settings.toggle();
                $(this).text(isVisible ? 'Options' : 'Hide Options')
                       .attr('aria-expanded', String(!isVisible));
            });

            // Subtitle settings — font, size, color
            $('#font-selector').on('change', function () {
                $('#video-subtitles div').css('font-family', $(this).val());
                $('.subtitle-settings-preview .test').css('font-family', $(this).val());
            });

            $('#font-size-selector').on('change', function () {
                $('#video-subtitles div').css('font-size', $(this).val() + 'px');
                $('.subtitle-settings-preview .test').css('font-size', $(this).val() + 'px');
            });

            if ($.fn.colpick) {
                $('#font-color').colpick({
                    layout: 'hex',
                    submit: 0,
                    colorScheme: 'dark',
                    onChange(_hsb, hex) {
                        $('#font-color').css('background-color', '#' + hex);
                        $('#video-subtitles div').css('color', '#' + hex);
                        $('.subtitle-settings-preview .test').css('color', '#' + hex);
                    }
                });

                $('#background-color').colpick({
                    layout: 'hex',
                    submit: 0,
                    colorScheme: 'dark',
                    onChange(_hsb, hex) {
                        $('#background-color').css('background-color', '#' + hex);
                        $('#video-subtitles div').css('background-color', '#' + hex);
                        $('.subtitle-settings-preview .test').css('background-color', '#' + hex);
                    }
                });
            }
        },

        sync(subTitles) {
            const current = player.getCurrent();
            const subs = subTitles.find(s => s.lang.toLowerCase() === state.track)?.subs;

            $('#video-subtitles div').removeClass('showing').hide().html('');

            if (subs) {
                subs.forEach(sub => {
                    if (current >= sub.start && current <= sub.end) {
                        $(`#video-subtitles #${state.track}`)
                            .addClass('showing')
                            .show()
                            .html(sub.text.replace('\n', '<br />'));
                    }
                });
            }
        }
    };

    // Transcript (simplified)
    const transcript = {
        setup(subTitles) {
            if (!$('#transcript-container').length) return;

            state.subtitles = subTitles;

            subTitles.forEach(({ lang, subs }) => {
                const language = lang.toLowerCase();
                $('.transcript-selector').append(`<option value="${language}">${lang}</option>`);
                $('#transcript-container #transcripts').append(`<div class="transcript transcript-${language}"></div>`);

                subs.forEach(sub => {
                    const line = `
            <div class="transcript-line" data-time="${sub.start}" role="button" tabindex="0" aria-label="Jump to ${utils.formatTime(sub.start)}: ${sub.text.replace(/<[^>]*>/g, '')}">
              <span class="transcript-line-time" aria-hidden="true">${utils.formatTime(sub.start)}</span>
              <span class="transcript-line-text">${sub.text}</span>
            </div>`;
                    $(`.transcript-${language}`).append(line);
                });
            });

            $('#transcripts .transcript').first().show();

            this.setupToggle();
            this.setupFontChanger();
            this.setupSearch();
            this.setupJumpTo();
            this.setupScrollSuppression();

            // Auto-show transcript if any subtitle has autoplay set
            const autoSub = subTitles.find(s => parseInt(s.auto));
            if (autoSub) {
                const lang = autoSub.lang.toLowerCase();
                $('.transcript-selector').val(lang).trigger('change');
            } else if (subTitles.length > 0) {
                // No explicit auto, show first available transcript
                const lang = subTitles[0].lang.toLowerCase();
                $('.transcript-selector').val(lang).trigger('change');
            }

            setInterval(() => this.sync(), 300);
        },

        setupToggle() {
            $('.transcript-selector').on('change', function () {
                const language = $(this).val();

                if (language) {
                    $('#transcript-container')
                        .attr('aria-hidden', 'false')
                        .slideDown(() => {
                            if (parent.HUB?.Resources) {
                                parent.HUB.Resources.resizeInlineHubpresenter($('body').outerHeight() + 20);
                            }
                        });
                } else {
                    $('#transcript-container')
                        .attr('aria-hidden', 'true')
                        .slideUp(() => {
                            if (parent.HUB?.Resources) {
                                parent.HUB.Resources.resizeInlineHubpresenter($('body').outerHeight() + 20);
                            }
                        });
                }

                $('#transcript-select').html($('.transcript-selector option:selected').text());
                $('#transcripts .transcript').hide();
                $(`.transcript-${language}`).show();
            });
        },

        setupFontChanger() {
            $('#font-smaller').on('click', (e) => {
                e.preventDefault();
                this.changeFontSize(-2, 12);
            });

            $('#font-bigger').on('click', (e) => {
                e.preventDefault();
                this.changeFontSize(2, 32);
            });
        },

        changeFontSize(delta, limit) {
            const lines = $('.transcript-line');
            const currentSize = parseFloat(lines.css('font-size'));
            const currentLineHeight = parseFloat(lines.css('line-height'));
            const newSize = currentSize + delta;
            const newLineHeight = currentLineHeight + delta;

            const withinLimits = delta > 0 ? newSize <= limit : newSize >= limit;

            if (withinLimits) {
                lines.css({
                    'font-size': `${newSize}px`,
                    'line-height': `${newLineHeight}px`
                });

                if (newSize === limit) {
                    $(delta > 0 ? '#font-bigger' : '#font-smaller').addClass('inactive');
                }
            }

            $(delta > 0 ? '#font-smaller' : '#font-bigger').removeClass('inactive');
            this.sync();
        },

        setupSearch() {
            $('#transcript-search').on('input', function () {
                const term = $(this).val().trim();
                // Clear previous state
                $('.transcript-line-text').removeHighlight();
                $('.transcript-line').removeClass('search-hidden');
                $('#transcript-search-clear').toggle(term.length > 0);

                if (!term) {
                    $('#transcript-search-count').hide();
                    return;
                }

                // Filter lines — hide those that don't contain the term
                let count = 0;
                let $firstMatch = null;
                $('.transcript-line').each(function () {
                    const text = $(this).find('.transcript-line-text').text();
                    if (text.toUpperCase().indexOf(term.toUpperCase()) === -1) {
                        $(this).addClass('search-hidden');
                    } else {
                        count++;
                        if (!$firstMatch) $firstMatch = $(this);
                    }
                });

                // Highlight matching text within visible lines
                $('.transcript-line:not(.search-hidden) .transcript-line-text').highlight(term);

                // Show result count
                $('#transcript-search-count')
                    .text(count > 0 ? `${count} result${count !== 1 ? 's' : ''}` : 'No results')
                    .show();

                // Scroll to first match
                if ($firstMatch) {
                    const containerTop = $('#transcripts').offset().top;
                    const matchTop = $firstMatch.offset().top;
                    const scrollTarget = $('#transcripts').scrollTop() + (matchTop - containerTop) - 20;
                    $('#transcripts').animate({ scrollTop: scrollTarget }, 200);
                }
            });

            $('#transcript-search-clear').on('click', () => {
                $('#transcript-search').val('').trigger('input').trigger('focus');
            });
        },

        setupJumpTo() {
            // After clicking a line, suppress auto-scroll for 2s so the
            // transcript box doesn't fight the user's intentional scroll position.
            const suppressScroll = () => {
                state.transcriptBoxScrolling = true;
                clearTimeout(state._transcriptScrollTimer);
                state._transcriptScrollTimer = setTimeout(() => {
                    state.transcriptBoxScrolling = false;
                }, 2000);
            };

            $(document).on('click', '.transcript-line', function (e) {
                e.preventDefault();
                suppressScroll();
                player.seek($(this).data('time'));
            });
            $(document).on('keydown', '.transcript-line', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    suppressScroll();
                    player.seek($(this).data('time'));
                }
            });
        },

        setupScrollSuppression() {
            $('#transcripts').on('scroll', function () {
                state.transcriptBoxScrolling = true;
                clearTimeout($.data(this, 'scrollTimer'));
                $.data(this, 'scrollTimer', setTimeout(() => {
                    state.transcriptBoxScrolling = false;
                }, 250));
            });
        },

        sync() {
            const currentTime = player.getCurrent();
            const currentTranscript = $('.transcript-selector').val();

            const subs = state.subtitles?.find(s =>
                s.lang.toLowerCase() === currentTranscript
            )?.subs;

            if (!subs) return;

            $('.transcript-line').removeClass('active').removeAttr('aria-current');

            subs.forEach((sub, i) => {
                if (currentTime >= sub.start && currentTime <= sub.end) {
                    $('.transcript-line').eq(i).addClass('active').attr('aria-current', 'true');

                    if (!state.transcriptBoxScrolling && state.transcriptLineActive !== i) {
                        const lineHeight = $('.transcript-line').outerHeight(true);
                        const containerHeight = $('#transcripts').outerHeight(true);

                        if (lineHeight * i > containerHeight / 2) {
                            const middle = containerHeight / 2;
                            $('#transcripts').scrollTo(lineHeight * i - middle + lineHeight, 300, 'easeInOutQuad');
                        }
                    }

                    state.transcriptLineActive = i;
                }
            });
        }
    };

    // Public API
    return {
        // Main initialization
        loading: () => init.loading(),
        doneLoading: () => init.doneLoading(),
        init: () => init.start(),

        // Player methods
        getPlayer: () => player.get(),
        isPaused: () => player.isPaused(),
        getCurrent: () => player.getCurrent(),
        getDuration: () => player.getDuration(),
        seek: (time) => player.seek(time),
        setVolume: (level) => player.setVolume(level),
        getVolume: () => player.getVolume(),

        // Slide methods
        showSlide: (slide) => slides.show(slide),
        syncSlides: () => slides.sync(),
        nextSlide: () => slides.next(),
        previousSlide: () => slides.previous(),

        // Control methods
        playPause: (clicking) => controls.playPause(clicking),
        switchVideo: () => controls.switchVideo(),
        linkVideo: () => controls.linkVideo(),
        getTimestampUrl: () => controls.getTimestampUrl(),

        // Progress methods
        setProgress: (time) => progressBar.setProgress(time),
        progressBar: () => progressBar.init(),

        // Volume methods
        syncVolume: () => player.syncVolume(),
        volumeBar: () => volumeBar.init(),
        volumeIcon: (volume) => volumeBar.updateIcon(volume),

        // List methods
        slideList: () => slideList.init(),
        slideListProgressBar: (item) => slideList.updateProgressBar(item),
        slideListProgressUpdate: (active) => slideList.updateProgress(active),
        getListItem: (slide, direction) => slides.getListItem(slide, direction),

        // Tracking methods
        startMediaTracking: () => tracking.start(),
        updateMediaTracking: () => tracking.update(),
        endMediaTracking: () => tracking.end(),
        replayMediaTracking: () => tracking.replay(),
        mediaTrackingEvent: (type) => tracking.sendEvent(type),

        // Utility methods
        formatTime: (seconds) => utils.formatTime(seconds),
        strip: (content) => utils.strip(content),
        toSeconds: (time) => utils.toSeconds(time),
        ucfirst: (string) => utils.ucfirst(string),

        // Other methods
        player: () => init.playerSetup(),
        controlBar: () => controls.init(),
        mobile: () => init.mobile(),
        navBar: () => init.navBar(),
        jsEnabled: () => init.jsEnabled(),
        updateBrowser: () => init.updateBrowser(),
        locationHash: () => init.locationHash(),
        resume: (time) => init.resume(time),
        doResume: () => init.doResume(),
        replay: () => init.replay(),
        doReplay: (element) => init.doReplay(element),
        popout: () => init.popout(),
        previews: () => init.previews(),

        // Subtitle methods
        subtitles: () => subtitles.init(),
        getSubtitles: () => subtitles.getSubtitles(),
        parseSubtitles: (content) => subtitles.parse(content),
        setupSubtitlePicker: (subs) => subtitles.setupPicker(subs),
        syncSubtitles: (subs) => subtitles.sync(subs),

        // Transcript methods
        transcriptSetup: (subs) => transcript.setup(subs),
        transcriptToggle: () => transcript.setupToggle(),
        transcriptFontChanger: () => transcript.setupFontChanger(),
        transcriptSearch: () => transcript.setupSearch(),
        transcriptJumpTo: () => transcript.setupJumpTo(),
        transcriptSync: () => transcript.sync(),

        // Expose state properties for backward compatibility
        get activeSlide() { return state.activeSlide; },
        set activeSlide(value) { state.activeSlide = value; },
        get tolerance() { return state.tolerance; },
        set tolerance(value) { state.tolerance = value; }
    };
})();

// Initialize when document is ready
$(() => {
    if ($('#presenter-header').length) {
        HUB.Presenter.loading();
    }
});

// Mobile-specific handling
if (/iPad|iPhone|iPod|Android/i.test(navigator.userAgent)) {
    $(window).on('load', () => {
        HUB.Presenter.doneLoading();
    });
}

/*
 * jQuery Highlight Plugin v4
 * MIT license
 */
jQuery.fn.highlight = function (term) {
    function highlightNode(node, term) {
        let skip = 0;
        if (node.nodeType === 3) {
            const pos = node.data.toUpperCase().indexOf(term);
            if (pos >= 0) {
                const span = document.createElement('span');
                span.className = 'highlight';
                const middle = node.splitText(pos);
                const end = middle.splitText(term.length);
                const clone = middle.cloneNode(true);
                span.appendChild(clone);
                middle.parentNode.replaceChild(span, middle);
                skip = 1;
            }
        } else if (node.nodeType === 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
            for (let i = 0; i < node.childNodes.length; i++) {
                i += highlightNode(node.childNodes[i], term);
            }
        }
        return skip;
    }

    return this.length && term && term.length
        ? this.each(function () { highlightNode(this, term.toUpperCase()); })
        : this;
};

jQuery.fn.removeHighlight = function () {
    return this.find('span.highlight').each(function () {
        with (this.parentNode) {
            replaceChild(this.firstChild, this);
            normalize();
        }
    }).end();
};
