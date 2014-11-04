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

$this->css('video.css')
     ->js('video.js')
     ->js('hubpresenter.plugins.js')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

$html5video = array("mp4","m4v","webm","ogv");
?>
<div id="video-container">
	<?php if (count($this->attachments) > 1) {
		// Multi formats for video provided (TBD)
	 ?>
		<!--<video controls="controls" id="video-player">
			<source src="/resource_files/2011/10/12196/Hilliardrollin.mp4" type="video/mp4" />
			<source src="/resource_files/2011/10/12196/Hilliardrollin.ogv" type="video/ogg" />
			<source src="/resource_files/2011/10/12196/Hilliardrollin.webm" type="video/webm" />
			<a href="/resource_files/2011/10/12196/Hilliardrollin.mp4" id="video-flowplayer"></a>
			<div data-type="subtitle" data-lang="English" data-src="/resource_files/2011/10/12196/english.srt"></div>
		</video>
		-->
	<?php  } else if ($this->firstattach->ext == 'swf') {
		$html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$this->height.'" id="SlideContent" VIEWASTEXT>'."\n";
		$html .= ' <param name="movie" value="'. $this->firstattach->url .'" />'."\n";
		$html .= ' <param name="quality" value="high" />'."\n";
		$html .= ' <param name="menu" value="false" />'."\n";
		$html .= ' <param name="loop" value="false" />'."\n";
		$html .= ' <param name="scale" value="showall" />'."\n";
		$html .= ' <embed src="'. $this->firstattach->url .'" menu="false" quality="best" loop="false" width="100%" height="'.$this->height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'."\n";
		$html .= '</object>'."\n";
		echo $html;
	}
	else { // May not load in all browsers ?>
		<?php if (in_array($this->firstattach->ext, $html5video)) { ?>
		<video controls="controls" id="video-player">
			<source src="<?php echo $this->firstattach->url; ?>" type="<?php echo $this->firstattach->mimetype; ?>" />
		<?php } ?>
			<object data="<?php echo $this->firstattach->url; ?>" width="750" height="500">
				<embed src="<?php echo $this->firstattach->url; ?>" autoplay="true"></embed>
			</object>
		<?php if (in_array($this->firstattach->ext, $html5video)) { ?></video><?php } ?>
	<?php } ?>
</div>

<!--
<div id="video-container">
	<video controls="controls" id="video-player">
		<source src="/resource_files/2011/10/12196/Hilliardrollin.m4v" type="video/mp4" />
		<source src="/resource_files/2011/10/12196/Hilliardrollin.ogv" type="video/ogg" />
		<source src="/resource_files/2011/10/12196/Hilliardrollin.webm" type="video/webm" />
		<div data-type="subtitle" data-lang="English" data-src="https://dev.nanohub.org/resource_files/2011/10/12196/english.srt"></div>
		<a id="video-flowplayer" href="/resource_files/2011/10/12196/Hilliardrollin.m4v"></a>
	</video>
	<div id="video-toolbar">
		<a id="previous" href="#" title="Previous Slide">Previous</a>
		<a id="play-pause" href="#" title="Play Presentation">Pause</a>
		<a id="next" href="#" title="Next Slide">Next</a>
		<div id="volume-bar"></div>
	</div>
</div>
-->
