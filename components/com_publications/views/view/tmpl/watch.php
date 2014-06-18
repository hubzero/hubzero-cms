<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get the manifest for the presentation
$contents = file_get_contents($this->manifest); 

//content folder
$content_folder = $this->content_folder;

//decode the json formatted manifest so we can use the information
$presentation = json_decode( $contents );  
$presentation = $presentation->presentation;
?>
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

<div id="presenter-container">
	<div id="presenter-header">
		<div id="title"><?php echo $this->publication->title; ?></div>
		<div id="author"><?php if($this->authors) { echo 'by '.$this->helper->showContributors( $this->authors, false, true ); } ?></div>
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
		
		<div id="presenter-right">
			<div id="media">
				<?php if(strtolower($presentation->type) == 'video') : ?>
					<video id="player" preload="auto" controls="controls">
						<?php foreach($presentation->media as $source): ?>
						   	<source src="<?php echo $content_folder.DS.$source->source; ?>" >
						<?php endforeach; ?>
						<a href="<?php echo $content_folder.DS.$presentation->media[0]->source; ?>" id="flowplayer"></a>
					</video>
				<?php else : ?>
					<audio id="player" preload="auto" controls="controls">          
						<?php foreach($presentation->media as $source): ?>
							<source src="<?php echo $content_folder.DS.$source->source; ?>" />
						<?php endforeach; ?>
						<a href="<?php echo $content_folder.DS.$presentation->media[0]->source; ?>" id="flowplayer" duration="<?php if($presentation->duration) { echo $presentation->duration; } ?>"></a>
					</audio>
				<?php endif; ?>
			</div>
			<div id="list">
				<ul id="list_items">
					<?php $num = 0; $counter = 0; $last_slide_id = 0; ?>
					<?php foreach($presentation->slides as $slide) : ?>
						<?php if((int)$slide->slide != $last_slide_id) : ?>
							<li id="list_<?php echo $counter; ?>">
								<img src="<?php echo $content_folder.DS.$slide->media; ?>" alt="<?php echo $slide->title; ?>" />
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
</div>
<a href="" id="shortcuts" title="Keyboard Shortcuts">Keyboard Shortcuts</a>
<div id="twofinger">Use two Fingers to Scroll</div>

<?php
$this->doc->setTitle($this->publication->title);
?>