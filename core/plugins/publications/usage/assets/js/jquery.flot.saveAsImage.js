/* Flot plugin that adds a function to allow user save the current graph as an image
	by right clicking on the graph and then choose "Save image as ..." to local disk.

Copyright (c) 2013 http://zizhujy.com.
Licensed under the MIT license.

Usage:
	Inside the <head></head> area of your html page, add the following lines:
	
	<script type="text/javascript" src="http://zizhujy.com/Scripts/base64.js"></script>
	<script type="text/javascript" src="http://zizhujy.com/Scripts/drawing/canvas2image.js"></script>
	<script type="text/javascript" src="http://zizhujy.com/Scripts/flot/jquery.flot.saveAsImage.js"></script>

	Now you are all set. Right click on your flot canvas, you will see the "Save image as ..." option.

Online examples:
	http://zizhujy.com/FunctionGrapher is using it, you can try right clicking on the function graphs and
	you will see you can save the image to local disk.

Dependencies:
	This plugin references the base64.js and canvas2image.js.

Customizations:
	The default behavior of this plugin is dynamically creating an image from the flot canvas, and then puts the 
	image above the flot canvas. If you want to add some css effects on to the dynamically created image, you can
	apply whatever css styles on to it, only remember to make sure the css class name is set correspondingly by 
	the options object of this plugin. You can also customize the image format through this options object:

	options: {
		imageClassName: "canvas-image",
		imageFormat: "png"
	}

*/

; (function ($, Canvas2Image) {
	var imageCreated = null;
	var mergedCanvas = null;
	var theClasses = null;

	function init(plot, classes) {
		theClasses = classes;

		var id = plot.getPlaceholder().attr('id');

		if (!$('#plot-download-' + id).length) {
			var btn = $('<button class="chart-download" id="plot-download-' + id + '">Download</button>')
				.on('click', function(e){
					e.preventDefault();

					var tmpcontainer = $('<div id="tmpcontainer-' + id + '" style="width:800px;height:450px;position:absolute;top:-1600px;left:-900px;visibility:hidden;"></div>').appendTo($('body'));
					var options = {
						series: {
							lines: {
								show: true,
								fill: false
							},
							points: {
								show: false
							},
							shadowSize: 0
						},
						legend: {
							show: true
						},
						xaxis: {
							mode: "time",
							tickDecimals: 0,
							tickFormatter: function (val, axis) {
								var d = new Date(val);
								return (d.getUTCMonth() + 1) + "/" + d.getFullYear();
							}
						},
						yaxis: {
							min: 0
						}
					}
					var plot2 = $.plot(tmpcontainer, plot.getData(), options);

					deleteStaleCanvasImage(plot2, mergedCanvas);
					mergedCanvas = mergeCanvases(plot2);
					createImageFromCanvas(mergedCanvas, plot2, plot2.getOptions().imageFormat);

					// For ubuntu chrome:
					setTimeout(function () { deleteStaleCanvasImage(plot2, mergedCanvas); }, 500);
				});
				plot.getPlaceholder().after(btn);
		}
	}

	function deleteStaleCanvasImage(plot, mergedCanvas) {
		if (!!mergedCanvas) {
			$(mergedCanvas).remove();
		}
		$(".mergedCanvas").remove();
	}

	function mergeCanvases(plot) {
		var theMergedCanvas = plot.getCanvas();

		if (!!theClasses) {
			theMergedCanvas = new theClasses.Canvas("mergedCanvas", plot.getPlaceholder());
			var mergedContext = theMergedCanvas.context;
			var plotCanvas = plot.getCanvas();
			
			theMergedCanvas.element.height = plotCanvas.height;
			theMergedCanvas.element.width = plotCanvas.width;

			mergedContext.restore();

			$(theMergedCanvas).css({
				"visibility": "hidden",
				"z-index": "-100",
				"position": "absolute"
			});

			var $canvases = $(plot.getPlaceholder()).find("canvas").not('.mergedCanvas');
			$canvases.each(function(index, canvas) {
				mergedContext.drawImage(canvas, 0, 0);
			});

			return theMergedCanvas.element;
		}

		return theMergedCanvas;
	}

	function createImageFromCanvas(canvas, plot, format) {
		if (!canvas) {
			canvas = plot.getCanvas();
		}

		var img = null;

		switch (format.toLowerCase()) {
			case "png":
				img = Canvas2Image.saveAsPNG(canvas, format);
				break;
			case "bmp":
				img = Canvas2Image.saveAsBMP(canvas, format);
				break;
			case "jpeg":
				img = Canvas2Image.saveAsJPEG(canvas, format);
				break;
			default:
				break;
		}

		if (!img) {
			img = Canvas2Image.saveAsPNG(canvas, "png");
		}

		if (!img) {
			img = Canvas2Image.saveAsPNG(canvas, "bmp");
		}

		if (!img) {
			img = Canvas2Image.saveAsJPEG(canvas, "jpeg");
		}

		if (!img) {
			alert(plot.getOptions().notSupportMessage || "Sorry, but this browser may not be capable of creating image files.");
			return false;
		}

		document.location.href = $(img).attr('src');
	}

	var options = {
		imageClassName: "canvas-image",
		imageFormat: "png"
	};

	$.plot.plugins.push({
		init: init,
		options: options,
		name: 'saveAsImage',
		version: '1.6'
	});

})(jQuery, Canvas2Image);
