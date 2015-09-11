/*
 * @package     hubzero-cms
 * @file        modules/mod_youtube/assets/js/mod_youtube.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

var youtube_feed = 0;


(function($){
	$.fn.youtube = function(options) {
		//options set by call to class
		var settings = $.extend( {
			type: "playlistItems",
			search: "",
			count: 3,
			random: false,
			google_api_browser_key: "",
			details: {
				showLogo: true,
				altLogo: "",
				showTitle: true,
				altTitle: "",
				showDesc: true,
				altDesc: "",
				showLink: true,
				altLink: ""
			}
		}, options);

		if (settings.google_api_browser_key == "") {
			throw("No Google API key specified, please see mod_youtube configuration.");
		}

		//base Youtube API URL
		var url = "https://www.googleapis.com/youtube/v3/";

		//querystring for Youtube API Call
		var youtube_querystring = "&key=" + settings.google_api_browser_key; 

		return this.each(function(i, widget) {
			//increment the feed count
			youtube_feed++;

			//build url based on type
			switch(settings.type)
			{
				case 'playlistItems':
					url += "playlistItems?part=snippet&playlistId=" + settings.search + youtube_querystring;
					break;
				case 'users':
					url += "search?part=snippet&q=" + settings.search + youtube_querystring;
					break;
				case 'videos':
					url += "search?part=snippet&q=" + settings.search + youtube_querystring;
					break;
			}

			$.getJSON(url, {}, function(data) {
				$(widget).html(Format(data));
			});
		});

		//format json data into tweets
		function Format(json) {
			var html = "",
				feed = json,
				videos = feed.items;
			if (settings.type == "playlistItems") {
				url = "https://www.googleapis.com/youtube/v3/playlists?part=snippet&id=" + settings.search + "&key=" + settings.google_api_browser_key;
				$.getJSON(url, {}, function(data) {
				  feed.snippet = data.items[0].snippet;
				});
			}

			//if we want random
			if (settings.random) {
				videos.sort(function() {return 0.5 - Math.random()});
			}

			//build the feed details
			html += FeedDetails( feed );

			//build html
			html += "<ul>";
			var displayed_count = 0;
			$.each(videos, function(i, item) {
				if (displayed_count >= settings.count || (item.kind == "youtube#searchResult" && item.id.kind != "youtube#video")) {
					html += "";
				} else {
					displayed_count++;
					var title = item.snippet.title;
					var thumb = item.snippet.thumbnails.default.url;
					if (settings.type == 'playlistItems') {
						var id = item.snippet.resourceId.videoId;
					} else {
						var id = item.id.videoId;
					}
					
					//run another api call for each video to retrieve the duration of each video
					url = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=" + id + "&key=" + settings.google_api_browser_key;

					$.getJSON(url, {}, function(data) {
						$("#yt" + id).html(Duration(data.items[0].contentDetails.duration));
					});

					html += "<li>";
					html += "<a class=\"entry-thumb\" rel=\"external\" title=\"" + title + "\" href=\"http://youtube.com/watch?v=" + id + "\"><img src=\"" + thumb.replace('http://','https://') + "\" alt=\"" + title + "\" width=\"80\" /></a>";
					html += "<a class=\"entry-title\" rel=\"external\" title=\"" + title + "\" href=\"http://youtube.com/watch?v=" + id + "\">" + title + "</a>";
					html += "<br /><span class=\"entry-duration\" id=yt" + id + "></span>";
					html += "</li>";
				}
			});
			html += "</ul>";

			//build the feed link
			html += FeedLink(feed);

			return html;
		}

		function FeedDetails(feed) {
			var topHTML = "";
			var title, description, logo;
			var details = settings.details;
return "";
			// if we have no details return nothing
			if (!details)
				return "";

			// set title based on if we have an alternative title
			if (details.altTitle && details.altTitle != "") {
				title = details.altTitle;
			} else {
				title = feed.title.$t;
			}

			// set description based on if we have an alternative desc
			if (details.altDesc && details.altDesc != "") {
				description = details.altDesc;
			} else {
				description = (feed.media$group && feed.media$group.media$description) ? feed.media$group.media$description.$t : '';
			}

			// set the logo based on if we have an alternative image
			if (details.altLogo && details.altLogo != "") {
				logo = details.altLogo;
			} else {
				logo = "https://www.youtube.com/img/pic_youtubelogo_123x63.gif";
			}

			// do we want to show the title
			if (details.showTitle) {
				topHTML += "<h3>" + title + "</h3>";
			}

			// do we want to show the description
			if (details.showDesc) {
				topHTML += "<p class=\"description\">" + description + "</p>";
			}

			//do we want to show the logo
			if (details.showLogo) {
				topHTML += "<img class=\"logo\" src=\"" + logo + "\" alt=\"Youtube Feed\" />";
			}

			// return html for feed details
			return topHTML;
		}

		function FeedLink(feed) {
			var bottomHTML = "";
			var link;
			var details = settings.details;

			//if we have no details return nothing
			if (!details) {
				return "";
			}

			//set link based on if we have an alternative link
			if (details.altLink != "" && details.altLink) {
				link = details.altLink;
			} else {
				switch (settings.type) {
					case 'playlists':
						link = "http://www.youtube.com/view_play_list?p=" + settings.search;
						break;
					case 'users':
						link = "http://www.youtube.com/user/" + settings.search;
						break;
					case 'videos':
						link = "http://www.youtube.com/results?search_query=" + settings.search;
						break;
				}
			}

			// Do we want to show the link
			if (details.showLink) {
				bottomHTML += '<p class="more"><a href="' + link + '" rel="external">More Videos &rsaquo;</a></p>';
			}

			// Return the html for the bottom link
			return bottomHTML;
		}

		function Duration(ptms) {
			//function to convert the PT#H#M#S time format to a #:#:# string
			hours = ptms.match(/([\d]+)H/);
			minutes = ptms.match(/([\d]+)M/);
			seconds = ptms.match(/([\d]+)S/);

			if (hours != null) {
				hours =  hours[1];
			}
			if (minutes != null) {
				minutes =  minutes[1];
			}
			if (seconds != null) {
				seconds =  seconds[1];
			}
			
			if(seconds < 10) {
				seconds = "0" + seconds;
			}
			if(minutes < 10 && hours != "") {
				minutes = "0" + minutes;
			}
			return "<span>" + minutes + ":" + seconds + "</span>";
		}
	}
})( jQuery );
