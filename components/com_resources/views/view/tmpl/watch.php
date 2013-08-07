<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get the manifest for the presentation
$contents = file_get_contents(JPATH_ROOT.$this->manifest);

//content folder
$content_folder = $this->content_folder;

//decode the json formatted manifest so we can use the information
$presentation = json_decode( $contents );
$presentation = $presentation->presentation;

//get this resource
$rr = new ResourcesResource( $this->database );
$rr->load( $this->resid );

//get the parent resource
$rh = new ResourcesHelper( $this->resid, $this->database );
$rh->getParents();

$parent = $rh->parents[0];

//check to see if parent type is series
$rt = new ResourcesType( $this->database );
$rt->load($parent->type);

//if we have a series get children
if($rt->type == "Series" || $rt->type == "Courses") {
	$rh->getChildren( $parent->id, 0, 'yes' );
	$children = $rh->children;

	//remove any children without a HUBpresenter
	foreach($children as $k => $c) {
		$rh = new ResourcesHelper( $c->id, $this->database );
		$rh->getChildren();
		$sub_child = $rh->children;
		$hasHUBpresenter = false;

		foreach($sub_child as $sc) {
			$rt = new ResourcesType( $this->database );
			$rt->load($sc->type);
			if(strtolower($rt->type) == "hubpresenter") {
				$hasHUBpresenter = true;
			}
		}

		if(!$hasHUBpresenter) {
			unset($children[$k]);
		}
	}
} else {
	$children = NULL;
}

//get the contributors for the resource
$sql = "SELECT authorid, role, name FROM #__author_assoc "
	 . "WHERE subtable='resources' "
	 . "AND subid=" . $parent->id . " "
	 . "ORDER BY ordering";
	
$this->database->setQuery( $sql );
$lectureAuthors = $this->database->loadObjectList();

//get the author names from ids
$a = array();
foreach ($lectureAuthors as $la)
{
	//if this is a submitter lets continue
	if ($la->role == 'submitter')
	{
		continue;
	}
	//load author object
	$author =& JUser::getInstance( $la->authorid );
	if (is_object($author) && $author->id)
	{
		$a[] = '<a href="/members/' . $author->id . '">' . $author->name . '</a>';
	}
	else
	{
		$a[] = $la->name;
	}
}

//check to see if already have subtitles
if (!isset($presentation->subtitles))
{
	$presentation->subtitles = array();
}

//get all local subtitles
$localSubtitles = JFolder::files(JPATH_ROOT . DS . $content_folder, '.srt|.SRT');

foreach ($localSubtitles as $k => $subtitle)
{
	$info     = pathinfo($subtitle);
	$name     = str_replace('-auto','', $info['filename']);
	$autoplay = (strstr($info['filename'],'-auto')) ? 1 : 0;
	$source   = $content_folder . DS . $subtitle;
	
	//add each subtitle
	$presentation->subtitles[$k]->type     = 'SRT';
	$presentation->subtitles[$k]->name     = ucfirst($name);
	$presentation->subtitles[$k]->source   = $source;
	$presentation->subtitles[$k]->autoplay = $autoplay;
}

//reset keys
$presentation->subtitles = array_values($presentation->subtitles);
?>

<div id="presenter-nav-bar">
	<a href="/resources/<?php echo $rr->id; ?>" id="nanohub" title="Close Window">&times; Close Window</a>
	
	<?php if($children) : ?>
		<form name="presentation-picker" id="presentation-picker" method="post">
			<label for="presentations">Select a different presentation: 
				<select name="presentation" id="presentation">
					<optgroup label="<?php echo $parent->title; ?>">
						<?php foreach($children as $c) : ?>
							<?php if(date("Y-m-d H:i:s") > $c->publish_up || $user->get("usertype") == 'Administrator' || $user->get("usertype") == 'Super Administrator') : ?>
								<option <?php if($c->title == $rr->title) { echo "selected"; } ?> value="<?php echo $c->id; ?>"><?php echo $c->title; ?></option>
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

<div id="presenter-shortcuts-box"> 
	<h2>Keyboard Shortcuts</h2>
	<a href="#" id="shortcuts-close">Close</a>
	<ul id="shortcuts-content">
		<li><kbd>Space</kbd> or <kbd>P</kbd><span>Pauses/Plays Presentation</li>
		<li><kbd>&darr;</kbd> or <kbd>&rarr;</kbd><span>Next Slide</span></li>
		<li><kbd>&uarr;</kbd> or <kbd>&larr;</kbd><span>Previous Slide</span></li>
		<li><kbd>+</kbd><span>Increase Volume</span></li>
		<li><kbd>-</kbd><span>Decrease Volume</span></li>
		<li><kbd>M</kbd><span>Mute Presentation</span></li>
	</dl>
</div>

