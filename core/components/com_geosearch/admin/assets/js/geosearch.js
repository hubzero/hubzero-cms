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
var markersCount = 0;
var markerList = []; //keeps track of markers
var debug = true;

var membersDisplayed = 0;
var eventsDisplayed  = 0;
var jobsDisplayed    = 0;
var orgsDisplayed    = 0;

var $ = jQuery;


$('document').ready(function(){

		var latlng = new google.maps.LatLng(40.397, -86.900);
		var mapOptions = {
			scrollwheel: false,
			zoom: 2,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

	map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);

	// Scroll the modal
	$(window).on("scroll", function() {
		var scrollTop = $(window).scrollTop();
		$('.map-editor').css('top', scrollTop);

		if (debug) {
			console.log('scrolling: ' + scrollTop);
		}

	});
		
	/*********************
		Adjust the marker
	*********************/
	$('.adjust').click(function(e) {
		e.preventDefault();
			
		// Get the ID
		var identifier = $(this).closest('tr').attr('data-scopeID');
		var scope = $(this).closest('tr').attr('data-scope');
	
		// Debugging statement
		if (debug)
		{
			console.log('Adjust ' + scope + identifier);
		}


		$('.map-editor').show();

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

			$('.location-title').html('<b>Location:</b> ' + location);
			$('.item-title').html('<b>Name:</b> ' + title);
		}); // end done()

		if (debug) {
			console.log('API request: ' + url);
		}

	}); // end .adjust click

	// Escape the editor
/*	$('.map-editor').click(function(){
		$('.map-editor').hide();
	});
	*/
	$('#exit-button').click(function(){
		$('.map-editor').hide();
	});

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
