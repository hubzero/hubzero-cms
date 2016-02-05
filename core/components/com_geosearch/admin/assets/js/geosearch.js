/**
 * @package     hubzero-cms
 * @file        components/com_groups/assets/js/groups.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 * @author			Kevin Wojkovich <kevinw@purdue.edu>
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Global-ish variables
// ---------------------------------------------------------

var map;
var bounds;
var infoWindow;
var oms;
var markerList = []; //keeps track of markers
var originLat;
var orignLng;
var coordinate;
var debug = true;
// A bucket to collect new markers
var newMarker; 
var markerID = 0;

var $ = jQuery;

// Adds a marker to the map and push to the array.
function addMarker(location) {
  var marker = new google.maps.Marker({
	position: location,
  map: map
  });
  markerList.push(marker);
}
// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
  setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
  clearMarkers();
  markerList = [];
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
  for (var i = 0; i < markerList.length; i++) {
    markerList[i].setMap(map);
  }
}

$('document').ready(function(){

		var latlng = new google.maps.LatLng(40.397, -86.900);
		var mapOptions = {
			zoom: 3,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

	map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);


	map.addListener('click', function(event) {

				// Clear the map
				deleteMarkers();

				newMarker = event.latLng;
				if (debug)
				{
					console.log('New Marker:');
					console.log(newMarker);
				}		

				// Push markers to the map
    		addMarker(event.latLng);
				addMarker(coordinate);

				// Show the save button 
				$('#saveLocation').show();
	});

		
	/*********************
		Adjust the marker
	*********************/
	$('.adjust').click(function(e) {
		e.preventDefault();

		// Get the lat and long 
		originLat = $(this).attr('data-lat');
		originLng = $(this).attr('data-long');

		if (debug)
		{
			console.log('lat: ' + originLat);
			console.log('lng: ' + originLng);
		}

		// Add marker original
		coordinate = new google.maps.LatLng(originLat, originLng);
		addMarker(coordinate);

		// Pan & Zoom in to the point 
		map.panTo(coordinate);
		map.setZoom(5);
		
		// Get the ID
		var identifier = $(this).closest('tr').attr('data-scopeID');
		var scope = $(this).closest('tr').attr('data-scope');
		markerID = $(this).closest('tr').attr('data-markerID');
	
		// Debugging statement
		if (debug)
		{
			console.log('Adjust ' + scope + identifier);
		}

		switch (scope)
		{
			case "member":
					var url = "/api/members/" + identifier;
					var data = {};
			break;
			case "event":
					var url = "/api/events/" + identifier;
					var data = { nicedate : 1};
			break;
			case "job":
				var url = "/api/jobs/job"
				var data = { jobcode : identifier}
			break;
			case "organization":
				var url = "/api/members/organizations";
				var data = {id : identifier}
			break;
		} //end switch

		// Go fetch information about this pin
		$.ajax({
				url:url,
				data:data
			})
			.done(function( data ) {

			if (debug) {
				console.log(data);
			}

			var html = '';
			switch (scope)
			{
				case "member":
					var title = data.profile.name;
					var location = data.profile.organization;
				break;
				case "job":
					// get the data for the popup view
					var title = data.job.title;
					var location = data.job.companyLocation;
				break;
				case "event":
					var title = data.event.title;
					var location = data.event.adresse_info;
				break;
				case "organization":
					// organization table needs to be updated for searching by ID
					var organizations = data.organizations;
					var title = '';
					$(organizations).each(function(org){
						if (this.id == identifier)
						{
							title = this.organization;
							location = this.organization;
						}
					});
				break;
			} //end switch

			$('.location-title').html('<b>Original Location:</b> ' + location);
			$('.item-title').html('<b>Marker Name:</b> ' + title);
		}); // end done()

		if (debug) {
			console.log('API request: ' + url);
		}
	}); // end .adjust click

	/*********************
	 Save the new location
	**********************/
	$('#saveLocation').click(function(e){
		e.preventDefault();
		if (debug)
		{
			console.log(newMarker.G);
			console.log(newMarker.K);
		}

		var lat = newMarker.G;
		var lng = newMarker.K;

		// Confirm location
		var confirmation = confirm('Update the location to (' + lng + ' , ' + lat + ') ?');
		if (confirmation)
		{
			$.ajax({url:'/administrator/index.php?option=com_geosearch&task=updateMarker',
				data: {lat:lat, lng:lng, flag:0, markerID:markerID},
				method: "POST"})
			.done(function() {
				location.reload();
			});
		}

	}); // end #saveLocation click
	/*********************
		Remove the marker
	*********************/
	$('.remove').click(function(e) {
		e.preventDefault();

		var confirmation = confirm('Are you sure you want to remove this marker?');

		// Get the ID
		var identifier = $(this).val();
	
		if (confirmation)
		{
			// Debugging statement
			if (debug)
			{
				console.log('Remove:' + identifier);
			}
			
			// Navigate to remove item
		}
	}); // end .remove click
}); // end document.ready

console.log('here');
