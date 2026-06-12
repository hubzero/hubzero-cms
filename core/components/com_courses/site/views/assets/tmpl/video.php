<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Set the path
$path = rtrim($this->model->path($this->course->get('id'), false), DS);

// Get the manifest
if (is_dir(PATH_APP . $path))
{
	$manifests = Filesystem::files(PATH_APP . $path, '.json', true, true);
	$manifest  = (count($manifests) > 0) ? $manifests[0] : '';
}

if (isset($manifest) && is_file($manifest))
{
	$media_path = $manifest;
	$media_dir  = dirname($manifest);
	$manifest   = json_decode(file_get_contents($manifest));

	if (is_null($manifest))
	{
		$type  = 'none';
		$error = Lang::txt('COM_COURSES_VIDEO_ERROR_INVALID_JSON');
	}
	else
	{
		$type = (isset($manifest->presentation->slides)) ? 'hubpresenter' : 'html5';
	}
}
else if (in_array(substr($this->model->get('url'), -3), array('mov', 'mp4', 'm4v', 'ogg', 'ogv', 'webm')))
{
	$type = 'standalone';
}
else
{
	$type = 'none';
}

Html::behavior('framework', true);

// Default width and height
$width  = 'auto';
$height = 'auto';

// If the video type is 'hubpresenter', perform next steps
if ($type == 'hubpresenter')
{
	// Check if path exists
	if (is_dir($media_dir))
	{
		// Get all files matching  /.mp4|.webs|.ogv|.m4v|.mp3/
		$media = Filesystem::files($media_dir, '.mp4|.webm|.ogv|.m4v|.mp3', false, false);
		$ext = array();
		foreach ($media as $m)
		{
			$pieces = explode('.', $m);
			$ext[]  = array_pop($pieces);
		}

		// If we dont have all the necessary media formats
		if ((in_array('mp4', $ext) && count($ext) < 3) || (in_array('mp3', $ext) && count($ext) < 2))
		{
			$this->setError(Lang::txt('COM_COURSES_VIDEO_ERROR_MISSING_FORMATS'));
		}

		// Make sure if any slides are video we have three formats of video and backup image for mobile
		$slide_path = $media_dir . DS . 'slides';
		$slides = Filesystem::files($slide_path, '', false, false);

		// Array to hold slides with video clips
		$slide_video = array();

		// Build array for checking slide video formats
		foreach ($slides as $s)
		{
			$parts = explode('.', $s);
			$ext = array_pop($parts);
			$name = implode('.', $parts);

			if (in_array($ext, array('mp4', 'm4v', 'webm', 'ogv')))
			{
				$slide_video[$name][$ext] = $name . '.' . $ext;
			}
		}

		// Make sure for each of the slide videos we have all three formats and has a backup image for the slide
		foreach ($slide_video as $k => $v)
		{
			if (count($v) < 3)
			{
				$this->setError(Lang::txt('COM_COURSES_VIDEO_ERROR_MISSING_SLIDES_FORMAT', count($v), $k . implode(", {$k}.", array_keys($v))));
			}

			if (!file_exists($slide_path . DS . $k . '.png'))
			{
				$this->setError(Lang::txt('COM_COURSES_VIDEO_ERROR_MISSING_STILL_IMAGE', $k));
			}
		}
	}

	// Get the manifest for the presentation
	$contents = file_get_contents($media_path);

	// Content folder
	$content_folder = ltrim(substr(rtrim($media_dir, DS), strlen(PATH_ROOT)), DS);

	// Decode the json formatted manifest so we can use the information
	$presentation = json_decode($contents);
	$presentation = $presentation->presentation;

	// get subs from json file
	$subs = (isset($presentation->subtitles)) ? $presentation->subtitles : array();

	// make sure source is full path to assets folder
	$subFiles = array();
	foreach ($subs as $k => $subtitle)
	{
		if (!strpos($subtitle->source, DS))
		{
			$subtitle->source = $content_folder . DS . $subtitle->source;
		}

		$subFiles[] = $subtitle->source;
	}

	// get local subs
	$local_subs = array();
	if (is_dir($content_folder))
	{
		$local_subs = Filesystem::files(PATH_ROOT . DS . $content_folder, '.srt|.SRT', true, false);
	}

	// add local subtitles too
	foreach ($local_subs as $k => $subtitle)
	{
		$info     = pathinfo($subtitle);
		$name     = str_replace('-auto', '', $info['filename']);
		$autoplay = (strstr($info['filename'], '-auto')) ? 1 : 0;
		$source   = $content_folder . DS . $subtitle;

		// add each subtitle
		$subtitle                  = new stdClass;
		$subtitle->type            = 'SRT';
		$subtitle->name            = ucfirst($name);
		$subtitle->source          = $source;
		$subtitle->autoplay        = $autoplay;

		// make sure we dont already have this file.
		if (!in_array($subtitle->source, $subFiles))
		{
			$subs[] = $subtitle;
		}
	}

	// Add the HUBpresenter stylesheet and scripts
	$this->css("hubpresenter.css", 'com_resources');
	$this->css("hubpresenter.css", 'com_courses');

	$this->js("hubpresenter", 'com_resources');
	$this->js("hubpresenter.plugins", 'com_resources');

	$this->js('jquery.colpick.js', 'system');
	$this->css('jquery.colpick.css', 'system');

}
elseif ($type == 'html5')
{
	$this->css('video.css', 'com_resources');
	$this->css('video.css', 'com_courses');

	$this->js('video.js', 'com_resources');
	$this->js('hubpresenter.plugins.js', 'com_resources');

	$this->js('jquery.colpick.js', 'system');
	$this->css('jquery.colpick.css', 'system');

	$presentation = $manifest->presentation;

	// Determine height and width
	$width  = (isset($presentation->width) && $presentation->width != 0) ? $presentation->width . 'px' : 'auto';
	$height = (isset($presentation->height) && $presentation->height != 0) ? $presentation->height . 'px' : 'auto';

	$this->css('
	#video-flowplayer {
		width: '. $width . ';
		height: ' . $height . ';
	}
	#font-color {
		background-color: #FFF;
	}
	#background-color {
		background-color: #000;
	}
	.test {
		font-family:arial;
		background-color: #000;
		color: #FFF;
		font-size:18px;
	}
	');
}

