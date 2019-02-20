/**
 * @package     hubzero-cms
 * @file        components/com_resources/admin/assets/js/media.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
});
