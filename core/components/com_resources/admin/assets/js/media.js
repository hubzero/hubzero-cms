/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

window.addEventListener('DOMContentLoaded', function(){
	var files = document.getElementsByClassName('delete-file');

	Array.prototype.forEach.call(files, function(el, index, array){
		el.addEventListener('click', function(e){
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	var folders = document.getElementsByClassName('delete-folder');

	Array.prototype.forEach.call(folders, function(el, index, array){
		el.addEventListener('click', function(e){
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
