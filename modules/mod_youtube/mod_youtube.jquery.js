/*
 * @package     hubzero-cms
 * @file        modules/mod_youtube/mod_youtube.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3

 Author:
 -----------------		
 Christopher Smoak <csmoak@purdue.edu>

 Created:
 -----------------
 April 2011

 Requires:
 -----------------
 Mootools 1.1

*/

if(!HUB) {
	var HUB = {};
}

//----------------------

var youtube_feed = 0;

//----------------------

(function($){
	$.fn.youtube = function(options) {
		//options set by call to class
		var settings = $.extend( {
		    type: "playlist",
			search: "",
			count: 3,
			random: false,
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
		
		//base Youtube API URL
		var youtube_base_url = "https://gdata.youtube.com/feeds/api/";

		//querystring for Youtube API Call
		var youtube_querystring = "?v=2&alt=json-in-script&callback=?"; //{CALLBACK}";

		return this.each(function(i, widget) {
			//incriment the feed count
			youtube_feed++;

			//append the type of feed we want to load to base url
			var url  = youtube_base_url + settings.type;

			//build url based on type
			if (settings.type == 'videos') {
				url += youtube_querystring.replace("{CALLBACK}", "YoutubeCallback" + youtube_feed);
				url += "&q=" + settings.search;
			} else {
				url += "/" + settings.search;
				if (settings.type == 'users') url += "/uploads"; 
				url += youtube_querystring.replace("{CALLBACK}", "YoutubeCallback" + youtube_feed);
			}

			//push the script to the bottom of the head element
			/*var script = Asset.javascript(url, {
				id: 'youtube_script_' + youtube_feed
			});

			//create a callback function for each youtube feed
			window['YoutubeCallback' + youtube_feed] = function( json ) {
				youtube.Format(json);
			}.bind(this);*/

			$.getJSON(url, {}, function(data) {
				$(widget).html(Format(data));
			});
		});

		//format json data into tweets
		function Format( json ) {
			var html = "",
				feed = json.feed,
				videos = feed.entry;

			//if we want random
			if (settings.random) {
				videos.sort(function() {return 0.5 - Math.random()});
			}

			//build the feed details
			html += FeedDetails( feed );

			//build html
			html += "<ul>";
			$.each(videos, function(i, item) {
				if (i >= settings.count) {
					html += "";
				} else {
					var title = item['title']['$t'];
					var id = item.media$group.yt$videoid.$t; //item['id']['$t'];
					var thumb = item.media$group.media$thumbnail[0].url;
					
					html += "<li>";
					html += "<a class=\"entry-thumb\" rel=\"external\" title=\"" + title + "\" href=\"http://youtube.com/watch?v=" + id + "\"><img src=\"" + thumb.replace('http://','https://') + "\" alt=\"" + title + "\" width=\"80\" /></a>";
					html += "<a class=\"entry-title\" rel=\"external\" title=\"" + title + "\" href=\"http://youtube.com/watch?v=" + id + "\">" + title + "</a>";
					html += "<br /><span class=\"entry-duration\">" + Duration(item.media$group.yt$duration.seconds) + "</span>";
					html += "</li>";
				}
			});
			html += "</ul>";

			//build the feed link
			html += FeedLink( feed );

			return html;
		}
		
		function FeedDetails( feed ) {
			var topHTML = "";
			var title, description, logo;
			var details = settings.details;

			//if we have no details return nothing
			if(!details)
				return "";

			//set title based on if we have an alternative title
			if(details.altTitle && details.altTitle != "") {
				title = details.altTitle;
			} else {
				title = feed.title.$t;
			}

			//set description based on if we have an alternative desc
			if(details.altDesc && details.altDesc != "") {
				description = details.altDesc;
			} else {
				description = (feed.media$group && feed.media$group.media$description) ? feed.media$group.media$description.$t : '';
			}

			//set the logo based on if we have an alternative image
			if(details.altLogo && details.altLogo != "") {
				logo = details.altLogo;
			} else {
				logo = "https://www.youtube.com/img/pic_youtubelogo_123x63.gif";
			}

			//do we want to show the title
			if(details.showTitle) {
				topHTML += "<h3>" + title + "</h3>";
			}

			//do we want to show the description
			if(details.showDesc) {
				topHTML += "<p class=\"description\">" + description + "</p>";
			}

			//do we want to show the logo
			if(details.showLogo) {
				topHTML += "<img class=\"logo\" src=\"" + logo + "\" alt=\"Youtube Feed\" />";
			}

			//return html for feed details
			return topHTML;
		}

		function FeedLink( feed ) {
			var bottomHTML = "";
			var link;
			var details = settings.details;

			//if we have no details return nothing
			if(!details)
				return "";

			//set link based on if we have an alternative link
			if(details.altLink != "" && details.altLink) {
				link = details.altLink;
			} else {
				switch(settings.type)
				{
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

			//do we want to show the link
			if(details.showLink) {
				bottomHTML += '<p class="more"><a href="' + link + '" rel="external">More Videos &rsaquo;</a></p>';
			}

			//return the html for the bottom link
			return bottomHTML;
		}

		function Duration(seconds) {
			minutes = Math.floor(seconds/60); 	
			seconds = seconds % 60;
			if(seconds < 10) {
				seconds = "0" + seconds;
			}
			return "<span>" + minutes + ":" + seconds + "</span>";
		}
	}
})( jQuery );