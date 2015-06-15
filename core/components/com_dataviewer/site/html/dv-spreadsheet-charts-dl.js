/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


jQuery(document).ready(function($) {
	$(document).on('click', '#dv_pdcharts_download_btn', function() {

		if ($.ui.ie) {
			alert("Notice [IE version: " + $.browser.version + "]:Sorry, you need Internet Explorer version 9 (or a later version) to download charts as images.");
			return;
		}

		var c = $('#dv_charts_preview_chart');
		var output_canvas = document.createElement("canvas");
		var ctx = output_canvas.getContext("2d");
		var title = $(c).find('div.jqplot-title').text();

		output_canvas.width = $(c).width() + 100;
		output_canvas.height = $(c).height() + 20;

		ctx.fillStyle = "rgb(255, 255, 255)";
		ctx.fillRect(0, 0, output_canvas.width, output_canvas.height);

		var canvas_offset = $(c).offset();

		window.dbg = $(c).find('div.jqplot-title');
		// Title
		ctx.font = '18px Arial';
		ctx.fillStyle = "#000";
		ctx.fillText(title, 80, 15);

		$(c).find('canvas:visible').each(function () {
			var offset = $(this).offset();
			var position = $(this).position();

			var l = position.left;
			var t = position.top;

			if ($(this).hasClass('jqplot-xaxis-tick') || $(this).hasClass('jqplot-xaxis-label')) {
				t = offset.top - canvas_offset.top;
			}

			ctx.drawImage(this, l, t);
		});

		$(c).find('table.jqplot-table-legend tbody td').each(function() {

			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			if ($(this).index() == 0) {
				draw_rect(ctx, pos.left, (pos.top - canvas_offset.top), $('div.jqplot-table-legend-swatch', this).css("border-left-color"));
			}

			if ($(this).index() == 1) {
				ctx.font = "13px sans-serif";
				ctx.fillStyle = "#000";
				ctx.fillText($(this).text(), pos.left, (pos.top - canvas_offset.top + 10));
			}
		});

		$(c).find('.jqplot-point-label').each(function() {
			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			ctx.font = $(this).css('font-size') + ' sans-serif';
			ctx.fillStyle = "#999";
			ctx.fillText($(this).text(), pos.left, (pos.top - canvas_offset.top + 20));
		});

		$(c).find('.jqplot-title span').each(function() {
			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			ctx.font = $(this).css('font-size') + ' sans-serif';
			ctx.fillStyle = "#999";
			ctx.fillText($(this).text(), pos.left, (pos.top - canvas_offset.top + 20));
		});

		var img = output_canvas.toDataURL();
		var chartdl_window = window.open("", "DownloadIPIChart", "menubar=no,scrollbars=1,width=" + (output_canvas.width + 50) + "px,height=" + (output_canvas.height + 50) + "px,toolbar=no,resizable=1");
		chartdl_window.document.write('<html><head><title>Download Chart as an Image [Right on the chart below to save it as an image]</title></head>');
		chartdl_window.document.write('<body><img src="' + img + '" /></body></html>');
		chartdl_window.document.close();

		return true;
	});

	function draw_rect(ctx, x, y, color) {
		var topLeftCornerX = x;
		var topLeftCornerY = y;
		var width = 10;
		var height = 10;
	
		ctx.beginPath();
		ctx.rect(topLeftCornerX, topLeftCornerY, width, height);
	
		ctx.fillStyle = color;
		ctx.fill();
		ctx.lineWidth = 1;
		ctx.strokeStyle = "#A0A0A0";
		ctx.stroke();
	};
});
