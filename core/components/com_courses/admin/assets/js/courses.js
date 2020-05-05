/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');
	if (!frm) {
		var frm = document.getElementById('component-form');
	}

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
	if ($('#adminForm').length) {
		$('a.edit-asset').on('click', function(e) {
			e.preventDefault();

			window.parent.$.fancybox.open({'href': $(this).attr('href'), 'type': 'iframe', 'width': 570, 'height': 550, 'autoHeight': false});
		});
	}

	if ($('#component-form').length) {
		$("#btn-save").on('click', function(e){
			Hubzero.submitbutton('save');

			window.top.setTimeout(function(){
				var src = window.parent.document.getElementById('assets').src;
				window.parent.document.getElementById('assets').src = src;

				window.parent.$.fancybox.close();
			}, 700);
		});

		$("#btn-cancel").on('click', function(e){
			Hubzero.submitbutton('cancel');

			window.parent.$.fancybox.close();
		});

		$("#btn-generate").on('click', function(e){
			Hubzero.submitbutton('generate');

			window.top.setTimeout("window.parent.location=" + $(this).attr('data-redirect'), 700);
		});

		$("#btn-attach").on('click', function(e){
			$('#task').val('link');
		});
	}

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
					if ($('#img-display').length) {
						$('#img-display').attr('src', '..' + response.directory + '/' + response.file);
						$('#img-name').text(response.file);
						$('#img-size').text(response.size);
						$('#img-width').text(response.width);
						$('#img-height').text(response.height);

						$('#img-delete').show();
					}
				}
			}
		});
	}

	$('#img-delete').on('click', function (e) {
		e.preventDefault();
		var el = $(this);
		$.getJSON(el.attr('href').nohtml(), {}, function(response) {
			if (response.success) {
				$('#img-display').attr('src', el.attr('data-defaultimg'));
				$('#img-name').text('[ none ]');
				$('#img-size').text('0');
				$('#img-width').text('0');
				$('#img-height').text('0');
			}
			el.hide();
		});
	});

	if ($('#section-document').length) {
		$('#section-document').tabs();
	}

	var offering_id = $('#offering_id');
	if (offering_id.length && $('#section_id').length) {
		var offeringsections = new Array,
			data = $('#offering-data');

		if (data.length) {
			offeringsections = JSON.parse(data.html());
		}

		offering_id.on('change', function(e){
			changeDynaList(
				'section_id',
				offeringsections,
				document.getElementById('offering_id').options[document.getElementById('offering_id').selectedIndex].value
				0,
				0
			);
		});
	}

	$('.datetime-field').datetimepicker({
		duration: '',
		showTime: true,
		constrainInput: false,
		stepMinutes: 1,
		stepHours: 1,
		altTimeField: '',
		time24h: true,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:00'
	});

	if (!$('#badge-published').is(':checked')) {
		$('.badge-field-toggle').hide();
	}

	$('#badge-published').on('click', function(e) {
		$('.badge-field-toggle').toggle();
	});
});
