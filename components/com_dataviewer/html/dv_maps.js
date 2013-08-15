/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


jQuery(document).ready(function($) {
	var map;
	var geocoder;

	$("#dv_maps_panel").resizable();

	$('#dv_maps').click(function() {
		$('.dv_top_pannel').hide();
		if ($(this).prop('checked') === true) {
			$('.dv_panel_btn').not(this).prop('checked', false);
			$('#dv_maps_panel').show();
			if (typeof map === 'undefined') {
				dv_show_map();
			}
		} else {
			$('#dv_maps_panel').hide('fast');
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

	function dv_show_map() {
		var iterator = 0;
		var locations = [];
		var location_index = {};
		var marker_index = {};
		var iw_content = [];
		var iw_cnt = {};
		var iw_title = [];
		var infowindows = [];
		var markers = [];
		var infowindow;
		var cood_type;
		geocoder = new google.maps.Geocoder();
		var directionsDisplay =  new google.maps.DirectionsRenderer();
		var directionsService = new google.maps.DirectionsService();
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
				}

				iw_content.push(info);
				iw_cnt[idx] = info;
				iw_title.push(title);
			}
		}		

		var center_loc;
		var polyline;

		function initialize() {
			var zoom = 12;

			if (typeof dv_data.maps[0].zoom != 'undefined' && dv_data.maps[0].zoom > 0) {
				zoom = +dv_data.maps[0].zoom;
			}

			var mapOptions = {
				zoom: zoom,
				mapTypeId: (typeof dv_data.maps[0].map_type != 'undefined')? dv_data.maps[0].map_type: google.maps.MapTypeId.ROADMAP,
				center: center_loc
			};

			polyline = new google.maps.Polyline({
				path: [],
				geodesic: true,
				strokeColor: '#00FF00',
				strokeWeight: 4
			});

			map = new google.maps.Map(document.getElementById("dv_maps_canvas"), mapOptions);
			map.setTilt(45);
//			dbg = map;
		}

		if (cood_type != 'address') {
			center_loc = new google.maps.LatLng(locations[0][0], locations[0][1]);
			initialize();
		} else {
			geocoder.geocode({'address': locations[0][0]}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					center_loc = results[0].geometry.location;
					initialize();
				}
			});
		}

		if (dv_data.maps[0]['show_track'] == true) {
			var route = {};
			var loc_o = locations[0];
			var loc_d = locations[(locations.length-1)];
			route.origin = new google.maps.LatLng(loc_o[0], loc_o[1]);
			route.destination = new google.maps.LatLng(loc_d[0], loc_d[1]);
			route.travelMode = google.maps.TravelMode.DRIVING;
			route.waypoints = [];

			var skip = Math.ceil((locations.length - 2) / 8);
			for (var i = 1; i < locations.length-1; i = i + skip) {
				var loc = locations[i];
				route.waypoints.push({location: new google.maps.LatLng(loc[0], loc[1])});
				location_index[loc[2]] = l;
			}

			directionsService.route(route, function(route, status) {
				if (status == google.maps.DirectionsStatus.OK) {
//					directionsDisplay.setDirections(route);

					path = route.routes[0].overview_path;

//					$(path).each(function(index, item) {
//						polyline.getPath().push(item);
//					});
//					polyline.setMap(map);
				} else {
					alert(google.maps.DirectionsStatus);
				}
			});
		}


		path = [];

		if (path_points.length > 0) {
			for (var i=0; i<path_points.length; i++) {
				var pp = path_points[i];
				pp = pp.split(',');
				var l = new google.maps.LatLng(pp[0], pp[1]);
				path.push(l);
			}

			$(path).each(function(index, item) {
				polyline.getPath().push(item);
			});

			polyline.setMap(map);
			//dr(0, path);
		}

		polyline3 = new google.maps.Polyline({
			path: [],
			geodesic: true,
			strokeColor: '#FF0000',
			strokeWeight: 2
		});

		for (var i = 0; i < locations.length; i++) {
			var loc = locations[i];
			if (cood_type != 'address') {
				var l = new google.maps.LatLng(loc[0], loc[1]);

				marker_index[loc[2]] = addMarker(l);
				location_index[loc[2]] = l;
			} else {
				(function() {
					var idx = loc[1];
					var loc0 = loc[0];
					geocoder.geocode({'address': loc[0]}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							location_index[idx] = results[0].geometry.location;
							addMarker(results[0].geometry.location);
						}
					});
				})();
			}
		}

		function addMarker(loc) {
			var mk;
			var elevator = new google.maps.ElevationService();

			mk = new google.maps.Marker({
				position: loc,
				map: map,
				draggable: false,
				title: iw_title[iterator]
			});

			var cnt = iw_content[iterator];

			
			google.maps.event.addListener(mk, "click", function() {
				if (infowindow) infowindow.close();
				infowindow = new google.maps.InfoWindow({content: cnt});
				infowindow.open(map, mk);

				if ($(infowindow.getContent()).find('.set_elevation').length) {
					elevator.getElevationForLocations({'locations': [mk.position]}, function(results, status) {
						if (status == google.maps.ElevationStatus.OK) {
							if (results[0]) {
								infowindow.setContent(infowindow.getContent().supplant({'elevation': results[0].elevation + " meters"}));
							} else {
								infowindow.setContent(infowindow.getContent().supplant({'elevation': "No Elevation data found"}));
							}
						} else {
							alert("Elevation service failed due to: " + status);
						}
					});
				}
			});

			markers.push(mk);
			iterator++;
			return mk;
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
			
			/*
			if (infowindow) infowindow.close();
			infowindow = new google.maps.InfoWindow({content: iw_cnt[selected]});
			infowindow.open(map, mkr);
			*/
			var pos1 = mkr.getPosition();
			map.setCenter(pos1);
			window.dv_selected_mkr = mkr;
//			mkr.setAnimation(google.maps.Animation.DROP);
			
			setTimeout("google.maps.event.trigger(window.dv_selected_mkr, 'click')", 500);
		});


		var polyline2 = new google.maps.Polyline({
			path: [],
			strokeColor: '#000000',
			strokeWeight: 2
		});

		function dr(idx, path) {
			if (idx == path.length-1) {
				return;
			}

			setTimeout(function() {
				polyline2.getPath().push(path[idx]);
				polyline2.setMap(map);
				map.setCenter(path[idx]);
				idx++;
				dr(idx, path);
			}, 1000);
		}


		$(document).bind('dv_event_update_map', function() {
			if (($("#dv_maps").prop('checked') === true)) {
				dv_show_map();
			}
		});
	}

	if (typeof dv_data.maps != 'undefined') {
	
		// Hide button if the fields are unavailable... custom views
		
		if ($.inArray(dv_data.maps[0]['lat'], dv_data.vis_cols) == -1 || $.inArray(dv_data.maps[0]['lng'], dv_data.vis_cols) == -1) {
			$('#dv_maps').remove();
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

		$('.dv_panel_btn').not(this).prop('checked', false);
		$('#dv_maps_panel').show();
		if (typeof map === 'undefined') {
			dv_show_map();
		}
//		$('#dv_charts_control_panel').accordion('option', 'fillSpace', 'false');
	}
});
