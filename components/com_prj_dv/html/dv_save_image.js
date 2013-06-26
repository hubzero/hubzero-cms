jQuery(document).ready(function($) {
	$('#dv_pdcharts_download_btn').live('click', function() {

		if ($.browser.msie && $.browser.version < 9) {
			alert("Notice [IE version: " + $.browser.version + "]:Sorry, you need Internet Explorer version 9 (or a later version) to download charts as images.");
			return;
		}

		var c = $('#dv_charts_preview_chart');
		var output_canvas = document.createElement("canvas");
		var ctx = output_canvas.getContext("2d");

		output_canvas.width = $(c).width() + 300;
		output_canvas.height = $(c).height() + 100;

		ctx.fillStyle = "rgb(255, 255, 255)";
		ctx.fillRect(0, 0, output_canvas.width, output_canvas.height);

		var canvas_offset = $(c).offset();

		$(c).find('canvas:visible').each(function () {
			var offset = $(this).offset();
			var position = $(this).position();

			var l = position.left;
			var t = position.top;

			if ($(this).hasClass('jqplot-xaxis-tick') || $(this).hasClass('jqplot-xaxis-label')) {
				t = offset.top - canvas_offset.top;
			}

			ctx.drawImage(this,l ,t);
		});

		$(c).find('table.jqplot-table-legend tbody td').each(function() {

			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			if ($(this).index() == 0) {
				draw_rect(ctx, pos.left, (pos.top - canvas_offset.top), $('div.jqplot-table-legend-swatch', this).css("border-left-color"));
			}

			if ($(this).index() == 1) {
				ctx.font = "13px sans-serif";
				ctx.strokeStyle = "#000";
				ctx.strokeText($(this).text(), pos.left, (pos.top - canvas_offset.top + 10));
			}
		});

		$(c).find('.jqplot-point-label').each(function() {
			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			ctx.font = $(this).css('font-size') + ' sans-serif';
			ctx.strokeStyle = "#999";
			ctx.strokeText($(this).text(), pos.left, (pos.top - canvas_offset.top + 20));
		});

		$(c).find('.jqplot-title span').each(function() {
			var pos = $(this).offset();
			pos.left = pos.left - canvas_offset.left;

			ctx.font = $(this).css('font-size') + ' sans-serif';
			ctx.strokeStyle = "#999";
			ctx.strokeText($(this).text(), pos.left, (pos.top - canvas_offset.top + 20));
		});

		var img = output_canvas.toDataURL();
		var chartdl_window = window.open("", "DownloadIPIChart", "menubar=no,scrollbars=1,width=850px,height=600px,toolbar=no,resizable=1");
		chartdl_window.document.write('<html><head><title>Download Chart as an Image</title></head>');
		chartdl_window.document.write('<body><strong><small>Right on the chart below to save it as an image</small></strong><br /><img src="' + img + '" /></body></html>');
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
