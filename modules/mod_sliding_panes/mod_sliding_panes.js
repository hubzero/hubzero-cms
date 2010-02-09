/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/*
 * Based off of the SlidingTabs mootools plugin by Jenna “Blueberry” Fox!
 * Documentation: http://creativepony.com/journal/scripts/sliding-tabs/
 * version: 1.8
 */

var ModSlidingPanes = new Class({
	options: {
		startingSlide: false, // sets the slide to start on, either an element or an id 
		activeButtonClass: 'active', // class to add to selected button
		activationEvent: 'click', // you can set this to ‘mouseover’ or whatever you like
		wrap: true, // calls to previous() and next() should wrap around?
		slideEffect: { // options for effect used to animate the sliding, see Fx.Base in mootools docs
			duration: 400 // 0.4 of a second
		},
		animateHeight: true, // animate height of container
		rightOversized: 0 // how much of the next pane to show to the right of the current pane
	},
	current: null, // zero based current pane number, read only
	buttons: false,
	outerSlidesBox: null,
	innerSlidesBox: null,
	panes: null,
	fx: null, // this one animates the scrolling inside
	heightFx: null, // this one animates the height
	periodical: null, // container for the periodical scrolling
	
	initialize: function(container, rotate, options) {
		this.setOptions(options);

		// Create a button container
		this.headings = new Element('div', {}).addClass('panes-headings').injectInside($(container));
		
		// Create a slides button container
		this.btnCtnr = new Element('ul', {}).addClass('panes-buttons').injectInside(this.headings);
		
		// Get the slides
		this.outerSlidesBox = $(container).getFirst();
		this.innerSlidesBox = this.outerSlidesBox.getFirst();
		this.panes = this.innerSlidesBox.getChildren();
		
		// Create a button for each slide and add it to the button container
		for (var i = 0; i < this.panes.length; i++)
		{
			var btnEl = new Element('li', {});
			btnEl.innerHTML = i + 1;
			btnEl.injectInside(this.btnCtnr);
		}
		this.buttons = this.btnCtnr.getChildren();

		// Create a "previous slide" button
		this.prevBtn = new Element('p', {
			//href: '#',
			title: 'Previous Slide'
		}).addClass('pane-prev').injectTop(this.headings);
		this.prevBtn.addEvent('click', this.previous.bind(this));

		// Create a "next slide" button
		this.nextBtn = new Element('p', {
			//href: '#',
			title: 'Next Slide'
		}).addClass('pane-next').injectInside(this.headings);
		this.nextBtn.addEvent('click', this.next.bind(this));
		
		// Initiate the scroll FX
		this.fx = new Fx.Scroll(this.outerSlidesBox, this.options.slideEffect);
		this.heightFx = this.outerSlidesBox.effect('height', this.options.slideEffect);
		
		// set up button highlight
		this.current = this.options.startingSlide ? this.panes.indexOf($(this.options.startingSlide)) : 0;
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); }
		
		// add needed stylings
		this.outerSlidesBox.setStyle('overflow', 'hidden');
		this.panes.each(function(pane, index) {
			pane.setStyles({
				'float': 'left',
				'overflow': 'hidden'
			});
		}.bind(this));
		
		// stupidness to make IE work - it boggles the mind why this has any effect
		// maybe it's something to do with giving it layout?
		this.innerSlidesBox.setStyle('float', 'left');
		
		if (this.options.startingSlide) this.fx.toElement(this.options.startingSlide);
		
		// add events to the buttons
		if (this.buttons) this.buttons.each( function(button) {
			button.addEvent(this.options.activationEvent, this.buttonEventHandler.bindWithEvent(this, button));
		}.bind(this));
		
		if (this.options.animateHeight) {
			this.heightFx.set(this.panes[this.current].offsetHeight);
		}
		
		// set up all the right widths inside the panes
		this.recalcWidths();
		
		// Set up the periodical
		if (rotate) {
			// Create a "pause" button
			this.pauseBtn = new Element('p', {
				//href: '#',
				title: 'Pause'
			}).addClass('pane-pause').injectTop($(container));
			this.pauseBtn.addEvent('click', function(){
				$clear(this.periodical);
			}.bind(this));

			// Create a "play" button
			this.pauseBtn = new Element('p', {
				//href: '#',
				title: 'Play'
			}).addClass('pane-play').injectTop($(container));
			this.pauseBtn.addEvent('click', function(){
				this.periodical = this.next.periodical(7500, this);
			}.bind(this));
			
			this.periodical = this.next.periodical(7500, this);
		}
	},
	
	// to change to a specific tab, call this, argument is the pane element you want to switch to.
	changeTo: function(element, animate) {
		if ($type(element) == 'number') element = this.panes[element - 1];
		if (!$defined(animate)) animate = true;
		var event = { cancel: false, target: $(element), animateChange: animate };
		this.fireEvent('change', event);
		if (event.cancel == true) { return; };
		
		if (this.buttons) { this.buttons[this.current].removeClass(this.options.activeButtonClass); };
		this.current = this.panes.indexOf($(event.target));
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); };
		
		this.fx.stop();
		if (event.animateChange) {
			this.fx.toElement(event.target);
		} else {
			this.outerSlidesBox.scrollTo(this.current * this.outerSlidesBox.offsetWidth.toInt(), 0);
		}
		
		if (this.options.animateHeight)
			this.heightFx.start(this.panes[this.current].offsetHeight);
	},
	
	// Handles a click
	buttonEventHandler: function(event, button) {
		if (event.target == this.buttons[this.current]) return;
		this.changeTo(this.panes[this.buttons.indexOf($(button))]);
		// Clear the periodical
		$clear(this.periodical);
	},
	
	// call this to go to the next tab
	next: function() {
		var next = this.current + 1;
		if (next == this.panes.length) {
			if (this.options.wrap == true) { next = 0 } else { return }
		}
		
		this.changeTo(this.panes[next]);
	},
	
	// to go to the previous tab
	previous: function() {
		var prev = this.current - 1
		if (prev < 0) {
			if (this.options.wrap == true) { prev = this.panes.length - 1 } else { return }
		}
		
		this.changeTo(this.panes[prev]);
	},
	
	// call this if the width of the sliding tabs container changes to get everything in line again
	recalcWidths: function() {
		this.panes.each(function(pane, index) {
			pane.setStyle('width', this.outerSlidesBox.offsetWidth.toInt() - this.options.rightOversized + 'px');
		}.bind(this));
		
		this.innerSlidesBox.setStyle(
			'width', (this.outerSlidesBox.offsetWidth.toInt() * this.panes.length) + 'px'
		);
		
		// fix positioning
		if (this.current > 0) {
			this.fx.stop();
			this.outerSlidesBox.scrollTo(this.current * this.outerSlidesBox.offsetWidth.toInt(), 0);
		}
	}
});

ModSlidingPanes.implement(new Options, new Events);
