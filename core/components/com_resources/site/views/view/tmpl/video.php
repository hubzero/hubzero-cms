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

// No direct access.
defined('_HZEXEC_') or die();

$this->css('video.css')
     ->js('video.js')
     ->js('hubpresenter.plugins.js')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

//base url for the resource
$base = PATH_APP . DS . trim($this->config->get('uploadpath'), DS);
$base = substr($base, strlen(PATH_ROOT));

//presentation manifest
$presentation = $this->manifest->presentation;

//determine height and width
$width  = (isset($presentation->width) && $presentation->width != 0) ? $presentation->width . 'px' : 'auto';
$height = (isset($presentation->height) && $presentation->height != 0) ? $presentation->height . 'px' : 'auto';
?>

<script>
/* Bug fix for Firefox bug. */
/* Firefox fails to update the readyState and does not call the canplay and canplaythrough */
/* Some background information: http://stackoverflow.com/questions/10235919/the-canplay-canplaythrough-events-for-an-html5-video-are-not-called-on-firefox */
if (navigator.userAgent.contains("Mozilla"))
{
	setInterval(function(){ 
		var vid = document.getElementById('video-player');
		if (vid.readyState >= 2)
			{
				HUB.Video.doneLoading();
				HUB.Video.locationHash();
			};
	}, 2000);
}
</script>