if ($type == 'hubpresenter' || $type == 'html5')
{
	// Include media tracking for html5 and hubpresenter videos
	require_once Component::path('com_resources') . DS . 'models' . DS . 'mediatracking.php';

	// Get tracking for this user for this resource
	$tracking = \Components\Resources\Models\MediaTracking::oneByUserAndResource(User::get('id'), $this->asset->id, 'course');

	// Check to see if we already have a time query param
	$hasTime = (Request::getString('time', '') != '') ? true : false;

	// Do we want to redirect user with time added to url
	if (is_object($tracking) && !$hasTime && $tracking->current_position > 0 && $tracking->current_position != $tracking->object_duration && !Request::getInt('no_html', 0))
	{
		$redirect = Request::current();

		// do we have tmpl=component in url?
		$delimeter = (strpos($redirect, '?') === false) ? '?' : '&';
		if (Request::getString('tmpl', '') == 'component')
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
<?php if ($type == 'html5') : ?>
	<div id="video-container">
		<?php if (count($presentation->media) > 0) : ?>
			<video controls="controls" id="video-player" data-mediaid="<?php echo $this->asset->id; ?>">
				<?php foreach ($presentation->media as $video) : ?>
					<?php
						switch ($video->type)
						{
							case 'ogg':
							case 'ogv':
								$type = 'video/ogg;';
								break;
							case 'webm':
								$type = 'video/webm;';
								break;
							case 'mp4':
							case 'm4v':
							default:
								$type = 'video/mp4;';
								break;
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
					data-mediaid="<?php echo $this->asset->id; ?>"></a>

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
								$modified = filemtime(PATH_ROOT . $source);
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
	            <div id="control-buttons-left">
	                <a id="play-pause" class="control" href="javascript:void(0);" role="button" aria-label="Play video" aria-pressed="false">
	                    <svg class="icon-pause" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <rect x="6" y="5" width="4" height="14" rx="1.5" fill="currentColor" />
	                        <rect x="14" y="5" width="4" height="14" rx="1.5" fill="currentColor" />
	                    </svg>
	                    <svg class="icon-play" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
	                        <path d="M7 5L19 12L7 19V5Z" fill="currentColor" />
	                    </svg>
	                </a>
	                <div id="media-progress"></div>
	            </div>
	            <div id="control-buttons-right">
	                <a id="subtitle" class="control" href="javascript:void(0);" role="button" aria-pressed="false" aria-label="Captions and Transcript settings" aria-haspopup="true" aria-expanded="false">
	                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <rect x="2" y="5" width="20" height="14" rx="2.5" stroke="currentColor" stroke-width="1.75" />
	                        <text x="5" y="16.5" font-family="sans-serif" font-size="9" font-weight="700" fill="currentColor" text-anchor="start" dominant-baseline="auto">CC</text>
	                    </svg>
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
	                                <select id="transcript-selector" class="transcript-selector">
	                                    <option value="">None/Off</option>
	                                </select>
	                            </div>
	                        </div>
	                        <button type="button" class="options-toggle" aria-expanded="false" aria-controls="subtitle-options">Options</button>
	                        <div class="subtitle-settings hide" id="subtitle-options">
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
	                                    <span id="font-color-label">Font Color:</span>
	                                </div>
	                                <div class="col span6 omega input">
	                                    <div id="font-color" data-color="FFF" role="button" tabindex="0" aria-labelledby="font-color-label" aria-label="Font color picker"></div>
	                                </div>
	                            </div>
	                            <div class="grid">
	                                <div class="col span6 label">
	                                    <span id="background-color-label">Background:</span>
	                                </div>
	                                <div class="col span6 omega input">
	                                    <div id="background-color" data-color="000" role="button" tabindex="0" aria-labelledby="background-color-label" aria-label="Background color picker"></div>
	                                </div>
	                            </div>
	                            <div class="grid">
	                                <div class="col span12 omega subtitle-settings-preview-container">
	                                    <div class="subtitle-settings-preview">
	                                        <div class="test">This is an Example</div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="actions">
	                                <button class="btn btn-info btn-secondary icon-save" id="subtitle-settings-save">Save</button>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	                <a id="volume" class="control" href="javascript:void(0);" role="button" aria-label="Volume" aria-haspopup="true" aria-expanded="false">
	                    <svg class="icon-vol-high" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
	                        <path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor" />
	                        <path d="M14 3.23v2.06A7 7 0 0 1 19 12a7 7 0 0 1-5 6.71v2.06A9 9 0 0 0 21 12 9 9 0 0 0 14 3.23z" fill="currentColor" />
	                    </svg>
	                    <svg class="icon-vol-medium" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
	                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
	                        <path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor" />
	                    </svg>
	                    <svg class="icon-vol-low" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
	                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
	                        <path d="M18.5 12a6.5 6.5 0 0 0-1-3.35v6.7A6.5 6.5 0 0 0 18.5 12z" fill="currentColor" opacity=".4" />
	                    </svg>
	                    <svg class="icon-vol-mute" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
	                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
	                        <line x1="17" y1="9" x2="23" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
	                        <line x1="23" y1="9" x2="17" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
	                    </svg>
	                    <div class="control-container volume-controls">
	                        <label for="volume-bar" class="sr-only">Volume</label>
	                        <div id="volume-bar" role="slider" aria-label="Volume" aria-valuemin="0" aria-valuemax="100" aria-valuenow="75"></div>
	                    </div>
	                </a>
	                <a id="settings" class="control" href="javascript:void(0);" role="button" aria-label="Playback settings" aria-haspopup="true" aria-expanded="false">
	                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="currentColor" />
	                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.4 13a7.6 7.6 0 0 0 .1-1c0-.34-.03-.67-.08-1l2.16-1.68a.5.5 0 0 0 .12-.64l-2.05-3.55a.5.5 0 0 0-.61-.22l-2.55 1.03a7.45 7.45 0 0 0-1.72-1l-.38-2.72A.49.49 0 0 0 14 2h-4a.49.49 0 0 0-.49.42L9.13 5.14a7.45 7.45 0 0 0-1.72 1L4.86 5.11a.49.49 0 0 0-.61.22L2.2 8.88a.48.48 0 0 0 .12.64L4.48 11c-.05.33-.08.66-.08 1s.03.67.08 1L2.32 14.68a.5.5 0 0 0-.12.64l2.05 3.55c.12.22.38.3.61.22l2.55-1.03c.53.39 1.1.72 1.72 1l.38 2.72c.06.28.28.42.49.42h4c.22 0 .43-.14.49-.42l.38-2.72a7.45 7.45 0 0 0 1.72-1l2.55 1.03c.23.08.49 0 .61-.22l2.05-3.55a.5.5 0 0 0-.12-.64L19.4 13z" fill="currentColor" opacity=".7" />
	                    </svg>
	                    <div class="control-container settings-controls">
	                        <h3>Settings</h3>
	                        <div class="grid">
	                            <div class="label"><label for="speed">Speed:</label></div>
	                            <div class="input">
	                                <select id="speed">
	                                    <option value=".25">.25×</option>
	                                    <option value=".5">.5×</option>
	                                    <option selected value="1">Normal</option>
	                                    <option value="1.25">1.25×</option>
	                                    <option value="1.5">1.5×</option>
	                                    <option value="2">2×</option>
	                                </select>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	                <a id="link" class="control" href="javascript:void(0);" role="button" aria-label="Share link at current time" aria-haspopup="true" aria-expanded="false">
	                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
	                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
	                    </svg>
	                    <div class="control-container link-controls">
	                        <h3>Share link <span>at current position</span></h3>
	                        <div class="grid">
	                            <div class="input">
	                                <label for="timestamp-link" class="sr-only">Link at current position</label>
	                                <input type="text" id="timestamp-link" value="" aria-label="Link at current position" readonly />
	                                <span class="hint">Cmd/Ctrl + C to copy</span>
	                            </div>
	                        </div>
	                    </div>
	                </a>
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
<?php elseif ($type == 'hubpresenter') : ?>
	<?php $presentationFormat = (isset($presentation->format) && strtoupper($presentation->format) == 'HD') ? 'presentation-hd' : ''; ?>
	<div id="presenter-container">
		<div id="presenter-header">
			<div id="title"><?php echo $this->asset->get('title'); ?></div>
		</div><!-- /#header -->

		<div id="presenter-content">
			<div id="presenter-left">
				<div id="slides">
					<ul class="no-js">
						<?php $counter = 0; ?>
						<?php foreach ($presentation->slides as $slide) : ?>
							<li id="slide_<?php echo $counter; ?>" title="<?php echo $slide->title; ?>" time="<?php echo $slide->time; ?>">
								<?php if ($slide->type == 'Image') : ?>
									<img src="<?php echo $content_folder . DS . $slide->media; ?>" alt="<?php echo $slide->title; ?>" />
								<?php else : ?>
									<video class="slidevideo">
										<?php foreach ($slide->media as $source): ?>
											<source src="<?php echo $content_folder . DS . $source->source; ?>" />
										<?php endforeach; ?>
										<a href="<?php echo $content_folder . DS . $slide->media[0]->source; ?>" class="flowplayer_slide" id="flowplayer_slide_<?php echo $counter; ?>"></a>
									</video>
									<img src="<?php echo $content_folder . DS . $slide->media[3]->source; ?>" alt="<?php echo $slide->title; ?>" class="imagereplacement" />
								<?php endif; ?>
							</li>
							<?php $counter++; ?>
						<?php endforeach; ?>
					</ul>
				</div><!-- /#slides -->

				<div id="control-box" class="no-controls" data-theme="dark">
					<div id="progress-bar"></div>
					<div id="control-buttons">
			            <div id="control-buttons-left">
			                <a id="play-pause" class="control" href="javascript:void(0);" role="button" aria-label="Play video" aria-pressed="false">
			                    <svg class="icon-pause" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			                        <rect x="6" y="5" width="4" height="14" rx="1.5" fill="currentColor" />
			                        <rect x="14" y="5" width="4" height="14" rx="1.5" fill="currentColor" />
			                    </svg>
			                    <svg class="icon-play" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
			                        <path d="M7 5L19 12L7 19V5Z" fill="currentColor" />
			                    </svg>
			                </a>
			                <div id="media-progress"></div>
			            </div>
			            <div id="control-buttons-right">
			                <a id="subtitle" class="control" href="javascript:void(0);" role="button" aria-pressed="false" aria-label="Captions and Transcript settings" aria-haspopup="true" aria-expanded="false">
			                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			                        <rect x="2" y="5" width="20" height="14" rx="2.5" stroke="currentColor" stroke-width="1.75" />
			                        <text x="5" y="16.5" font-family="sans-serif" font-size="9" font-weight="700" fill="currentColor" text-anchor="start" dominant-baseline="auto">CC</text>
			                    </svg>
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
			                                <select id="transcript-selector" class="transcript-selector">
			                                    <option value="">None/Off</option>
			                                </select>
			                            </div>
			                        </div>
			                        <button type="button" class="options-toggle" aria-expanded="false" aria-controls="subtitle-options">Options</button>
			                        <div class="subtitle-settings hide" id="subtitle-options">
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
			                                    <span id="font-color-label">Font Color:</span>
			                                </div>
			                                <div class="col span6 omega input">
			                                    <div id="font-color" data-color="FFF" role="button" tabindex="0" aria-labelledby="font-color-label" aria-label="Font color picker"></div>
			                                </div>
			                            </div>
			                            <div class="grid">
			                                <div class="col span6 label">
			                                    <span id="background-color-label">Background:</span>
			                                </div>
			                                <div class="col span6 omega input">
			                                    <div id="background-color" data-color="000" role="button" tabindex="0" aria-labelledby="background-color-label" aria-label="Background color picker"></div>
			                                </div>
			                            </div>
			                            <div class="grid">
			                                <div class="col span12 omega subtitle-settings-preview-container">
			                                    <div class="subtitle-settings-preview">
			                                        <div class="test">This is an Example</div>
			                                    </div>
			                                </div>
			                            </div>
			                            <div class="actions">
			                                <button class="btn btn-info btn-secondary icon-save" id="subtitle-settings-save">Save</button>
			                            </div>
			                        </div>
			                    </div>
			                </a>
			                <a id="volume" class="control" href="javascript:void(0);" role="button" aria-label="Volume" aria-haspopup="true" aria-expanded="false">
			                    <svg class="icon-vol-high" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
			                        <path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor" />
			                        <path d="M14 3.23v2.06A7 7 0 0 1 19 12a7 7 0 0 1-5 6.71v2.06A9 9 0 0 0 21 12 9 9 0 0 0 14 3.23z" fill="currentColor" />
			                    </svg>
			                    <svg class="icon-vol-medium" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
			                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
			                        <path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor" />
			                    </svg>
			                    <svg class="icon-vol-low" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
			                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
			                        <path d="M18.5 12a6.5 6.5 0 0 0-1-3.35v6.7A6.5 6.5 0 0 0 18.5 12z" fill="currentColor" opacity=".4" />
			                    </svg>
			                    <svg class="icon-vol-mute" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
			                        <path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor" />
			                        <line x1="17" y1="9" x2="23" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
			                        <line x1="23" y1="9" x2="17" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
			                    </svg>
			                    <div class="control-container volume-controls">
			                        <label for="volume-bar" class="sr-only">Volume</label>
			                        <div id="volume-bar" role="slider" aria-label="Volume" aria-valuemin="0" aria-valuemax="100" aria-valuenow="75"></div>
			                    </div>
			                </a>
			                <a id="settings" class="control" href="javascript:void(0);" role="button" aria-label="Playback settings" aria-haspopup="true" aria-expanded="false">
			                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="currentColor" />
			                        <path fill-rule="evenodd" clip-rule="evenodd" d="M19.4 13a7.6 7.6 0 0 0 .1-1c0-.34-.03-.67-.08-1l2.16-1.68a.5.5 0 0 0 .12-.64l-2.05-3.55a.5.5 0 0 0-.61-.22l-2.55 1.03a7.45 7.45 0 0 0-1.72-1l-.38-2.72A.49.49 0 0 0 14 2h-4a.49.49 0 0 0-.49.42L9.13 5.14a7.45 7.45 0 0 0-1.72 1L4.86 5.11a.49.49 0 0 0-.61.22L2.2 8.88a.48.48 0 0 0 .12.64L4.48 11c-.05.33-.08.66-.08 1s.03.67.08 1L2.32 14.68a.5.5 0 0 0-.12.64l2.05 3.55c.12.22.38.3.61.22l2.55-1.03c.53.39 1.1.72 1.72 1l.38 2.72c.06.28.28.42.49.42h4c.22 0 .43-.14.49-.42l.38-2.72a7.45 7.45 0 0 0 1.72-1l2.55 1.03c.23.08.49 0 .61-.22l2.05-3.55a.5.5 0 0 0-.12-.64L19.4 13z" fill="currentColor" opacity=".7" />
			                    </svg>
			                    <div class="control-container settings-controls">
			                        <h3>Settings</h3>
			                        <div class="grid">
			                            <div class="label"><label for="speed">Speed:</label></div>
			                            <div class="input">
			                                <select id="speed">
			                                    <option value=".25">.25×</option>
			                                    <option value=".5">.5×</option>
			                                    <option selected value="1">Normal</option>
			                                    <option value="1.25">1.25×</option>
			                                    <option value="1.5">1.5×</option>
			                                    <option value="2">2×</option>
			                                </select>
			                            </div>
			                        </div>
			                    </div>
			                </a>
			                <a id="link" class="control" href="javascript:void(0);" role="button" aria-label="Share link at current time" aria-haspopup="true" aria-expanded="false">
			                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
			                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
			                    </svg>
			                    <div class="control-container link-controls">
			                        <h3>Share link <span>at current position</span></h3>
			                        <div class="grid">
			                            <div class="input">
			                                <label for="timestamp-link" class="sr-only">Link at current position</label>
			                                <input type="text" id="timestamp-link" value="" aria-label="Link at current position" readonly />
			                                <span class="hint">Cmd/Ctrl + C to copy</span>
			                            </div>
			                        </div>
			                    </div>
			                </a>
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
						<video id="player" preload="auto" controls="controls" data-mediaid="<?php echo $this->asset->get('id'); ?>">
							<?php foreach ($presentation->media as $source): ?>
								<?php
									switch (strtolower($source->type))
									{
										case 'm4v':
										case 'mp4':  $type = 'video/mp4;';
break;
										case 'ogv':  $type = 'video/ogg;';
break;
										case 'webm': $type = 'video/webm;';
break;
									}
								?>
								<source src="<?php echo $content_folder . DS . $source->source; ?>" type='<?php echo $type; ?>'>
							<?php endforeach; ?>
							<a href="<?php echo $content_folder . DS . $presentation->media[0]->source; ?>" id="flowplayer"></a>

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
								<source src="<?php echo $content_folder . DS . $source->source; ?>" />
							<?php endforeach; ?>
							<a href="<?php echo $content_folder . DS . $presentation->media[0]->source; ?>" id="flowplayer" duration="<?php if (isset($presentation->duration) && $presentation->duration) { echo $presentation->duration; } ?>"></a>
						</audio>
					<?php endif; ?>
					<div id="video-subtitles"></div>
				</div>
				<div id="list">
					<ul id="list_items">
						<?php $num = 0;
$counter = 0;
$last_slide_id = 0; ?>
						<?php foreach ($presentation->slides as $slide) : ?>
							<?php if ((int)$slide->slide != $last_slide_id) : ?>
								<li id="list_<?php echo $counter; ?>">
									<?php
										// Use thumb if possible
										$thumb = '';
										if (isset($slide->thumb) && $slide->thumb && file_exists(PATH_ROOT.DS.$content_folder.DS.$slide->thumb))
										{
											$thumb = $content_folder.DS.$slide->thumb;
										}
										else if (!is_array($slide->media) && file_exists(PATH_ROOT.DS.$content_folder.DS.$slide->media))
										{
											$thumb = $content_folder.DS.$slide->media;
										}
									?>
									<img src="<?php echo $thumb; ?>" alt="<?php echo $slide->title; ?>" />
									<span>
										<?php
											$num++;
											$max = 30;
											$elipsis = '&hellip;';
											echo ($num) . '. ';
											echo substr($slide->title, 0, $max);

											if (strlen($slide->title) > $max) {
												echo $elipsis;
											}
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
<?php elseif ($type == 'standalone') : ?>
	<?php
		$path = DS . trim(substr(PATH_APP, strlen(PATH_ROOT)), DS) . DS . ltrim($path . DS . $this->model->get('url'), DS);
		$ext  = strtolower(Filesystem::extension(PATH_ROOT . $path));

		Document::addStyleSheet('//releases.flowplayer.org/6.0.5/skin/minimalist.css');
		Document::addScript('//releases.flowplayer.org/6.0.5/flowplayer.min.js');
	?>
	<div class="flowplayer">
		<video id="movie<?php echo rand(0, 1000); ?>" preload controls>
			<?php
				switch ($ext)
				{
					case 'mov':
					case 'mp4':
					case 'm4v':
						echo '<source src="' . $path . '" type="video/mp4" />';
					break;

					case 'ogg':
					case 'ogv':
						echo '<source src="' . $path . '" type="video/ogg" />';
					break;

					case 'webm':
						echo '<source src="' . $path . '" type="video/webm" />';
					break;
				}
			?>
		</video>
	</div>
<?php else : ?>
	<p class="warning"><?php echo (isset($error)) ? $error : Lang::txt('COM_COURSES_VIDEO_ERROR_NO_PLAYABLE_ASSETS'); ?></p>
<?php endif; 

