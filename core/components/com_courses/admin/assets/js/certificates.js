/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var boxes = []
	grabbing = false;

function Box() {
	this.x = 0;
	this.y = 0;
	this.w = 1; // default width and height?
	this.h = 1;
	this.text = '';
	this.id = '';
}

jQuery(window).on('load', function() {

	if ($('#field-properties').length && $('#field-properties').val()) {
		var props = JSON.parse($('#field-properties').val());

		for (var i = 0; i < props.elements.length; i++)
		{
			var rect = new Box;
			rect.x = props.elements[i]['x'];
			rect.y = props.elements[i]['y'];
			rect.w = props.elements[i]['w'];
			rect.h = props.elements[i]['h'];
			rect.text = props.elements[i]['text'];
			rect.id = props.elements[i]['id'];

			boxes.push(rect);
		}
	}

	//Initialize a new Box, add it, and invalidate the canvas
	function addRect(x, y, w, h, id, text) {
		var rect = new Box;
		rect.x = x;
		rect.y = y;
		rect.w = w;
		rect.h = h;
		rect.text = (text ? text : 'unknown');
		rect.id = (id ? id : 'unknown');

		boxes.push(rect);

		invalidate();
	}

	var canvas;
	var ctx;
	var WIDTH;
	var HEIGHT;
	var INTERVAL = 20;  // how often, in milliseconds, we check to see if a redraw is needed

	var isDrag = false;
	var mx, my; // mouse coordinates

	// when set to true, the canvas will redraw everything
	// invalidate() just sets this to false right now
	// we want to call invalidate() whenever we make a change
	var canvasValid = false;

	// The node (if any) being selected.
	// If in the future we want to select multiple objects, this will get turned into an array
	var mySel; 

	// The selection color and width. Right now we have a red selection with a small width
	var mySelColor = '#CC0000';
	var mySelWidth = 2;

	// we use a fake canvas to draw individual shapes for selection testing
	var ghostcanvas;
	var gctx; // fake canvas context

	// since we can drag from anywhere in a node
	// instead of just its x/y corner, we need to save
	// the offset of the mouse when we start dragging.
	var offsetx, offsety;

	// Padding and border style widths for mouse offsets
	var stylePaddingLeft, stylePaddingTop, styleBorderLeft, styleBorderTop;

	// initialize our canvas, add a ghost canvas, set draw loop
	// then add everything we want to initially exist on the canvas
	function init() {
		//certificate = $('#certificate');

		canvas = document.getElementById('secondLayer');
		//canvas.width = certificate.width();
		//canvas.height = certificate.height();
		HEIGHT = canvas.height;
		WIDTH = canvas.width;
		ctx = canvas.getContext('2d');

		ghostcanvas = document.createElement('canvas');
		ghostcanvas.height = HEIGHT;
		ghostcanvas.width = WIDTH;
		gctx = ghostcanvas.getContext('2d');

		//fixes a problem where double clicking causes text to get selected on the canvas
		canvas.onselectstart = function () { return false; }

		// fixes mouse co-ordinate problems when there's a border or padding
		// see getMouse for more detail
		if (document.defaultView && document.defaultView.getComputedStyle) {
			stylePaddingLeft = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingLeft'], 10)      || 0;
			stylePaddingTop  = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingTop'], 10)       || 0;
			styleBorderLeft  = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderLeftWidth'], 10)  || 0;
			styleBorderTop   = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderTopWidth'], 10)   || 0;
		}

		// make draw() fire every INTERVAL milliseconds
		setInterval(draw, INTERVAL);

		// set our events. Up and down are for dragging,
		// double click is for making new boxes
		canvas.onmousedown = myDown;
		canvas.onmouseup = myUp;

		$('.placeholder').on('click', function(e){
			e.preventDefault();

			addRect(200, 200, 30, 30, $(this).attr('data-id'), $(this).text());
		});

		$('#clear-canvas').on('click', function(e){
			e.preventDefault();

			boxes = [];
			invalidate();
		});
	}

	// wipes the canvas context
	function clear(c) {
		c.clearRect(0, 0, WIDTH, HEIGHT);
	}

	// While draw is called as often as the INTERVAL variable demands,
	// It only ever does something if the canvas gets invalidated by our code
	function draw() {
		if (canvasValid == false) {
			clear(ctx);

			// draw all boxes
			var l = boxes.length;
			for (var i = 0; i < l; i++) {
				drawshape(ctx, boxes[i]); //, boxes[i].fill);
			}

			// draw selection
			// right now this is just a stroke along the edge of the selected box
			if (mySel != null) {
				ctx.strokeStyle = mySelColor;
				ctx.lineWidth = mySelWidth;
				ctx.strokeRect(mySel.x, mySel.y, mySel.w, mySel.h);
			}

			// Add stuff you want drawn on top all the time here
			canvasValid = true;
		}
	}

	// Draws a single shape to a single context
	// draw() will call this with the normal canvas
	// myDown will call this with the ghost canvas
	function drawshape(context, shape) { //, fill) {
		// We can skip the drawing of elements that have moved off the screen:
		if (shape.x > WIDTH || shape.y > HEIGHT) {
			return;
		}
		if (shape.x + shape.w < 0 || shape.y + shape.h < 0) {
			return;
		}

		shape.h = (shape.h > 30) ? shape.h : 30;

		context.font = "30px Georgia";
		textLength = context.measureText(shape.text).width;
		shape.w = (shape.w > textLength + 10) ? shape.w : textLength + 10;

		context.fillStyle = 'rgba(250, 200, 200, 0.8)';
		context.fillRect(shape.x, shape.y, shape.w, shape.h);

		context.fillStyle = 'black';
		var v = shape.h > 41 ? (shape.h / 2) + 11 : 30;
		context.fillText(shape.text, shape.x + (shape.w / 2) - (textLength / 2), shape.y + v - 4);
	}

	// Happens when the mouse is moving inside the canvas
	function myMove(e){
		if (isDrag){
			getMouse(e);

			if (grabbing === 'right') {
				mySel.w = e.offsetX - mySel.x;
			} else if (grabbing === 'top') {
				mySel.h = mySel.y + 30 - e.offsetY;
			} else {
				mySel.x = mx - offsetx;
				mySel.y = my - offsety;
			}

			// something is changing position so we better invalidate the canvas!
			invalidate();
		}
	}

	// Happens when the mouse is clicked in the canvas
	function myDown(e){
		getMouse(e);
		clear(gctx);
		var l = boxes.length;
		for (var i = l-1; i >= 0; i--) {
			// draw shape onto ghost context
			drawshape(gctx, boxes[i], 'black');

			// get image data at the mouse x,y pixel
			var imageData = gctx.getImageData(mx, my, 1, 1);
			var index = (mx + my * imageData.width) * 4;

			// if the mouse pixel exists, select and break
			if (imageData.data[3] > 0) {
				mySel = boxes[i];

				if (e.offsetX > mySel.x && e.offsetX < (mySel.w + mySel.x - 5) && e.offsetY < (mySel.y + 5) && e.offsetY > (mySel.y - 5)) {
					canvas.style.cursor = 'n-resize';
					grabbing = 'top';
				} else if (e.offsetX >= (mySel.x + mySel.w - 5) && e.offsetX <= (mySel.x + mySel.w + 5) && e.offsetY < my && e.offsetY > my - mySel.h) {
					canvas.style.cursor = 'e-resize';
					grabbing = 'right';
				} else {
					canvas.style.cursor = 'move';
					grabbing = undefined;
				}

				offsetx = mx - mySel.x;
				offsety = my - mySel.y;
				mySel.x = mx - offsetx;
				mySel.y = my - offsety;

				isDrag = true;

				canvas.onmousemove = myMove;
				//canvas.style.cursor = 'move';

				invalidate();

				clear(gctx);
				return;
			}
		}
		// havent returned means we have selected nothing
		mySel = null;
		// clear the ghost canvas for next time
		clear(gctx);
		// invalidate because we might need the selection border to disappear
		invalidate();
	}

	function myUp() {
		isDrag = false;
		canvas.onmousemove = null;
		canvas.style.cursor = 'auto';
	}

	function invalidate() {
		canvasValid = false;
	}

	// Sets mx,my to the mouse position relative to the canvas
	// unfortunately this can be tricky, we have to worry about padding and borders
	function getMouse(e) {
		var element = canvas, offsetX = 0, offsetY = 0;

		if (element.offsetParent) {
			do {
				offsetX += element.offsetLeft;
				offsetY += element.offsetTop;
			} while ((element = element.offsetParent));
		}

		// Add padding and border style widths to offset
		offsetX += stylePaddingLeft;
		offsetY += stylePaddingTop;

		offsetX += styleBorderLeft;
		offsetY += styleBorderTop;

		mx = e.pageX - offsetX;
		my = e.pageY - offsetY
	}

	/*function imageLoaded() {
		// function to invoke for loaded image
		// decrement the counter
		counter--; 
		if (counter === 0) {
			init();
		}
	}

	var images = $('img');
	var counter = images.length;  // initialize the counter

	images.each(function(i, el) {
		if ( this.complete ) {
			imageLoaded.call( this );
		} else {
			$(this).one('load', imageLoaded);
		}
	});*/
	init();
});

Hubzero.submitbutton = function(task)
{
	var form = document.adminForm;

	if (task == 'cancel') {
		Hubzero.submitform(task, form);
		return;
	}

	//certificate = $('#certificate');

	/*var l = boxes.length;
	for (var i = 0; i < l; i++) {
		boxes[i].px  = boxes[i].x / certificate.width();
		boxes[i].py = boxes[i].y / certificate.height();
	}*/
	var cert = {
		width: $('#certificate').attr('data-width'),
		height: $('#certificate').attr('data-height'),
		elements: boxes
	};

	$('#field-properties').val(JSON.stringify(cert));

	// form field validation
	Hubzero.submitform(task, form);
}
