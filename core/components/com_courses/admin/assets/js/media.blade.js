/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function dirup()
{
	var urlquery = frames['imgManager'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('listdir=') + 8);
	var listdir = curdir.substring(0, curdir.lastIndexOf('/'));
	frames['imgManager'].location.href = document.getElementById('imgManager').getAttribute('data-dir') + '&listdir=' + listdir;
}

function goUpDir()
{
	var listdir = document.getElementById('listdir');
	var selection = document.forms[0].subdir;
	var dir = selection.options[selection.selectedIndex].value;
	frames['imgManager'].location.href = document.getElementById('imgManager').getAttribute('data-dir') + '&listdir=' + listdir.value + '&subdir=' + dir;
}

document.addEventListener('DOMContentLoaded', function() {
	var deleteFiles = document.querySelectorAll('a.delete-file');
	for (var i = 0; i < deleteFiles.length; i++) {
		deleteFiles[i].addEventListener('click', function(e) {
			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	}

	var deleteFolders = document.querySelectorAll('a.delete-folder');
	for (var i = 0; i < deleteFolders.length; i++) {
		deleteFolders[i].addEventListener('click', function(e) {
			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			var numFiles = parseInt(this.getAttribute('data-files'));
			if (numFiles > 0) {
				e.preventDefault();
				alert(this.getAttribute('data-notempty'));
				return false;
			}
			return res;
		});
	}

	var attach = document.getElementById('ajax-uploader');
	if (attach) {
		var uploader = new qq.FileUploader({
			element: attach,
			action: attach.getAttribute('data-action'),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.getAttribute('data-instructions') + '</span></div>' +
						'<div class="qq-upload-drop-area"><span>' + attach.getAttribute('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' +
					'</div>',
			onComplete: function(id, file, response) {
				var imgManager = document.getElementById('imgManager');
				if (imgManager) {
					imgManager.src = imgManager.src;
				}
			}
		});
	}
});
