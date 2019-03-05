function dirup()
{
	var urlquery = frames['filer'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('dir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['filer'].location.href = selection.getAttribute('data-path') + listdir;
}

function goUpDir()
{
	var selection = document.getElementById('dir');
	var dir = selection.options[selection.selectedIndex].value;
	frames['filer'].location.href = selection.getAttribute('data-path') + dir;
}

function deleteFile(file)
{
	if (confirm('Delete file "' + file + '"?')) {
		return true;
	}
	return false;
}
function deleteFolder(folder, numFiles)
{
	if (numFiles > 0) {
		alert('There are ' + numFiles + ' files/folders in "' + folder + '". Please delete all files/folder in "' + folder + '" first.');
		return false;
	}
	if (confirm('Delete folder "' + folder + '"?')) {
		return true;
	}
	return false;
}

jQuery(document).ready(function($){
	$("a.deletefolder").on('click', function(e){
		e.preventDefault();

		return deleteFolder($(this).attr('data-folder'), $(this).attr('data-files'));
	});

	$("a.deletefile").on('click', function(e){
		e.preventDefault();

		return deleteFile($(this).attr('data-file'));
	});
});
