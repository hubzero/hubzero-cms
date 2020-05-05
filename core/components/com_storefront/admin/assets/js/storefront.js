/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function($){
	var attach = $("#ajax-uploader");

	if (attach.length) {
		var uploader = new qq.FileUploader({
			element: attach[0],
			action: attach.attr("data-action"),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
				'<div class="qq-upload-button"><span>' + attach.attr('data-instructions') + '</span></div>' +
				'<div class="qq-upload-drop-area"><span>' + attach.attr('data-instructions') + '</span></div>' +
				'<ul class="qq-upload-list"></ul>' +
			'</div>',
			onComplete: function(id, file, response) {
				if (response.success) {
					$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
					$('#img-name').text(response.file);
					$('#img-size').text(response.size);
					$('#img-width').text(response.width);
					$('#img-height').text(response.height);
					$('#currentfile').val(response.imgId);

					$('#img-delete').show();
				}
			}
		});
	}

	$('#img-delete').on('click', function (e) {
		e.preventDefault();

		var el = $(this);
		var currentfileVal = $('#currentfile').val();

		$.getJSON(el.attr('href').nohtml(), {currentfile: currentfileVal}, function(response) {
			if (response.success) {
				$('#img-display').attr('src', el.attr('data-noimg'));
				$('#img-name').text('[ none ]');
				$('#img-size').text('0');
				$('#img-width').text('0');
				$('#img-height').text('0');
			}
			el.hide();
		});
	});
});
