/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

dv_jQuery = jQuery.noConflict();
var dv_table;
var dv_data;
var dv_settings;
var dl_vars;
var dv_charts = [];
var dv_compare_lists = [];
var asInitVals = [];
var first_load = true;
var dbg;
var is_IE_six = (navigator.userAgent.indexOf("MSIE 6.") !== -1);

jQuery.fn.dataTableExt.oApi.fnGetFilteredData = function(oSettings) {
	var a = [];
	var i;
	for (i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++) {
		a.push(oSettings.aoData[ oSettings.aiDisplay[i] ]._aData);
	}
	return a;
};

jQuery.fn.dataTableExt.oSort['int-asc']  = function(a,b) {

	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );

	if (x === y) { return 0; }
	if (x === '-') { return -1; }
	if (y === '-') { return 1; }

	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['float-asc'] = jQuery.fn.dataTableExt.oSort['int-asc'];
jQuery.fn.dataTableExt.oSort['real-asc'] = jQuery.fn.dataTableExt.oSort['int-asc'];

jQuery.fn.dataTableExt.oSort['int-desc'] = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );

	if (x === y) { return 0; }
	if (x === '-') { return 1; }
	if (y === '-') { return -1; }

	x = parseFloat( x );
	y = parseFloat( y );

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['float-desc'] = jQuery.fn.dataTableExt.oSort['int-desc'];
jQuery.fn.dataTableExt.oSort['real-desc'] = jQuery.fn.dataTableExt.oSort['int-desc'];

