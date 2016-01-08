<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Content\Moderator;

$this->oWidth   = '780';
$this->oHeight  = '460';

$manifest     = $this->items->findFirstWithExtension('json');
$presentation = json_decode($manifest->read());

// Content folder
$content_folder     = dirname($manifest->getAbsolutePath());
$rel_content_folder = substr($content_folder, strlen(PATH_ROOT));
$presentation       = $presentation->presentation;

// Get subs from json file
$subs = (isset($presentation->subtitles)) ? $presentation->subtitles : [];

// Make sure source is full path to assets folder
$subFiles = [];
foreach ($subs as $k => $subtitle)
{
	if (!strpos($subtitle->source, DS))
	{
		$subtitle->source = $rel_content_folder . DS . $subtitle->source;
	}

	$subFiles[] = $subtitle->source;
}

// Add any local subtitles too
foreach ($this->items->findAllWithExtension(['srt', 'SRT']) as $k => $subtitle)
{
	$info     = pathinfo($subtitle);
	$name     = str_replace('-auto','', $info['filename']);
	$autoplay = (strstr($info['filename'],'-auto')) ? 1 : 0;
	$source   = $rel_content_folder . DS . $subtitle;

	// Add each subtitle
	$subtitle           = new stdClass;
	$subtitle->type     = 'SRT';
	$subtitle->name     = ucfirst($name);
	$subtitle->source   = $source;
	$subtitle->autoplay = $autoplay;

	// Make sure we don't already have this file
	if (!in_array($subtitle->source, $subFiles))
	{
		$subs[] = $subtitle;
	}
}

// Add the HUBpresenter stylesheet and scripts
$this->css('hubpresenter');

$this->js('hubpresenter');
$this->js('hubpresenter.plugins');

$this->js('jquery.colpick.js', 'system');
$this->css('jquery.colpick.css', 'system');


// Include media tracking
if (isset($this->entityId) && isset($this->entityType))
{
	require_once PATH_CORE . DS . 'components' . DS . 'com_system' . DS . 'tables' . DS . 'mediatracking.php';
	$dbo           = \App::get('db');
	$mediaTracking = new \Components\System\Tables\MediaTracking($dbo);

	// Get tracking for this user for this resource
	$tracking = $mediaTracking->getTrackingInformationForUserAndResource(User::get('id'), $this->entityId, $this->entityType);

	// Check to see if we already have a time query param
	$hasTime = (Request::getVar('time', '') != '') ? true : false;

	// Do we want to redirect user with time added to url
	if (is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration && !Request::getInt('no_html', 0))
	{
		$redirect = Request::current(true);

		// do we have tmpl=componet in url?
		$delimeter = (strpos($redirect, '?') === false) ? '?' : '&';
		if (Request::getVar('tmpl', '') == 'component')
		{
			$redirect .= $delimeter . 'tmpl=component';
		}

		$delimeter = (strpos($redirect, '?') === false) ? '?' : '&';

		// Append current position to redirect
		$redirect .= $delimeter . 'time=' . gmdate("H:i:s", $tracking->current_position);

		// Redirect
		App::redirect(Route::url($redirect, false), '', '', false);
	}
}
?>

