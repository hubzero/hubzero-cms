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

HUB.Youtube = new Class ({
	
	//options set by call to class
	options: {
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
	},
	
	//base Youtube API URL
	youtube_base_url: "https://gdata.youtube.com/feeds/api/",
	
	//querystring for Youtube API Call
	youtube_querystring: "?v=2&alt=jsonc&callback={CALLBACK}",
	
	//takes container and options
	initialize: function(container, options) {
		//set our container in this namespace
		this.container = $(container);
	
		//verify the container exists
		if(!this.container) {
			console.log("no container");
			return;
		}
		
		//set our options from defaults and user defined
		this.setOptions(options);
		
		//incriment the feed count
		youtube_feed++;
		
		//append the type of feed we want to load to base url
		var url  = this.youtube_base_url + this.options.type;
		
		//build url based on type
		if(this.options.type == 'videos') {
			url += this.youtube_querystring.replace("{CALLBACK}", "YoutubeCallback" + youtube_feed);
			url += "&q=" + this.options.search;
		} else {
			url += "/" + this.options.search;
			if(this.options.type == 'users') url += "/uploads"; 
			url += this.youtube_querystring.replace("{CALLBACK}", "YoutubeCallback" + youtube_feed);
		}
		
		//push the script to the bottom of the head element
		var script = Asset.javascript(url, {
			id: 'youtube_script_' + youtube_feed
		});
	
		//create a callback function for each youtube feed
		window['YoutubeCallback' + youtube_feed] = function( json ) {
			this.Format(json);
		}.bind(this);
	},
	
	//format json data into youtube feed
	Format: function( json ) {
		var html = "";
		var feed = json.data;
		var videos = feed.items;
		
		//if we want random
		if(this.options.random) {
			videos.sort(function() {return 0.5 - Math.random()});
		}
		
		//build the feed details
		html += this.FeedDetails( feed );
		
		//build html
		html += "<ul>";
		for(var i=0; i<this.options.count; i++) {
			if(this.options.type == "playlists") {
				var video = videos[i].video;
			} else {
				var video = videos[i];
			}
			
			html += "<li>";
			html += "<a class=\"entry-thumb\" rel=\"external\" title=\"" + video.title + "\" href=\"http://youtube.com/watch?v=" + video.id + "\"><img src=\"" + video.thumbnail.hqDefault.replace('http','https') + "\" alt=\"" + video.title + "\" width=\"80\" /></a>";
			html += "<a class=\"entry-title\" rel=\"external\" title=\"" + video.title + "\" href=\"http://youtube.com/watch?v=" + video.id + "\">" + video.title + "</a>";
			html += "<br /><span class=\"entry-duration\">" + this.Duration(video.duration) + "</span>";
			html += "</li>";
		}
		html += "</ul>";
		
		//build the feed link
		html += this.FeedLink( feed );
		
		this.container.innerHTML = html;
	},
	
	FeedDetails: function( feed ) {
		var topHTML = "";
		var title, description, logo;
		var details = this.options.details;
		
		//if we have no details return nothing
		if(!details)
			return "";
		
		//set title based on if we have an alternative title
		if(details.altTitle && details.altTitle != "") {
			title = details.altTitle;
		} else {
			title = feed.title;
		}
		
		//set description based on if we have an alternative desc
		if(details.altDesc && details.altDesc != "") {
			description = details.altDesc;
		} else {
			description = feed.description;
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
	},
	
	FeedLink: function( feed ) {
		var bottomHTML = "";
		var link;
		var details = this.options.details;
		
		//if we have no details return nothing
		if(!details)
			return "";
		
		//set link based on if we have an alternative link
		if(details.altLink != "" && details.altLink) {
			link = details.altLink;
		} else {
			switch(this.options.type)
			{
				case 'playlists':
					link = "http://www.youtube.com/view_play_list?p=" + this.options.search;
					break;
				case 'users':
					link = "http://www.youtube.com/user/" + this.options.search;
					break;
				case 'videos':
					link = "http://www.youtube.com/results?search_query=" + this.options.search;
					break;
			}
		}
		
		//do we want to show the link
		if(details.showLink) {
			bottomHTML += "<p class=\"more\"><a href=\"" + link + "\" rel=\"external\">View More &rsaquo;</a></p>";
		}
		
		//return the html for the bottom link
		return bottomHTML;
	},
	
	Duration: function (seconds) {
		minutes = Math.floor(seconds/60); 	
		seconds = seconds % 60;
		if(seconds < 10) {
			seconds = "0" + seconds;
		}
		return "<span>" + minutes + ":" + seconds + "</span>";
	}

});

HUB.Youtube.implement(new Events, new Options);