jQuery.fn.dataTableExt.oSort['cnum-asc']  = function(a,b) {

	var x = a.replace( /<.*?>/g, "" );
	x = a.replace( /<|>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	y = b.replace( /<|>/g, "" );

	if (x === y) { return 0; }
	if (x === '-') { return -1; }
	if (y === '-') { return 1; }
//	a = (() ? return -1 : ((y === '-') ? return 1 : 0));
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['cnum-desc'] = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	x = a.replace( /<|>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	y = b.replace( /<|>/g, "" );

	if (x === y) { return 0; }
	if (x === '-') { return 1; }
	if (y === '-') { return -1; }

	x = parseFloat( x );
	y = parseFloat( y );

	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['datetime-desc'] = jQuery.fn.dataTableExt.oSort['string-desc'];
jQuery.fn.dataTableExt.oSort['datetime-asc'] = jQuery.fn.dataTableExt.oSort['string-asc'];

jQuery.fn.dataTableExt.oSort['char-num-asc'] = function(x,y) {
	x = x.replace(/[^\d\-\.\/]/g,'');
	y = y.replace(/[^\d\-\.\/]/g,'');
	if(x.indexOf('/')>=0) {x = eval(x);}
	if(y.indexOf('/')>=0) {y = eval(y);}
	return x/1 - y/1;
};

jQuery.fn.dataTableExt.oSort['char-num-desc'] = function(x,y) {
	x = x.replace(/[^\d\-\.\/]/g,'');
	y = y.replace(/[^\d\-\.\/]/g,'');
	if(x.indexOf('/')>=0) {x = eval(x);}
	if(y.indexOf('/')>=0) {y = eval(y);}
	return y/1 - x/1;
};

jQuery.fn.dataTableExt.ofnSearch['int'] = function (data) {
	return data.replace(/\n/g, " ").replace( /<.*?>/g, "").replace(/&nbsp;/g, "");
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

	// Hide/Show Charts
	$('#dv_hide_charts').click(function() {
		if ($('#dv_ss_charts_container:hidden').length <= 0) {
			$('#dv_ss_charts_container').hide();
			$(this).val('Show Charts');
		} else {
			$('#dv_ss_charts_container').show();
			$(this).val('Hide Charts');
		}
	});

	function dv_plot(chart) {
		var plot;

		if (chart.type === 'line') {
			plot = $.jqplot(chart.cid, chart.clines, {
					title: chart.cname,
					series: chart.cseries,
					legend: {
						renderer: $.jqplot.EnhancedLegendRenderer,
						placement:'inside',
						location:'ne',
						rendererOptions:{
							numberColumns: 3
						},
						show: true
					},
					axes: {
						xaxis: {
							min: chart.x_min,
							label: chart.x_label,
							autoscale: true,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
							rendererOptions: {
								tickRenderer: $.jqplot.CanvasAxisTickRenderer
								},
							tickOptions: {
								showLabel: true,
								angle:-60,
								formatString:'%d'
							}
						},

						yaxis: {
							min: chart.y_min,
							label: chart.y_label,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer
						}
					},

					highlighter: {
						show: true,
						showTooltip:true
					},

					cursor: {
						show: true
					}
			});
		} else if (chart.ticks) {
			plot = $.jqplot(chart.cid, chart.clines, {
					title: chart.cname,
					seriesDefaults: {
						renderer: $.jqplot.BarRenderer,
						rendererOptions: {
							barPadding: 1,
							barMargin: 1
						},
						pointLabels: {
							show: false
						}
					},
					series: chart.cseries,
					legend: {
						renderer: $.jqplot.EnhancedLegendRenderer,
						placement:'inside',
						location:'ne',
						rendererOptions:{
							numberColumns: 3
						},
						show: true
					},
					axes: {
						xaxis: {
							min: chart.x_min,
							ticks: chart.ticks,
							tickInterval: chart.interval,
							label: chart.x_label,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
							renderer:$.jqplot.CategoryAxisRenderer,
							rendererOptions: {
								tickRenderer: $.jqplot.CanvasAxisTickRenderer
								},
							tickOptions: {
								showLabel: true,
								angle:-60,
								formatString:'%d'
							}
						},

						yaxis: {
							min: chart.y_min,
							label: chart.y_label,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer
						}
					},

					highlighter: {
						show: false
					}
			});
		} else {
			plot = $.jqplot(chart.cid, chart.clines, {
					title: chart.cname,
					seriesDefaults: {
						renderer: $.jqplot.BarRenderer,
						rendererOptions: {
							barPadding: 0,
							barMargin: 0
						},
						pointLabels: {
							show: false
						}
					},
					series: chart.cseries,
					legend: {
						renderer: $.jqplot.EnhancedLegendRenderer,
						placement:'inside',
						location:'nw',
						rendererOptions:{
							numberColumns: 3
						},
						show: true
					},
					axes: {
						xaxis: {
							label: chart.x_label,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
							renderer:$.jqplot.CategoryAxisRenderer,
							rendererOptions: {
								tickRenderer: $.jqplot.CanvasAxisTickRenderer
								},
							tickOptions: {
								showLabel: true
							}
						},

						yaxis: {
							min: chart.y_min,
							label: chart.y_label,
							labelRenderer: $.jqplot.CanvasAxisLabelRenderer
						}
					},

					highlighter: {
						show: false
					}
			});
		}

		return plot;
	}

	function draw_charts(charts, chart_table, container) {
		var tabs;
		var tab_id = 0;

		var cwidth = 700;
		$('.dv_ss_charts').width(cwidth + 20);

		$(charts).each(function() {
			var cnt = 0;
			var chart = {};
			var tid = container + 'chart-tab-' + tab_id;
			var cid = container + 'chart-' + tab_id;
			var cname = this.name;
			var clines = [];
			var cheight = 300;
			var c_type = this.type;

			$('#' + container + ' .dv_ss_charts_tabs ul').append('<li><a href="#' + tid + '">' + cname + '</a></li>');
			$('#' + container + ' .dv_ss_charts_tabs').append('<div id="' + tid + '"></div>');
			$('#' + tid).append('<div style="width: '+ (cwidth-60) +'px; height: ' + cheight + 'px" id="' + cid + '"></div>');

			var data = chart_table.fnGetData();
			var cseries = [];

			$(this.lines).each(function() {
				chart.x_max = 0;
				chart.y_max = 0;

				var line = [];
				if (!this[1]) {
					for (i=0; i<data.length;i++) {
						line.push([i, parseInt(data[i][this])]);
					}

					s = {};
					s.label = $(chart_table).find('thead th:eq(' + this + ')').text();
				}  else {
					if (c_type === 'line') {
						for (i=0; i<data.length;i++) {
							line.push([parseInt(data[i][this[0]]), parseFloat(data[i][this[1]])]);
							if (chart.x_max < parseInt(data[i][this[0]])) {
								chart.x_max = parseInt(data[i][this[0]]);
							}
							if (chart.y_max < parseInt(data[i][this[1]])) {
								chart.y_max = parseFloat(data[i][this[1]]);
							}
						}
					} else {
						for (i=0; i<data.length;i++) {
							line.push([data[i][this[0]] + "", parseFloat(data[i][this[1]])]);
							if (chart.y_max < parseFloat(data[i][this[1]])) {
								chart.y_max = parseFloat(data[i][this[1]]);
							}
						}
					}

					s = {};
					s.label = $(chart_table).find('thead th:eq(' + this[0] + ')').text() + ', ' + $(chart_table).find('thead th:eq(' + this[1] + ')').text();
				}
				clines.push(line);
				cseries.push(s);
			});

			legend_cols = 4;
			if (cseries.length < 4) {
				legend_cols = cseries.length;
			}

			chart.cid = cid;
			chart.clines = clines;
			chart.interval = this.interval;
			chart.cname = cname;
			chart.cseries = cseries;
			chart.x_label = this.x_label;
			chart.y_label = this.y_label;
			chart.type = this.type;
			chart.x_min = this.x_min;
			chart.y_min = this.y_min;
			chart.x_max = parseInt(chart.x_max * 1.1);
			chart.y_max = parseInt(chart.y_max * 1.1);
			dv_plot(chart);

			// Fix legend
			jQuery('table.jqplot-table-legend').each(function() {
				$(this).width($(this).find('tr').width() + 20);
			});

			if(this.desc) {
				$('#' + tid).append('<div class="ui-widget" style="width: 100%;"><div class="ui-state-highlight ui-corner-all" style="padding: 5px 0.7em; width: 80%; margin: 20px auto;">' + '<strong>' + this.desc + '</strong>' + '</div></div>');
			}

			tab_id++;
		});

		tabs = $('#' + container + ' .dv_ss_charts_tabs').tabs();
		$('#' + container).height($('#' + container + ' .dv_ss_charts').height());
	}


	var dv_tbl_length = [10, 25, 50, 100];
	var dv_tbl_length_lbl = [10, 25, 50, 100];

 
	if (!dv_settings.serverside) {
		dv_settings.num_rows.values.push(1000);
		dv_settings.num_rows.labels.push('1000');
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
								$('.' + id + ':checkbox[value="' + dv_compare_lists[id][i] + '"]').attr('checked', true);
							}

							if ($('.' + id + ':checkbox[checked=false]').length === 0) {
								$(this).attr('checked', true);
							} else {
								$(this).attr('checked', false);
							}
						}
					});

					$(document).trigger('dv_event_update_map');
				}
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
			//	dv_table.fnProcessingIndicator();
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
	
	$('#dv_download').click(function() {
		var set = dv_table.fnSettings();
		var iColumns = set.aoColumns.length;
		var data = [];
		var i;

		/* Paging and general */
		data.push({ "name": "iColumns",	   "value": iColumns });
		data.push({ "name": "iDisplayStart",  "value": set._iDisplayStart });
		data.push({ "name": "iDisplayLength", "value": set.oFeatures.bPaginate !== false ? set._iDisplayLength : -1 });

		/* Filtering */
		if ( set.oFeatures.bFilter !== false ) {
			data.push({ "name": "sSearch", "value": set.oPreviousSearch.sSearch });
			data.push({ "name": "bRegex",  "value": set.oPreviousSearch.bRegex });
			for ( i=0 ; i<iColumns ; i++ ) {
				data.push( { "name": "sSearch_"+i,	 "value": set.aoPreSearchCols[i].sSearch } );
				data.push( { "name": "bRegex_"+i,	  "value": set.aoPreSearchCols[i].bRegex } );
				data.push( { "name": "bSearchable_"+i, "value": set.aoColumns[i].bSearchable } );
			}
		}

		for (i=0; i<set.aoColumns.length; i++) {
			var fieldtype = 'fieldtype_' + i;
			data.push({"name": fieldtype, "value": set.aoColumns[i]['sType']});
		}

		/* Sorting */
		if (set.oFeatures.bSort !== false) {
			var iFixed = set.aaSortingFixed !== null ? set.aaSortingFixed.length : 0;
			var iUser = set.aaSorting.length;
			data.push( { "name": "iSortingCols",   "value": iFixed+iUser } );
			for (i=0 ; i<iFixed ; i++) {
				data.push( { "name": "iSortCol_"+i,  "value": set.aaSortingFixed[i][0] } );
				data.push( { "name": "sSortDir_"+i,  "value": set.aaSortingFixed[i][1] } );
			}

			for (i=0 ; i<iUser ; i++) {
				data.push( { "name": "iSortCol_"+(i+iFixed),  "value": set.aaSorting[i][0] } );
				data.push( { "name": "sSortDir_"+(i+iFixed),  "value": set.aaSorting[i][1] } );
			}

			for (i=0 ; i<iColumns ; i++) {
				data.push( { "name": "bSortable_"+i,  "value": set.aoColumns[i].bSortable } );
			}
		}

		for (i=0; i< data.length; i++) {
			$('#ss_download_form').append('<input type="hidden" name="' + data[i].name + '" value="' + data[i].value + '" />');
		}

		$('#ss_download_form').submit();
	});

	if (dv_settings.serverside) {
		$(".dataTables_filter input").unbind('keyup');
		$(".dataTables_filter input").unbind('keypress');

		$(".dataTables_filter input").keypress( function(e) {
			if (!dv_settings.serverside || e.keyCode === 13) {
				//$('#indicator').fadeIn('slow');
				dv_table.fnFilter($(".dataTables_filter input").attr('value'));
			}
		});
	}

	$(".dataTables_filter input").keypress( function(e) {
		if (!dv_settings.serverside && e.keyCode === 13) {
			hightlight_keywords();
		}
	});

	$("tfoot input").keyup(function(e) {
		if (dv_settings.serverside && e.keyCode === 13) {
			dv_table.fnFilter(this.value, $("tfoot input").index(this));
		} else if (!dv_settings.serverside){
			var fieldtype = dv_data.aoColumns[$("tfoot input").index(this)]['sType'];
			var filter_str = this.value;

			if (fieldtype === 'int' || fieldtype === 'real' || fieldtype === 'float') {
				filter_str = filter_str.toLowerCase();
				if (filter_str.indexOf('to') !== -1) {
					filter_str = "#range#" + filter_str;
				}
			}

			dv_table.fnFilter(filter_str, $("tfoot input").index(this));

			if (e.keyCode === 13) {
				hightlight_keywords();
			}
		}
	});

	$("tfoot input").each(function(i) {
		asInitVals[i] = this.value;
	});

	$("tfoot input").focus(function() {
		if (this.className === "search_init") {
			this.className = "";
			this.value = "";
		}
	});

	$("tfoot input").blur(function() {
		var col_idx = $("tfoot input").index(this);
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

	// Highlight keywords
	function hightlight_keywords() {
		if (!dv_table) {  // When not serverside!
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
			if (res.aaData.length > 1) {
				$('#more_information').append('<div id="more_info_'+obj+id+'" style="overflow: auto;" title="More Information"></div>');
				$('#more_info_'+obj+id).append('<div id="more_info_tbl_cont_' + obj + id  + '"></div>');
				$('#more_info_tbl_cont_' + obj + id).append('<table class="more_info_table" id="more_info_tbl_' + obj + id  + '"><table>');

				$('#more_info_'+obj+id).dialog({
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
					"aLengthMenu": [[5, 10, 100, -1], [5, 10, 100, 'All']],
					"fnInitComplete": function() {
						if(res.charts) {
							$('#more_info_'+obj+id).prepend('<div id="' + cont_id + '"><div class="dv_ss_charts"><div class="dv_ss_charts_tabs"><ul></ul></div></div></div><br />');
							if (!is_IE_six) {
								draw_charts(res.charts, this, cont_id);
							} else {
								$('#'+cont_id).html("<strong>Sorry! Charts are not supported in IE6. Please use a newer web browser...</strong>");
							}
						}
					}
				});

				$('#' + cont_id + ' .dv_ss_charts_tabs').tabs('add', '#more_info_tbl_' + obj + id + '_wrapper', 'Data');
				$('#more_info_tbl_' + obj + id).width(new_width - 20);
				$('#more_info_tbl_' + obj + id + '_wrapper').width(new_width - 20);
				$('#more_info_'+obj+id).css('max-width', ($(window).width()-100)+'px');
				$('#more_info_'+obj+id).dialog('option', 'position', 'center');

			} else if(res.aaData.length === 1) {
				$('#more_information').append('<div id="more_info_'+obj+id+'" style="display: none; overflow: auto;" title="More Information"></div>');
				data = '<table class="spreadsheet more_info_table"><tbody>';

				for (i=0; i<res['aoColumns'].length; i++) {
					data += '<tr><td>' + res['aoColumns'][i].sTitle + '</td><td>' + res['aaData'][0][i] + '</td></tr>';
				}

				data += '</tbody></table>';
				data += '<br />';
				data +=  '<p style="float: right;">';
				data +=  '<a style="color: #44AA44; font-weight: bold;" href="/' + dv_settings.com_name + '/spreadsheet/' + obj + '" target="_blank">Click here to view all.</a>';
				data +=  '</p>';

				$('#more_info_'+obj+id).html(data);

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#more_info_'+obj+id).dialog();
					return false;
				}

				$('#more_info_'+obj+id).css('max-height', ($(window).height()-80)+'px');

				$('#more_info_'+obj+id).dialog({
					width: 'auto',
					title: $('#more_info_'+obj+id+' h1.ss_title').html(),
					modal: true
				});
			} else {
				$('#more_information').append('<div id="more_info_'+obj+id+'" style="display: none; overflow: auto;" title="More Information"></div>');

				$('#more_info_'+obj+id).html('<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><strong>Notice!</strong><br />Don\'t have any data available for this item.</p></div></div>');

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#more_info_'+obj+id).dialog();
					return false;
				}

				$('#more_info_'+obj+id).css('max-height', ($(window).height()-80)+'px');

				$('#more_info_'+obj+id).dialog({
					width: 'auto',
					title: $('#more_info_'+obj+id+' h1.ss_title').html(),
					modal: true
				});
			}
		} else {
			$('#more_info_'+obj+id).dialog({
				modal: true
			});
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

				$('#more_info_' + obj + id).css('max-height', ($(window).height()-80)+'px');

				$('#more_info_' + obj + id).css('max-width', ($(window).width() - 50) + 'px');
				
				$('#more_info_'+obj+id).dialog({
					width: 'auto',
					title: $('#more_info_'+obj+id+' h1.ss_title').html(),
					modal: true
				});
			} else {
				$('#more_information').append('<div id="more_info_'+obj+id+'" style="display: none; overflow: auto;" title="More Information"></div>');

				$('#more_info_'+obj+id).html('<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><strong>Notice!</strong><br />Don\'t have any data available for this item.</p></div></div>');

				if (jQuery.browser.msie && parseInt(jQuery.browser.version) < 8) {
					$('#more_info_'+obj+id).dialog();
					return false;
				}

				$('#more_info_'+obj+id).css('max-height', ($(window).height()-80)+'px');

				$('#more_info_'+obj+id).dialog({
					width: 'auto',
					title: $('#more_info_'+obj+id+' h1.ss_title').html(),
					modal: true
				});
			}
		} else {
			$('#more_info_'+obj+id).dialog({
				modal: true
			});
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

			$('#more_info_' + obj + ids).css('max-height', ($(window).height()-80)+'px');

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
//			console.log('#more_info_' + obj + ids);
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
			$("body").append("<div style='position: absolute; z-index: 9999;' id='dv_img_preview'><img src='"+ $(this).data('preview-img') +"' alt='Loading preview image...' id='dv_preview_image' class='shadow' /></div>");
			$("#dv_img_preview").show();
			$("#dv_img_preview").position({
				my: "left top",
				of: event,
				offset: "15",
				collision: "flip fit"
			});
		} else {
			$("#dv_img_preview").remove();
		}
	});

	// Truncated text
	$('tbody .truncate').live('click', function() {
		$('#truncated_text_dialog').html($(this).attr('title'));
		$('#truncated_text_dialog').dialog({
			title: 'Full Text'
		});
	});

	// Hide header & footer
	$('#dv_fullscreen').click(function() {
		if ($(this).attr('checked') === true) {
			$('#header,#trail,#footer,#header-corner,#nav,#secondary').hide();
		} else {
			$('#header,#trail,#footer,#header-corner,#nav,#secondary').show();
		}
	});

	// Tools select all
	$('.dv_header_select_all').click(function(e) {
		$(dv_table.fnGetNodes()).find('.' + $(this).val() + ':checkbox').attr('checked', $(this).attr('checked')).trigger('change');
		e.stopPropagation();
	});

	// Update tools download multiple
	$('.dv_tools_down_multi').click(function(e) {
		var id = $(this).parent().children('.dv_header_select_all').val();
		var url = $(this).attr('href');
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
		});
		url = url.slice(0, (url.length-1));
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
		$(this).attr('href', url);
		e.stopPropagation();
	});

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
	}

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
			$('#truncated_text_dialog').html($(this).attr('title').replace(/  /g, "&nbsp;&nbsp;").replace(/\n/g, "<br />"));
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
	$(document).keyup(function(event) {
		if (event.ctrlKey) {
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
});
