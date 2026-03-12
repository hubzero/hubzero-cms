/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function dirup()
{
	var urlquery = frames['filer'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('dir=') + 8);
	var listdir = curdir.substring(0, curdir.lastIndexOf('/'));
	var selection = document.getElementById('dir');
	frames['filer'].location.href = selection.getAttribute('data-path') + listdir;
}

document.addEventListener('DOMContentLoaded', function() {
	var deleteFolders = document.querySelectorAll('a.deletefolder');
	for (var i = 0; i < deleteFolders.length; i++) {
		deleteFolders[i].addEventListener('click', function(e) {
			var numFiles = parseInt(this.getAttribute('data-files'));

			if (numFiles > 0) {
				e.preventDefault();
				alert(this.getAttribute('data-notempty'));
				return false;
			}

			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	}

	var deleteFiles = document.querySelectorAll('a.deletefile');
	for (var i = 0; i < deleteFiles.length; i++) {
		deleteFiles[i].addEventListener('click', function(e) {
			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	}

	var dirSelect = document.getElementById('dir');
	if (dirSelect) {
		dirSelect.addEventListener('change', function(e) {
			var selection = document.getElementById('dir');
			var dir = selection.options[selection.selectedIndex].value;
			frames['filer'].location.href = selection.getAttribute('data-path') + dir;

			var currentdir = document.getElementById('currentdir');
			if (currentdir) {
				currentdir.value = dir;
			}
		});
	}
});
