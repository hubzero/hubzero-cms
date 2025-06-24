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
			<label for="presentations">Select a different presentation:
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
					<div id="control-buttons-left" class="cf">
						<a id="previous" class="tooltips control" href="javascript:void(0);" title="Previous Slide">Previous</a>
						<a id="play-pause" class="tooltips control" href="javascript:void(0);" title="Play Presentation">Pause</a>
						<a id="next" class="tooltips control" href="javascript:void(0);" title="Next Slide">Next</a>
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
											<div id="font-color" data-color="FFF"></div>
										</div>
									</div>
									<div class="grid">
										<div class="col span6 label">
											<label for="background-color">Background:</label>
										</div>
										<div class="col span6 omega input">
											<div id="background-color" data-color="000"></div>
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
						<a id="volume" class="control" href="javascript:void(0);">
							Volume
							<div class="control-container volume-controls">
								<div id="volume-bar"></div>
							</div>
						</a>
						<a id="settings" class="control" href="javascript:void(0);" title="Adjust Settings for Playback">
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
							</div>
						</a>
						<a id="link" class="control" href="javascript:void(0);" title="Link to this Spot in Presentation">
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
						<a id="switch" class="tooltips control" href="javascript:void(0);" title="Switch Placement of Video and Slides">Switch</a>
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
						<img src="<?php echo $content_url.DS.$presentation->placeholder; ?>" title="" id="placeholder" />
					<?php endif; ?>
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
									//use thumb if possible
									$thumb = $content_folder . DS . is_array($slide->media) ? $slide->media[0] : $slide->media;
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
</div>
