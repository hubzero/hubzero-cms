/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('adminForm');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

function setTask(task)
{
	$('#task').value = task;
}

function saveAndUpdate()
{
	Hubzero.submitbutton('save');
	window.parent.setTimeout(function(){
		var src = window.parent.document.getElementById('locationslist').src;

		window.parent.document.getElementById('locationslist').src = src + '&';
		window.parent.$.fancybox.close();
	}, 700);
}

jQuery(document).ready(function($){
	$('a.edit-asset').on('click', function(e) {
		e.preventDefault();

		window.parent.$.fancybox.open($(this).attr('href'), {type: 'iframe', size: {x: 570, y: 550}});
	});

	var from = $('#field-ipFROM');
	var to   = $('#field-ipTO');

	from.on('keyup', function(e) {
		if ($(this).val().indexOf('/') !== -1) {
			$('.ipTOrow').fadeTo(200, 0.3);
			to.prop('disabled', true);
		} else {
			$('.ipTOrow').fadeTo(200, 1);
			to.prop('disabled', false);
		}
	});

	$('#btn-save').on('click', function(e){
		saveAndUpdate();
	});
	$('#btn-close').on('click', function(e){
		window.parent.$.fancybox.close();
	});

	var continentcountry = new Array,
		countrydata = $('#country-data');

	if (countrydata.length) {
		var k = 0;
		continentcountry[k++] = new Array('', '', countrydata.attr('data-select'));

		var cdata = JSON.parse($('#country-data').html());

		for (var i = 0; i < cdata.data.length; i++)
		{
			continentcountry[k++] = new Array(
				cdata.data[i]['continent'],
				cdata.data[i]['code'],
				cdata.data[i]['name']
			);
		}
	}

	$('#field-continent').on('change', function(e){
		changeDynaList('field-countrySHORT', continentcountry, document.getElementById('field-continent').options[document.getElementById('field-continent').selectedIndex].value, 0, 0);
	});
});
