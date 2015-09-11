<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
				$url .= $prfx . 'enablejsapi=1&origin=' . Request::base();

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
			</script>
		<?php } ?>
	<?php } ?>
	</div><!-- /#video-container -->
<?php endif; ?>