/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
	var files = document.querySelectorAll('.delete-file');

	files.forEach(function(el) {
		el.addEventListener('click', function(e) {
			var res = confirm(this.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	var folders = document.querySelectorAll('.delete-folder');

	folders.forEach(function(el) {
		el.addEventListener('click', function(e) {
			var res = confirm(el.getAttribute('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			var numFiles = parseInt(el.getAttribute('data-files'));
			if (numFiles > 0) {
				e.preventDefault();
				alert(el.getAttribute('data-notempty'));
				return false;
			}
			return res;
		});
	});
});
