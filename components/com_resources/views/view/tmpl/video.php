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
?>


<?php if(count($this->videos) > 0) : ?>
	<video controls="controls" id="video-player">
		<?php foreach($this->videos as $v) : ?>
			<?php
				$info = pathinfo($v);
				$type = "";
				switch( $info['extension'] )
				{
					case 'mp4': 	$type = "video/mp4";	break;
					case 'ogv':		$type = "video/ogg";	break;
					case 'webm':	$type = "video/webm";	break;
				}
			?>
			<source src="<?php echo $this->path . DS . $v; ?>" type="<?php echo $type; ?>" />
		<?php endforeach; ?>
		
		<a href="<?php echo $this->path . DS . $this->video_mp4; ?>" id="video-flowplayer"></a>
		
		<?php if(count($this->subs) > 0) : ?>
			<?php foreach($this->subs as $s) : ?>
				<?php $info2 = pathinfo($s); ?>
				<div data-type="subtitle" data-lang="<?php echo $info2['filename']; ?>" data-src="<?php echo $this->path . DS . $s; ?>"></div>
			<?php endforeach; ?>
		<?php endif; ?>
	</video>
<?php endif; ?>

