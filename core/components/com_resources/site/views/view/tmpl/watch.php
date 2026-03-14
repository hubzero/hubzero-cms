<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('hubpresenter.css')
     ->js('hubpresenter.js')
     ->js('hubpresenter.plugins.js')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

//get the manifest for the presentation
$contents = file_get_contents(PATH_APP . DS . $this->manifest);
//content folder
$content_folder = $this->content_folder;
$content_url = $this->content_url;

//decode the json formatted manifest so we can use the information
$presentation = json_decode($contents);
$presentation = $presentation->presentation;
if (!is_object($presentation))
{
	$presentation = new stdClass;
	$presentation->slides = array();
	$presentation->media = array();
	$presentation->placeholder = null;
	$presentation->duration = null;
}

//get this resource
$rr = \Components\Resources\Models\Entry::oneOrFail($this->resid);

$this->doc->setTitle(stripslashes($rr->title));

//get the parent resource
$parent = $rr->parents()
	->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
	->rows()
	->first();

//check to see if parent type is series
if ($parent && $parent->id && ($parent->type->get('type') == 'Series' || $parent->type->get('type') == 'Courses'))
{
	//if we have a series get children
	$children = $parent->children()
		->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
		->rows();

	//remove any children without a HUBpresenter
	foreach ($children as $k => $c)
	{
		$sub_child = $c->children()
			->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
			->rows();

		$hasHUBpresenter = false;

		foreach ($sub_child as $sc)
		{
			if (strtolower($sc->type->get('title')) == 'hubpresenter')
			{
				$hasHUBpresenter = true;
			}
		}

		if (!$hasHUBpresenter)
		{
			$children->drop($k);
		}
	}
}
else
{
	$children = null;
}

$lectureAuthors = array();
if ($parent && $parent->id)
{
	//get the contributors for the resource
	$sql = "SELECT authorid, role, name FROM `#__author_assoc` "
		 . "WHERE subtable='resources' "
		 . "AND subid=" . $parent->id . " "
		 . "ORDER BY ordering";

	$database = App::get('db');
	$database->setQuery($sql);
	$lectureAuthors = $database->loadObjectList();
}

//get the author names from ids
$a = array();

if (!empty($lectureAuthors))
{
	foreach ($lectureAuthors as $la)
	{
		//if this is a submitter lets continue
		if ($la->role == 'submitter')
		{
			continue;
		}
		//load author object
		$author = User::getInstance($la->authorid);
		if (is_object($author) && $author->id)
		{
			$a[] = '<a href="' . $author->link() . '">' . $author->name . '</a>';
		}
		else
		{
			$a[] = $la->name;
		}
	}
}

//check to see if already have subtitles
if (!isset($presentation->subtitles))
{
	$presentation->subtitles = array();
}

// make sure source is full path to assets folder
$subFiles = array();
foreach ($presentation->subtitles as $k => $subtitle)
{
	if (!strpos($subtitle->source, DS))
	{
		$subtitle->source_url = $content_url . DS . $subtitle->source;
		$subtitle->source = $content_folder . DS . $subtitle->source;
	}

	$subFiles[] = $subtitle->source;
}

//get all local subtitles
$localSubtitles = Filesystem::files(PATH_APP . DS . $content_folder, '.srt|.SRT');

// add local subtitles too
foreach ($localSubtitles as $k => $subtitle)
{
	$info     = pathinfo($subtitle);
	$name     = str_replace('-auto', '', $info['filename']);
	$autoplay = (strstr($info['filename'], '-auto')) ? 1 : 0;
	$source   = $content_folder . DS . $subtitle;

	// add each subtitle
	$subtitle = new stdClass;
	$subtitle->type     = 'SRT';
	$subtitle->name     = ucfirst($name);
	$subtitle->source   = $source;
	$subtitle->source_url = $content_url . $subtitle;
	$subtitle->autoplay = $autoplay;

	// make sure we dont already have this file.
	if (!in_array($subtitle->source, $subFiles))
	{
		$presentation->subtitles[] = $subtitle;
	}
}

//reset keys
$presentation->subtitles = array_values($presentation->subtitles);
?>

