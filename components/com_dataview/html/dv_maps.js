/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

jQuery(document).ready(function($) {
	var map;

	$('#dv_maps').click(function() {
		$('.dv_top_pannel').hide();
		if ($(this).attr('checked') === true) {
			$('.dv_panel_btn').not(this).attr('checked', false);
			$('#dv_maps_panel').show();
			if (typeof map === 'undefined') {
				dv_show_map();
			}
		} else {
			$('#dv_maps_panel').hide('fast');
		}
	});

	function dv_show_map() {
		var iterator = 0;
		var locations = [];
		var location_index = [];
		var iw_content = [];
		var infowindows = [];
		var markers = [];
		var infowindow;

		var data_arr = dv_table.fnGetFilteredData();

		if (data_arr.length < 1) {
			return;
		}

		for (var i=0; i<data_arr.length; i++) {
			var lat, lng, idx, title;

			if ($(data_arr[i][0]).text() !== '') {
				idx = $(data_arr[i][0]).text();
			} else {
				idx = data_arr[i][0];
			}

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

			if ($(data_arr[i][$.inArray(dv_data.maps[0].title, dv_data.vis_cols)]).text() !== '') {
				title = dv_data.col_labels[$.inArray(dv_data.maps[0].title, dv_data.vis_cols)];
				title += '<br />' + $(data_arr[i][$.inArray(dv_data.maps[0].title, dv_data.vis_cols)]).text();
			} else {
				title = dv_data.col_labels[$.inArray(dv_data.maps[0].title, dv_data.vis_cols)];
				title += '<br />' + data_arr[i][$.inArray(dv_data.maps[0].title, dv_data.vis_cols)];
			}

			if (lat === '-' || lng === '-') {
				continue;
			} else {
				if (typeof dv_data.maps[0].cood_type !== 'undefined' && dv_data.maps[0].cood_type === 'dms') {
					lat = dms2dc(lat);
					lng = dms2dc(lng);
				}
				locations.push(new google.maps.LatLng(lat, lng));
				a = new google.maps.LatLng(lat, lng);
				location_index[idx] = (new google.maps.LatLng(lat, lng));

				iw_content.push(title);
			}
		}

		function initialize() {
			var mapOptions = {
				zoom: 5,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				center: locations[0]
			};

			map = new google.maps.Map(document.getElementById("dv_maps_canvas"), mapOptions);
			dbg = map;
		}

		initialize();

		for (var i = 0; i < locations.length; i++) {
			addMarker();
		}

		function addMarker() {
			var mk;

			mk = new google.maps.Marker({
				position: locations[iterator],
				map: map,
				draggable: false
			});

			var cnt = iw_content[iterator];

			google.maps.event.addListener(mk, "click", function() {
				if (infowindow) infowindow.close();
				infowindow = new google.maps.InfoWindow({content: cnt});
				infowindow.open(map, mk);
			});

			markers.push(mk);
			iterator++;
		}

		function dms2dc(cood) {
			cood = cood.split('°');
			d = cood[0];
			cood = cood[1].split('\'');
			m = cood[0];
			cood = cood[1].split('"');
			s = cood[0];
			dir = cood[1];

			var dc = +d + (+m/60) + (+s/(60*60));

			if (dir.trim() == "S" || dir.trim() == "W") {
				dc = dc * -1;
			}

			return dc.toFixed(6);
		}

		$('#dv_maps_panel').bind('resizestop', function(event, ui) {
			google.maps.event.trigger(map, 'resize');
		});

		$('#spreadsheet tbody tr').live('dblclick', function() {
			var selected;

			if ($(this).find('td').first().text() !== '') {
				selected = $(this).find('td').first().text();
			} else {
				selected = $(this).find('td').first();
			}

			map.setCenter(location_index[selected]);
		});

		$(document).bind('dv_event_update_map', function() {
			if (($("#dv_maps").attr('checked') === true)) {
				dv_show_map();
			}
		});
	}
});