<div id="presenter-container" class="width-container">
	<div id="presenter-header">
		<div id="title"></div>
	</div><!-- /#header -->

	<div id="presenter-content">
		<div id="presenter-left">
			<div id="slides">
				<ul class="no-js">
					<?php $counter = 0; ?>
					<?php foreach ($presentation->slides as $slide) : ?>
						<li id="slide_<?php echo $counter; ?>" title="<?php echo $slide->title; ?>" time="<?php echo $slide->time; ?>">
							<?php if ($slide->type == 'Image') : ?>
								<img src="<?php echo with(new Moderator($content_folder . DS . $slide->media))->getUrl(); ?>" alt="<?php echo $slide->title; ?>" />
							<?php else : ?>
								<video class="slidevideo">
									<?php foreach ($slide->media as $source): ?>
										<source src="<?php echo with(new Moderator($content_folder . DS . $source->source))->getUrl(); ?>" />
									<?php endforeach; ?>
									<a href="<?php echo with(new Moderator($content_folder . DS . $slide->media[0]->source))->getUrl(); ?>" class="flowplayer_slide" id="flowplayer_slide_<?php echo $counter; ?>"></a>
								</video>
								<img src="<?php echo with(new Moderator($content_folder . DS . $slide->media[3]->source))->getUrl(); ?>" alt="<?php echo $slide->title; ?>" class="imagereplacement" />
							<?php endif; ?>
						</li>
						<?php $counter++; ?>
					<?php endforeach; ?>
				</ul>
			</div><!-- /#slides -->

			<div id="control-box" class="no-controls" data-theme="dark">
				<div id="progress-bar"></div>
				<div id="control-buttons">
					<div id="control-buttons-left" class="cf">
						<a id="previous" class="control" href="javascript:void(0);" title="Previous Slide"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_PREV'); ?></a>
						<a id="play-pause" class="control" href="javascript:void(0);" title="Play Presentation"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_PAUSE'); ?></a>
						<a id="next" class="control" href="javascript:void(0);" title="Next Slide"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_NEXT'); ?></a>
						<div id="media-progress"></div>
					</div>
					<div id="control-buttons-right" class="cf">
						<a id="subtitle" class="control" href="javascript:void(0);">
							<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT'); ?>
							<div class="control-container subtitle-controls">
								<h3><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT'); ?></h3>
								<div class="grid">
									<div class="col span4 label">
										<label for="subtitle-selector"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS'); ?>:</label>
									</div>
									<div class="col span8 omega input">
										<select id="subtitle-selector">
											<option value=""><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT_OFF'); ?></option>
										</select>
									</div>
								</div>
								<div class="grid">
									<div class="col span4 label">
										<label for="transcript-selector"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_TRANSCRIPT'); ?>:</label>
									</div>
									<div class="col span8 omega input">
										<select class="transcript-selector">
											<option value=""><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTIONS_TRANSCRIPT_OFF'); ?></option>
										</select>
									</div>
								</div>

								<span class="options-toggle"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTIONS'); ?></span>
								<div class="subtitle-settings hide">
									<div class="grid">
										<div class="col span6 label">
											<label for="font-selector"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT'); ?>:</label>
										</div>
										<div class="col span6 omega input">
											<select id="font-selector">
												<option value="Arial" selected><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_ARIAL'); ?></option>
												<option value="Times New Roman"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TIMES'); ?></option>
												<option value="Tahoma"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TAHOMA'); ?></option>
												<option value="Trebuchet MS"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_TREBUCHET'); ?></option>
												<option value="Verdana"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_VERDANA'); ?></option>
												<option value="Courier New"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_COURIER'); ?></option>
											</select>
										</div>
									</div>
									<div class="grid">
										<div class="col span6 label">
											<label for="font-size-selector"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE'); ?>:</label>
										</div>
										<div class="col span6 omega input">
											<select id="font-size-selector">
												<option value="12"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_SMALL'); ?></option>
												<option value="18" selected><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_MEDIUM'); ?></option>
												<option value="24"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_SIZE_LARGE'); ?></option>
											</select>
										</div>
									</div>
									<div class="grid">
										<div class="col span6 label">
											<label for="font-color"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_FONT_COLOR'); ?>:</label>
										</div>
										<div class="col span6 omega input">
											<div id="font-color" data-color="FFF" style="background-color: #FFF;"></div>
										</div>
									</div>
									<div class="grid">
										<div class="col span6 label">
											<label for="background-color"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_BACKGROUND'); ?>:</label>
										</div>
										<div class="col span6 omega input">
											<div id="background-color" data-color="000" style="background-color: #000;"></div>
										</div>
									</div>
									<div class="grid">
										<div class="col span12 omega subtitle-settings-preview-container">
											<div class="subtitle-settings-preview">
												<div class="test" style="font-family:arial; background-color: #000; color: #FFF; font-size:18px;"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_EXAMPLE'); ?></div>
											</div>
										</div>
									</div>
									<div class="actions">
										<button class="btn btn-info btn-secondary icon-save" id="subtitle-settings-save"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_CAPTION_OPTION_SAVE'); ?></button>
									</div>
								</div>
							</div>
						</a>
						<a id="volume" class="control " href="javascript:void(0);">
							<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_VOLUME'); ?>
							<div class="control-container volume-controls">
								<div id="volume-bar"></div>
							</div>
						</a>
						<a id="settings" class="control" href="javascript:void(0);" title="Adjust Settings for Playback">
							<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS'); ?>
							<div class="control-container settings-controls">
								<h3><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS'); ?></h3>
								<div class="grid">
									<div class="col span6 label">
										<label for="speed"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE'); ?>:</label>
									</div>
									<div class="col span6 omega input">
										<select id="speed">
											<option value=".25"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_025'); ?></option>
											<option value=".5"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_05'); ?></option>
											<option selected value="1"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_NORMAL'); ?></option>
											<option value="1.25"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_125'); ?></option>
											<option value="1.5"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_15'); ?></option>
											<option value="2"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SETTINGS_PLAYBACK_RATE_2'); ?></option>
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
						<a id="link" class="control" href="javascript:void(0);" title="<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_THIS_SPOT'); ?>">
							<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK'); ?>
							<div class="control-container link-controls">
								<h3><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_TO_VIDEO'); ?> <span><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_TO_VIDEO_AT_POSITION'); ?></span></h3>
								<div class="grid">
									<div class="col span12 omega">
										<input type="text" value="ss" />
										<span class="hint"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_LINK_HINT'); ?></span>
									</div>
								</div>
							</div>
						</a>
						<a id="switch" class="control" href="javascript:void(0);" title="<?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SWITCH_VIDEO_SLIDES'); ?>"><?php echo Lang::txt('PLG_HANDLERS_HUBPRESENTER_CONTROL_SWITCH'); ?></a>
					</div>
				</div>
			</div><!-- /#control-box -->
		</div><!-- /#left -->
		<?php $cls = (isset($presentation->videoPosition)
						&& $presentation->videoPosition == "left"
						&& strtolower($presentation->type) == 'video') ? "move-left": ""; ?>
		<div id="presenter-right">
			<div id="media" class="<?php echo $cls; ?>">
				<?php if (strtolower($presentation->type) == 'video') : ?>
					<video id="player" preload="auto" controls="controls" data-mediaid="<?php echo (isset($this->entityId) ? $this->entityId : 0); ?>" data-mediatype="<?php echo (isset($this->entityType) ? $this->entityType : ''); ?>">
						<?php foreach ($presentation->media as $source): ?>
							<?php
								switch (strtolower($source->type))
								{
									case 'm4v':
									case 'mp4':  $type = 'video/mp4;';  break;
									case 'ogv':  $type = 'video/ogg;';  break;
									case 'webm': $type = 'video/webm;'; break;
								}
							?>
							<source src="<?php echo with(new Moderator($content_folder . DS . $source->source))->getUrl(); ?>" type='<?php echo $type; ?>'>
						<?php endforeach; ?>
						<a href="<?php echo with(new Moderator($content_folder . DS . $presentation->media[0]->source))->getUrl(); ?>" id="flowplayer"></a>

						<?php if (isset($subs) && count($subs) > 0) : ?>
							<?php foreach ($subs as $sub) : ?>
								<div
									data-autoplay="<?php echo $sub->autoplay; ?>"
									data-type="subtitle"
									data-lang="<?php echo $sub->name; ?>"
									data-src="<?php echo $sub->source; ?>?v=<?php echo filemtime( $sub->source ); ?>"></div>
							<?php endforeach; ?>
						<?php endif; ?>

					</video>
				<?php else : ?>
					<audio id="player" preload="auto" controls="controls">
						<?php foreach ($presentation->media as $source): ?>
							<source src="<?php echo with(new Moderator($content_folder . DS . $source->source))->getUrl(); ?>" />
						<?php endforeach; ?>
						<a href="<?php echo with(new Moderator($content_folder . DS . $presentation->media[0]->source))->getUrl(); ?>" id="flowplayer" duration="<?php if (isset($presentation->duration) && $presentation->duration) { echo $presentation->duration; } ?>"></a>
					</audio>
				<?php endif; ?>
				<div id="video-subtitles"></div>
			</div>
			<div id="list">
				<ul id="list_items">
					<?php $num = 0; $counter = 0; $last_slide_id = 0; ?>
					<?php foreach ($presentation->slides as $slide) : ?>
						<?php if ((int)$slide->slide != $last_slide_id) : ?>
							<li id="list_<?php echo $counter; ?>">
								<?php
									// Use thumb if possible
									$thumb = '';
									if (isset($slide->thumb) && $slide->thumb && file_exists($content_folder . DS . $slide->thumb))
									{
										$thumb = $content_folder . DS . $slide->thumb;
									}
									else if (!is_array($slide->media) && file_exists($content_folder . DS . $slide->media))
									{
										$thumb = $content_folder . DS . $slide->media;
									}
								?>
								<img src="<?php echo with(new Moderator($thumb))->getUrl(); ?>" alt="<?php echo $slide->title; ?>" />
								<span>
									<?php
										$num++;
										$max = 30;
										$elipsis = '&hellip;';
										echo ($num) . '. ';
										echo substr($slide->title, 0, $max);

										if (strlen($slide->title) > $max)
											echo $elipsis;
									?>
								</span>
								<span class="time"><?php echo $slide->time; ?></span>
								<div id="list-slider-<?php echo $counter; ?>" class="list-slider"></div>
								<div class="list-progress">00:00/00:00</div>
							</li>
						<?php endif; ?>
						<?php
							$last_slide_id = $slide->slide;
							$counter++;
						?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div><!-- /#right -->
	</div><!-- /#content -->
	<div id="transcript-container">
		<div id="transcript-toolbar">
			<div id="transcript-select"></div>
			<input type="text" id="transcript-search" placeholder="Search Transcript..." />
			<a href="javascript:void(0);" id="font-bigger"></a>
			<a href="javascript:void(0);" id="font-smaller"></a>
		</div>
		<div id="transcripts"></div>
	</div>
</div>