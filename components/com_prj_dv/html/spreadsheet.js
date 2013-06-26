/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

var dv_table;
var dv_data;
var dv_settings;
var dl_vars;
var dv_charts = [];
var dv_compare_lists = [];
var asInitVals = [];
var first_load = true;
var dbg;
var dv_hide_table = false;
var is_IE_six = (navigator.userAgent.indexOf("MSIE 6.") !== -1);

jQuery.fn.dataTableExt.oApi.fnGetFilteredData = function(oSettings) {
	var a = [];
	var i;
	for (i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++) {
		a.push(oSettings.aoData[ oSettings.aiDisplay[i] ]._aData);
	}
	return a;
};

jQuery.fn.dataTableExt.oSort['number-asc']  = function(a,b) {

	if (a === b) { return 0; }
	if (jQuery(a).text().trim() === '-') { return -1; }
	if (jQuery(b).text().trim() === '-') { return 1; }

	var x = a.stripTags();
	var y = b.stripTags();

	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['number-desc'] = function(a,b) {

	if (a === b) { return 0; }
	if (jQuery(a).text().trim() === '-') { return 1; }
	if (jQuery(b).text().trim() === '-') { return -1; }

	var x = a.stripTags();
	var y = b.stripTags();

	x = parseFloat( x );
	y = parseFloat( y );

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['numrange-asc']  = function(a,b) {
	if (a === b) { return 0; }
	if (jQuery(a).text() === '-') { return -1; }
	if (jQuery(b).text() === '-') { return 1; }

	var x_min = +jQuery(a).data('min');
	var x_max = +jQuery(a).data('max');
	var y_min = +jQuery(b).data('min');
	var y_max = +jQuery(b).data('max');

	if (x_max === y_max) {
		y = y_min;
	} else {
		y = y_max;
	}

	x = x_max;

	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['numrange-desc']  = function(a,b) {
	if (a === b) { return 0; }
	if (jQuery(a).text() === '-') { return 1; }
	if (jQuery(b).text() === '-') { return -1; }

	var x_min = +jQuery(a).data('min');
	var x_max = +jQuery(a).data('max');
	var y_min = +jQuery(b).data('min');
	var y_max = +jQuery(b).data('max');

	if (x_min === y_min) {
		y = y_max;
	} else {
		y = y_min;
	}

	x = x_min;

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};


jQuery.fn.dataTableExt.oSort['datetime-desc'] = jQuery.fn.dataTableExt.oSort['date-desc'];
jQuery.fn.dataTableExt.oSort['datetime-asc'] = jQuery.fn.dataTableExt.oSort['date-asc'];


jQuery.fn.dataTableExt.ofnSearch['number'] = function (data) {
	return data.replace(/\n/g, " ").replace(/&nbsp;/g, "").stripTags();
};

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

	dv_table = $('#spreadsheet').dataTable({
		"bFilter": true,
		"bInfo": true,
		"bJQueryUI": true,
		"bAutoWidth": true,
		"aaSorting": dv_data.aaSorting,
		"aaData": dv_data.aaData,
		"aoColumns": dv_data.aoColumns,
		"bProcessing": true,
		"bServerSide": dv_settings.serverside,
		"sAjaxSource": (dv_settings.serverside)? dv_settings.url + '&format=json': null,
		"sDom": '<"H"lpf<"clear">>rt<"F"lip<"clear">>',
		"sPaginationType": "full_numbers",
		"iDisplayLength": dv_settings.limit,
		"aLengthMenu": [dv_settings.num_rows.values, dv_settings.num_rows.labels],
		"fnDrawCallback": function() {
				if ($('body').width() < ($("#spreadsheet tbody").width() + 100)) {
					$('body').width($("#spreadsheet tbody").width() + 100);
				}

				$('.dataTables_wrapper').width($("#spreadsheet tbody").width());

				if (first_load || dv_settings.serverside) {
					update_pos();
					hightlight_keywords();
				}

				if (dv_settings.serverside) {
					$('.dv_header_select_all').each(function() {
						var id = $(this).val();
						if(dv_compare_lists[id]) {
							for (i=0; i<dv_compare_lists[id].length; i++) {
								$('.' + id + ':checkbox[value="' + dv_compare_lists[id][i] + '"]').prop('checked', true);
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

				// Update column styles
				$(dv_data.col_styles).each(function(idx, val) {
					if (val !== '') {
						$('#spreadsheet tbody td:nth-child(' + (idx + 1) + ')').attr('style', val);
					}
				});

				$(dv_data.col_h_styles).each(function(idx, val) {
					if (val !== '') {
						$('#spreadsheet thead th:nth-child(' + (idx + 1) + ')').attr('style', val);
					}
				});
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

	$('.dv_download_button').click(function(e) {
		var format = $(this).data('format');
		var set = dv_table.fnSettings();
		var iColumns = set.aoColumns.length;
		var data = [];
		var i;

		e.stopPropagation();
		e.preventDefault();

		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', format + ' downloads', $('#ss_download_form').attr('action'));
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

		$('#ss_download_form').empty();
		for (i=0; i< data.length; i++) {
			$('#ss_download_form').append('<input type="hidden" name="' + data[i].name + '" value="' + data[i].value + '" />');
		}

		$('#ss_download_form').append('<input type="hidden" name="format" value="' + format + '" />');
		$('#ss_download_form').submit();

		$('#dv_download').prop('checked', false);
		$("label[for='dv_download']").removeClass('ui-state-active');

		return false;
	});

	if (dv_settings.serverside) {
		$(".dataTables_filter input").unbind('keyup');
		$(".dataTables_filter input").unbind('keypress');

		$(".dataTables_filter input").keypress( function(e) {
			if (!dv_settings.serverside || e.keyCode === 13) {
				dv_table.fnFilter($(".dataTables_filter input").attr('value'));
			}
		});
	}

	var ctrl_chars = {'!=': '', '>=': '', '<=': '', '>': '', '<': '', '!': '', '=': ''};
	var filter_key = '';
	$("tfoot input").bind('keyup', function(e) {
		var idx = $("tfoot input").index(this);
		if (e.keyCode === 38 || e.keyCode === 40 || asInitVals[idx] === this.value) { return; }

		if (this.value in ctrl_chars) {
			filter_key = this.value;
			$(this).autocomplete("search", '');
		} else if (this.value.substring(0, 2) in ctrl_chars) {
			dv_table.fnFilter(this.value, idx);
			$(this).autocomplete("search", this.value.substring(2, this.value.length));
		} else if (this.value.substring(0, 1) in ctrl_chars) {
			dv_table.fnFilter(this.value, idx);
			$(this).autocomplete("search", this.value.substring(1, this.value.length));
		} else {
			dv_table.fnFilter(this.value, idx);
		}
	});

	$("tfoot input").bind('filter-changed', function(e) {
		if (asInitVals[$("tfoot input").index(this)] !== this.value) {
			var idx = $("tfoot input").index(this);
			dv_table.fnFilter(this.value, idx);
		}
	});

	$("tfoot input").focus(function() {
		if ($(this).hasClass("search_init")) {
			this.className = "";
			this.value = "";
			if (!dv_settings.show_filter_options) { return; };
			$(this).autocomplete("option", "source", dv_table.fnGetColumnData($("tfoot input").index(this)));
			$(this).autocomplete("option", "disabled", false);
			$(this).autocomplete("search", '');
		} else {
			if (!dv_settings.show_filter_options) { return; };
			$(this).autocomplete("option", "source", dv_table.fnGetColumnData($("tfoot input").index(this)));
			$(this).autocomplete("option", "disabled", false);
			$(this).autocomplete("search", '');
		}
	});

	$("tfoot input").each(function(i) {
		asInitVals[i] = this.value;
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

	$("tfoot input").blur(function() {
		var col_idx = $("tfoot input").index(this);
		var field = $("tfoot input").get(col_idx);
		settings = dv_table.fnSettings();

		if (this.value !== '' && this.value !== "!=" && this.value !== ">=" && this.value !== "<=" && this.value !== ">" && this.value !== "<" && this.value !== "=" && this.value !== "!") {
			$(this).removeClass("search_init");
			settings.aoPreSearchCols[col_idx].sSearch = this.value;
			settings.aoPreSearchCols[col_idx].bRegex = false;
			settings.aoPreSearchCols[col_idx].bSmart = true;
		} else {
			$(this).addClass("search_init");
			field.value = asInitVals[$("tfoot input").index(field)];
			settings.aoPreSearchCols[col_idx].sSearch = '';
			settings.aoPreSearchCols[col_idx].bRegex = false;
			settings.aoPreSearchCols[col_idx].bSmart = true;
		}
	});


	$(".ss_sg_input").bind('blur keypress', function(e) {
		settings = dv_table.fnSettings();
		var sg = dv_settings.sg[$(this).attr('name')];

		for (i=0; i<sg.columns.length; i++) {

			var col_idx = sg.columns[i]['idx'];
			var field = $("tfoot input").get(col_idx);

			if ($(this).val() !== '') {
				field.className = "";
				field.value = $(this).val();

				settings.aoPreSearchCols[col_idx].sSearch = $(this).val();
				settings.aoPreSearchCols[col_idx].bRegex = false;
				settings.aoPreSearchCols[col_idx].bSmart = true;
			} else {
				field.className = "search_init";
				field.value = asInitVals[$("tfoot input").index(field)];

				settings.aoPreSearchCols[col_idx].sSearch = '';
				settings.aoPreSearchCols[col_idx].bRegex = false;
				settings.aoPreSearchCols[col_idx].bSmart = true;
			}
		}

		if (e.keyCode === 13 || e.keyCode === 9) {
			dv_table.fnFilter($(this).val(), sg.columns[0]['idx']);
		}
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

	// Clear filters
	function clear_column_filters() {
		$("tfoot input").each(function() {
			var col_idx = $("tfoot input").index(this);
			this.value = "";
			this.className = "search_init";
			this.value = asInitVals[col_idx];

			settings = dv_table.fnSettings();
			settings.aoPreSearchCols[col_idx].sSearch = '';
			settings.aoPreSearchCols[col_idx].bRegex = false;
			settings.aoPreSearchCols[col_idx].bSmart = true;
		});

		$("#ss_sg input").each(function() {
			this.value = "";
		});
	}

	$('#clear_column_filters').click(function() {
		clear_column_filters();
		dv_table.fnFilter('', 0);
	});

	// Clear all filters
	$('#clear_all_filters').click(function() {
		clear_column_filters();
		dv_table.fnFilter('');
	});


	// More information
	$('.more_info').live('click', function(e) {
		e.stopPropagation();
		e.preventDefault();
		url = $(this).attr('href');
		obj = getUrlVars(url, 'obj');
		var id = '';
		try {
			id = getUrlVars(url, 'id');
		} catch (err) {
			id = '';
		}

		var tmp_table;
		var more_info_id = ('more_info_' + obj + id).replace(/\./g, '_');
		if ($('#' + more_info_id).html() === null) {

			var res = $.parseJSON($.ajax({ type: "GET", url: url, async: false }).responseText);

			if (res.aaData.length > 1) {
				$('#more_information').append('<div id="' + more_info_id + '" style="overflow: auto;" title="More Information"></div>');
				$('#' + more_info_id).append('<div id="more_info_tbl_cont_' + obj + id  + '"></div>');
				$('#more_info_tbl_cont_' + obj + id).append('<table class="more_info_table" id="more_info_tbl_' + obj + id  + '"><table>');

				$('#' + more_info_id).dialog({
					width: 'auto',
					position: 'left, top',
					modal: true
				});

				var cont_id = 'more_info_chart_cont_' + obj + id;
				var new_width = 680;

				tmp_table = $('#more_info_tbl_' + obj + id).dataTable({
					"bInfo": true,
					"bJQueryUI": true,
					"aaData": res.aaData,
					"aoColumns": res.aoColumns,
					"sDom": '<"H"l<"clear">p>rt<"F">',
					"iDisplayLength": 5,
					"aLengthMenu": [[5, 10, 100, -1], [5, 10, 100, 'All']]
				});

				//$('#' + cont_id + ' .dv_ss_charts_tabs').tabs('add', '#more_info_tbl_' + obj + id + '_wrapper', 'Data');
				$('#more_info_tbl_' + obj + id).width(new_width - 20);
				$('#more_info_tbl_' + obj + id + '_wrapper').width(new_width - 20);
				$('#' + more_info_id).css('max-width', ($(window).width() - 100) + 'px');
				$('#' + more_info_id).dialog('option', 'position', 'center');

			} else if(res.aaData.length === 1) {
				$('#more_information').append('<div id="' + more_info_id + '" style="display: none; overflow: auto;" title="More Information"></div>');
				data = '<table class="spreadsheet more_info_table"><tbody>';

				for (i=0; i<res['aoColumns'].length; i++) {
					data += '<tr><td>' + res['aoColumns'][i].sTitle + '</td><td>' + res['aaData'][0][i] + '</td></tr>';
				}

				data += '</tbody></table>';
				data += '<br />';
				data +=  '<p style="float: right;">';
				data +=  '<a style="color: #44AA44; font-weight: bold;" href="/' + dv_settings.com_name + '/spreadsheet/' + obj + '" target="_blank">Click here to view all.</a>';
				data +=  '</p>';

				$('#' + more_info_id).html(data);

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#' + more_info_id).dialog();
					return false;
				}

				$('#' + more_info_id).css('max-height', ($(window).height() - 80) + 'px');
				$('#' + more_info_id).dialog({
					width: 'auto',
					title: $('#' + more_info_id + ' h1.ss_title').html(),
					modal: true
				});
			} else {
				$('#more_information').append('<div id="more_info_' + obj + id + '" style="display: none; overflow: auto;" title="More Information"></div>');

				$('#more_info_' + obj + id).html('<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><strong>Notice!</strong><br />Don\'t have any data available for this item.</p></div></div>');

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#more_info_' + obj + id).dialog();
					return false;
				}

				$('#more_info_' + obj + id).css('max-height', ($(window).height() - 80) + 'px');

				$('#more_info_' + obj + id).dialog({
					width: 'auto',
					title: $('#more_info_' + obj + id + ' h1.ss_title').html(),
					modal: true
				});
			}
		} else {
			$('#' + more_info_id).dialog({ modal: true });
		}

		return false;
	});

	//More info. multi
	$('.more_info_multi').live('click', function(e) {
		e.preventDefault();
		url = $(this).attr('href');
		obj = getUrlVars(url, 'obj');
		var id = '';
		try {
			id = getUrlVars(url, 'id');
		} catch (err) {
			id = '';
		}

		var tmp_table;

		if ($('#more_info_' + obj + id).html() === null) {

			var res = $.parseJSON($.ajax({ type: "GET", url: url, async: false }).responseText);
			if(res.aaData.length > 0) {
				$('#more_information').append('<div id="more_info_' + obj + id + '" style="overflow: auto;" title="More Information"></div>');
				data = '<table class="more_info_table"><tbody>';

				for (i=0; i<res['aoColumns'].length; i++) {
	//				data += '<tr onMouseOver="this.bgColor=\'#F1EDC2\';" onMouseOut="this.bgColor=\'transparent\';">';
					data += '<tr>';
					data += '<td>' + res['aoColumns'][i].sTitle + '</td>';

					for (j=0; j<res['aaData'].length; j++) {
						data += '<td>' + res['aaData'][j][i] + '</td>';
					}
					data += '</tr>';
				}

				data += '</tbody></table>';
				data += '<br />';
				data +=  '<p style="float: right;">';
				data +=  '<a style="color: #44AA44; font-weight: bold;" href="/' + dv_settings.com_name + '/spreadsheet/' + obj + '" target="_blank">Click here to view all.</a>';
				data +=  '</p>';

				$('#more_info_' + obj + id).html(data);

				$('#more_info_' + obj + id).css('max-height', ($(window).height() - 80) + 'px');

				$('#more_info_' + obj + id).css('max-width', ($(window).width() - 50) + 'px');

				$('#more_info_' + obj + id).dialog({
					width: 'auto',
					title: $('#more_info_' + obj + id + ' h1.ss_title').html(),
					modal: true
				});
			} else {
				$('#more_information').append('<div id="more_info_' + obj + id + '" style="display: none; overflow: auto;" title="More Information"></div>');

				$('#more_info_' + obj + id).html('<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><strong>Notice!</strong><br />Don\'t have any data available for this item.</p></div></div>');

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#more_info_' + obj + id).dialog();
					return false;
				}

				$('#more_info_' + obj + id).css('max-height', ($(window).height() - 80) + 'px');

				$('#more_info_' + obj + id).dialog({
					width: 'auto',
					title: $('#more_info_' + obj + id + ' h1.ss_title').html(),
					modal: true
				});
			}
		} else {
			$('#more_info_' + obj + id).dialog({ modal: true });
		}

		return false;
	});

	// Compare
	$('.dv_compare_chk:checkbox').live('change', function() {
		if ($(this).is(':checked')) {
			if (!dv_compare_lists[$(this).data('colname')]) {
				dv_compare_lists[$(this).data('colname')] = [];
			}
			dv_compare_lists[$(this).data('colname')].push($(this).val());
		} else {
			dv_compare_lists[$(this).data('colname')].remove($(this).val());
		}
	});

	$('a.dv_compare_multi').click(function(e) {
		var id = $(this).parent().children('.dv_header_select_all').val();
		var url = $(this).attr('href');
		var ids = '';
		url = url.slice(0, url.lastIndexOf('='));
		url += '=';

		if (!dv_compare_lists[id] || dv_compare_lists[id].length <= 0) {
			alert('Please select at least one item');
			return false;
		}

		for (i = 0; i < dv_compare_lists[id].length; i++) {
			url += dv_compare_lists[id][i] + ",";
			ids += "_" + dv_compare_lists[id][i];
		}

		url = url.slice(0, (url.length-1));
		obj = getUrlVars(url, 'obj');

		if ($('#more_info_' + obj + ids).html() === null) {

			var res = $.parseJSON($.ajax({ type: "GET", url: url, async: false }).responseText);

			$('#more_information').append('<div id="more_info_' + obj + ids + '" style="overflow: auto;" title="Side-by-Side Comparison"></div>');
			data = '<table class="compare-table more_info_table"><tbody>';

			for (i=0; i<res['aoColumns'].length; i++) {
//				data += '<tr onMouseOver="this.bgColor=\'#F1EDC2\';" onMouseOut="this.bgColor=\'transparent\';">';
				data += '<tr>';
				data += '<td>' + res['aoColumns'][i].sTitle + '</td>';

				for (j=0; j<res['aaData'].length; j++) {
					data += '<td>' + res['aaData'][j][i] + '</td>';
				}
				data += '</tr>';
			}

			data += '</tbody></table>';
			data += '<br />';
			data +=  '<p style="float: right;">';
			data +=  '<a style="color: #44AA44; font-weight: bold;" href="/' + dv_settings.com_name + '/spreadsheet/' + obj + '" target="_blank">Click here to view all.</a>';
			data +=  '</p>';

			$('#more_info_' + obj + ids).html(data);

			$('#more_info_' + obj + ids).css('max-height', ($(window).height() - 80) + 'px');

			$('#more_info_' + obj + ids).css('max-width', ($(window).width() - 50) + 'px');
		}
			var dialog_width = 'auto';
			if($.browser.msie) {
				dialog_width = $(window).width() - 50;
			}

			$('#more_info_' + obj + ids).dialog({
				width: dialog_width,
				title: $('#more_info_' + obj + ids + ' h1.ss_title').html(),
				modal: true
			});

			if (is_IE_six) {
				$('#more_info_' + obj + ids).parent().width($('#more_info_' + obj + ids).find('table').first().width());
			}

		return false;
	});

	$('.ss_sg_columns').live('click', function() {
		var idx = $(this).attr('name');
		$("tfoot input:eq(" + idx + ")").focus();
		$("tfoot input:eq(" + idx + ")").select();
	});

	$('.collapsible-button').toggle(function() {
		$(this).children().switchClass('ui-icon-plus', 'ui-icon-minus');
		$(this).parent().children('.collapsible').show('slide');
	}, function() {
		$(this).children().switchClass('ui-icon-minus', 'ui-icon-plus');
		$(this).parent().children('.collapsible').hide('slide');
	});

	// Image previews
	$('.ss_image.img_expand').live('click', function() {
		window.open($(this).attr('src'));
	});

	$('.dv_img_preview').live('mouseover mouseout', function(event) {
		if (event.type === 'mouseover') {
			$("body").append("<div style='position: absolute; z-index: 9999;' id='dv_img_preview'><img src='" + $(this).data('preview-img') + "' alt='Loading preview image...' id='dv_preview_image' class='shadow' /></div>");
			$("#dv_img_preview").show();
			$("#dv_img_preview").position({
				my: "left top",
				at: 'left top',
				of: $(this),
				offset: "25",
				collision: "fit flip"
			});
		} else {
			$("#dv_img_preview").remove();
		}
	});

	// Truncated text
	$('tbody .truncate').live('click', function() {
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

	$('tbody .truncate a').live('click', function(e) {
		e.stopPropagation();
	});

	// Hide header & footer
	$('#dv_fullscreen').click(function() {
		if ($(this).prop('checked') === true) {
			$('#top,#header,#nav,#footer').hide();
		} else {
			$('#top,#header,#nav,#footer').show();
		}
	});

	// Tools select all
	$('.dv_header_select_all').click(function(e) {
		$(dv_table.fnGetNodes()).find('.' + $(this).val() + ':checkbox').prop('checked', $(this).prop('checked')).trigger('change');
		e.stopPropagation();
	});

	$('.dv_tools_launch_link').live('click', function(e) {
		if (typeof pageTracker != 'undefined') {
			pageTracker._trackEvent('Data viewer', 'Tools launch', $(this).attr('href'));
		}
	});

	$('.dv_tools_dl_link').live('click', function(e) {
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

	// Table Hide/Show
	if ($('#dv_table_container:visible').length > 0) {
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Hide table');
		});
	} else {
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Show table');
		});
	}

	$('.dv_toggle_data_btn').click(function() {
		$('#dv_table_container').toggle();
		if ($('#dv_table_container:visible').length > 0) {
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
		$('#dv_table_container').hide();
		$('.dv_toggle_data_btn').each(function() {
			$(this).val('Show table');
		});
	}

	// Search and Page number position
	function update_pos() {
		$('#spreadsheet_wrapper .dataTables_filter').css("position","absolute");
		$('#spreadsheet_wrapper .dataTables_info').css("position","absolute");
		$('#spreadsheet_wrapper .dataTables_paginate').last().css("position","absolute");
		$('#spreadsheet_wrapper .dataTables_paginate').first().css("position","absolute");
		$('#dv_ss_charts_container .dv_ss_charts').css("position","absolute");

		var ww = $(window).width();
		var sw = $('#spreadsheet_wrapper.dataTables_wrapper').width();

		var searchw = $('#spreadsheet_wrapper .dataTables_filter').width();

		var ssr = $(document).scrollLeft() + ww - searchw - 40;
		if ((ssr + searchw) > sw) {
			ssr = sw - searchw - 5;
		}
		$('#spreadsheet_wrapper .dataTables_filter').css('left', ssr + 'px');

		var pagew = $('#spreadsheet_wrapper .dataTables_paginate').last().width();
		var spr = $(document).scrollLeft() + ww - pagew - 45;
		if ((spr + pagew) > sw) {
			spr = sw - pagew - 45;
		}
		$('#spreadsheet_wrapper .dataTables_paginate').last().css('left', spr + 'px');

		var sstm = parseInt(($(document).scrollLeft() + $('#spreadsheet_wrapper .dataTables_filter').position().left)/2) - 110;
		var ssbm = parseInt(($(document).scrollLeft() + $('#spreadsheet_wrapper .dataTables_paginate').last().position().left)/2) - 80;
//		$('#spreadsheet_wrapper .dataTables_info').first().css('left', sstm + 'px');
		$('#spreadsheet_wrapper .dataTables_info').last().css('left', ssbm + 'px');
		$('#spreadsheet_wrapper .dataTables_paginate').first().css('left', sstm + 'px');
		$('#dv_ss_charts_container .dv_ss_charts').css("left", $(document).scrollLeft() + 20 + 'px');

		move_filters();
	}

	function move_filters() {
		/*
		// Start with Filters at the top
		if ($('#spreadsheet thead').offset().top < $(window).scrollTop()) {
			$('#spreadsheet tfoot').css('display', 'table-footer-group');
		} else {
			$('#spreadsheet tfoot').css('display', 'table-header-group');
		}
		*/

		if ((($(window).height() + $(window).scrollTop()) - ($('#spreadsheet tbody').offset().top + $('#spreadsheet tbody').height())) > 25) {
			$('#spreadsheet tfoot').css('display', 'table-footer-group');
		} else {
			$('#spreadsheet tfoot').css('display', 'table-header-group');
		}
	}

	$('select[name="spreadsheet_length"]').change(function() {
		move_filters();
	});

	$(window).bind('scroll resize', function() {
		update_pos();
	});

	// Quick-tip
	$('.quick_tip').tipsy({gravity: 'nw', live: true});
	$('.quick_tip').live('click', function(e) {
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
	$('.dv_gallery_link').live('click', function() {
		var window_opts = "toolbars=no,menubar=no,location=no,scrollbars=no,resizable=yes,status=no,height=670,width=800";
		window.open(this.href, '', window_opts);
		return false;
	});

	//Column descriptions
	$('#spreadsheet thead th .dv_label_text').click(function(e) {
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
	$(document).keydown(function(event) {
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
					$('#spreadsheet_previous').trigger('click');
					event.stopPropagation();
					break;
				case 38:
					$('#spreadsheet_last').trigger('click');
					event.stopPropagation();
					break;
				case 39:
					$('#spreadsheet_next').trigger('click');
					event.stopPropagation();
					break;
				case 40:
					$('#spreadsheet_first').trigger('click');
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
	var tpl_title = '<li><a href="#tab-{id}">{name}</a></li>';
	var tpl_field = '<tr><td>{field_name}</td><td><input type="text" class="filter_dialog_field" data-column-index="{idx}" data-column-id="{col_id}" data-filter_hint="{hint}" /></td></tr>';

	if(dv_data.filters.length > 0) {

		// Show filters button
		$('#ss_title').append('&nbsp;&nbsp;<input type="button" value="Show Filters" id="dv_show_filters">');
		$('#dv_show_filters').live('click', function() {
			$('#dv_filters_dialog').dialog('open');
		});

		for (i=0; i < dv_data.filters.length; i++) {
			var filter_div = '<div id="tab-' + i + '"><table>';
			var col_count = 0;

			for (j=0; j<dv_data.filters[i].cols.length; j++) {
				var col_id = dv_data.filters[i].cols[j];

				if (dv_data.cols.visible.indexOf(col_id) !== -1) {
					col_count = col_count + 1;

					var hint = ('' + $('#spreadsheet tfoot th input:eq(' + dv_data.cols.visible.indexOf(col_id) + ')').attr('title')).replace(/  /g, "&nbsp;&nbsp;").replace(/\n/g, "<br />");
					filter_div = filter_div + tpl_field.supplant({'field_name': dv_data.col_labels[dv_data.cols.visible.indexOf(col_id)].replace(/<br \/>/g,'&nbsp;').replace(/<hr \/>/g,'/').stripTags(), 'idx': dv_data.cols.visible.indexOf(col_id), 'col_id': col_id, 'hint': hint});
				}
			}

			if (col_count > 0) {
				filter_div = filter_div + '</table><br /><input type="button" data-filter_hint="" value="Filter" class="filter_button" style="float: right; margin: 0px 5px;"><input data-filter_hint="" type="button" value="Done" class="filter_done_button" style="float: right; margin: 0px 5px;"><br /></div>';
				$('#dv_filters_tabs').append(filter_div);

				$('#dv_filters_tabs ul').append(tpl_title.supplant({'id': i, 'name': dv_data.filters[i].filter_name}));
			}

		}

		$('#dv_filters_tabs').tabs();

		$(".filter_dialog_field").live('focus', function() {
			var idx = $(this).data('column-index');
			$(this).autocomplete({
				minLength: 0,
				close: function() {$(this).trigger('filter');},
//				close: function() {$(this).trigger('filter-changed');},
				source: dv_table.fnGetColumnData(idx)
			});
		});

		$(".filter_dialog_field").focus(function() {
			$(this).autocomplete("search", '');
		});

		$('.filter_dialog_field').bind('filter', function(e) {
			var col_idx = $(this).data('column-index');
			var field = $("tfoot input").get(col_idx);
			settings = dv_table.fnSettings();

			if ($(this).val() !== '') {
				field.className = "";
				field.value = $(this).val();
				settings.aoPreSearchCols[col_idx].sSearch = $(this).val();
				settings.aoPreSearchCols[col_idx].bRegex = false;
				settings.aoPreSearchCols[col_idx].bSmart = true;
			} else {
				field.className = "search_init";
				field.value = asInitVals[$("tfoot input").index(field)];
				settings.aoPreSearchCols[col_idx].sSearch = '';
				settings.aoPreSearchCols[col_idx].bRegex = false;
				settings.aoPreSearchCols[col_idx].bSmart = true;
			}

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
				$(this).autocomplete("search", '');
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

		$('#dv_filters_tabs input').tipsy({
			gravity: 'w',
			live: true,
			title: 'data-filter_hint',
			html: true
		});

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


		$("#dv_filter_dialog_btn").click(function() {
			$('#dv_filters_dialog').dialog('open');
		});
	}

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
