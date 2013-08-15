/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


jQuery(document).ready(function($) {

	if (typeof dv_data.customizer != 'undefined') {

		if ($(this).find('li').length < 1) {
			$('#dv_customizer_launch_view_btn').hide();
		} else {
			$('#dv_customizer_launch_view_btn').show('fade');
		}

		if (typeof dv_data.customizer.width != 'undefined') {
			$('#dv_customizer_panel').width(dv_data.customizer.width);
		} else {
			if ($('#dv_customizer_panel').width() < ($(window).width() - 100)) {
				$('#dv_customizer_panel').width($(window).width() - 100);
			}
		}


		if (typeof dv_data.customizer.height != 'undefined') {
			$('#dv_customizer_panel').height(dv_data.customizer.height);
		} else {
			if (($(window).height() - 250) > 500) {
				$('#dv_customizer_panel').height($(window).height() - 250);
			} else {
				$('#dv_customizer_panel').height(500);
			}
		}

		$('.dv_customizer_lists').height($('#dv_customizer_panel').height() - 100);

		if (typeof dv_data.customizer.show_table != 'undefined') {
			if (!dv_data.customizer.show_table) {
				$('#dv_download').remove();
				$('label[for="dv_download"]').remove();
				$('#dv_maps').remove();
				$('label[for="dv_maps"]').remove();
				$('#dv_customizer_btn').prop('disabled', true);

				$('#dv_table_container').hide();
			} else {
				$('#dv_table_container').show();
			}
		}

		$(function() {
			$( "#dv_customizer_full_list, #dv_customizer_selected" ).sortable({
				connectWith: ".dv_customizer_col_lists"
			}).disableSelection();
		});

		if (dv_show_customizer) {
			$('#dv_customizer_panel').show();
			$('#dv_customizer_btn').prop('checked', true);
		}

	}

	$('#dv_customizer_full_list li').live('dblclick', function() {
		$('#dv_customizer_selected').append($(this).clone());
		$(this).remove();
		$('.dv_customizer_col_lists').trigger('sortupdate');
	});

	$('#dv_customizer_selected li').live('dblclick', function() {
		$('#dv_customizer_full_list').append($(this).clone());
		$(this).remove();
		$('.dv_customizer_col_lists').trigger('sortupdate');
	});

	$('#dv_customizer_selected').bind('sortupdate', function(event, ui) {
		if ($(this).find('li').length < 1) {
			$('#dv_customizer_launch_view_btn').hide();
		} else {
			$('#dv_customizer_launch_view_btn').show('fade');
		}
	});

	$('#dv_customizer_launch_view_btn').click(function() {
		var url = $(this).data('view-url');
		url = window.location.protocol + '//' + window.location.hostname + url + '&custom_view=';
		var cols = '';
		var sel = $('#dv_customizer_selected li');

		// Custom Columns
		$('#dv_customizer_selected li').each(function() {
			url = url + $(this).data('dv-id') + ',';
		});

		url = url.substring(0, url.length-1);

		// Custom Title
		url = url + "&custom_title=" + encodeURIComponent($('#dv_customizer_view_title').val());

		// Group By
		if ($('.dv_customizer_group_by_item:checked').length > 0) {
			url = url + "&group_by=";

			$('.dv_customizer_group_by_item:checked').each(function() {
				url = url + $(this).val() + ',';
			});

			url = url.substring(0, url.length-1);
		}

		window.open (url, "DataViewerCustomView");
	});

	// Group By
	if ($('.dv_customizer_group_by_item').length > 0) {
		$('#dv_customizer_group_by_btn').show();
	} else {
		$('#dv_customizer_group_by_btn').hide();
	}

	$('.dv_customizer_group_by_item_div label').click(function() {
		$(this).parent().find(':checkbox').attr('checked', !$(this).parent().find(':checkbox').attr('checked'));
	});

	$('#dv_customizer_group_by_btn').click(function() {
		$('#dv_customizer_group_by').dialog({
			height: 250,
			width: 400
		});
	});

	// Hide/Show Customizer
	$('#dv_customizer_btn').live('click', function() {
		$('.dv_top_pannel').hide();
		if ($(this).prop('checked') === true) {
			$('.dv_panel_btn').not(this).prop('checked', false);
			$('#dv_customizer_panel').show();
		} else {
			$('#dv_customizer_panel').hide();
		}
	});
});
