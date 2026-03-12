/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var boxes = [],
	grabbing = false;

function Box() {
	this.x = 0;
	this.y = 0;
	this.w = 1;
	this.h = 1;
	this.text = '';
	this.id = '';
}

window.addEventListener('load', function() {

	var fieldProps = document.getElementById('field-properties');
	if (fieldProps && fieldProps.value) {
		var props = JSON.parse(fieldProps.value);

		for (var i = 0; i < props.elements.length; i++) {
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
	var INTERVAL = 20;

	var isDrag = false;
	var mx, my;

	var canvasValid = false;

	var mySel;

	var mySelColor = '#CC0000';
	var mySelWidth = 2;

	var ghostcanvas;
	var gctx;

	var offsetx, offsety;

	var stylePaddingLeft, stylePaddingTop, styleBorderLeft, styleBorderTop;

	function init() {
		canvas = document.getElementById('secondLayer');
		HEIGHT = canvas.height;
		WIDTH = canvas.width;
		ctx = canvas.getContext('2d');

		ghostcanvas = document.createElement('canvas');
		ghostcanvas.height = HEIGHT;
		ghostcanvas.width = WIDTH;
		gctx = ghostcanvas.getContext('2d');

		canvas.onselectstart = function () { return false; };

		if (document.defaultView && document.defaultView.getComputedStyle) {
			stylePaddingLeft = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingLeft'], 10)      || 0;
			stylePaddingTop  = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingTop'], 10)       || 0;
			styleBorderLeft  = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderLeftWidth'], 10)  || 0;
			styleBorderTop   = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderTopWidth'], 10)   || 0;
		}

		setInterval(draw, INTERVAL);

		canvas.onmousedown = myDown;
		canvas.onmouseup = myUp;

		var placeholders = document.querySelectorAll('.placeholder');
		for (var i = 0; i < placeholders.length; i++) {
			placeholders[i].addEventListener('click', function(e) {
				e.preventDefault();
				addRect(200, 200, 30, 30, this.getAttribute('data-id'), this.textContent);
			});
		}

		var clearBtn = document.getElementById('clear-canvas');
		if (clearBtn) {
			clearBtn.addEventListener('click', function(e) {
				e.preventDefault();
				boxes = [];
				invalidate();
			});
		}
	}

	function clear(c) {
		c.clearRect(0, 0, WIDTH, HEIGHT);
	}

	function draw() {
		if (canvasValid == false) {
			clear(ctx);

			var l = boxes.length;
			for (var i = 0; i < l; i++) {
				drawshape(ctx, boxes[i]);
			}

			if (mySel != null) {
				ctx.strokeStyle = mySelColor;
				ctx.lineWidth = mySelWidth;
				ctx.strokeRect(mySel.x, mySel.y, mySel.w, mySel.h);
			}

			canvasValid = true;
		}
	}

	function drawshape(context, shape) {
		if (shape.x > WIDTH || shape.y > HEIGHT) {
			return;
		}
		if (shape.x + shape.w < 0 || shape.y + shape.h < 0) {
			return;
		}

		shape.h = (shape.h > 30) ? shape.h : 30;

		context.font = "30px Georgia";
		var textLength = context.measureText(shape.text).width;
		shape.w = (shape.w > textLength + 10) ? shape.w : textLength + 10;

		context.fillStyle = 'rgba(250, 200, 200, 0.8)';
		context.fillRect(shape.x, shape.y, shape.w, shape.h);

		context.fillStyle = 'black';
		var v = shape.h > 41 ? (shape.h / 2) + 11 : 30;
		context.fillText(shape.text, shape.x + (shape.w / 2) - (textLength / 2), shape.y + v - 4);
	}

	function myMove(e) {
		if (isDrag) {
			getMouse(e);

			if (grabbing === 'right') {
				mySel.w = e.offsetX - mySel.x;
			} else if (grabbing === 'top') {
				mySel.h = mySel.y + 30 - e.offsetY;
			} else {
				mySel.x = mx - offsetx;
				mySel.y = my - offsety;
			}

			invalidate();
		}
	}

	function myDown(e) {
		getMouse(e);
		clear(gctx);
		var l = boxes.length;
		for (var i = l - 1; i >= 0; i--) {
			drawshape(gctx, boxes[i], 'black');

			var imageData = gctx.getImageData(mx, my, 1, 1);
			var index = (mx + my * imageData.width) * 4;

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

				invalidate();

				clear(gctx);
				return;
			}
		}
		mySel = null;
		clear(gctx);
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

	function getMouse(e) {
		var element = canvas, oX = 0, oY = 0;

		if (element.offsetParent) {
			do {
				oX += element.offsetLeft;
				oY += element.offsetTop;
			} while ((element = element.offsetParent));
		}

		oX += stylePaddingLeft;
		oY += stylePaddingTop;

		oX += styleBorderLeft;
		oY += styleBorderTop;

		mx = e.pageX - oX;
		my = e.pageY - oY;
	}

	init();
});

Hubzero.submitbutton = function(task) {
	var form = document.adminForm;

	if (task == 'cancel') {
		Hubzero.submitform(task, form);
		return;
	}

	var certEl = document.getElementById('certificate');
	var cert = {
		width: certEl ? certEl.getAttribute('data-width') : 0,
		height: certEl ? certEl.getAttribute('data-height') : 0,
		elements: boxes
	};

	var fieldProps = document.getElementById('field-properties');
	if (fieldProps) {
		fieldProps.value = JSON.stringify(cert);
	}

	Hubzero.submitform(task, form);
};
