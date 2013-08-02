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

//base url for the resource
$base = DS . trim($this->config->get('uploadpath'), DS);

//presentation manifest
$presentation = $this->manifest->presentation;

//determine height and width
$width  = (isset($presentation->width) && $presentation->width != 0) ? $presentation->width . 'px' : 'auto';
$height = (isset($presentation->height) && $presentation->height != 0) ? $presentation->height . 'px' : 'auto';
?>

<div id="video-container">
	<?php if(count($presentation->media) > 0) : ?>
		<video controls="controls" id="video-player" data-mediaid="<?php echo $this->resource->id; ?>">
			<?php foreach($presentation->media as $video) : ?>
				<?php
					switch( $video->type )
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
		
			<?php if(count($presentation->subtitles) > 0) : ?>
				<?php foreach($presentation->subtitles as $subtitle) : ?>
					<?php
						//get file modified time
						$source = $subtitle->source;
						$auto   = $subtitle->autoplay;
						
						//if were playing local files
						if (substr($subtitle->source, 0, 4) != 'http')
						{
							$source   = $base . $source;
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
	<?php endif; ?>   
</div><!-- /#video-container -->

<div id="transcript-container">
	<div id="transcript-toolbar">
		<select id="transcript-selector"></select>
		<input type="text" id="transcript-search" placeholder="Search Transcript..." />
		<a href="javascript:void(0);" id="font-bigger"></a>
		<a href="javascript:void(0);" id="font-smaller"></a>
	</div>
	<div id="transcripts"></div>
</div>

<?php
$document =& JFactory::getDocument();
$document->setTitle( $this->resource->title );
?>
