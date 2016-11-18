/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */


var dv = {};
var dv_table;
var dv_data;
var dv_settings;
var dl_vars;
var dv_charts = [];
var asInitVals = [];
var first_load = true;
var dbg;
var dv_hide_table = false;

function getUrlVars(url, n) {
	var vars = [], hash;
	var hashes = url.slice(url.indexOf('?') + 1).split('&');
	var i;
	for(i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars[n];
}

jQuery(document).ready(function($) {

	if (!dv_settings.serverside) {
		dv_settings.num_rows.values.push(-1);
		dv_settings.num_rows.labels.push('All');
	}

	/* Fix table width */
	$('#dv-spreadsheet-tbl').on('dv-table-size-change', function() {
		var table = $('#dv-spreadsheet-tbl');
		var content = $('#content').length !== 0 ? $('#content') : $('#component-body');
		var width_tbody = table.find('tbody').width() + 10;
		var width_wrap = content.width();
		var title_width = $('#dv_return_link_container').width()

		if (width_wrap < width_tbody && title_width < width_tbody) {
			content.width(width_tbody);
		} else if(width_wrap < title_width) {
			content.width(title_width + 50);
			if (title_width > width_tbody) {
				$("#dv-spreadsheet-tbl_wrapper").width(title_width);
				$("#dv-spreadsheet-tbl").width(title_width);
			}
		}

		$('.dataTables_wrapper').width($("#dv-spreadsheet-tbl tbody").width());
	});

	dv_table = $('#dv-spreadsheet-tbl').dataTable({
		"bFilter": true,
		"bInfo": true,
		"bJQueryUI": true,
		"bAutoWidth": true,
		"aaSorting": dv_data.aaSorting || [[0, 'asc']],
		"aaData": dv_data.aaData,
		"aoColumns": dv_data.aoColumns,
		"bProcessing": true,
		"bServerSide": dv_settings.serverside,
		"sAjaxSource": (dv_settings.serverside)? dv_settings.data_url + '&type=json&format=raw': null,
		"sDom": '<"H"lpf<"clear">>rt<"F"lip<"clear">>',
		"sPaginationType": "full_numbers",
		"iDisplayLength": +dv_settings.limit,
		"aLengthMenu": [dv_settings.num_rows.values, dv_settings.num_rows.labels],
		"fnDrawCallback": function() {

			// Update column styles
			$(dv_data.col_styles).each(function(idx, val) {
				if (val !== '') {
					$('#dv-spreadsheet-tbl tbody td:nth-child(' + (idx + 1) + ')').attr('style', val);
				}
			});

			$(dv_data.col_h_styles).each(function(idx, val) {
				if (val !== '') {
					$('#dv-spreadsheet-tbl thead th:nth-child(' + (idx + 1) + ')').attr('style', val);
				}
			});

			$('#dv-spreadsheet-tbl').trigger('dv-table-size-change');
			if (first_load || dv_settings.serverside) {
				update_pos();
				hightlight_keywords();
			}

			if (dv_settings.serverside) {
				$('.dv_header_select_all').each(function() {
					var id = $(this).val();
					if(dv.selected_cells && dv.selected_cells[id]) {
						for (i=0; i<dv.selected_cells[id].length; i++) {
							$('.' + id + ':checkbox[value="' + dv.selected_cells[id][i] + '"]').prop('checked', true);
						}

						if ($('.' + id + ':checkbox[checked=false]').length === 0) {
							$(this).prop('checked', true);
						} else {
							$(this).prop('checked', false);
						}
					}
				});

				$(document).trigger('dv_event_update_map');
			}

			/* Lazy loading dataviewer images */
			$('#dv-spreadsheet tbody .lazy-load').lazyload();

		},
		"fnInitComplete": function() {
			first_load = false;
			if(dv_data.charts) {
				draw_charts(dv_data.charts, this, 'dv_ss_charts_container');
			} else {
				$('#dv_hide_charts').hide();
			}
			$('#dv_ss_charts_container').hide();
		},
		"fnServerData": function(sSource, aoData, fnCallback) {
			if (!first_load) {
				var set = dv_table.fnSettings();
				for (i=0; i<set.aoColumns.length; i++) {
					var fieldtype = 'fieldtype_' + i;
					aoData.push({"name": fieldtype, "value": set.aoColumns[i]['sType']});
				}
			}

			$.ajax({
					"url": sSource,
					"data": aoData,
					"success": function(json) {
						dv_data = json;
						fnCallback(json);
					},
					"dataType": "json",
					"type": "POST",
					"cache": false,
					"error": function () {
						alert( "Error: Network or Data error!" );
					}
				});

		}
	});


	if (dv_settings.serverside) {
		var global_search = $('.dataTables_filter input');
		global_search.unbind('keyup').unbind('keypress');

		global_search.bind('keyup', function(e) {
			if (!dv_settings.serverside || e.keyCode === 13) {
				dv_table.fnFilter(this.value);
			}
		});
	}

	var ctrl_chars = {'!=': '', '>=': '', '<=': '', '>': '', '<': '', '!': '', '=': ''};
	var filter_key = '';
	$('tfoot input').bind('keyup filter-changed', function(e) {
		var idx = $('tfoot input').index(this);
		var val = this.value;
		if (e.keyCode === 38 || e.keyCode === 40) { return; }

		if (val in ctrl_chars) {
			filter_key = val;
			$(this).autocomplete('search', '');
			return;
		} else if (val.substring(0, 2) in ctrl_chars) {
			$(this).autocomplete('search', val.substring(2, val.length));
		} else if (val.substring(0, 1) in ctrl_chars) {
			$(this).autocomplete('search', val.substring(1, val.length));
		}

		column_filter(this, idx)
	});


	var filter_timeout;
	var timeout_delay = dv_settings.serverside ? 600 : 300;
	function column_filter(elem, idx) {

		if (filter_timeout) {
			clearTimeout(filter_timeout);
		}

		filter_timeout = setTimeout(function() {
			if ($(elem).data('filter-value') !== elem.value) {
				dv_table.fnFilter(elem.value, idx);
				$(elem).data('filter-value', elem.value);
			}
		}, timeout_delay);
	}

	$('tfoot input').on('focus', function() {

		$(this).parent().find('span.dv-col-clear-filter').css('color', '#000;');

		if (!dv_settings.show_filter_options) { return; };

		$(this).autocomplete('option', 'source', dv_table.fnGetColumnData($('tfoot input').index(this)));
		$(this).autocomplete('option', 'disabled', false);
		$(this).autocomplete('search', '');
	});

	$('tfoot input').on('blur', function() {
		var col_idx = $('tfoot input').index(this);
		var field = $('tfoot input').get(col_idx);
		settings = dv_table.fnSettings();

		$(this).parent().find('span.dv-col-clear-filter').css('color', '#FFF;');

		$(this).trigger('filter-changed');

	});

	$('tfoot input').each(function(i) {
		$(this).data('filter-value', '');
 
		if (dv_settings.show_filter_options) {
			$(this).autocomplete({
				disabled: true,
				minLength: 0,
				close: function() {
					$(this).trigger('filter-changed');
					filter_key = '';
				},
				select: function(e, ui) {
					ui.item.value = filter_key + ui.item.value;
					filter_key = '';
				},
				source: dv_table.fnGetColumnData(i)
			});
		}
	});

	/* Clear filters */
	$('#dv-btn-filter-clear-all').click(function() {
		dv_table.fnFilterClear();
	});

	/* Clear column filter */
	$('.dv-col-clear-filter').on('click', function() {
		var col = $(this).parent().find('input[type="text"]')[0];

		if (col.value == '') {
			return false;
		}

		col.value = '';
		col.className = 'search_init';

		$(this).trigger('filter-changed');
	});


	// Highlight keywords [Disabled for now]
	function hightlight_keywords() {
		return;
		if (!dv_table) {	// When not serverside!
			return;
		}

		settings = dv_table.fnSettings();

		dv_table.fnGetNodes().each(function(row) {
			$(row).find('td').each(function() {
				var skw = $(".dataTables_filter input").attr('value');
				$(this).highlightRegex(new RegExp(skw, 'ig'), 'dv_highlight_search');

				idx = $(this).index();
				if (settings.aoPreSearchCols[idx]) {
					var kw = settings.aoPreSearchCols[idx].sSearch;
					$(this).highlightRegex(new RegExp(kw, 'ig'), 'dv_highlight_filter');
				}
			});
		});
	}


	//More info.
	function dv_render_more_info(url) {
		var res = dv.more_info[url];
		var mi_dialog = $('#more_information').empty();

		if(res.aaData.length > 0) {
			data = '<table class="more_info_table"><tbody>';

			for (i=0; i<res['aoColumns'].length; i++) {
				data += '<tr>';
				data += '<td>' + res['aoColumns'][i].sTitle + '</td>';

				for (j=0; j<res['aaData'].length; j++) {
					data += '<td>' + res['aaData'][j][i] + '</td>';
				}
				data += '</tr>';
			}

			var view_all_url = (url.split('?id=')[0]).replace('/dataviewer/data/', '/dataviewer/view/').replace('/json/', '/');

			data += '</tbody></table>';
			data += '<br />';
			data +=  '<p style="float: right;">';
			data +=  '<a style="color: #44AA44; font-weight: bold;" href="' + view_all_url + '" target="_blank">Click here to view all.</a>';
			data +=  '</p>';

			mi_dialog.html(data);

			mi_dialog.css('max-height', ($(window).height() - 80) + 'px');

			mi_dialog.css('max-width', ($(window).width() - 50) + 'px');

			mi_dialog.dialog({
				width: 'auto',
				title: res.title,
				modal: true
			}).find('.dv_image').lazyload();
			
		}
	}

	$(document).on('click', '.more_info, .more_info_multi', function(e) {
		var result;
		var url = $(this).attr('href');

		e.preventDefault();

		dv.more_info = dv.more_info || {};

		if (typeof dv.more_info[url] == 'undefined') {
			result = $.parseJSON($.ajax({ type: 'POST', url: url, async: false }).responseText);
			dv.more_info[url] = result;
		}

		dv_render_more_info(url);

		return false;
	});

	// Select cells
	function dv_select_cell(chk, checked) {
		dv.selected_cells = dv.selected_cells || {};
		var col = chk.data('col-id');

		if (checked) {
			dv.selected_cells[col] = dv.selected_cells[col] || [];
			dv.selected_cells[col].push(chk.val());
		} else {
			dv.selected_cells[col].splice($.inArray(chk.val(), dv.selected_cells[col]), 1);
		}
	}

	$(document).on('change', 'input.select-cell', function() {
		dv_select_cell($(this), $(this).is(':checked'));
	});

	// Select all cells
	$('input.dv-select-all').click(function(e) {
		var th = $(this).closest('th');
		var idx = $(this).closest('tr').find('th').index(th);
		var checked = $(this).prop('checked');
		$(dv_table.fnGetNodes()).find('td:eq(' + idx + ') input:checkbox').each(function() {
			var chk = checked;
			$(this).prop('checked', chk);
			dv_select_cell($(this), chk);
		});

		e.stopPropagation();
	});

	$(document).on('change', 'input.select-cell, input.dv-select-all', function() {
		var col_id = $(this).data('col-id');
		var btn = $('button.dv-compare[data-col-id="' + col_id + '"]');

		if($(this).is(':checked') || (dv.selected_cells[col_id] && dv.selected_cells[col_id].length > 0)) {
			btn.attr('title', 'Click here to compare selected items');
			btn.addClass('btn-success').prop('disabled', false);
		} else {
			btn.attr('title', 'Select one or more items');
			btn.removeClass('btn-success').prop('disabled', true);
		}
	});

	$('button.dv-compare').click(function(e) {
		var id = $(this).data('col-id');
		var url = $(this).data('link');
		var ids = '';

		if (!dv.selected_cells[id] || dv.selected_cells[id].length <= 0) {
			alert('Please select at least one item');
			return false;
		}

		var cells = dv.selected_cells[id];
		for (i = 0; i < cells.length; i++) {
			url += cells[i] + ",";
		}

		url = url.slice(0, (url.length - 1));

		dv.more_info = dv.more_info || {};

		if (typeof dv.more_info[url] == 'undefined') {
			result = $.parseJSON($.ajax({ type: 'POST', url: url, async: false }).responseText);
			dv.more_info[url] = result;
		}

		dv_render_more_info(url);

		return false;
	});





	$('.collapsible-button').toggle(function() {
		$(this).children().switchClass('ui-icon-plus', 'ui-icon-minus');
		$(this).parent().children('.collapsible').show('slide');
	}, function() {
		$(this).children().switchClass('ui-icon-minus', 'ui-icon-plus');
		$(this).parent().children('.collapsible').hide('slide');
	});

	// Image previews
	$(document).on('click', '.ss_image.img_expand', function() {
		window.open($(this).attr('src'));
	});

	$(document).on('mouseover mouseout', '.dv_img_preview', function(event) {
		if (event.type === 'mouseover') {
			$('body').append("<div style='position: absolute; z-index: 9999;' id='dv_img_preview'><img src='" + $(this).data('preview-img') + "' alt='Loading preview image...' id='dv_preview_image' class='shadow' /></div>");
			$('#dv_img_preview').show().position({
				my: "left top",
				at: 'left top',
				of: $(this),
				offset: '25',
				collision: 'fit flip'
			});
		} else {
			$('#dv_img_preview').remove();
		}
	});

	// Truncated text
	$(document).on('click', 'tbody .truncate', function() {
		$('#truncated_text_dialog').html(linkify($(this).html()));
		$('#truncated_text_dialog').dialog({
			title: 'Full Text',
			maxHeight: $(window).height(),
			maxWidth: $(window).width(),
			height: 'auto'
		});

		$('#truncated_text_dialog').dialog('option', 'width', '550');
		if ($('#truncated_text_dialog').height() > $(window).height()) {
			$('#truncated_text_dialog').dialog('option', 'height', ($(window).height()-10));
		}
	});

	/* Handle links in truncated cells */
	$(document).on('click', 'tbody .truncate a', function(e) {
		e.stopPropagation();
	});




	$(document).on('click', '.dv_tools_launch_link', function(e) {
		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', 'Tools launch', $(this).attr('href'));
		}
	});

	$(document).on('click', '.dv_tools_dl_link', function(e) {
		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', 'Tools datafile download', $(this).data('data-file'));
		}
	});

	// Update tools download multiple
	$('.dv_tools_down_multi').click(function(e) {

		var id = $(this).parent().children('.dv_header_select_all').val();
		var url = $(this).attr('href');
		var df_list = '';
		url = url.slice(0, url.lastIndexOf('='));
		url += '=';

		if ($(dv_table.fnGetNodes()).find('.' + id + ':checkbox:checked').length <= 0) {
			alert('Please select at least one file');
			return false;
		}
		$(dv_table.fnGetNodes()).find('.' + id + ':checkbox:checked').each(function() {
			var link = $(this).parent().children('a.dv_tools_dl_link').attr('href');
			var hash = getUrlVars(link, 'hash');
			url += hash + ',';
			df_list += $(this).val() + ',';
		});

		url = url.slice(0, (url.length-1));

		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', 'Tools datafile (multiple)', df_list);
		}

		$(this).attr('href', url);

		e.stopPropagation();
	});

	// Update tools launch multiple
	$('.dv_tools_launch_multi').click(function(e) {
		var id = $(this).parent().children('.dv_header_select_all').val();
		var url = $(this).attr('href');
		url = url.slice(0, url.lastIndexOf('='));
		url += '=';

		if ($(dv_table.fnGetNodes()).find('.' + id + ':checkbox:checked').length <= 0) {
			alert('Please select at least one file');
			return false;
		}

		$(dv_table.fnGetNodes()).find('.' + id + ':checkbox:checked').each(function() {
			url += $(this).val() + ',';
		});

		url = url.slice(0, (url.length-1));

		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', 'Tools launch (multiple)', url);
		}

		$(this).attr('href', url);
		e.stopPropagation();
	});


	// dv-custom-field-link
	var dv_cfl_list = {};
	$(document).on('click', 'input:checkbox.dv-custom-field-link', function() {
		var id = $(this).data('col-id');
		var base = $('#' + id).data('link-base');
		var append = $(this).data('url-append');

		if (typeof dv_cfl_list[id] == 'undefined') {
			dv_cfl_list[id] = [];
		}

		if ($(this).is(':checked')) {
			dv_cfl_list[id].push($(this).val());
		} else {
			dv_cfl_list[id].splice(dv_cfl_list[id].indexOf($(this).val()), 1);
		}

		var url = base + dv_cfl_list[id].join(',');
		$('#' + id).attr('href', url + append);

		if (dv_cfl_list[id].length < 1) {
			$('#' + id).hide();
		} else {
			$('#' + id).show();
		}
	});

	$('.dv-multi-link').click(function(e) {
		e.stopPropagation();
	});

	// Table Hide/Show
	if ($('#dv-spreadsheet-container:visible').length > 0) {
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Hide table');
		});
	} else {
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Show table');
		});
	}

	$('.dv_toggle_data_btn').click(function() {
		$('#dv-spreadsheet-container').toggle();
		if ($('#dv-spreadsheet-container:visible').length > 0) {
			$('.dv_toggle_data_btn').each(function() {
				$(this).val('Hide table');
			});
		} else {
			$('.dv_toggle_data_btn').each(function() {
				$(this).val('Show table');
			});
		}
		update_pos();
	});

	if (typeof dv_settings['hide_data'] != 'undefined' && dv_settings['hide_data']) {
		$('#dv-spreadsheet-container').hide();
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Show table');
		});
	}

	// Search and Page number position
	function update_pos() {
		var left_min = 200;
		var scroll_left = $(document).scrollLeft();
		var ww = $(window).width();
		var sw = $('#dv-spreadsheet-tbl_wrapper.dataTables_wrapper').width();

		var sstm = parseInt((scroll_left + $('#dv-spreadsheet-tbl_wrapper .dataTables_filter').position().left)/2) - 110;

		var searchw = $('#dv-spreadsheet-tbl_wrapper .dataTables_filter').width();

		var ssr = scroll_left + ww - searchw - 40;
		ssr = ((sstm + 450) < ssr) ? ssr : sstm + 450;
		if ((ssr + searchw) > sw) {
			ssr = sw - searchw - 5;
		}

		$('#dv-spreadsheet-tbl_wrapper .dataTables_filter').css('left', ssr + 'px');

		var pagew = $('#dv-spreadsheet-tbl_wrapper .dataTables_paginate').last().width();
		var spr = scroll_left + ww - pagew - 45;
		spr = ((sstm + 400) < spr) ? spr : sstm + 400;
		if ((spr + pagew) > sw) {
			spr = sw - pagew - 45;
		}

		$('#dv-spreadsheet-tbl_wrapper .dataTables_paginate').last().css('left', spr + 'px');

		sstm = (left_min < sstm) ? sstm : left_min;

		var ssbm = parseInt((scroll_left + $('#dv-spreadsheet-tbl_wrapper .dataTables_paginate').last().position().left)/2) - 80;

		$('#dv-spreadsheet-tbl_wrapper .dataTables_paginate').first().css('left', sstm + 'px');
		$('#dv-spreadsheet-tbl_wrapper .dataTables_info').last().css('left', ssbm + 'px');
		$('#dv_ss_charts_container .dv_ss_charts').css("left", scroll_left + 20 + 'px');

		move_filters();
	}

	function move_filters() {
		// Start with Filters at the top
		if ($('#dv-spreadsheet-tbl tbody').offset().top > $(window).scrollTop()) {
			$('#dv-spreadsheet-tbl tfoot').css('display', 'table-header-group');
		} else {
			$('#dv-spreadsheet-tbl tfoot').css('display', 'table-footer-group');
		}

	}

	$('select[name="spreadsheet_length"]').change(function() {
		move_filters();
	});

	$(window).bind('scroll resize', function() {
		update_pos();
	});

	// Quick-tip
