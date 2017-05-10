/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

(function(window, document, ds, $, undefined) {
	ds.dd = {};
	ds.tab = {};
	ds.data = {};

	$(document).ready(function() {
		// back button
		$('.prj-db-back').on('click', function() {

			// last setp
			var step = $(this).data('step');

			if ($(this).data('warning')) {
				var ok = confirm("Any column customizations you have been doing will be lost if you go back.\n\nPlease click OK if you want to go back to the previous step");
				if (!ok) { return; }
			}

			if (step == 2 && ds.recreate) {
				window.location = window.location.href.split('create')[0];
				return false;
			}

			if (step != 3) {
				$('.main-content').css('margin-left', $('.main-menu').show().width());
			}

			if (step == 3) {
				$('.main-menu').hide();
				$('.main-content').css('margin-left', 0);
			}

			$('#prj-db-step-' + step).hide();
			$('#prj-db-step-' + (step - 1)).show();
		});


		// Setp 1: Select File
		$('#prj-db-select-src').chosen({
			search_contains: true
		});
		$('.chzn-single').css('border', '1px solid #AAA');


		// Setp 2: Preview data
		$('#prj-db-preview-file').on('click', function() {

			var msg = 'Processing...';
			if (HUB.Projects)
			{
				HUB.Projects.setStatusMessage(msg, 1);
			}
			else
			{
				ds.status_msg(msg);
			}

			$.get($('#prj-db-select-form').attr('action'), {
				file: $('#prj-db-select-src').val(),
				dir: $('#prj-db-select-src option:selected').data('dir')
			}, function(res) {
				if (HUB.Projects)
				{
					HUB.Projects.setStatusMessage(false, false);
				}
				else
				{
					ds.status_msg(false);
				}

				show_preview(res);

				$('#prj-db-step-1').hide();
				$('.main-menu').hide();
				$('.main-content').css('margin-left', 0);

				$('#prj-db-step-2').show();
			}, 'json');

			return false;
		});

		// Recreate database
		if ($('#prj-db-select-form input[name="db_id"]').length == 1) {
			ds.db_id = $('#prj-db-select-form input[name="db_id"]').val();
			ds.recreate = true;
			$('#prj-db-preview-file').trigger('click');
		} else {
			ds.db_id = '';
			ds.recreate = false;
		}

		function show_preview(res) {
			var table = res.data;
			ds.dd = table.dd;
			ds.data = table.data;
			ds.repo = res.data.repo;


			/* Populating the Repository Path list */
			var wd = (ds.repo.wd != '') ? '/' + ds.repo.wd + '/' : '/';
			var title = 'Files from the \'' + wd + '\' folder';
			if (wd == '/') {
				title = 'Files from the \'Repository Home\' folder';
			}
			var sub_dirs = '<option value="" title="' + title + '">Same folder as the CSV file</option>';

			$.each(ds.repo.sub_dirs, function(i, val) {
				var title = 'Files from : ' + wd + val;		
				sub_dirs += '<option value="' + val + '" title="' + title + '">' + val + '</option>';
			});
			$('#prj-db-col-linkpath').html(sub_dirs);
			
			$('#prj-db-rec-limit').html('Total number of records: ' + table.rec_total + ' | Loaded first ' + table.rec_display + ' records');
			$('#prj-db-preview-table-wrapper').empty();
			$('#prj-db-preview-table-wrapper').html('<table id="prj-db-preview-table" class="dv-spreadsheet"></table>');

			ds.tab = $('#prj-db-preview-table').dataTable({
				"bFilter": true,
				"bInfo": true,
				"bJQueryUI": true,
				"bAutoWidth": true,
				"aaData": table.data,
				"aoColumns": table.header,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"fnInitComplete": function() {
					$('#prj-db-preview-table thead th').append('<div title="Click here to edit column properties" class="col-edit">&nbsp;&nbsp;</div>');

					var that = this;

					$('div.col-edit').on('click', function(e) {
						var col = ds.dd[$('div.col-edit').index(this)];

						//$('#col-prop-dialog').html(function(i, h) { return h.supplant(col); });
						$('.col-prop').each(function() {

							var prop = $(this).attr('id').replace(/prj-db-col-/, '');
							var val = col[prop];


							if ($(this).is(':checkbox')) {
								if (typeof val !== 'undefined') {
									$(this).prop('checked', true);
								} else {
									$(this).prop('checked', false);
								}
							} else {
								$(this).val(val);
							}

							if ($(this).hasClass('color-picker')) {
								if (typeof val !== 'undefined') {
									$(this).spectrum('set', val);
									switch (prop) {
										case 'text-color':
											$(this).data('style-type', 'color');
											break;
										case 'bg-color':
											$(this).data('style-type', 'background');
											break;
									}
									$(this).data('style-val', val);

								} else {
									$(this).spectrum('set', $(this).data('default'));
									$(this).data('style-val', '');
								}
							}

							$(this).data('changed', false);

						});

						// When a base href is set, jQuery UI Tabs will try to use it
						// as the URL for links that only have the anchor defined. This
						// means it's (incorrectly) loading tab content via AJAX.
						//
						// https://bugs.jqueryui.com/ticket/7822
						//
						// So, we temporarily remove the base href.
						var bases = document.getElementsByTagName('base');
						var baseHref = null;

						if (bases.length > 0) {
							baseHref = bases[0].href;
							bases[0].href = '';
						}

						$('#col-prop-dialog').find('.tabs').tabs().tabs('option', 'active', 0).end()
						.dialog({
							title: 'Column Properties : ' + col.label,
							width: 650,
							buttons: {
								'Update Column': function() {
									var idx = col.idx;
									update_dd(ds.tab, ds.dd[idx]);
									$(this).dialog( "close" );
								},
								Cancel: function() {
									$(this).dialog( "close" );
									bases[0].href = baseHref;
								}
							}
						});

						$('.col-prop').on('change', function() {
							$(this).data('changed', '1');
						});

						// Type specific : Text
						$('#prj-db-col-type').on('change', function() {
							var val = $(this).val();
							if (val == 'text_small' || val == 'text_large') {
								$('#prj-db-col-type-text').show();
							} else {
								$('#prj-db-col-type-text').hide();
							}
						}).trigger('change');

						// Type specific : Links
						$('#prj-db-col-type').on('change', function() {
							var val = $(this).val();
							if (val == 'link' || val == 'image') {
								$('#prj-db-col-type-link').show();
							} else {
								$('#prj-db-col-type-link').hide();
							}
						}).trigger('change');

						e.stopPropagation();
						e.preventDefault();

						return false;

					});
				}
			});

			$(ds.dd).each(function() {
				update_col_properties(ds.tab, this);
			});
		}



		function update_dd(tab, col) {
			var idx = col.idx;
			var style = '';

			$('.col-prop').each(function() {
				var prop = $(this).attr('id').replace(/prj-db-col-/, '');
				if ($(this).is(':checkbox')) {
					if ($(this).prop('checked') === true) {
						ds.dd[idx][prop] = $(this).val();
					} else {
						delete ds.dd[idx][prop];
					}
				} else {
					ds.dd[idx][prop] = $(this).val();
				}

				if ($(this).hasClass('dv-style')) {
					var type = $(this).data('style-type');
					var val = $(this).data('style-val');

					ds.dd[idx][prop] = val;

					if (val !== '') {
						style += type + ' : ' + val + ';';
					} else {
						delete ds.dd[idx][prop];
					}
				}

				if (prop == 'units' && $(this).val() == '') {
					delete ds.dd[idx][prop];
				}
			});

			if (typeof ds.dd[idx]['linktype'] == 'undefined') {
				delete(ds.dd[idx]['linkpath']);
			}

			if (style != '') {
				ds.dd[idx]['styles'] = style;
			} else {
				delete ds.dd[idx]['styles'];
			}

			update_col_properties(tab, col);
		}

		function update_col_properties(tab, col) {
			var idx = col.idx;

			// Label
			var label = col.label;
			var units = col.units || '';
			units = (units != '') ? '<br />[<small>' + units + '</small>]' : '';

			$('div.col-edit:eq(' + idx + ')').closest('th').find('.DataTables_sort_wrapper').html(label + units);

			// Description
			$('div.col-edit:eq(' + idx + ')').parent().attr('title', col.desc);

			// Width
			if (col.width) {
				$('div.col-edit:eq(' + idx + ')').closest('th').css('min-width', col.width + 'px');
			}



			// Table cell data changes
			if (col.type == 'link') {
				$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
					var val = ds.data[i][col.idx];
					var is_repo = (col.linktype && col.linktype == 'repofiles') ? true : false;
					var rel_path = (col.linkpath && col.linkpath != '') ? '/' + col.linkpath : '';

					if (val != '' && is_repo) {
						var file = val;
						val = ds.repo.base + rel_path + '&file=' + val;
						val = '<a href="' + val + '">' + file + '</a>';
					} else if (val != '') {
						delete(col.linkpath);
						val = '<a href="' + val + '">' + val + '</a>';
					}

					$(this).html(val);
				});
			} else if (col.type == 'image') {
				$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
					var val = ds.data[i][col.idx];
					var is_repo = (col.linktype && col.linktype == 'repofiles') ? true : false;
					var rel_path = (col.linkpath && col.linkpath != '') ? '/' + col.linkpath : '';

					if (val != '' && is_repo) {
						val = ds.repo.base + rel_path + '&file=' + val;
						val = '<a href="' + val + '"><img src="' + val + '" style="height: 35px;" /></a>';
					} else if (val != '') {
						val = '<a href="' + val + '"><img src="' + val + '" style="height: 35px;" /></a>';
					}

					$(this).html(val);
				});
			} else if (col.type == 'email') {
				$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
					var val = ds.data[i][col.idx];
					val = (val != '') ? '<a href="mailto:' + val + '">' + val + '</a>' : '';
					$(this).html(val);
				});
			} else if (col.truncate == 'truncate') {
				$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
					var val = ds.data[i][col.idx];
					var p_width = tab.find('thead th:eq(' + col.idx + ')').css('min-width');
					val = '<p class="truncate" title="' + val.entityify() + '" style="width: ' + p_width + '; white-space: nowrap;">' + val + '</p>'
					$(this).html(val);
					if ($('#prj-db-col-width').val() == '') {
						ds.dd[col.idx]['width'] = '70';
					}
				});
			} else {
				$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
					var val = ds.data[i][col.idx];
					$(this).html(val);
				});
			}


			// Update text align
			$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
				$(this).removeClass('left right center');
				$(this).addClass(ds.dd[idx]['align']);
			});

			// Cell Formatting
			ds.dd[idx]['styles'] = '';
			if (typeof ds.dd[idx]['text-color'] != 'undefined') {
				ds.dd[idx]['styles'] += 'color: ' + ds.dd[idx]['text-color'] + ';';
			}

			if (typeof ds.dd[idx]['bg-color'] != 'undefined') {
				ds.dd[idx]['styles'] += 'background: ' + ds.dd[idx]['bg-color'] + ';';
			}

			$('td:eq(' + col.idx + ')', tab.fnGetNodes()).each(function(i, v) {
				if (ds.dd[idx]['styles'] != '') {
					$(this).attr('style', ds.dd[idx]['styles']);
				} else {
					$(this).removeAttr('style');
				}
			});

		}


		// Init color picker
		$(".color-picker").spectrum({
			showInput: true,
			className: "full-spectrum",
			showInitial: true,
			showPalette: true,
			showSelectionPalette: true,
			maxPaletteSize: 10,
			preferredFormat: "hex",
			localStorageKey: "dataview.cellformatting",
			show: function () {
				if ($(this).data('style-val') == '') {
					$(this).spectrum('set', 'rgba(255, 255, 255, 1)');
				}
			},
			hide: function () {
				if ($(this).data('style-val') == '') {
					$(this).spectrum('set', 'rgba(255, 255, 255, 0)');
				}
			},
			change: function(color) {
				$(this).data('style-val', color.toHexString());
			},
			palette: [
				["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
				"rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
				"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
				["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
				"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
				"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
				"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
				"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
				"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
				"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
				"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
				"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
				"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
			]
		});

		$('.color-picker-clear').click(function() {
			$elm = $('#' + $(this).data('target'));
			$elm.spectrum('set', $elm.data('default'));
			$elm.data('style-val', '');
		});




		// Step 3
		$('#prj-db-step-2 .prj-db-next').on('click', function () {
			$('#prj-db-step-2').hide();
			$('#prj-db-step-3').show();
			$('.main-content').css('margin-left', $('.main-menu').show().width());

			// Set the title and the description
			if (!ds.recreate) {
				var file = $('#prj-db-select-src').val();
				$('#prj-db-title').val(file.slice(0, -4));
				$('#prj-db-desc').val('Database using the data from ' + file + ' file');
			} else {
				$('#prj-db-title').val($('#prj-db-select-form input[name="title"]').val());
				$('#prj-db-desc').val($('#prj-db-select-form input[name="desc"]').val());
			}
		});

		$('#prj-db-finish-form').on('submit', function() {
			var dd = ds.dd;

			if ($('#prj-db-title').val() == '') {
				alert('Please enter a Title');
				return false;
			}

			$('#prj-db-finish-btn').prop('disabled', true);
			var msg = 'Please wait. Your database is being created...';
			if (HUB.Projects)
			{
				HUB.Projects.setStatusMessage(msg, true);
			}
			else
			{
				ds.status_msg(msg, true);
			}

			$.post($(this).attr('action'), {
				file: $('#prj-db-select-src').val(),
				dir: $('#prj-db-select-src option:selected').data('dir'),
				title: $('#prj-db-title').val(),
				desc: $('#prj-db-desc').val(),
				dd: JSON.stringify(dd),
				db_id: ds.db_id
			}, function(res) {
				if (res.status == 'failed') {
					if (HUB.Projects)
					{
						HUB.Projects.setStatusMessage(false, false);
					}
					else
					{
						ds.status_msg(false, false);
					}

					alert(res.msg);
					$('#prj-db-finish-btn').prop('disabled', false);
				} else {
					window.location = res.data;
				}
			}, 'json');

			return false;
		});

	});

}) (this, document, DataStore, jQuery);