<div id="presenter-nav-bar">
	<a href="/resources/<?php echo $rr->id; ?>" id="powered" title="Powered by <?php echo Config::get('sitename'); ?>">
		<span>powered by</span> <?php echo Config::get('sitename'); ?>
	</a>

	<?php if ($children) : ?>
		<form name="presentation-picker" id="presentation-picker" method="post">
			<label for="presentation">Select a different presentation:
				<select name="presentation" id="presentation">
					<optgroup label="<?php echo $parent->title; ?>">
						<?php foreach ($children as $c) : ?>
							<?php if (Date::toSql() > $c->publish_up || $user->get("usertype") == 'Administrator' || $user->get("usertype") == 'Super Administrator') : ?>
								<option <?php if ($c->title == $rr->title) { echo "selected"; } ?> value="<?php echo $c->id; ?>"><?php echo $c->title; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</optgroup>
				</select>
			</label>
			<noscript><input type="submit" name="presentations-submit" value="Go" /></noscript>
			<input type="hidden" name="option" value="com_resources" />
			<input type="hidden" name="task" value="selectpresentation" />
		</form>
	<?php endif; ?>
</div>

<?php $presenationFormat = (isset($presentation->format) && strtoupper($presentation->format) == 'HD') ? 'presentation-hd' : ''; ?>
<div id="presenter-container" class="<?php echo $presenationFormat; ?>" data-id="<?php echo $this->resid; ?>">
	<div id="presenter-header">
		<div id="title"><?php echo $rr->title; ?></div>
		<div id="author"><?php if ($a) { echo "by: " . implode(", ", $a); } ?></div>
		<!--<div id="slide_title"></div>-->
	</div><!-- /#header -->

	<div id="presenter-content">
		<div id="presenter-left">
			<div id="slides">
				<ul class="no-js">
					<?php $counter = 0; ?>
					<?php foreach ($presentation->slides as $slide) : ?>
						<li id="slide_<?php echo $counter; ?>" title="<?php echo $slide->title; ?>" time="<?php echo $slide->time; ?>">
							<?php if ($slide->type == 'Image') : ?>
								<img src="<?php echo $content_url.DS.$slide->media; ?>" alt="<?php echo $slide->title; ?>" />
							<?php else : ?>
								<video class="slidevideo" preload="metadata" muted>
									<?php foreach ($slide->media as $source): ?>
										<source src="<?php echo $content_url.DS.$source->source; ?>" />
									<?php endforeach; ?>
									<a href="<?php echo $content_url.DS.$slide->media[0]->source; ?>" class="flowplayer_slide" id="flowplayer_slide_<?php echo $counter; ?>"></a>
								</video>
								<img src="<?php echo $content_url.DS.$slide->media[3]->source; ?>" alt="<?php echo $slide->title; ?>" class="imagereplacement">
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
						<a id="previous" class="control" href="javascript:void(0);" role="button" aria-label="Previous slide">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="5" y="5" width="2" height="14" rx="1" fill="currentColor"/>
								<path d="M18 6L10 12L18 18V6Z" fill="currentColor"/>
							</svg>
						</a>
						<a id="play-pause" class="control" href="javascript:void(0);" role="button" aria-label="Play presentation" aria-pressed="false">
							<!-- pause icon shown when playing; play icon shown when paused (toggled via JS class) -->
							<svg class="icon-pause" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="6" y="5" width="4" height="14" rx="1.5" fill="currentColor"/>
								<rect x="14" y="5" width="4" height="14" rx="1.5" fill="currentColor"/>
							</svg>
							<svg class="icon-play" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
								<path d="M7 5L19 12L7 19V5Z" fill="currentColor"/>
							</svg>
						</a>
						<a id="next" class="control" href="javascript:void(0);" role="button" aria-label="Next slide">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="17" y="5" width="2" height="14" rx="1" fill="currentColor"/>
								<path d="M6 6L14 12L6 18V6Z" fill="currentColor"/>
							</svg>
						</a>
						<div id="media-progress"></div>
					</div>
					<div id="control-buttons-right">
						<a id="subtitle" class="control" href="javascript:void(0);" role="button" aria-pressed="false" aria-label="Captions and Transcript settings" aria-haspopup="true" aria-expanded="false">
							<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;flex-shrink:0">
								<rect x="2" y="5" width="20" height="14" rx="2.5" stroke="currentColor" stroke-width="1.75"/>
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
						<a id="volume" class="control" href="javascript:void(0);" role="button" aria-label="Volume">
							<svg class="icon-vol-high" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
								<path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor"/>
								<path d="M14 3.23v2.06A7 7 0 0 1 19 12a7 7 0 0 1-5 6.71v2.06A9 9 0 0 0 21 12 9 9 0 0 0 14 3.23z" fill="currentColor"/>
							</svg>
							<svg class="icon-vol-medium" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
								<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
								<path d="M16.5 12A4.5 4.5 0 0 0 14 7.97v8.05A4.5 4.5 0 0 0 16.5 12z" fill="currentColor"/>
							</svg>
							<svg class="icon-vol-low" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
								<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
								<path d="M18.5 12a6.5 6.5 0 0 0-1-3.35v6.7A6.5 6.5 0 0 0 18.5 12z" fill="currentColor" opacity=".4"/>
							</svg>
							<svg class="icon-vol-mute" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
								<path d="M3 9v6h4l5 5V4L7 9H3z" fill="currentColor"/>
								<line x1="17" y1="9" x2="23" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
								<line x1="23" y1="9" x2="17" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							</svg>
							<div class="control-container volume-controls">
								<div id="volume-bar"></div>
							</div>
						</a>
						<a id="settings" class="control" href="javascript:void(0);" role="button" aria-label="Playback settings" aria-haspopup="true" aria-expanded="false">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="currentColor"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M19.4 13a7.6 7.6 0 0 0 .1-1c0-.34-.03-.67-.08-1l2.16-1.68a.5.5 0 0 0 .12-.64l-2.05-3.55a.5.5 0 0 0-.61-.22l-2.55 1.03a7.45 7.45 0 0 0-1.72-1l-.38-2.72A.49.49 0 0 0 14 2h-4a.49.49 0 0 0-.49.42L9.13 5.14a7.45 7.45 0 0 0-1.72 1L4.86 5.11a.49.49 0 0 0-.61.22L2.2 8.88a.48.48 0 0 0 .12.64L4.48 11c-.05.33-.08.66-.08 1s.03.67.08 1L2.32 14.68a.5.5 0 0 0-.12.64l2.05 3.55c.12.22.38.3.61.22l2.55-1.03c.53.39 1.1.72 1.72 1l.38 2.72c.06.28.28.42.49.42h4c.22 0 .43-.14.49-.42l.38-2.72a7.45 7.45 0 0 0 1.72-1l2.55 1.03c.23.08.49 0 .61-.22l2.05-3.55a.5.5 0 0 0-.12-.64L19.4 13z" fill="currentColor" opacity=".7"/>
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
								<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
						<a id="switch" class="control" href="javascript:void(0);" role="button" aria-label="Switch video and slides">
							<svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="2" y="4" width="9" height="7" rx="1.5" fill="currentColor"/>
								<rect x="13" y="4" width="9" height="7" rx="1.5" fill="currentColor" opacity=".5"/>
								<rect x="2" y="13" width="9" height="7" rx="1.5" fill="currentColor" opacity=".5"/>
								<rect x="13" y="13" width="9" height="7" rx="1.5" fill="currentColor"/>
							</svg>
						</a>
						<a id="fullscreen" class="control" href="javascript:void(0);" role="button" aria-label="Enter fullscreen">
							<svg class="icon-enter-fs" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<svg class="icon-exit-fs" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
								<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
					</div>
				</div>
			</div><!-- /#control-box -->
		</div><!-- /#left -->
		<?php $cls = (isset($presentation->videoPosition) && $presentation->videoPosition == "left" && strtolower($presentation->type) == 'video') ? "move-left": ""; ?>
		<div id="presenter-right">
			<div id="media" class="<?php echo $cls; ?>">
				<?php if (strtolower($presentation->type) == 'video') : ?>
					<video id="player" preload="auto" controls="controls" data-mediaid="<?php echo $rr->id; ?>">
						<?php foreach ($presentation->media as $media): ?>
							<?php
								switch ($media->type)
								{
									case 'ogg':
									case 'ogv':
										$type = "video/ogg;";
										break;
									case 'webm':
										$type = "video/webm;";
										break;
									case 'mp4':
									case 'm4v':
									default:
										$type = "video/mp4;";
										break;
								}

								//get the source
								$source = $media->source;

								//is this the mp4 (need for flash)
								if (in_array($media->type, array('mp4','m4v')))
								{
									$mp4 = $media->source;
								}

								//if were playing local files
								if (substr($media->source, 0, 4) != 'http')
								{
									$source = $content_url . DS . $source;
									if (in_array($media->type, array('mp4','m4v')))
									{
										$mp4 = $content_url . DS . $mp4;
									}
								}
							?>
							<source src="<?php echo $source; ?>" type="<?php echo $type; ?>">
						<?php endforeach; ?>

						<a href="<?php echo $mp4; ?>" 
							id="flowplayer" 
							data-mediaid="<?php echo $rr->id; ?>"></a>
						<?php if (count($presentation->subtitles) > 0) : ?>
							<?php foreach ($presentation->subtitles as $subtitle) : ?>
								<?php
									//get file modified time
									$source = $subtitle->source;
									$source_url = $subtitle->source_url;
									$auto   = $subtitle->autoplay;

									//if were playing local files
									if (substr($subtitle->source, 0, 4) != 'http')
									{
										$modified = filemtime(PATH_APP . DS . $source);
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
									data-src="<?php echo $source_url ?>?v=<?php echo $modified; ?>"></div>
							<?php endforeach; ?>
						<?php endif; ?>
					</video>
				<?php else : ?>
					<audio id="player" preload="auto" controls="controls" data-mediaid="<?php echo $rr->id; ?>">
						<?php foreach ($presentation->media as $source): ?>
							<?php
								switch ($source->type)
								{
									case 'mp3':
										$type = 'audio/mp3';
										break;
									case 'ogv':
									case 'ogg':
										$type = 'audio/ogg';
										break;
								}
							?>
							<source src="<?php echo $content_url.DS.$source->source; ?>" type="<?php echo $type; ?>" />
						<?php endforeach; ?>
						<a href="<?php echo $content_url.DS.$presentation->media[0]->source; ?>" id="flowplayer" duration="<?php if (isset($presentation->duration) && $presentation->duration) { echo $presentation->duration; } ?>" data-mediaid="<?php echo $rr->id; ?>"></a>
					</audio>

					<?php if (isset($presentation->placeholder) && $presentation->placeholder) : ?>
						<img src="<?php echo $content_url.DS.$presentation->placeholder; ?>" alt="Audio presentation placeholder" id="placeholder" />
					<?php endif; ?>
				<?php endif; ?>
				<div id="video-subtitles" role="region" aria-label="Closed captions" aria-live="polite" aria-atomic="true"></div>
			</div>
			<div id="list">
				<ul id="list_items">
					<?php $num = 0;
$counter = 0;
$last_slide_id = 0; ?>
					<?php foreach ($presentation->slides as &$slide) : ?>
						<?php if ((int)$slide->slide != $last_slide_id) : ?>
							<li id="list_<?php echo $counter; ?>">
								<?php
									//use thumb if possible
									$thumb = $slide->media;
									$thumb = is_array($thumb)? $thumb[0] : $thumb;
									if (is_string($thumb)){
										$thumb = $content_url . DS . $thumb;
									}
									if (isset($slide->thumb) && $slide->thumb && file_exists(PATH_APP . DS . $content_folder . DS . $slide->thumb))
									{
										$thumb = $content_url.DS.$slide->thumb;
									}
								?>
								<img src="<?php echo $thumb; ?>" alt="<?php echo $slide->title; ?>" />
								<span>
									<?php 
										$num++;
										$max = 30;
										$elipsis = "&hellip;";
										echo ($num) . ". ";
										echo substr($slide->title, 0, $max);

										if (strlen($slide->title) > $max)
										{
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

	<div id="transcript-container" role="region" aria-label="Transcript">
		<div id="transcript-toolbar" role="toolbar" aria-label="Transcript controls">
			<div id="transcript-select" aria-live="polite"></div>
			<div id="transcript-search-wrap">
				<svg class="transcript-search-icon" aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.75"/>
					<line x1="12.5" y1="12.5" x2="17" y2="17" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
				</svg>
				<label for="transcript-search" class="sr-only">Search transcript</label>
				<input type="search" id="transcript-search" placeholder="Search transcript…" aria-label="Search transcript" autocomplete="off" />
				<button type="button" id="transcript-search-clear" aria-label="Clear search" style="display:none">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
						<line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
					</svg>
				</button>
			</div>
			<span id="transcript-search-count" aria-live="polite" aria-atomic="true"></span>
			<div id="transcript-font-controls">
				<button type="button" id="font-smaller" aria-label="Decrease transcript font size">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<text x="3" y="15" font-family="serif" font-size="11" fill="currentColor">A</text>
						<line x1="13" y1="10" x2="19" y2="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
				</button>
				<button type="button" id="font-bigger" aria-label="Increase transcript font size">
					<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<text x="1" y="16" font-family="serif" font-size="14" fill="currentColor">A</text>
						<line x1="13" y1="10" x2="19" y2="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						<line x1="16" y1="7" x2="16" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
				</button>
			</div>
		</div>
		<div id="transcripts" role="log" aria-label="Transcript text" aria-live="off" tabindex="0"></div>
	</div>
	<div class="bottom-controls">
		<a href="javascript:void(0);" class="btn btn-secondardy icon-popout embed-popout">Pop Out</a>
	</div>
</div>
