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

var membersDisplayed = 0;
var eventsDisplayed  = 0;
var jobsDisplayed    = 0;
var orgsDisplayed    = 0;


HUB.Geosearch = {
	jQuery: jq,

	//initialize: function(latlng,uids,jids,eids,oids)
	initialize: function(latlng)
	{
		var $ = this.jQuery;

		var mapOptions = {
			scrollwheel: false,
			zoom: 2,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);
		//bounds = new google.maps.LatLngBounds();
		infoWindow = new google.maps.InfoWindow();
		oms = new OverlappingMarkerSpiderfier(map, {keepSpiderfied:true});

		// create info windows, callback for click event
		oms.addListener('click', function(marker, event) {

			//clear it
			infoWindow.setContent('');
			switch (marker.scope)
			{
				case "member":
						var url = "/api/members/" + marker.scope_id;
						var data = {};
				break;
				case "event":
						var url = "/api/events/" + marker.scope_id;
						var data = { nicedate : 1};
				break;
				case "job":
					var url = "/api/jobs/job"
					var data = { jobcode : marker.scope_id}
				break;
				case "organization":
					var url = "/api/members/organizations";
					/* not supported yet */
					//var data = { orgID : marker.scope_id};
				break;
			} //end switch

			$.ajax({
				url:url,
				data:data
			})
			.done(function( data ) {

				var html = '';
				switch (marker.scope)
				{
					case "member":
						var name = data.profile.name;
						var organization = data.profile.organization;
						var link = "/members/" + data.profile.id
						var thumb = data.profile.picture.full;

						//contruct the popup view
						html += '<div data-id="' + marker.id + '" class="popup member-popup">'; // classes for styling, because CSS
						html += '<img src="' + thumb + '">';
						html += '<h1>'+name+'</h1>';
						html += '<p class="organization">'+organization+'</p>';
						html += '<a href="'+link+'"><span class="link">'+data.profile.first_name+'\'s Profile</p></span>';
						html += '</div>';

					break;
					case "event":
						// get the data for the popup view
						var title = data.event.title;
						var content = data.event.content;
						var start =  data.event.publish_up;
						var end = data.event.publish_down;
						var location = data.event.adresse_info;
						var link = "/events/details/" + data.event.id;

						//contruct the popup view
						html += '<div data-id="' + marker.id + '" class="popup event-popup">'; // classes for styling, because CSS
						html += '<h1>'+title+'</h1>';
						html += '<p class="date">'+start+' - '+ end + '</p>';
						html += '<p class="location">'+location+'</p>';
						html += '<p>'+content+'</p>';
						html += '<a href="'+link+'"><span class="link">Event Details</p></span>';
						html += '</div>';
					break;
					case "job":
						// get the data for the popup view
						var title = data.job.title;
						var jobcode = data.job.code;
						var type = data.job.typename;
						var description = data.job.description;
						var company = data.job.companyName;
						var website =  data.job.companyWebsite;
						var location = data.job.companyLocation;
						var country = data.job.companyLocationCountry;
						var link = "/jobs/job/" + data.job.code;

						//contruct the popup view
						html += '<div data-id="' + marker.id + '" class="popup job-popup">'; // classes for styling, because CSS
						html += '<h1>'+title+'</h1>';
						html += '<p class="type">'+type+'</p>';
						html += '<a href="'+ website +'" class="company">' + company + '</a>';
						html += '<p class="location">'+location+'</p>';
						html += '<p class="location">'+country+'</p>';
						html += '<br />';
						html += '<a class="link" href="'+link+'">View Full Job Posting</a>';
						html += '</div>';

					break;
					case "organization":
						// organization table needs to be updated for searching by ID
						var organizations = data.organizations;
						var title = '';
						$(organizations).each(function(org){
							if (this.id == marker.scope_id)
							{
								title = this.organization;
							}
						});

						//contruct the popup view
						html += '<div data-id="' + marker.id + '" class="popup org-popup">'; // classes for styling, because CSS
						html += '<h1>'+title+'</h1>';
						html += '</div>';

					break;
				} //end switch

				infoWindow.setContent(html);
			});
			  infoWindow.open(map, marker);
		});

		// get markers
		this.loadMarkers();
	},

	loadMarkers: function()
	{
		var $ = this.jQuery;

		// container for holding desired markers to display
		var resources = [];

		// get the resources desired for display
		$('input:checkbox:checked.resck').each(function(){
			resources.push(this.value);
		});

		//get the markers
		$.post("index.php?option=com_geosearch&task=getMarkers",
		{
			resources: resources
		},
		function(data)
		{
			var markers = $.parseJSON(data);

			// places correct icon type
			// @todo: make dynamic types, icon would be stored in DB
			$.each(markers, function(index, marker)
			{
				switch(marker.scope)
				{
					case "member":
						var icon = "/core/components/com_geosearch/site/assets/img/icn_member.png";
						break;
					case "event":
						var icon = "/core/components/com_geosearch/site/assets/img/icn_event.png";
						break;
					case "job":
						var icon = "/core/components/com_geosearch/site/assets/img/icn_job.png";
						break;
					case "organization":
						var icon = "/core/components/com_geosearch/site/assets/img/icn_org.png";
					break;
				}

				//var loc = new map.LatLng(marker.addressLatitude, marker.addressLongitude);
				var mlatlng = new google.maps.LatLng(marker.addressLatitude, marker.addressLongitude);
				var point = new google.maps.Marker({
					position: mlatlng,
					map: map,
					icon:icon,
					scope: marker.scope,
					scope_id: marker.scope_id,
					id: marker.id
				});

				// this adds the spidifier making clusters of markers easier to read.
				oms.addMarker(point)

				// keep track of markers so we can remove them later.
				markerList.push(point);

			}); // end each()

		});


	},

	displayCheckedMarkers: function()
	{
		// clear map
		$.each(markerList, function(index, marker)
		{
			marker.setMap(null);
		});

		HUB.Geosearch.loadMarkers();
	},

	createMarker: function(mlatlng, uid, type)
	{

	},

	reportMarker: function()
	{
		var id = $('.popup').attr('data-id');
		if (id > 0)
		{
			$.ajax({url:'/geosearch?task=reportMarker',
				data:{markerID:id },
				method: "POST"});

				alert('This location will be reviewed.');
		}
	}
};

//-----------------------------------------------------------

jQuery(document).ready(function($){

	var latlng = new google.maps.LatLng(40.397, -86.900);
	HUB.Geosearch.initialize(latlng);

	$('.resck').on('click', function(event){
		HUB.Geosearch.displayCheckedMarkers();
	});

	$('#reportMarker').on('click', function(event) {
		HUB.Geosearch.reportMarker();
	});
});
