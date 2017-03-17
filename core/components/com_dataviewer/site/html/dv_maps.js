/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

function dms2dc(cood)
{
	// get the degrees
	values = cood.split('°');
	degrees = parseInt(values[0]);
	remain = values[1].trim();

	// get the minutes
	values = remain.split('\'');
	minutes = parseInt(values[0]);
	remain = values[1].trim();

	// get the seconds and the direction
	values = remain.split('"');
	seconds = parseInt(values[0]);
	dir = values[1].trim();
	
	// mix values into a single value
	dc = degrees + (minutes / 60) + (seconds / (60 * 60));

	// if direction is south or west then invert the value
	if (dir == "S" || dir == "W") {
		dc = dc * -1;
	}

	// return value
	return dc;
}

jQuery(document).ready(function($) {
	var map;

	$("#dv_maps_panel").resizable();

	$('#dv-spreadsheet-maps').click(function() {
		var panel = $('#dv_maps_panel');

		if ($(this).is('.btn-inverse')) {
			panel.hide();
			$(this).removeClass('btn-inverse');
		} else {
			panel.show();
			$(this).addClass('btn-inverse');
			if (typeof map === 'undefined') {
				dv_show_map();
			}
		}
	});

	$('#dv_map_reload').click(function(e) {
		e.preventDefault();
		dv_show_map();
		return false;
	});
	
	$("#spreadsheet_filter input, tfoot input").bind('keyup', function(e) {
		if (e.keyCode !== 13) {
			return;
		}

		if ($('#dv_maps_panel:visible').length === 1) {
			dv_show_map();
		}
	});

	var marker_index = {};
	var markers;

	function dv_show_map() {
		var zoom = 12;

		if (typeof dv_data.maps[0].zoom != 'undefined' && dv_data.maps[0].zoom > 0) {
			zoom = +dv_data.maps[0].zoom;
		}

		var locations = [];
		var marker_group = [];
		var iterator = 0;
		var latlngs = [];
		var location_index = {};
		var iw_content = [];
		var iw_cnt = {};
		var iw_title = [];
		var infowindows = [];
		var infowindow;
		var cood_type;
		var path_points = [];

		var data_arr = dv_table.fnGetFilteredData();

		if (data_arr.length < 1) {
			return;
		}

		for (var i=0; i<data_arr.length; i++) {
			var lat, lng, idx, idx_col, title, info, address;
			var row = data_arr[i];
			
			if (typeof dv_data.maps[0].rec_index != 'undefined' && typeof dv_data.maps[0].rec_index == 'string') {
				idx_col = $.inArray(dv_data.maps[0].rec_index, dv_data.vis_cols);
				idx = ("" + data_arr[i][idx_col]).stripTags();
			} else if (typeof dv_data.maps[0].rec_index != 'undefined' && typeof dv_data.maps[0].rec_index != 'string') {
				idx = '';
				for (var r_idx=0; r_idx <dv_data.maps[0].rec_index.length; r_idx++) {
					idx = idx + ("" + data_arr[i][$.inArray(dv_data.maps[0].rec_index[r_idx], dv_data.vis_cols)]).stripTags()
				}
			} else {
				idx = ("" + data_arr[i][0]).stripTags();
			}

			if (typeof dv_data.maps[0].cood_type !== 'undefined' && dv_data.maps[0].cood_type === 'address') {
				if ($(data_arr[i][$.inArray(dv_data.maps[0].address, dv_data.vis_cols)]).text() !== '') {
					address = $(data_arr[i][$.inArray(dv_data.maps[0].address, dv_data.vis_cols)]).text();
				} else {
					address = data_arr[i][$.inArray(dv_data.maps[0].address, dv_data.vis_cols)];
				}
			} else {
				if ($(data_arr[i][$.inArray(dv_data.maps[0].lat, dv_data.vis_cols)]).text() !== '') {
					lat = $(data_arr[i][$.inArray(dv_data.maps[0].lat, dv_data.vis_cols)]).text();
				} else {
					lat = data_arr[i][$.inArray(dv_data.maps[0].lat, dv_data.vis_cols)];
				}

				if ($(data_arr[i][$.inArray(dv_data.maps[0].lng, dv_data.vis_cols)]).text() !== '') {
					lng = $(data_arr[i][$.inArray(dv_data.maps[0].lng, dv_data.vis_cols)]).text();
				} else {
					lng = data_arr[i][$.inArray(dv_data.maps[0].lng, dv_data.vis_cols)];
				}
			}

			if (typeof dv_data.maps[0].title != 'undefined') {
				title = dv_data.col_labels[$.inArray(dv_data.maps[0].title, dv_data.vis_cols)].stripTags();
				title += "   \n" + ("" + data_arr[i][$.inArray(dv_data.maps[0].title, dv_data.vis_cols)]).stripTags();
			} else {
				title = '';
			}

			if (typeof dv_data.maps[0].info != 'undefined') {
				info = dv_data.maps[0].info.dv_replace($, row);
			} else {
				info = '';
			}

			if (typeof dv_data.maps[0].path_points != 'undefined') {
				if ($(data_arr[i][$.inArray(dv_data.maps[0].path_points, dv_data.vis_cols)]).text() !== '') {
					pl = $(data_arr[i][$.inArray(dv_data.maps[0].path_points, dv_data.vis_cols)]).text();
				} else {
					pl = data_arr[i][$.inArray(dv_data.maps[0].path_points, dv_data.vis_cols)];
				}
				pl = pl.split("\n");

				path_points = path_points.concat(pl);
			}

			if (lat === '-' || lng === '-') {
				continue;
			} else {
				if (typeof dv_data.maps[0].cood_type !== 'undefined' && dv_data.maps[0].cood_type === 'address' && address != '' && address != '-') {
					cood_type = 'address';
					locations.push([address, idx]);
				} else {
					if (typeof dv_data.maps[0].cood_type !== 'undefined' && dv_data.maps[0].cood_type === 'dms') {
						lat = dms2dc(lat);
						lng = dms2dc(lng);
					}
					locations.push([lat, lng, idx]);
					latlngs.push([lat, lng]);
				}

				iw_content.push(info);
				iw_cnt[idx] = info;
				iw_title.push(title);
			}
		}


		if(typeof map == 'undefined') {
			map = L.map('dv_maps_canvas', {
				scrollWheelZoom: false
			});

			map.attributionControl.setPrefix('');
			L.control.scale().addTo(map);
			var minimal = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//				maxZoom: zoom			
			}).addTo(map);
			map.fitBounds(latlngs);
		} else {
			markers.clearLayers();
			map.fitBounds(latlngs);
		}

		map.setView([locations[0][0], locations[0][1]], zoom);

		
		marker_index = {};
		for (var i = 0; i < locations.length; i++) {
			var loc = locations[i];
			if (cood_type != 'address') {
				var idx = loc[2];
				marker_index[idx] = L.marker([loc[0], loc[1]]).bindPopup(iw_cnt[idx]);
				marker_group.push(marker_index[idx]);
			} else {
				
			}
		}

		
		markers = L.layerGroup(marker_group).addTo(map);

		map.on('popupopen', function(e) {
			$('div.leaflet-popup-content img.lazy-load').each(function() { this.src = $(this).data('original')});
		});

	}

	$('#dv-spreadsheet-tbl').on('dblclick', 'tr', function() {
		var selected = '';
		
		if (typeof dv_data.maps[0].rec_index == 'undefined') {
			idx_col = 0;
			if ($(this).find('td:eq(' + idx_col + ')').text() !== '') {
				selected = $(this).find('td:eq(' + idx_col + ')').text();
			} else {
				selected = $(this).find('td:eq(' + idx_col + ')');
			}
		} else if (typeof dv_data.maps[0].rec_index == 'string') {
			idx_col = $.inArray(dv_data.maps[0].rec_index, dv_data.vis_cols);
			if ($(this).find('td:eq(' + idx_col + ')').text() !== '') {
				selected = $(this).find('td:eq(' + idx_col + ')').text();
			} else {
				selected = $(this).find('td:eq(' + idx_col + ')');
			}
		} else if (typeof dv_data.maps[0].rec_index != 'string') {
			for (var r_idx=0; r_idx <dv_data.maps[0].rec_index.length; r_idx++) {
				idx_col = $.inArray(dv_data.maps[0].rec_index[r_idx], dv_data.vis_cols);

				if ($(this).find('td:eq(' + idx_col + ')').text() !== '') {
					selected = selected.trim() + $(this).find('td:eq(' + idx_col + ')').text();
				} else {
					selected = selected.trim() + $(this).find('td:eq(' + idx_col + ')');
				}
			}
		}

		selected = (("" + selected).stripTags()).trim();
		var mkr = marker_index['' + selected]; // TODO: use one list
		mkr.openPopup();
		map.panTo(mkr.getLatLng());
		
		return false;
	});

	$('#dv_maps_panel').bind('resizestop', function(event, ui) {
		map.invalidateSize();
	});


	$('#dv_map_reload').click(function(e) {
		dv_show_map();
	});

    $(document).on('dv_event_update_map', function() {
    	if ($('#dv_maps_panel:visible').length === 1) {
			dv_show_map();
		}
    });


	if (typeof dv_data.maps != 'undefined') {
	
		// Hide button if the fields are unavailable... custom views
		
		if ($.inArray(dv_data.maps[0]['lat'], dv_data.vis_cols) == -1 || $.inArray(dv_data.maps[0]['lng'], dv_data.vis_cols) == -1) {
			$('#dv-spreadsheet-maps').remove();
			$('label[for="dv_maps"]').remove();
			dv_show_maps = undefined;
		}

		if (typeof dv_data.maps[0].width != 'undefined') {
			$('#dv_maps_panel').width(dv_data.maps[0].width);
		} else {
			if ($('#dv_maps_panel').width() < ($(window).width() - 100)) {
				$('#dv_maps_panel').width($(window).width() - 100);
			}
		}

		if (typeof dv_data.maps[0].height != 'undefined') {
			$('#dv_maps_panel').height(dv_data.maps[0].height);
		} else {
			$('#dv_maps_panel').height(380);
		}
	}

	if(typeof dv_show_maps != 'undefined') {

		$('#dv-spreadsheet-maps').trigger('click');
	}
});