//	$('.quick_tip').tipsy({gravity: 'nw', live: true});
	$(document).on('click', '.quick_tip', function(e) {
		if ($(this).attr('original-title') !== '') {
			$('#truncated_text_dialog').html($(this).attr('original-title').replace(/  /g, "&nbsp;&nbsp;").replace(/\n/g, "<br />"));
		} else {
			$('#truncated_text_dialog').html("No description available.");
		}
		$('#truncated_text_dialog').dialog({
			title: $(this).text()
		});

		e.stopPropagation();
	});

	// Launch DV Gallery
	$(document).on('click', '.dv_gallery_link', function() {
		var window_opts = "toolbars=no,menubar=no,location=no,scrollbars=no,resizable=yes,status=no,height=670,width=800";
		window.open(this.href, '', window_opts);
		return false;
	});

	//Column descriptions
	$('#dv-spreadsheet-tbl thead th .colum-label-text').click(function(e) {
		if ($(this).attr('title') !== '') {
			$('#truncated_text_dialog').html(linkify($(this).attr('title').replace(/  /g, "&nbsp;&nbsp;").replace(/\n/g, "<br />")));
		} else {
			$('#truncated_text_dialog').html("No description available for this column.");
		}
		$('#truncated_text_dialog').dialog({
			title: $(this).text(),
			width: 360
		});

		e.stopPropagation();
	});

	// Next/Previous page
	$(document).on('keydown', function(event) {
		if (event.ctrlKey && event.shiftKey) {
			switch(event.keyCode) {
				case 37:
					event.preventDefault();
					window.scroll(window.scrollX - $(window).width(), window.scrollY);
					event.stopPropagation();
					break;
				case 39:
					event.preventDefault();
					window.scroll(window.scrollX + $(window).width(), window.scrollY);
					event.stopPropagation();
					break;
			}
		} else if (event.ctrlKey) {
			switch(event.keyCode) {
				case 37:
					$('#dv-spreadsheet-tbl_previous').trigger('click');
					event.stopPropagation();
					break;
				case 38:
					$('#dv-spreadsheet-tbl_last').trigger('click');
					event.stopPropagation();
					break;
				case 39:
					$('#dv-spreadsheet-tbl_next').trigger('click');
					event.stopPropagation();
					break;
				case 40:
					$('#dv-spreadsheet-tbl_first').trigger('click');
					event.stopPropagation();
					break;
				case 122:
					$('#dv_fullscreen').trigger('click');
					event.stopPropagation();
					break;
			}
		}
	});

	//Show help page
	$('#dv_show_help').click(function() {
		$('#dv_help_dialog').dialog({
			title: 'Help: ' + $('#dv_title').text(),
			height: 500,
			width: 800
		});
	});

	// Remove top-left corner
	$('.dataTables_wrapper div.ui-corner-tl').removeClass('ui-corner-tl');

	// Show filter dialog
	var filters = [];
	var tpl_title = '<li><a href="' + window.location.href + '#dv-filter-tab-{id}">{name}</a></li>';
	var tpl_field = '<tr><td>{field_name}</td><td><input type="text" class="filter_dialog_field" data-column-index="{idx}" data-column-id="{col_id}" data-filter_hint="{hint}" /></td></tr>';


	if(dv_data.filters.length > 0) {

		// Show filters button
		$('#ss_title').append('&nbsp;&nbsp;<input type="button" value="Show Filters" id="dv_show_filters">');
		$(document).on('click', '#dv_show_filters', function() {
			$('#dv_filters_dialog').dialog('open');
		});


		$('#dv_filters_tabs').tabs();

		for (i=0; i < dv_data.filters.length; i++) {
			var filter_div = '<div id="dv-filter-tab-' + i + '"><table>';
			var col_count = 0;

			for (j=0; j<dv_data.filters[i].cols.length; j++) {
				var col_id = dv_data.filters[i].cols[j];

				if (dv_data.cols.visible.indexOf(col_id) !== -1) {
					col_count = col_count + 1;

					var hint = ('' + $('#dv-spreadsheet-tbl tfoot th input:eq(' + dv_data.cols.visible.indexOf(col_id) + ')').attr('title')).replace(/  /g, "&nbsp;&nbsp;").replace(/\n/g, "<br />");
					filter_div = filter_div + tpl_field.supplant({'field_name': dv_data.col_labels[dv_data.cols.visible.indexOf(col_id)].replace(/<br \/>/g,'&nbsp;').replace(/<hr \/>/g,'/').stripTags(), 'idx': dv_data.cols.visible.indexOf(col_id), 'col_id': col_id, 'hint': hint});
				}
			}

			if (col_count > 0) {
				filter_div = filter_div + '</table><br /><input type="button" data-filter_hint="" value="Filter" class="filter_button" style="float: right; margin: 0px 5px;"><input data-filter_hint="" type="button" value="Done" class="filter_done_button" style="float: right; margin: 0px 5px;"><br /></div>';
				$('#dv_filters_tabs').append(filter_div);
				$('#dv_filters_tabs ul').append(tpl_title.supplant({'id': i, 'name': dv_data.filters[i].filter_name}));
			}
			
		}
		
		$('#dv_filters_tabs').tabs("refresh").tabs( 'option', "active", 0);


		$(document).on('focus', '.filter_dialog_field', function() {
			var idx = $(this).data('column-index');
			$(this).autocomplete({
				minLength: 0,
				close: function() {$(this).trigger('filter');},
//				close: function() {$(this).trigger('filter-changed');},
				source: dv_table.fnGetColumnData(idx)
			});
		});


		$('.filter_dialog_field').focus(function() {
			$(this).autocomplete('search', '');
		});

		$('.filter_dialog_field').bind('filter', function(e) {
			var col_idx = $(this).data('column-index');
			var field = $('tfoot input').get(col_idx);
			settings = dv_table.fnSettings();

			field.value = $(this).val();
			settings.aoPreSearchCols[col_idx].sSearch = $(this).val();
			settings.aoPreSearchCols[col_idx].bRegex = false;
			settings.aoPreSearchCols[col_idx].bSmart = true;

			if (dv_settings.serverside) {
				dv_table.fnFilter(this.value, col_idx);
			} else {
				var fieldtype = dv_data.aoColumns[col_idx]['sType'];
				var filter_str = this.value;

				if (fieldtype == 'int' || fieldtype == 'real' || fieldtype == 'float' || fieldtype == 'number') {
					filter_str = filter_str.toLowerCase();
				}
				dv_table.fnFilter(filter_str, col_idx);

			}
		});

		$('.filter_dialog_field').keypress(function(e) {
			if (e.keyCode === 13) {
				$(this).parent().find('.filter_dialog_field').each(function() {
					$(this).trigger('filter');
					var idx = $(this).data('column-index');
					$(this).autocomplete({
						minLength: 0,
						source: dv_table.fnGetColumnData(idx)
					});
				});
			}
			if ($(this).val() == '') {
				$(this).autocomplete('search', '');
			}
		});

		$('.filter_button').click(function() {
			$(this).parent().find('.filter_dialog_field').each(function() {
				$(this).trigger('filter');
			});
		});

		$('.filter_done_button').click(function() {
			$(this).parent().find('.filter_dialog_field').each(function() {
				$(this).trigger('filter');
			});

			$('#dv_filters_dialog').dialog('close');
		});

/*
		$('#dv_filters_tabs input').tipsy({
			gravity: 'w',
			live: true,
			title: 'data-filter_hint',
			html: true
		});
*/

		$('#dv_filters_dialog').dialog({
			modal: true,
			width: 'auto',
			height: 'auto',
			autoOpen: dv_show_filters,
			open: function() {$('#dv_filters_tabs').trigger('tabsshow');}
		});

		// Keep filter dialog box centered & correct size
		$('#dv_filters_tabs').bind('tabsshow', function() {
			if ($(window).height() < $('#dv_filters_dialog').parent().height()) {
				$('#dv_filters_dialog').dialog('option', 'height', $(window).height() - 20);
				$('#dv_filters_dialog').dialog('option', 'width', 'auto');
			} else {
				$('#dv_filters_dialog').dialog('option', 'height', 'auto');
			}

			$('#dv_filters_dialog').dialog('option', 'position', 'center');
		});


		$('#dv-btn-filters').on('click', function() {
			$('#dv_filters_dialog').dialog('open');
		});
	}




	/* Downloading data view data */
	$('button.dv-btn-download').on('click', function() {
		var dl_form = $('#dv-spreadsheet-dl');
		var format = $(this).data('format');
		var set = dv_table.fnSettings();
		var iColumns = set.aoColumns.length;
		var data = [];
		var i;

		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', format + ' downloads', dl_form.attr('action'));
		}

		/* Paging and general */
		data.push({ "name": "iColumns", "value": iColumns });
		data.push({ "name": "iDisplayStart", "value": set._iDisplayStart });
		data.push({ "name": "iDisplayLength", "value": set.oFeatures.bPaginate !== false ? set._iDisplayLength : -1 });

		/* Filtering */
		if (set.oFeatures.bFilter !== false) {
			data.push({ "name": "sSearch", "value": set.oPreviousSearch.sSearch });
			data.push({ "name": "bRegex", "value": set.oPreviousSearch.bRegex });
			for (i=0; i<iColumns; i++) {
				data.push({ "name": "sSearch_" + i, "value": set.aoPreSearchCols[i].sSearch });
				data.push({ "name": "bRegex_" + i, "value": set.aoPreSearchCols[i].bRegex });
				data.push({ "name": "bSearchable_" + i, "value": set.aoColumns[i].bSearchable });
			}
		}

		for (i=0; i<set.aoColumns.length; i++) {
			var fieldtype = 'fieldtype_' + i;
			data.push({"name": fieldtype, "value": set.aoColumns[i]['sType']});
		}

		/* Sorting */
		if (set.oFeatures.bSort !== false) {
			var iFixed = set.aaSortingFixed !== null ? set.aaSortingFixed.length: 0;
			var iUser = set.aaSorting.length;

			data.push({ "name": "iSortingCols",   "value": iFixed+iUser });

			for (i=0; i<iFixed; i++) {
				data.push({ "name": "iSortCol_" + i,  "value": set.aaSortingFixed[i][0] });
				data.push({ "name": "sSortDir_" + i,  "value": set.aaSortingFixed[i][1] });
			}

			for (i=0; i<iUser; i++) {
				data.push({ "name": "iSortCol_" + (i + iFixed),  "value": set.aaSorting[i][0] });
				data.push({ "name": "sSortDir_" + (i + iFixed),  "value": set.aaSorting[i][1] });
			}

			for (i=0; i<iColumns; i++) {
				data.push({ "name": "bSortable_" + i,  "value": set.aoColumns[i].bSortable });
			}
		}

		form_elements = '<input type="hidden" name="type" value="' + format + '" />';

		for (i=0; i< data.length; i++) {
			form_elements += '<input type="hidden" name="' + data[i].name + '" value="' + data[i].value + '" />';
		}

		dl_form.empty().append(form_elements).submit();

		$('label[for="dv_download"]').removeClass('ui-state-active');
	});


	/* Toggle Fullscreen */
	$('#dv-btn-fullscreen').on('click', function() {
		var mode = $(this).data('screen-mode');
		var content = $('#content');

		if (mode === 'full') {
			$(this).removeClass('btn-inverse');
			content.removeClass('fullscreen');
			$('#header').show();
			$(this).data('screen-mode', '').find('.lbl').text('Fullscreen');
		} else {
			$(this).addClass('btn-inverse');
			content.addClass('fullscreen');
			$('#header').hide();
			$(this).data('screen-mode', 'full').find('.lbl').text('Exit Fullscreen');
		}
	});

	/* Toggle Text Wrap */
	$('#dv-btn-no-wrap').on('click', function() {
		var current = $(this).data('current');
		var cells = $('#dv-spreadsheet-tbl>tbody>tr>td');

		if ($('#dv-cell-text-wrap').length < 1) {
			$('body').append('<style id="dv-cell-text-wrap">#dv-spreadsheet-tbl>tbody>tr>td {white-space: nowrap; width: auto;}</style>');
		}

		if (current === 'normal') {
			$('#dv-cell-text-wrap').html('#dv-spreadsheet-tbl>tbody>tr>td {white-space: nowrap; width: auto;}');

			$(this).data('current', 'nowrap').find('.lbl').text('Clear No-Wrap');
			$(this).addClass('btn-inverse');
			cells.css('white-space', 'nowrap');
		} else {
			$('#dv-cell-text-wrap').html('#dv-spreadsheet-tbl>tbody>tr>td {white-space: normal;}');

			$(this).data('current', 'normal').find('.lbl').text('No-Wrap');
			$(this).removeClass('btn-inverse');
			cells.css('white-space', 'normal');
		}

		$('#dv-spreadsheet-tbl').trigger('dv-table-size-change');

	});

	// Open links in a popup window
	$(document).on('click', 'a.dv-popup', function() {
		var url = $(this).attr('href');
		var name = $(this).data('popup-name');
		var features = $(this).data('popup-features');
		window.open(url, name, features);
		return false;
	});

});

String.prototype.dv_replace = function ($, arr) {
	return this.replace(/{([^{}]*)}/g,
		function (a, b) {
			var r, type;

			b = b.split('|');
			type = (typeof b[1] != 'undefined')? b[1]: '';
			b = b[0];

			if (type == 'html') {
				r = arr[$.inArray(b, dv_data.vis_cols)];
			} else {
				r = ("" + arr[$.inArray(b, dv_data.vis_cols)]).stripTags();
			}

			return (typeof r === 'string' || typeof r === 'number') && r != 'undefined' ? r : '[-]';
		}
	);
};
