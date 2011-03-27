/**
 * @package     hubzero-cms
 * @file        modules/mod_youtube/mod_youtube.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/**
 * Copyright 2005-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-----

if(!HUB.Modules) {
	HUB.Modules = {};
}

//-----------------------------------------------------------
//  Youtube feed js
//-----------------------------------------------------------

HUB.Modules.Youtube = { 
	buildFeed: function(options) {
		var html = "";
		var entries = options.feed.entry;
		var title = options.feed.title.$t;
		if(options.type == 'playlists') {
			var desc = options.feed.subtitle.$t;
		}
		var logo = options.feed.logo.$t;
		
		//check to see if we are to display a title and which title to display
		if(options.showTitle) {
			if(options.altTitle != '') {
				html += '<h3>' + options.altTitle + '</h3>';
			} else {
				html += '<h3>' + title + '</h3>';
			}
		}
		
		//check to see if we are to display a title and which title to display
		if(options.showDesc) {
			if(options.altDesc != '') {
				html += '<p class="description">' + options.altDesc + '</p>';
			} else if(options.type == 'playlists') {
				html += '<p class="description">' + desc + '</p>';
			}
		}
		
		//check to see if we are to display a title and which title to display
		if(options.showImage) {
			if(options.altImage != '') {
				html += '<img class="logo" src="' + options.altImage + '" alt="Youtube" />';
			} else {
				html += '<img class="logo" src="' + logo + '" alt="Youtube" />';
			}
		}
		
		//check to see if we should randomize entries
		if(options.random) {
			entries.sort(function() {return 0.5 - Math.random()});
		}
		
		//console.log(entries);
		
		//create the list of videos
		html += '<ul>';
		for(var i = 0; i < options.number; i++) {
			var entry = entries[i];
			if(entry) {
				var media = entry.media$group;
				html += "<li>";
				html += "<a class=\"entry-thumb\" rel=\"external\" href=\"" + entry.link[0].href + "\"><img src=\"" + media.media$thumbnail[3].url + "\" alt=\"\" /></a>";
				html += "<a class=\"entry-title\" rel=\"external\" href=\"" + entry.link[0].href + "\">" + entry.title.$t + "</a>";
				html += "<br /><span class=\"entry-duration\">" + HUB.Modules.Youtube.formatDuration(media.yt$duration.seconds) + "</span>";
				html+="</li>";
			}
		}
		html += '</ul>';
		
		//check to see if we are to display a title and which title to display
		if(options.showLink) {
			if(options.altLink != '') {
				html += '<p class="more"><a rel="external" href="' + options.altLink + '" title="Youtube">More Videos</a></p><br class="clear" />';
			} else {
				switch(options.type)
				{
					case 'playlists':
						link = "http://www.youtube.com/view_play_list?p=" + options.content;
						break;
					case 'users':
						link = "http://www.youtube.com/user/" + options.content;
						break;
					case 'videos':
						link = "http://www.youtube.com/results?search_query=" + options.content;
						break;
				}
				
				html += '<p class="more"><a rel="external" href="' + link + '" title="Youtube">More Videos</a></p><br class="clear" />';
			}
		}
		
		//set the content of the container
		$('youtube_feed_' + options.id).innerHTML = html;
	},
	
	formatDuration: function( seconds ) {
		minutes = Math.floor(seconds/60); 	
		seconds = seconds % 60;
		if(seconds < 10) {
			seconds = "0" + seconds;
		}
		return "<span>" + minutes + ":" + seconds + "</span>";
	}
	
}

