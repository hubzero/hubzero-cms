/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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

function getAuthorImage()
{
	var filew = window.filer;
	if (filew) {
		var conimg = filew.document.forms['filelist'].conimg;
		if (conimg) {
			document.forms['adminForm'].elements['picture'].value = conimg.value;
		}
	}
}

function checkState(checkboxname)
{
	if (checkboxname.checked == false) {
		checkboxname.checked = false;
	}
}

jQuery(document).ready(function($) {
	$('.fancybox-inline').fancybox({
		padding: 0,
		helpers: {
			overlay: {
				locked: false
			}
		},
	});

	$('.fancybox-inline').on('click', function(e){
		e.preventDefault();
	});

	$('.delete-image').on('click', function(e){
		$('#picture-' + e.target.id).remove();
	});

	function readURL(input) {
		var files = Array.prototype.slice.call($(input)[0].files);
		files.forEach(function(file) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#uploadImages').append('<img src="' + e.target.result + '" width="100" height="100" alt="" />');
			}
			reader.readAsDataURL(file);
		});
	}

	$("#imgInp").change(function(e){
		$('#uploadImages').html("");
		readURL(this);
	});
});
