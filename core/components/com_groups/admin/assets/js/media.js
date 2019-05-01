/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

function dirup()
{
	var urlquery = frames['filer'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('dir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['filer'].location.href = selection.getAttribute('data-path') + listdir;
}

jQuery(document).ready(function($){
	$("a.deletefolder").on('click', function(e){
		var numFiles = parseInt($(this).attr('data-files'));

		if (numFiles > 0) {
			e.preventDefault();
			alert($(this).attr('data-notempty'));
			return false;
		}

		var res = confirm($(this).attr('data-confirm'));
		if (!res) {
			e.preventDefault();
		}
		return res;
	});

	$("a.deletefile").on('click', function(e){
		var res = confirm($(this).attr('data-confirm'));
		if (!res) {
			e.preventDefault();
		}
		return res;
	});

	$("#dir").on('change', function(e){
		var selection = document.getElementById('dir');
		var dir = selection.options[selection.selectedIndex].value;
		frames['filer'].location.href = selection.getAttribute('data-path') + dir;

		$('#currentdir').val(dir);
	});
});
