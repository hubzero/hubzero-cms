/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('.datetime').datetimepicker({
		step: 15,
		time24h: true,
		format: 'Y-m-d H:i:s',
		defaultTime: '08:00'
	});

	$('#btn-save').on('click', function(e) {
		Hubzero.submitbutton('saveZone');
		window.parent.setTimeout(function(){
			var src = window.parent.document.getElementById('zoneslist').src;

			window.parent.document.getElementById('zoneslist').src = src + '&';
			window.parent.$.fancybox.close();
		}, 700);
	});

	$('#btn-cancel').on('click', function(e) {
		window.parent.$.fancybox.close();
	});

	$('#field-zone-params-websocket-enable').on('click', function(e) {
		$('.websocket')
			.prop('disabled', function(i, v) { return !v; })
			.toggleClass('opaque');
	});

	$('#field-zone-params-vnc-enable').on('click', function(e) {
		$('.vnc')
			.prop('disabled', function(i, v) { return !v; })
			.toggleClass('opaque');
	});

	var upload = $("#ajax-uploader");

	if (upload.length) {
		var uploader = new qq.FileUploader({
			element: upload[0],
			action: upload.attr("data-action"),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + upload.attr('data-instructions') + '</span></div>' +
						'<div class="qq-upload-drop-area"><span>' + upload.attr('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' +
					   '</div>',
			onComplete: function(id, file, response) {
				if (response.success) {
					$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
					$('#img-name').text(response.file);
					$('#img-size').text(response.size);
					$('#img-width').text(response.width);
					$('#img-height').text(response.height);

					$('#img-delete').show();
				}
			}
		});
	}

	$('#img-delete').on('click', function (e) {
		e.preventDefault();

		var el = $(this);

		$.getJSON(el.attr('href').nohtml(), {}, function(response) {
			if (response.success) {
				$('#img-display').attr('src', '../media/images/blank.png');
				$('#img-name').text('[ none ]');
				$('#img-size').text('0');
				$('#img-width').text('0');
				$('#img-height').text('0');
			}
			el.hide();
		});
	});
});
