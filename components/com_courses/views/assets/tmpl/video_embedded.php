<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	if (preg_match('/<iframe(.*?)src="([^"]+)"([^>]*)>(.*?)<\/iframe>/si', $this->asset->content, $matches))
		{
			if (stristr($matches[2], 'youtube'))
			{
				$url = $matches[2];

				$prfx = '&';
				if (!stristr($url, '?'))
				{
					$prfx = '?';
				}
				$url = str_replace('http:', 'https:', $url);
				$url .= $prfx . 'enablejsapi=1&origin=' . JURI::getInstance()->base();

				$this->asset->content = str_replace($matches[2], $url, $this->asset->content);
				$this->asset->content = str_replace('<iframe', '<iframe id="player"', $this->asset->content);
			}
			else if (stristr($matches[2], 'vimeo'))
			{
				$url = $matches[2];

				$prfx = '&';
				if (!stristr($url, '?'))
				{
					$prfx = '?';
				}
				$url .= $prfx . 'api=1&amp;player_id=player';
				$url = str_replace('http:', 'https:', $url);

				$this->asset->content = str_replace($matches[2], $url, $this->asset->content);
				$this->asset->content = str_replace('<iframe', '<iframe id="player"', $this->asset->content);
			}
			else if (stristr($matches[2], 'blip'))
			{

			}
			else if (stristr($matches[2], 'kaltura'))
			{

			}
		}
?>

<?php if ($this->asset->subtype == 'embedded') : ?>
	<div id="video-container" class="embedded-video">
		<?php echo $this->asset->content; ?>

	<?php if (stristr($this->asset->content, 'iframe')) { ?>
		<?php if (stristr($this->asset->content, 'youtube')) { ?>
			<script type="text/javascript">
			/*var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			window.ytPlayer = null;
			function onYouTubeIframeAPIReady() {
				window.ytPlayer = new YT.Player('player', {
					events: {
						'onReady': function(event) {
							console.log(player.getCurrentTime());
						},
						'onError': function() {
							console.log('error');
						}
					}
				});
				//console.log(window.ytPlayer);
			}*/
			</script>
		<?php } else if (stristr($this->asset->content, 'vimeo')) { ?>
			<script type="text/javascript" src="//a.vimeocdn.com/js/froogaloop2.min.js?8cca6-1372090955"></script>
			<script type="text/javascript">
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
								} else if(tmp < 10) {
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
			</script>
		<?php } ?>
	<?php } ?>
	</div><!-- /#video-container -->
<?php endif; ?>