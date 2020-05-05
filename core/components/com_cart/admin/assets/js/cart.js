/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	$(document).trigger('editorSave');

	var frm = document.getElementById('item-form');

	if (frm) {
		Hubzero.submitform(task, frm);
	}
}

jQuery(document).ready(function ($) {
	var dateFormat = "mm/dd/yy",
		from = $("#filter-report-from")
			.datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 1
			})
			.on( "change", function() {
				to.datepicker( "option", "minDate", getDate(this));
			}),
		to = $("#filter-report-to").datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 1
			})
			.on( "change", function() {
				from.datepicker( "option", "maxDate", getDate(this));
			});

	function getDate(element) {
		var date;

		try {
			date = $.datepicker.parseDate(dateFormat, element.value);
		} catch (error) {
			date = null;
		}

		return date;
	}

	$('#filter_pId-clear').on('click', function(e){
		$('#filter_pId').val('');
		this.form.submit();
	});

	$('#filter_pId-clear').on('click', function(e){
		$('#filter_sId').val('');
		this.form.submit();
	});

	$('#filter_uidNumber-clear').on('click', function(e){
		$('#filter_uidNumber').val('');
		this.form.submit();
	});
});
/*
document.addEventListener('DOMContentLoaded', function() {
	var frm = document.getElementById('item-form');

	if (frm) {
		frm.addEventListener('submit', function(event) {
			event.preventDefault();
			if (!document.formvalidator.isValid(frm)) {
				alert(frm.getAttribute('data-invalid-msg'));
				return false;
			}
			return true;
		});
	}
});
*/