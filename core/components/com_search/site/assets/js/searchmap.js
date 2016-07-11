// Map selection example (selecting coordinates of a search rectangle)

var BASE_PROJ = 'EPSG:4326'; // default projection
console.log('here');

var vectors;
var box;
var transform;
var map;

function endDrag(bbox) {
	var bounds = bbox.getBounds();
	setBounds(bounds);
	drawBox(bounds);
	box.deactivate();
}

function boxResize(event) {
	setBounds(event.feature.geometry.bounds);
}

function drawBox(bounds) {
	var feature = new OpenLayers.Feature.Vector(bounds.toGeometry());
	vectors.addFeatures(feature);
	if (typeof transform !== 'undefined') {
		transform.setFeature(feature);
	}
}

String.prototype.toNum = function() {
	return parseInt(this,10);
}

function toPrecision(zoom, value) {
	var decimals = Math.pow(10,Math.floor(zoom/3));
	return Math.round(value * decimals) / decimals;
}

function setBounds(bounds) {
	b = bounds.clone().transform(map.getProjectionObject(), new OpenLayers.Projection(BASE_PROJ));
	minlon = toPrecision(map.getZoom(), b.left);
	minlat = toPrecision(map.getZoom(), b.bottom);
	maxlon = toPrecision(map.getZoom(), b.right);
	maxlat = toPrecision(map.getZoom(), b.top);
	$('#minlat').val(minlat);
	$('#maxlat').val(maxlat);
	$('#minlon').val(minlon);
	$('#maxlon').val(maxlon);
	displayBounds();
}

function displayBounds() {
	var output = ''; // &nbsp;&nbsp;&nbsp;';
	output    += 'Latitude: <strong>' +$('#minlat').val()+'.0 to '+$('#maxlat').val()+'.0';
	output    += '</strong>, ';
	output    += 'Longitude: <strong>'+$('#minlon').val()+'.0 to '+$('#maxlon').val()+'.0';
	output    += '</strong>';

	// First, clear text criteria
	$('#pattern').val('');

	// Display bounds
	$('#bounds').html(output);
	$('#map').focus();
}

function resetSearchFeatures() {
	vectors = new OpenLayers.Layer.Vector('Vector Layer', {displayInLayerSwitcher: false});
	map.addLayer(vectors);

	box = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon, {
		handlerOptions: {
			sides     : 4
			,snapAngle: 90
			,irregular: true
			,persist  : true
		}
	});

	box.handler.callbacks.done = endDrag;
	map.addControl(box);

	transform = new OpenLayers.Control.TransformFeature(vectors, {
		rotate     : false
		,irregular : true
	});
	transform.events.register("transformcomplete", transform, boxResize);
	map.addControl(transform);
	box.activate();
}

function init_search_map(baseNum, zoomLimit) {

	// Create map
	map = new OpenLayers.Map('map',{
		controls:[
			 new OpenLayers.Control.Navigation()
			,new OpenLayers.Control.ZoomPanel()
			,new OpenLayers.Control.ScaleLine()
			,new OpenLayers.Control.Attribution()
			,new OpenLayers.Control.MousePosition(
				{
					 prefix           : '' // 'LAT/LONG: '
					,displayProjection: new OpenLayers.Projection(BASE_PROJ)
					,emptyString      : '(off map)'
					,numDigits        : 3
				}
			)
		]
	});

	// Limit zoom-out
	center = new OpenLayers.LonLat(0, 0);
	map.events.register("zoomend", this, function (e) {
		// Use "2" for vmap0 and worldmap_world_1004, "0" for bing
		if (map.getZoom() < zoomLimit) {
			map.setCenter(center,zoomLimit,false,true);
		}
	});

	// Create base layer
	switch (baseNum) {
		case 0:
			var base = new OpenLayers.Layer.WMS(
				"osgeo"
				,"http://vmap0.tiles.osgeo.org/wms/vmap0"
				,{layers: 'basic'} );
			break;
		case 1:
			var base = new OpenLayers.Layer.WMS(
				"OpenLayers WMS"
				,"https://geoserver.rcac.purdue.edu:8443/geoserver/geoshare/wms"
				,{layers: 'worldmap_world_1004'} );
			break;
		case 2:
			var base = new OpenLayers.Layer.Bing({
				name : 'bing'
				,key : 'AhJbCY6RNyaw4P5EkEq4xgI75lO8rlIyO55HWJg9Va1r5axbZLNywWQCvLPC9Ucy' // 'AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf'
				,type: 'AerialWithLabels' // 'Road', 'Aerial'
			})
			break
		default:
			alert('Invalid base layer.');
			break
	};
	map.addLayer(base);

	// Mode 1: Accepting search input - let user draw search box
	if ($('#minlon').val().length == 0) {
		resetSearchFeatures();
	}

	// Mode 2: Search failed - show user search box they used
	else {
		var bounds = new OpenLayers.Bounds($('#minlon').val().toNum(),$('#minlat').val().toNum(),$('#maxlon').val().toNum(),$('#maxlat').val().toNum());

		// Transform bounds in case base proj is different
		source = new OpenLayers.Projection(BASE_PROJ);
		dest   = new OpenLayers.Projection(map.getProjection());
		bounds.transform(source,dest);

		// Draw existing box
		var boxLayer  = new OpenLayers.Layer.Boxes('Box Layer');
		var searchBox = new OpenLayers.Marker.Box(bounds);
		boxLayer.addMarker(searchBox);
		map.addLayer(boxLayer);
		displayBounds();
	}

	map.setCenter(new OpenLayers.LonLat(0,0),2);

	// Clear bounds display when text criteria changes
	$('#pattern').focus( function () {
		$('#bounds').html('');
		vectors.destroyFeatures();
		map.removeLayer(vectors);
		resetSearchFeatures();
	});

	$('#map').click(function () {
		$('#pattern').blur();
	});

	$('#map').hover(function() {
		$('#pattern').blur();
	});

	$('.olControlZoomInItemInactive'         ).html('+');
	$('.olControlZoomToMaxExtentItemInactive').html('O');
	$('.olControlZoomOutItemInactive'        ).html('&ndash;');
}

$(document).ready(function() {
	init_search_map(0,1); // baselayer (0=osgeo,1=Geoserver,2=Bing), init zoom (change w/base)
});

