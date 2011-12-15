/**

Author:
-----------------		
Christopher Smoak <csmoak@purdue.edu>

Created:
-----------------
April 2011

Requires:
-----------------
Mootools 1.1

Parameters:
-----------------
- Type
	The type of feed to display. Can be 'user' or 'trend'.
	
- Username
	The Twitter username for the user feed.

- Trend
	The Twitter trend or hashtag that we will search for.
	
- Tweets
	The Number of tweets to display (1 to 20).
	
- Linkify
	Link up all external links as well as all mentions and trends in tweets. True or False.


**/

if(!HUB) {
	var HUB = {};
}

//----------------------

var twitter_feed = 0;

HUB.Twitter = new Class ({
	
	//options set by call to class
	options: {
		type: 'user',
		username: '',
		trend: '',
		linkify: true,
		tweets: 3
	},
	
	//url for getting user tweets
	user_url: "https://api.twitter.com/statuses/user_timeline/{USER}.json?callback=Format",
	
	//url for searching tweets by trend
	trend_url: "https://search.twitter.com/search.json?q={TREND}&callback=Format",
	
	//takes container and options
	initialize: function(container, options) {
		//set our container to this
		this.container = $(container);
	
		//verify the container exists
		if(!this.container) 
			return;
		
		//set our options from defaults and user defined
		this.setOptions(options);
		
		//vars used
		var message = null;
		var url = null;
		var check_first = null;
		var type = this.options.type;
		var username = this.options.username;
		var trend = this.options.trend;
		
		//are wer loading user feed
		if(type == 'user') {
			
			//check to see if we have a username
			//if we dont end and display message
			//remove @ symbol if exists in username
			if(username != "") {
				check_first = username.substring(0,1);
				if(check_first == '@') {
					username = username.substring(1);
				}
				url = this.user_url.replace("{USER}", username);
				
				twitter_feed++;
				url += twitter_feed;
			} else {
				this.container.innerHTML = "<p class=\"info\">Please enter a Twitter username.</p>";
				return;
			}
		//are we searching a trend
		} else if(type == 'trend') {
			
			//check to see if we have a trend
			//if not end and display error message
			//remove # symbol if exits in trend
			if(trend != "") {
				check_first = trend.substring(0,1);
				if(check_first == '#') {
					trend = trend.substring(1);
				}
				url = this.trend_url.replace("{TREND}", trend);
				
				twitter_feed++;
				url += twitter_feed;
			} else {
				this.container.innerHTML = "<p class=\"info\">Please enter a Twitter trend.</p>";
				return;
			}
		}
		
		//push the script to the bottom of the head element
		var script = Asset.javascript(url, {
			id: 'twitter' + twitter_feed
		});
		
		//create a callback function for each twitter feed
		window['Format' + twitter_feed] = function( json ) {
			this.Format(json);
		}.bind(this);
	},
	
	//format json data into tweets
	Format: function( json ) {
		var html = "";
		var text, time;
		var feed = json;
		
		if(this.options.type == 'trend') {
			feed = feed.results;
			if(feed.length < 1) {
				this.container.innerHTML = "<p class=\"info\">There are currently no trends matching '" + this.options.trend + "'.</p>";
				return;
			}
		}
		
		//heading
		html += "<h3>";
		if(this.options.type == 'trend') {
			html += this.options.trend;
		} else {
			html += this.options.username;
		}
		html += "</h3>";
		
		html += "<ul>";
		for(var i = 0; i < this.options.tweets; i++) {
			if(feed[i]) {
				text = feed[i].text;
				time = feed[i].created_at;
		
				if(this.options.type == "trend") {
					img = feed[i].profile_image_url;
					user = feed[i].from_user
				} else {
					img = feed[i].user.profile_image_url;
					user = feed[i].user.screen_name;
				}
			
				//load images through https
				img = img.replace(/http:\/\/\w{2}.twimg.com/, 'https://s3.amazonaws.com/twitter_production');
			
				html += "<li>";
				html += "<span class=\"tweetProfilePicture\">";
				html += "<img src=\"" + img + "\" />";
				html += "</span>";
			
				html += "<span class=\"tweetProfile\">";
				html += "<a rel=\"external\" href=\"http://twitter.com/#!/" + user + "\">@" + user + "</a>";
				html += "</span>";
			
				html += "<span class=\"tweetStatus\">";
				if(this.options.linkify) {
					html += this.Linkify(text); 
				} else {
					html += text;
				}
				html += "</span>";
			
				html += "<span class=\"tweetTime\">" + this.Time(time) + "</span>";
				html += "</li>";
			}
		}
		html += "</ul>";
		
		this.container.innerHTML = html;
	},
	
	//link up external links, users, and trends
	Linkify: function(text){
		return text.replace(/(https?:\/\/[\w\-:;?&=+.%#\/]+)/gi, '<a href="$1">$1</a>')
				   .replace(/(^|\W)@(\w+)/g, '$1<a href="http://twitter.com/$2">@$2</a>')
				   .replace(/(^|\W)#(\w+)/g, '$1<a href="http://search.twitter.com/search?q=%23$2">#$2</a>');
	},
	
	//format time to be Twitter like
	Time: function( time_value ) {
		var values = time_value.split(" "),
            parsed_date = Date.parse(values[1] + " " + values[2] + ", " + values[5] + " " + values[3]),
            date = new Date(parsed_date),
            relative_to = (arguments.length > 1) ? arguments[1] : new Date(),
            delta = parseInt((relative_to.getTime() - parsed_date) / 1000),
			months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            r = '';
			
		if(this.options.type == 'trend') {
			parsed_date = Date.parse(values[2] + " " + values[1] + ", " + values[3] + " " + values[4]),
            date = new Date(parsed_date),
            relative_to = (arguments.length > 1) ? arguments[1] : new Date(),
            delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
		}
        
        function formatTime(date) {
            var hour = date.getHours(),
                min = date.getMinutes() + "",
                ampm = 'AM';
            
            if (hour == 0) {
                hour = 12;
            } else if (hour == 12) {
                ampm = 'PM';
            } else if (hour > 12) {
                hour -= 12;
                ampm = 'PM';
            }
            
            if (min.length == 1) {
                min = '0' + min;
            }
            
            return hour + ':' + min + ' ' + ampm;
        }
        
        function formatDate(date) {
            var ds = date.toDateString().split(/ /),
                mon = months[date.getMonth()],
                day = date.getDate()+'',
                dayi = parseInt(day),
                year = date.getFullYear(),
                thisyear = (new Date()).getFullYear(),
                th = 'th';
            
            // anti-'th' - but don't do the 11th, 12th or 13th
            if ((dayi % 10) == 1 && day.substr(0, 1) != '1') {
                th = 'st';
            } else if ((dayi % 10) == 2 && day.substr(0, 1) != '1') {
                th = 'nd';
            } else if ((dayi % 10) == 3 && day.substr(0, 1) != '1') {
                th = 'rd';
            }
            
            if (day.substr(0, 1) == '0') {
                day = day.substr(1);
            }
            
            return mon + ' ' + day + th + (thisyear != year ? ', ' + year : '');
        }
        
        delta = delta + (relative_to.getTimezoneOffset() * 60);

        if (delta < 5) {
            r = 'less than 5 seconds ago';
        } else if (delta < 30) {
            r = 'half a minute ago';
        } else if (delta < 60) {
            r = 'less than a minute ago';
        } else if (delta < 120) {
            r = '1 minute ago';
        } else if (delta < (45*60)) {
            r = (parseInt(delta / 60)).toString() + ' minutes ago';
        } else if (delta < (2*90*60)) { // 2* because sometimes read 1 hours ago
            r = 'about 1 hour ago';
        } else if (delta < (24*60*60)) {
            r = 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
        } else {
            if (delta < (48*60*60)) {
                r = formatTime(date) + ' yesterday';
            } else {
                r = formatTime(date) + ' ' + formatDate(date);
                // r = (parseInt(delta / 86400)).toString() + ' days ago';
            }
        }

        return r;
	}
});

HUB.Twitter.implement(new Events, new Options);