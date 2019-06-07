/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['imgManager'].location.href = document.getElementById('imgManager').getAttribute('data-dir') + '&listdir=' + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].subdir;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href = document.getElementById('imgManager').getAttribute('data-dir') + '&listdir=' + listdir.value +'&subdir='+ dir;
}

jQuery(document).ready(function ($) {
	$('a.delete-file')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});

	$('a.delete-folder')
		.on('click', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			var numFiles = parseInt($(this).attr('data-files'));
			if (numFiles > 0) {
				e.preventDefault();
				alert($(this).attr('data-notempty'));
				return false;
			}
			return res;
		});

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
				$('#imgManager').attr('src', $('#imgManager').attr('src'));
			}
		});
	}
});
