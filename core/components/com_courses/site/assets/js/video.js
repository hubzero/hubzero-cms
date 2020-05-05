/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function(jq){
	var $ = jq,
		iframe = $('#player')[0],
		player = $f(iframe);

	/*if (!HUB) {
		var HUB = {};
	}*/
	//if (typeof HUB.Presenter === 'undefined') {
		function foo() {
			return fa
		}
		HUB.Presenter = {
			tm: null,

			getCurrent: function() {
				/*player.api('getCurrentTime', function (value, player_id) {
					HUB.Presenter.tm = value;
				});*/
				return HUB.Presenter.tm;
			},

			formatTime: function(seconds) {
				var times = new Array(3600, 60, 1),
					time = '',
					tmp;

				for (var i = 0; i < times.length; i++)
				{
					tmp = Math.floor(seconds / times[i]);

					if (tmp < 1) {
						tmp = '00';
					} else if (tmp < 10) {
						tmp = '0' + tmp;
					}

					time += tmp;

					if (i < 2) {
						time += ':';
					}

					seconds = seconds % times[i];
				}
				return time;
			},

			locationHash: function() {
				//var to hold time component
				var timeComponent = '';

				//get the url query string and clean up
				var urlQuery = window.location.search,
					urlQuery = urlQuery.replace("?", ""),
					urlQuery = urlQuery.replace(/&amp;/g, "&");

				//split query string into individual params
				var params = urlQuery.split('&');

				for (var i = 0; i < params.length; i++)
				{
					if (params[i].substr(0,4) == 'time') {
						timeComponent = params[i];
						break;
					}
				}

				// do we have a time component (time=00:00:00 or time=00%3A00%3A00)
				if (timeComponent != '') {
					//get the hours, minutes, seconds
					var timeParts = timeComponent.split("=")[1].replace(/%3A/g, ':').split(':');

					//get time in seconds from hours, minutes, seconds
					var time = (parseInt(timeParts[0]) * 60 * 60) + (parseInt(timeParts[1]) * 60) + parseInt(timeParts[2]);

					//seek to time
					player.api('seekTo', time);
				}
			}
		};
	//}

	// When the player is ready, add listeners for pause, finish, and playProgress
	player.addEvent('ready', function() {
		player.addEvent('playProgress', function onPlayProgress(data, id) {
			HUB.Presenter.tm = data.seconds;
		});
		HUB.Presenter.locationHash();
	});
});