<?php $presenationFormat = (isset($presentation->format) && strtoupper($presentation->format) == 'HD') ? 'presentation-hd' : ''; ?>
<div id="presenter-container" class="<?php echo $presenationFormat; ?>">
	<div id="presenter-header">
		<div id="title"><?php echo $rr->title; ?></div>
		<div id="author"><?php if($a) { echo "by: " . implode(", ", $a); } ?></div>
		<!--<div id="slide_title"></div>-->
	</div><!-- /#header -->
	
	<div id="presenter-content">
		<div id="presenter-left">
			<div id="slides">
				<ul class="no-js">
					<?php $counter = 0; ?>
					<?php foreach($presentation->slides as $slide) : ?>
						<li id="slide_<?php echo $counter; ?>" title="<?php echo $slide->title; ?>" time="<?php echo $slide->time; ?>">
							<?php if($slide->type == 'Image') : ?>
								<img src="<?php echo $content_folder.DS.$slide->media; ?>" alt="<?php echo $slide->title; ?>" />
							<?php else : ?>
								<video class="slidevideo">  
									<?php foreach($slide->media as $source): ?>
										<source src="<?php echo $content_folder.DS.$source->source; ?>" /> 
									<?php endforeach; ?>
									<a href="<?php echo $content_folder.DS.$slide->media[0]->source; ?>" class="flowplayer_slide" id="flowplayer_slide_<?php echo $counter; ?>"></a> 
								</video>
								<img src="<?php echo $content_folder.DS.$slide->media[3]->source; ?>" alt="<?php echo $slide->title; ?>" class="imagereplacement">
							<?php endif; ?>
						</li>
						<?php $counter++; ?>
					<?php endforeach; ?>
				</ul>
			</div><!-- /#slides -->
			<div id="control-box" class="no-controls">
				<div id="control-buttons">
					<div id="volume-icon"></div>
					<div id="volume-bar"></div>
					<a id="previous" href="#" title="Previous Slide">Previous</a>
					<a id="play-pause" href="#" title="Play Presentation">Pause</a>
					<a id="next" href="#" title="Next Slide">Next</a>
					<a id="shortcuts" href="#" title="Keyboard Shortcuts">Shortcuts</a>
					<a id="link" href="#" title="Link to this Spot in Presentation">Link</a>
					<a id="switch" href="#" title="Switch Placement of Video and Slides">Switch</a>
				</div>
				<div id="control-progress">
					<div id="progress-bar"></div>
					<div id="slide-markers"></div>
					<div id="media-progress"></div>
				</div>
			</div><!-- /#control-box -->
		</div><!-- /#left -->
		<?php $cls = (isset($presentation->videoPosition) && $presentation->videoPosition == "left" && strtolower($presentation->type) == 'video') ? "move-left": ""; ?>
		<div id="presenter-right">
			<div id="media" class="<?php echo $cls; ?>">
				<?php if(strtolower($presentation->type) == 'video') : ?>
					<video id="player" preload="auto" controls="controls" data-mediaid="<?php echo $rr->id; ?>">
						<?php foreach($presentation->media as $media): ?>
						   	<?php
								switch( $media->type )
								{
									case 'ogg':
									case 'ogv':     $type = "video/ogg;";    break;
									case 'webm':    $type = "video/webm;";   break;
									case 'mp4':
									case 'm4v':
									default:        $type = "video/mp4;";    break;
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
									$source = $content_folder . DS . $source;
									if (in_array($media->type, array('mp4','m4v')))
									{
										$mp4 = $content_folder . DS . $mp4;
									}
								}
							?>
						   	<source src="<?php echo $source; ?>" type="<?php echo $type; ?>">
						<?php endforeach; ?>
						
						<a href="<?php echo $mp4; ?>" 
							id="flowplayer" 
							data-mediaid="<?php echo $rr->id; ?>"></a>
						<?php if(count($presentation->subtitles) > 0) : ?>
							<?php foreach($presentation->subtitles as $subtitle) : ?>
								<?php
									//get file modified time
									$source = $subtitle->source;
									$auto   = $subtitle->autoplay;
									
									//if were playing local files
									if (substr($subtitle->source, 0, 4) != 'http')
									{
										$modified = filemtime( JPATH_ROOT . $source );
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
				<?php else : ?>
					<audio id="player" preload="auto" controls="controls" data-mediaid="<?php echo $rr->id; ?>">          
						<?php foreach($presentation->media as $source): ?>
							<?php
								switch( $source->type )
								{
									case 'mp3':		$type = 'audio/mp3';	break;
									case 'ogv':
									case 'ogg':		$type = 'audio/ogg';	break;
								}
							?>
							<source src="<?php echo $content_folder.DS.$source->source; ?>" type="<?php echo $type; ?>" />
						<?php endforeach; ?>
						<a href="<?php echo $content_folder.DS.$presentation->media[0]->source; ?>" id="flowplayer" duration="<?php if($presentation->duration) { echo $presentation->duration; } ?>" data-mediaid="<?php echo $rr->id; ?>"></a>
					</audio>
					
					<?php if($presentation->placeholder) : ?>
						<img src="<?php echo $content_folder.DS.$presentation->placeholder; ?>" title="" id="placeholder" />
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div id="list">
				<ul id="list_items">
					<?php $num = 0; $counter = 0; $last_slide_id = 0; ?>
					<?php foreach($presentation->slides as $slide) : ?>
						<?php if((int)$slide->slide != $last_slide_id) : ?>
							<li id="list_<?php echo $counter; ?>">
								<?php
									//use thumb if possible
									$thumb = $content_folder.DS.$slide->media;
									if($slide->thumb && file_exists(JPATH_ROOT . $content_folder.DS.$slide->thumb))
									{
										$thumb = $content_folder.DS.$slide->thumb;
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

										if(strlen($slide->title) > $max)
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
			<select id="transcript-selector"></select>
			<input type="text" id="transcript-search" placeholder="Search Transcript..." />
			<a href="javascript:void(0);" id="font-bigger"></a>
			<a href="javascript:void(0);" id="font-smaller"></a>
		</div>
		<div id="transcripts"></div>
	</div>
	
</div>
<div id="twofinger">Use two Fingers to Scroll</div>


<?php
$this->doc->setTitle(stripslashes($rr->title));
?>