<div id="video-container">
	<?php if (count($presentation->media) > 0) : ?>
		<video controls="controls" id="video-player" data-mediaid="<?php echo $this->resource->id; ?>">
			<?php foreach ($presentation->media as $video) : ?>
				<?php
					switch ($video->type)
					{
						case 'ogg':
						case 'ogv':     $type = "video/ogg;";    break;
						case 'webm':    $type = "video/webm;";   break;
						case 'mp4':
						case 'm4v':
						default:        $type = "video/mp4;";    break;
					}

					//video source
					$source = $video->source;

					//is this the mp4 (need for flash)
					if (in_array($video->type, array('mp4','m4v')))
					{
						$mp4 = $video->source;
					}

					//if were playing local files
					if (substr($video->source, 0, 4) != 'http')
					{
						$source = $base . $source;
						if (in_array($video->type, array('mp4','m4v')))
						{
							$mp4 = $base . $mp4;
						}
					}
				?>
				<source src="<?php echo $source; ?>" type="<?php echo $type; ?>" />
			<?php endforeach; ?>

			<a href="<?php echo $mp4; ?>"
				id="video-flowplayer"
				style="<?php echo "width:{$width};height:{$height};"; ?>"
				data-mediaid="<?php echo $this->resource->id; ?>"></a>

			<?php if (count($presentation->subtitles) > 0) : ?>
				<?php foreach ($presentation->subtitles as $subtitle) : ?>
					<?php
						//get file modified time
						$source = $subtitle->source;
						$auto   = $subtitle->autoplay;

						//if were playing local files
						if (substr($subtitle->source, 0, 4) != 'http')
						{
							$source   = $base . $source;
							$modified = filemtime( PATH_CORE . $source );
						}
						else
						{
							$modified = '123456789';
						}
					?>
					<div
						data-autoplay="<?php echo $auto; ?>"
						data-type="subtitle"
						data-lang="<?php echo $subtitle->name; ?>"
						data-src="<?php echo $source ?>?v=<?php echo $modified; ?>"></div>
				<?php endforeach; ?>
			<?php endif; ?>
		</video>
	<?php endif; ?>

	<div id="control-box" class="no-controls" data-theme="dark">
		<div id="progress-bar"></div>
		<div id="control-buttons">
			<div id="control-buttons-left" class="cf">
				<a id="play-pause" class="tooltips control" href="javascript:void(0);" title="Play Presentation">Pause</a>
				<div id="media-progress"></div>
			</div>
			<div id="control-buttons-right" class="cf">
				<a id="subtitle" class="tooltips control" href="javascript:void(0);">
					Subtitles/Captions
					<div class="control-container subtitle-controls">
						<h3>Captions/Transcript</h3>
						<div class="grid">
							<div class="col span4 label">
								<label for="subtitle-selector">Captions:</label>
							</div>
							<div class="col span8 omega input">
								<select id="subtitle-selector">
									<option value="">None/Off</option>
								</select>
							</div>
						</div>
						<div class="grid">
							<div class="col span4 label">
								<label for="transcript-selector">Transcript:</label>
							</div>
							<div class="col span8 omega input">
								<select class="transcript-selector">
									<option value="">None/Off</option>
								</select>
							</div>
						</div>

						<span class="options-toggle">Options</span>
						<div class="subtitle-settings hide">
							<div class="grid">
								<div class="col span6 label">
									<label for="font-selector">Font:</label>
								</div>
								<div class="col span6 omega input">
									<select id="font-selector">
										<option value="Arial" selected>Arial</option>
										<option value="Times New Roman">Times New Roman</option>
										<option value="Tahoma">Tahoma</option>
										<option value="Trebuchet MS">Trebuchet MS</option>
										<option value="Verdana">Verdana</option>
										<option value="Courier New">Courier New</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<label for="font-size-selector">Font Size:</label>
								</div>
								<div class="col span6 omega input">
									<select id="font-size-selector">
										<option value="12">Small</option>
										<option value="18" selected>Medium</option>
										<option value="24">Large</option>
									</select>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<label for="font-color">Font Color:</label>
								</div>
								<div class="col span6 omega input">
									<div id="font-color" data-color="FFF" style="background-color: #FFF;"></div>
								</div>
							</div>
							<div class="grid">
								<div class="col span6 label">
									<label for="background-color">Background:</label>
								</div>
								<div class="col span6 omega input">
									<div id="background-color" data-color="000" style="background-color: #000;"></div>
								</div>
							</div>
							<div class="grid">
								<div class="col span12 omega subtitle-settings-preview-container">
									<div class="subtitle-settings-preview">
										<div class="test" style="font-family:arial; background-color: #000; color: #FFF; font-size:18px;">This is an Example</div>
									</div>
								</div>
							</div>
							<div class="actions">
								<button class="btn btn-info btn-secondary icon-save" id="subtitle-settings-save">Save</button>
							</div>
						</div>
					</div>
				</a>
				<a id="volume" class="tooltips control " href="javascript:void(0);">
					Volume
					<div class="control-container volume-controls">
						<div id="volume-bar"></div>
					</div>
				</a>
				<a id="settings" class="tooltips control" href="javascript:void(0);" title="Adjust Settings for Playback">
					Settings
					<div class="control-container settings-controls">
						<h3>Settings</h3>
						<div class="grid">
							<div class="col span6 label">
								<label for="speed">Playback Rate:</label>
							</div>
							<div class="col span6 omega input">
								<select id="speed">
									<option value=".25">.25</option>
									<option value=".5">.5</option>
									<option selected value="1">Normal</option>
									<option value="1.25">1.25</option>
									<option value="1.5">1.5</option>
									<option value="2">2</option>
								</select>
							</div>
						</div>
						<!-- <div class="grid">
							<div class="col span6 label">
								<label for="theme">Player Theme:</label>
							</div>
							<div class="col span6 omega input">
								<select id="theme">
									<option value="dark">Dark (default)</option>
								</select>
							</div>
						</div> -->
					</div>
				</a>
				<a id="link" class="tooltips control" href="javascript:void(0);" title="Link to this Spot in Presentation">
					Link
					<div class="control-container link-controls">
						<h3>Link to Video <span>- at current position</span></h3>
						<div class="grid">
							<div class="col span12 omega">
								<input type="text" value="ss" />
								<span class="hint">(Command/Ctrl + C to Copy)</span>
							</div>
						</div>
					</div>
				</a>
				<a id="full-screen" class="tooltips control" href="javascript:void(0);" title="View Video Fullscreen">Fullscreen</a>
			</div>
		</div>
	</div><!-- /#control-box -->

	<div id="video-subtitles"></div>
</div><!-- /#video-container -->

<div id="transcript-container">
	<div id="transcript-toolbar">
		<div id="transcript-select"></div>
		<input type="text" id="transcript-search" placeholder="Search Transcript..." />
		<a href="javascript:void(0);" id="font-bigger"></a>
		<a href="javascript:void(0);" id="font-smaller"></a>
	</div>
	<div id="transcripts"></div>
</div>
<div class="bottom-controls">
	<a href="javascript:void(0);" class="btn btn-secondardy icon-popout embed-popout">Pop Out</a>
</div>
<?php
Document::setTitle($this->resource->title);
